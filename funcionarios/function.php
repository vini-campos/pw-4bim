<?php
ob_start();

include('../config.php');
include(DBAPI);
include_once('../inc/auth.php');

const FUNCIONARIOS_TABELA = 'Funcionario';

$funcionarios = null;
$funcionario = null;

function definir_erro($mensagens)
{
	$_SESSION['message'] = is_array($mensagens) ? implode('<br>', $mensagens) : $mensagens;
	$_SESSION['type'] = 'danger';
}

function formatData($data, $formato)
{
	if (empty($data)) {
		return '—';
	}
	$dt = new DateTime($data, new DateTimeZone('America/Sao_Paulo'));
	return $dt->format($formato);
}

function calcularIdade($dataNasc)
{
	$dt = DateTime::createFromFormat('!Y-m-d', $dataNasc);
	if (!$dt) {
		return '';
	}
	return (string) $dt->diff(new DateTime('today', new DateTimeZone('America/Sao_Paulo')))->y;
}

function dadosFuncionario($origem)
{
	$dataNasc = trim($origem['DataNasc'] ?? '');

	return [
		'Nome' => trim($origem['Nome'] ?? ''),
		'Endereco' => trim($origem['Endereco'] ?? ''),
		'Idade' => calcularIdade($dataNasc), // ignora qualquer Idade vinda do formulário
		'DataNasc' => $dataNasc,
	];
}

function validarFuncionario($dados)
{
	$erros = [];

	if ($dados['Nome'] === '') {
		$erros[] = 'O campo Nome é obrigatório.';
	} elseif (mb_strlen($dados['Nome']) > 50) {
		$erros[] = 'O Nome aceita no máximo 50 caracteres.';
	}

	if ($dados['Endereco'] === '') {
		$erros[] = 'O campo Endereço é obrigatório.';
	} elseif (mb_strlen($dados['Endereco']) > 50) {
		$erros[] = 'O Endereço aceita no máximo 50 caracteres.';
	}

	$dataNasc = DateTime::createFromFormat('!Y-m-d', $dados['DataNasc']);
	$errosData = DateTime::getLastErrors();
	if (!$dataNasc || ($errosData && ($errosData['warning_count'] || $errosData['error_count']))
		|| $dataNasc->format('Y-m-d') !== $dados['DataNasc']
		|| $dataNasc > new DateTime('today')) {
		$erros[] = 'Informe uma data de nascimento válida, que não seja futura.';
	} elseif ((int) $dados['Idade'] > 120) {
		$erros[] = 'A data de nascimento resulta em uma idade maior que 120 anos.';
	}

	return $erros;
}

function uploadFotoFuncionario($fotoAtual = null)
{
	if (empty($_FILES['foto']['name']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
		return $fotoAtual;
	}

	if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
		definir_erro('Não foi possível enviar a foto.');
		return false;
	}

	$extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
	if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
		definir_erro('Formato não permitido. Use JPG, PNG, GIF ou WEBP.');
		return false;
	}
	if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
		definir_erro('A foto deve ter no máximo 2MB.');
		return false;
	}
	if (@getimagesize($_FILES['foto']['tmp_name']) === false) {
		definir_erro('O arquivo enviado não é uma imagem válida.');
		return false;
	}

	$pasta = ABSPATH . 'uploads/funcionarios/';
	if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
		definir_erro('Não foi possível preparar a pasta para salvar a foto.');
		return false;
	}

	$nomeArquivo = bin2hex(random_bytes(8)) . '.' . $extensao;
	if (!move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . $nomeArquivo)) {
		definir_erro('Não foi possível salvar a foto no servidor.');
		return false;
	}

	return $nomeArquivo;
}

function apagarFotoFuncionario($nomeArquivo)
{
	if (empty($nomeArquivo)) {
		return;
	}
	$caminho = ABSPATH . 'uploads/funcionarios/' . basename($nomeArquivo);
	if (is_file($caminho)) {
		unlink($caminho);
	}
}

function index()
{
	global $funcionarios;
	$funcionarios = find_all(FUNCIONARIOS_TABELA, 'Nome');
}

function view($id = null)
{
	global $funcionario;

	$id = (int) $id;
	$funcionario = $id > 0 ? find(FUNCIONARIOS_TABELA, $id) : null;
	if (!$funcionario) {
		$_SESSION['message'] = 'Funcionário não encontrado.';
		$_SESSION['type'] = 'warning';
		header('Location: index.php');
		exit;
	}
}

function add()
{
	global $funcionario;
	exigir_login();

	if (empty($_POST['funcionario'])) {
		return;
	}

	$dados = dadosFuncionario($_POST['funcionario']);
	$funcionario = $dados;
	$erros = validarFuncionario($dados);
	if ($erros) {
		definir_erro($erros);
		return;
	}

	$foto = uploadFotoFuncionario();
	if ($foto === false) {
		return;
	}

	$registro = [
		'Nome' => $dados['Nome'],
		'Endereco' => $dados['Endereco'],
		'Idade' => (int) $dados['Idade'],
		'DataNasc' => $dados['DataNasc'] . ' 00:00:00',
	];
	if ($foto) {
		$registro['Foto'] = $foto;
	}

	if (save(FUNCIONARIOS_TABELA, $registro)) {
		header('Location: index.php');
		exit;
	}
	apagarFotoFuncionario($foto);
}

function edit()
{
	global $funcionario;
	exigir_login();

	$id = (int) ($_GET['id'] ?? 0);
	if ($id <= 0) {
		header('Location: index.php');
		exit;
	}

	$atual = find(FUNCIONARIOS_TABELA, $id);
	if (!$atual) {
		$_SESSION['message'] = 'Funcionário não encontrado.';
		$_SESSION['type'] = 'warning';
		header('Location: index.php');
		exit;
	}

	if (isset($_POST['funcionario'])) {
		$dados = dadosFuncionario($_POST['funcionario']);
		$erros = validarFuncionario($dados);
		if ($erros) {
			definir_erro($erros);
			$funcionario = array_merge($atual, $dados);
			return;
		}

		$foto = uploadFotoFuncionario($atual['Foto'] ?? null);
		if ($foto === false) {
			$funcionario = array_merge($atual, $dados);
			return;
		}

		$registro = [
			'Nome' => $dados['Nome'],
			'Endereco' => $dados['Endereco'],
			'Idade' => (int) $dados['Idade'],
			'DataNasc' => $dados['DataNasc'] . ' 00:00:00',
		];
		if ($foto) {
			$registro['Foto'] = $foto;
		}

		if (update(FUNCIONARIOS_TABELA, $id, $registro)) {
			if ($foto && $foto !== ($atual['Foto'] ?? null)) {
				apagarFotoFuncionario($atual['Foto'] ?? null);
			}
			header('Location: index.php');
			exit;
		}

		if ($foto && $foto !== ($atual['Foto'] ?? null)) {
			apagarFotoFuncionario($foto);
		}
		$funcionario = array_merge($atual, $dados);
		return;
	}

	$funcionario = $atual;
}

function delete($id = null)
{
	exigir_login();

	$id = (int) $id;
	if ($id > 0) {
		$atual = find(FUNCIONARIOS_TABELA, $id);
		if ($atual) {
			if (remove(FUNCIONARIOS_TABELA, $id)) {
				apagarFotoFuncionario($atual['Foto'] ?? null);
			}
		} else {
			$_SESSION['message'] = 'Funcionário não encontrado.';
			$_SESSION['type'] = 'warning';
		}
	}

	header('Location: index.php');
	exit;
}
?>