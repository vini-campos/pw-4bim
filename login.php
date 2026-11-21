<?php
include('config.php');
include(DBAPI);
include_once(ABSPATH . 'inc/auth.php');

// quem já está logado não precisa ver a tela de login
if (esta_logado()) {
	header('Location: ' . BASEURL . 'index.php');
	exit;
}

// token anti-CSRF
if (empty($_SESSION['csrf'])) {
	$_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$erro = null;
$loginDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$loginDigitado = trim($_POST['login'] ?? '');
	$senha = $_POST['senha'] ?? '';
	$token = $_POST['csrf'] ?? '';

	if (!hash_equals($_SESSION['csrf'], $token)) {
		$erro = "Sessão expirada. Tente novamente.";
	} elseif ($loginDigitado === '' || $senha === '') {
		$erro = "Informe usuário e senha.";
	} else {
		try {
			$db = open_database();

			// sem JOIN: o nível de acesso está na própria tabela Usuarios
			$stmt = $db->prepare(
				"SELECT Id_Usu, Login_usu, Senha_usu, Ativo_usu, Foto_usu, Nivel_usu
				FROM Usuarios
				WHERE Login_usu = ?
				LIMIT 1"
			);
			$stmt->bind_param('s', $loginDigitado);
			$stmt->execute();
			$stmt->bind_result($idUsu, $loginUsu, $hash, $ativo, $foto, $nivel);
			$achou = $stmt->fetch();
			$stmt->close();
			close_database($db);

			if ($achou && (int) $ativo === 1 && password_verify($senha, $hash)) {
				session_regenerate_id(true); // evita fixação de sessão

				$_SESSION['usuario'] = [
					'id'     => (int) $idUsu,
					'login'  => $loginUsu,
					// ENUM volta como string: '1' = gerente, '0' = funcionário normal
					// mantive "Admin"/"Comum" para não quebrar o que já checa o perfil
					'perfil' => ($nivel === '1') ? 'Admin' : 'Comum',
					'foto'   => $foto,   // pode ser null
				];
				$_SESSION['csrf'] = bin2hex(random_bytes(32));
				unset($_SESSION['message'], $_SESSION['type']);

				header('Location: ' . BASEURL . 'index.php');
				exit;
			}

			// mensagem única de propósito: não revela se o usuário existe
			$erro = "Usuário ou senha inválidos.";
		} catch (Exception $e) {
			$erro = "Não foi possível acessar o sistema agora. Tente novamente.";
		}
	}
}

// aviso vindo de outra página (ex.: "Faça login para continuar.")
$aviso = null;
if (!empty($_SESSION['message'])) {
	$tiposOk = ['success', 'danger', 'warning', 'info'];
	$tipo = in_array($_SESSION['type'] ?? '', $tiposOk) ? $_SESSION['type'] : 'info';
	$aviso = ['texto' => $_SESSION['message'], 'tipo' => $tipo];
	unset($_SESSION['message'], $_SESSION['type']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<title>Entrar | Crachá</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/all.min.css">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/style.css?v=2">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/login.css?v=2">
</head>

<body class="auth-body">
	<div class="auth">

		<a class="auth-brand" href="<?php echo BASEURL; ?>index.php" style="text-decoration:none">
			<i class="fa-solid fa-id-badge" aria-hidden="true"></i>
			<span class="nome">Crachá</span>
		</a>

		<div class="auth-card">
			<h1>Entrar</h1>
			<p class="auth-sub">Acesse com seu usuário e senha.</p>

			<?php if ($aviso): ?>
				<div class="alert alert-<?php echo $aviso['tipo']; ?>" role="alert">
					<?php echo $aviso['texto']; ?>
				</div>
			<?php endif; ?>

			<?php if ($erro): ?>
				<div class="alert alert-danger" role="alert">
					<?php echo htmlspecialchars($erro); ?>
				</div>
			<?php endif; ?>

			<form action="login.php" method="post">
				<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">

				<div class="form-group">
					<label for="login">Usuário</label>
					<input type="text" class="form-control" id="login" name="login" maxlength="50"
						value="<?php echo htmlspecialchars($loginDigitado); ?>" required autofocus>
				</div>

				<div class="form-group">
					<label for="senha">Senha</label>
					<input type="password" class="form-control" id="senha" name="senha" required>
				</div>

				<button type="submit" class="btn-login">
					<i class="fa-solid fa-right-to-bracket"></i> Entrar
				</button>
			</form>

			<div class="auth-links">
				<a href="cadastro.php">Ainda não tem usuário? Cadastre-se</a>
				<a href="index.php?visitante=1">Continuar sem login</a>
			</div>
		</div>

		<p class="auth-footer">&copy; <?php echo date('Y'); ?> Vinicius Raphael &amp; Caio Henrique</p>

	</div>
</body>

</html>
