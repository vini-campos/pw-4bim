<?php
ob_start();

include('../config.php');
include(DBAPI);
include_once('../inc/auth.php');

const CLIENTES_TABELA = 'Cliente';
const CLIENTES_PK = 'Id_Cli';

$clientes = null;
$cliente = null;

/** (00) 00000-0000 ou (00) 0000-0000 */
function formatTelefone($tel)
{
	$d = preg_replace('/\D/', '', (string) $tel);
	if (strlen($d) === 11) {
		return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 5), substr($d, 7));
	}
	if (strlen($d) === 10) {
		return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 4), substr($d, 6));
	}
	return $tel ? $tel : '—';
}

/** 000.000.000-00 */
function formatCpf($cpf)
{
	$d = preg_replace('/\D/', '', (string) $cpf);
	if (strlen($d) !== 11) {
		return $cpf;
	}
	return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2);
}

/** Confere os dígitos verificadores do CPF */
function validar_cpf($cpf)
{
	$cpf = preg_replace('/\D/', '', (string) $cpf);

	if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
		return false;
	}

	for ($t = 9; $t < 11; $t++) {
		$soma = 0;
		for ($c = 0; $c < $t; $c++) {
			$soma += $cpf[$c] * (($t + 1) - $c);
		}
		$digito = ((10 * $soma) % 11) % 10;
		if ($cpf[$c] != $digito) {
			return false;
		}
	}
	return true;
}

/** Pega só os campos permitidos do formulário */
function dadosCliente($origem)
{
	return [
		'Nome_Cli'     => trim($origem['Nome_Cli'] ?? ''),
		'Tel_Cli'      => preg_replace('/\D/', '', $origem['Tel_Cli'] ?? ''),
		'CPF_Cli'      => preg_replace('/\D/', '', $origem['CPF_Cli'] ?? ''),
		'Email_Cli'    => trim($origem['Email_Cli'] ?? ''),
		'Endereco_Cli' => trim($origem['Endereco_Cli'] ?? ''),
	];
}

/** Validação dos campos. $idAtual = ID do cliente sendo editado (0 no cadastro) */
function validarCliente($d, $idAtual = 0)
{
	$erros = [];

	if ($d['Nome_Cli'] === '') {
		$erros[] = "O campo Nome é obrigatório.";
	} elseif (mb_strlen($d['Nome_Cli']) > 100) {
		$erros[] = "O Nome aceita no máximo 100 caracteres.";
	}

	if ($d['CPF_Cli'] === '') {
		$erros[] = "O campo CPF é obrigatório.";
	} elseif (!validar_cpf($d['CPF_Cli'])) {
		$erros[] = "CPF inválido.";
	} else {
		$outro = find_by(CLIENTES_TABELA, 'CPF_Cli', formatCpf($d['CPF_Cli']));
		if ($outro && (int) $outro[CLIENTES_PK] !== (int) $idAtual) {
			$erros[] = "Já existe um cliente cadastrado com esse CPF.";
		}
	}

	if ($d['Tel_Cli'] !== '' && !in_array(strlen($d['Tel_Cli']), [10, 11])) {
		$erros[] = "Telefone inválido. Informe DDD + número (10 ou 11 dígitos).";
	}

	if ($d['Email_Cli'] !== '') {
		if (mb_strlen($d['Email_Cli']) > 100) {
			$erros[] = "O E-mail aceita no máximo 100 caracteres.";
		} elseif (!filter_var($d['Email_Cli'], FILTER_VALIDATE_EMAIL)) {
			$erros[] = "E-mail inválido.";
		}
	}

	if (mb_strlen($d['Endereco_Cli']) > 200) {
		$erros[] = "O Endereço aceita no máximo 200 caracteres.";
	}

	return $erros;
}

/** Deixa os dados no formato que vai para o banco (CPF formatado, vazios viram NULL) */
function prepararCliente($d)
{
	return [
		'Nome_Cli'     => $d['Nome_Cli'],
		'Tel_Cli'      => $d['Tel_Cli'] !== '' ? $d['Tel_Cli'] : null,
		'CPF_Cli'      => formatCpf($d['CPF_Cli']),
		'Email_Cli'    => $d['Email_Cli'] !== '' ? $d['Email_Cli'] : null,
		'Endereco_Cli' => $d['Endereco_Cli'] !== '' ? $d['Endereco_Cli'] : null,
	];
}

function definir_erro($mensagens)
{
	$_SESSION['message'] = is_array($mensagens) ? implode('<br>', $mensagens) : $mensagens;
	$_SESSION['type'] = "danger";
}

/** Listagem (pública) */
function index()
{
	global $clientes;
	$clientes = find_all(CLIENTES_TABELA, 'Nome_Cli');
}

/** Visualização (pública) */
function view($id = null)
{
	global $cliente;

	$id = (int) $id;
	$cliente = $id > 0 ? find(CLIENTES_TABELA, $id, CLIENTES_PK) : null;

	if (!$cliente) {
		$_SESSION['message'] = "Cliente não encontrado.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}
}

/** Cadastro (exige login) */
function add()
{
	exigir_login();

	if (empty($_POST['cliente'])) {
		return;
	}

	$dados = dadosCliente($_POST['cliente']);
	$erros = validarCliente($dados);
	if ($erros) {
		definir_erro($erros);
		global $cliente;
		$cliente = $dados; // mantém o que foi digitado
		return;
	}

	if (save(CLIENTES_TABELA, prepararCliente($dados))) {
		header('location: index.php');
		exit;
	}
}

/** Edição (exige login) */
function edit()
{
	global $cliente;
	exigir_login();

	$id = (int) ($_GET['id'] ?? 0);
	if ($id <= 0) {
		header('location: index.php');
		exit;
	}

	$atual = find(CLIENTES_TABELA, $id, CLIENTES_PK);
	if (!$atual) {
		$_SESSION['message'] = "Cliente não encontrado.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}

	if (isset($_POST['cliente'])) {
		$dados = dadosCliente($_POST['cliente']);
		$erros = validarCliente($dados, $id);

		if ($erros) {
			definir_erro($erros);
			$cliente = array_merge($atual, $dados);
			return;
		}

		if (update(CLIENTES_TABELA, $id, prepararCliente($dados), CLIENTES_PK)) {
			header('location: index.php');
			exit;
		}
		$cliente = array_merge($atual, $dados);
		return;
	}

	$cliente = $atual;
}

/** Exclusão (exige login) */
function delete($id = null)
{
	exigir_login();

	$id = (int) $id;
	if ($id > 0) {
		if (find(CLIENTES_TABELA, $id, CLIENTES_PK)) {
			remove(CLIENTES_TABELA, $id, CLIENTES_PK);
		} else {
			$_SESSION['message'] = "Cliente não encontrado.";
			$_SESSION['type'] = "warning";
		}
	}

	header("location: index.php");
	exit;
}
?>