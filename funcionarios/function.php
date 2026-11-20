<?php
ob_start(); //output buffer aberto, para não dar erro de header location

include('../config.php');
include(DBAPI);
include_once('../inc/auth.php');

const MOTOS_TABELA = 'motos';

$customers = null;
$customer = null;

/**
 *  Formatar as datas
 */
function formatData($data, $formato)
{
	if (empty($data)) {
		return '—';
	}
	$dt = new Datetime($data, new DateTimeZone("America/Sao_Paulo"));
	return $dt->format($formato);
}

/**
 *  Formatar os telefones
 */
function telefone($tel)
{ //15999990000
	return "(" . substr($tel, 0, 2) . ")" . substr($tel, 2, 5)
		. "-" . substr($tel, 7, 4);
}

/**
 *  Formatar os cep´s
 */
function cep($cep)
{
	return substr($cep, 0, 5) . "-" . substr($cep, 5, 3);
}

/**
 *  Pega só os campos permitidos do formulário (ignora qualquer outro)
 */
function dadosMoto($origem)
{
	return [
		'Modelo' => trim($origem['Modelo'] ?? ''),
		'Marca'  => trim($origem['Marca'] ?? ''),
		'Motor'  => trim($origem['Motor'] ?? ''),
	];
}

/**
 *  Validação: campos obrigatórios e tamanho máximo (igual às colunas do banco)
 */
function validarMoto($dados)
{
	$limites = ['Modelo' => 50, 'Marca' => 30, 'Motor' => 20];
	$erros = [];

	foreach ($limites as $campo => $max) {
		if ($dados[$campo] === '') {
			$erros[] = "O campo $campo é obrigatório.";
		} elseif (mb_strlen($dados[$campo]) > $max) {
			$erros[] = "O campo $campo aceita no máximo $max caracteres.";
		}
	}
	return $erros;
}

function definir_erro($mensagens)
{
	$_SESSION['message'] = is_array($mensagens) ? implode('<br>', $mensagens) : $mensagens;
	$_SESSION['type'] = "danger";
}

/**
 *  Listagem de Motos (pública: visitante também vê)
 */
function index()
{
	global $customers;
	$customers = find_all(MOTOS_TABELA);
}

/**
 *  Upload da imagem.
 *  Retorna: nome do arquivo novo, a imagem atual (se nada foi enviado) ou false se deu erro.
 */
function uploadImagem($imagemAtual = null)
{
	if (empty($_FILES['customer']['name']['imagem']) || $_FILES['customer']['error']['imagem'] === UPLOAD_ERR_NO_FILE) {
		return $imagemAtual;
	}

	if ($_FILES['customer']['error']['imagem'] !== UPLOAD_ERR_OK) {
		definir_erro("Não foi possível enviar a imagem.");
		return false;
	}

	$extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
	$nomeOriginal = $_FILES['customer']['name']['imagem'];
	$extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

	if (!in_array($extensao, $extensoesPermitidas)) {
		definir_erro("Formato de imagem não permitido. Use JPG, PNG, GIF ou WEBP.");
		return false;
	}

	if ($_FILES['customer']['size']['imagem'] > 2 * 1024 * 1024) { // 2MB
		definir_erro("A imagem deve ter no máximo 2MB.");
		return false;
	}

	// confere se o conteúdo é mesmo uma imagem, e não só a extensão
	if (@getimagesize($_FILES['customer']['tmp_name']['imagem']) === false) {
		definir_erro("O arquivo enviado não é uma imagem válida.");
		return false;
	}

	$pastaUploads = __DIR__ . '/../uploads/';
	if (!is_dir($pastaUploads)) {
		mkdir($pastaUploads, 0755, true);
	}

	// nome curto o suficiente para caber na coluna varchar(30)
	$nomeArquivo = substr(md5(uniqid('', true)), 0, 20) . '.' . $extensao;

	if (!move_uploaded_file($_FILES['customer']['tmp_name']['imagem'], $pastaUploads . $nomeArquivo)) {
		definir_erro("Não foi possível salvar a imagem no servidor.");
		return false;
	}

	return $nomeArquivo;
}

/**
 *  Apaga o arquivo de imagem da pasta uploads
 */
function apagarImagem($nomeArquivo)
{
	if (empty($nomeArquivo)) {
		return;
	}
	$caminho = __DIR__ . '/../uploads/' . basename($nomeArquivo);
	if (is_file($caminho)) {
		unlink($caminho);
	}
}

/**
 *  Visualização de uma Moto (pública)
 */
function view($id = null)
{
	global $customer;

	$id = (int) $id;
	$customer = $id > 0 ? find(MOTOS_TABELA, $id) : null;

	if (!$customer) {
		$_SESSION['message'] = "Moto não encontrada.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}
}

/**
 *  Cadastro de Motos (exige login)
 */
function add()
{
	global $customer;
	exigir_login();

	if (empty($_POST['customer'])) {
		return;
	}

	$dados = dadosMoto($_POST['customer']);
	$customer = $dados; // mantém o que foi digitado se houver erro

	$erros = validarMoto($dados);
	if ($erros) {
		definir_erro($erros);
		return;
	}

	$imagem = uploadImagem();
	if ($imagem === false) {
		return;
	}

	$hoje = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
	$dados['DataCad'] = $hoje->format("Y-m-d H:i:s");
	if ($imagem) {
		$dados['Imagem'] = $imagem;
	}

	save(MOTOS_TABELA, $dados);
	header('location: index.php');
	exit;
}

/**
 *	Atualização/Edição de Moto (exige login)
 */
function edit()
{
	global $customer;
	exigir_login();

	$id = (int) ($_GET['id'] ?? 0);
	if ($id <= 0) {
		header('location: index.php');
		exit;
	}

	$atual = find(MOTOS_TABELA, $id);
	if (!$atual) {
		$_SESSION['message'] = "Moto não encontrada.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}

	if (isset($_POST['customer'])) {
		$dados = dadosMoto($_POST['customer']);
		$erros = validarMoto($dados);

		if ($erros) {
			definir_erro($erros);
			$customer = array_merge($atual, $dados); // mantém o que foi digitado
			return;
		}

		$imagem = uploadImagem($atual['Imagem'] ?? null);
		if ($imagem === false) {
			$customer = array_merge($atual, $dados);
			return;
		}

		if ($imagem) {
			$dados['Imagem'] = $imagem;
		}

		update(MOTOS_TABELA, $id, $dados);

		// trocou a imagem: apaga a antiga
		if ($imagem && $imagem !== ($atual['Imagem'] ?? null)) {
			apagarImagem($atual['Imagem'] ?? null);
		}

		header("location: index.php");
		exit;
	}

	$customer = $atual;
}

/**
 *  Exclusão de uma Moto (exige login)
 */
function delete($id = null)
{
	exigir_login();

	$id = (int) $id;
	if ($id > 0) {
		$atual = find(MOTOS_TABELA, $id);

		if ($atual) {
			remove(MOTOS_TABELA, $id);

			if (($_SESSION['type'] ?? '') === 'success') {
				apagarImagem($atual['Imagem'] ?? null);
			}
		} else {
			$_SESSION['message'] = "Moto não encontrada.";
			$_SESSION['type'] = "warning";
		}
	}

	header("location: index.php");
	exit;
}

?>