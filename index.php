<?php
include 'config.php';
include DBAPI;
include_once ABSPATH . 'inc/auth.php';

// visitante que clicou em "Continuar sem login"
if (isset($_GET['visitante'])) {
	$_SESSION['visitante'] = true;
}

// abriu o site sem login e sem escolher ser visitante: vai para o login
if (!esta_logado() && empty($_SESSION['visitante'])) {
	header('Location: ' . BASEURL . 'login.php');
	exit;
}

include HEADER_TEMPLATE;
$erro = null;
try {
	$db = open_database();
} catch (Exception $e) {
	$erro = $e->getMessage();
}
?>

<section class="dashboard-welcome">
	<div>
		<p class="eyebrow">Painel de controle</p>
		<h1>Gestão de pessoas</h1>
		<p class="page-description">Organize as informações da sua equipe em um só lugar.</p>
	</div>
	<span class="welcome-icon" aria-hidden="true"><i class="fa-solid fa-people-group"></i></span>
</section>

<?php if (!$erro): ?>

	<div class="dashboard-grid">

		<?php if (esta_logado()): ?>
			<a href="<?php echo BASEURL; ?>funcionarios/add.php" class="dashboard-card">
				<span class="dashboard-card-icon"><i class="fa-solid fa-user-plus"></i></span>
				<span><strong>Novo funcionário</strong><small>Adicionar alguém à equipe</small></span>
			</a>
		<?php endif; ?>

		<a href="<?php echo BASEURL; ?>funcionarios/" class="dashboard-card">
			<span class="dashboard-card-icon"><i class="fa-solid fa-people-group"></i></span>
			<span><strong>Funcionários</strong><small>Consultar e gerenciar a equipe</small></span>
		</a>

		<?php if (esta_logado()): ?>
			<a href="<?php echo BASEURL; ?>customers/add.php" class="dashboard-card">
				<span class="dashboard-card-icon"><i class="fa-solid fa-user-tag"></i></span>
				<span><strong>Novo cliente</strong><small>Adicionar um novo cliente</small></span>
			</a>
		<?php endif; ?>

		<a href="<?php echo BASEURL; ?>customers/" class="dashboard-card">
			<span class="dashboard-card-icon"><i class="fa-solid fa-address-book"></i></span>
			<span><strong>Clientes</strong><small>Consultar cadastros de clientes</small></span>
		</a>

		<?php if (eh_admin()): ?>
			<a href="<?php echo BASEURL; ?>usuarios/add.php" class="dashboard-card">
				<span class="dashboard-card-icon"><i class="fa-solid fa-user-shield"></i></span>
				<span><strong>Novo usuário</strong><small>Criar acesso ao sistema</small></span>
			</a>

			<a href="<?php echo BASEURL; ?>usuarios/" class="dashboard-card">
				<span class="dashboard-card-icon"><i class="fa-solid fa-users-gear"></i></span>
				<span><strong>Usuários</strong><small>Gerenciar acessos ao sistema</small></span>
			</a>
		<?php endif; ?>

	</div>

<?php else: ?>
	<div class="alert alert-danger" role="alert">
		<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.<br>
		<?= h($erro) ?>
	</div>
<?php endif; ?>

<?php include FOOTER_TEMPLATE; ?>