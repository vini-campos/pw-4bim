<?php
include('config.php');
include_once(ABSPATH . 'inc/auth.php');

// encerra a sessão por completo
$_SESSION = [];
if (ini_get('session.use_cookies')) {
	$p = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// abre uma sessão nova só para mostrar a mensagem
session_start();
$_SESSION['message'] = "Você saiu do sistema.";
$_SESSION['type'] = "success";

header('Location: ' . BASEURL . 'login.php');
exit;