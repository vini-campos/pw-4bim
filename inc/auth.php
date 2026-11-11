<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

/** Quem está logado? Guardado na sessão no momento do login */
function esta_logado()
{
	return !empty($_SESSION['usuario']);
}

/** Admin = Nome_Permi da tabela Permi igual a "Admin" */
function is_admin()
{
	return esta_logado() && ($_SESSION['usuario']['perfil'] ?? '') === 'Admin';
}

function exigir_login()
{
	if (!esta_logado()) {
		$_SESSION['message'] = "Faça login para continuar.";
		$_SESSION['type'] = "warning";
		header('Location: ' . BASEURL . 'login.php');
		exit;
	}
}

function exigir_admin()
{
	exigir_login();
	if (!is_admin()) {
		$_SESSION['message'] = "Você não tem permissão para acessar essa área.";
		$_SESSION['type'] = "danger";
		header('Location: ' . BASEURL . 'index.php');
		exit;
	}
}

/** Limpa a mensagem exibida (para ela não ficar aparecendo para sempre) */
function clear_messages()
{
	unset($_SESSION['message'], $_SESSION['type']);
}

/** URL da foto do funcionario, ou null se nao tiver (ou se o arquivo sumiu) */
function foto_funcionario_url($foto)
{
	if (empty($foto)) {
		return null;
	}
	$arquivo = basename($foto);
	if (!is_file(ABSPATH . 'uploads/funcionarios/' . $arquivo)) {
		return null;
	}
	return BASEURL . 'uploads/funcionarios/' . rawurlencode($arquivo);
}