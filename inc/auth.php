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
function eh_admin()
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
	if (!eh_admin()) {
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

/** Escapa texto para exibir no HTML (evita XSS) */
function h($texto)
{
	return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** URL da foto do usuário, ou null se não tiver (ou se o arquivo sumiu) */
function foto_usuario_url($foto)
{
	if (empty($foto)) {
		return null;
	}
	$arquivo = basename($foto);
	if (!is_file(ABSPATH . 'uploads/usuarios/' . $arquivo)) {
		return null;
	}
	return BASEURL . 'uploads/usuarios/' . rawurlencode($arquivo);
}

/** Mostra a mensagem da sessão (se houver) e limpa */
function exibir_mensagem()
{
	if (empty($_SESSION['message'])) {
		return;
	}
	$tiposOk = ['success', 'danger', 'warning', 'info'];
	$tipo = in_array($_SESSION['type'] ?? '', $tiposOk) ? $_SESSION['type'] : 'info';

	echo '<div class="alert alert-' . $tipo . ' alert-dismissible fade show" role="alert">'
		. $_SESSION['message']
		. '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>'
		. '</div>';

	clear_messages();
}