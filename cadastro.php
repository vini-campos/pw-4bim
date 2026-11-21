<?php
include 'config.php';
include DBAPI;
include_once ABSPATH . 'inc/auth.php';

if (esta_logado()) {
	header('Location: ' . BASEURL . 'index.php');
	exit;
}

if (empty($_SESSION['csrf_cadastro'])) {
	$_SESSION['csrf_cadastro'] = bin2hex(random_bytes(32));
}

$erro = null;
$loginDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$loginDigitado = trim($_POST['login'] ?? '');
	$senha = $_POST['senha'] ?? '';
	$confirmacao = $_POST['confirmacao'] ?? '';
	$token = $_POST['csrf'] ?? '';

	if (!hash_equals($_SESSION['csrf_cadastro'], $token)) {
		$erro = 'Sessão expirada. Atualize a página e tente novamente.';
	} elseif (!preg_match('/^[A-Za-z0-9._ -]{3,50}$/', $loginDigitado)) {
		$erro = 'O usuário deve ter de 3 a 50 caracteres: letras, números, espaços, ponto, hífen ou sublinhado.';
	} elseif (strlen($senha) < 6 || strlen($senha) > 72) {
		$erro = 'A senha deve ter entre 6 e 72 caracteres.';
	} elseif ($senha !== $confirmacao) {
		$erro = 'As senhas informadas não coincidem.';
	} else {
		$db = null;
		$stmt = null;

		try {
			$db = open_database();
			$hash = password_hash($senha, PASSWORD_DEFAULT);
			$stmt = $db->prepare(
				"INSERT INTO Usuarios (Login_usu, Senha_usu, Nivel_usu, Ativo_usu)
				VALUES (?, ?, '0', 1)"
			);
			$stmt->bind_param('ss', $loginDigitado, $hash);
			$stmt->execute();

			$_SESSION['csrf_cadastro'] = bin2hex(random_bytes(32));
			$_SESSION['message'] = 'Cadastro realizado. Entre com seu usuário e senha.';
			$_SESSION['type'] = 'success';
			header('Location: ' . BASEURL . 'login.php');
			exit;
		} catch (mysqli_sql_exception $e) {
			if ((int) $e->getCode() === 1062) {
				$erro = 'Esse nome de usuário já está em uso.';
			} else {
				error_log($e->getMessage());
				$erro = 'Não foi possível concluir o cadastro agora. Tente novamente.';
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			$erro = 'Não foi possível acessar o sistema agora. Tente novamente.';
		} finally {
			if ($stmt instanceof mysqli_stmt) {
				$stmt->close();
			}
			if ($db instanceof mysqli) {
				$db->close();
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<title>Criar acesso | Gestão de Pessoas</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/all.min.css">
	<link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/login.css">
</head>

<body class="login-page">
	<div class="login-box">
		<div class="login-brand">
			<i class="fa-solid fa-people-group"></i><span class="login-tag">Gestão</span> de pessoas
		</div>

		<div class="login-card">
			<h1>Criar acesso</h1>
			<p class="login-sub">Cadastre seu usuário para acessar o sistema como funcionário.</p>

			<?php if ($erro): ?>
				<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php endif; ?>

			<form action="cadastro.php" method="post">
				<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_cadastro'], ENT_QUOTES, 'UTF-8'); ?>">

				<div class="mb-3">
					<label for="login" class="form-label">Usuário</label>
					<input type="text" class="form-control" id="login" name="login" minlength="3" maxlength="50"
						pattern="[A-Za-z0-9._ -]+" autocomplete="username" required autofocus
						value="<?php echo htmlspecialchars($loginDigitado, ENT_QUOTES, 'UTF-8'); ?>">
					<small class="login-hint">3 a 50 caracteres: letras, números, espaços, ponto, hífen ou sublinhado.</small>
				</div>

				<div class="mb-3">
					<label for="senha" class="form-label">Senha</label>
					<input type="password" class="form-control" id="senha" name="senha" minlength="6" maxlength="72"
						autocomplete="new-password" required>
				</div>

				<div class="mb-3">
					<label for="confirmacao" class="form-label">Confirmar senha</label>
					<input type="password" class="form-control" id="confirmacao" name="confirmacao" minlength="6" maxlength="72"
						autocomplete="new-password" required>
				</div>

				<button type="submit" class="btn-login">
					<i class="fa-solid fa-user-plus"></i> Cadastrar
				</button>
			</form>

			<a href="login.php" class="login-visitor">Já tem acesso? Entrar</a>
		</div>

		<div class="login-footer">&copy; <?php echo date('Y'); ?> - Matheus Mello e Luiz Felipe</div>
	</div>
</body>

</html>
