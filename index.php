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

<header class="page-heading">
	<div>
		<h1><?php echo esta_logado() ? 'Olá, ' . h($_SESSION['usuario']['login']) : 'Olá, visitante'; ?></h1>
		<p class="page-description">
			<?php echo esta_logado() ? 'Escolha por onde começar.' : 'Você pode consultar os cadastros. Entre para criar e editar.'; ?>
		</p>
	</div>
</header>

<?php if (!$erro): ?>

	<div class="painel">

		<div class="painel-linha">
			<a href="<?php echo BASEURL; ?>funcionarios/" class="painel-card">
				<span class="icone"><i class="fa-solid fa-users"></i></span>
				<span><strong>Funcionários</strong><small>Consultar e gerenciar a equipe</small></span>
			</a>
			<?php if (esta_logado()): ?>
				<a href="<?php echo BASEURL; ?>funcionarios/add.php" class="painel-card novo">
					<span class="icone"><i class="fa-solid fa-user-plus"></i></span>
					<span><strong>Novo funcionário</strong><small>Adicionar alguém à equipe</small></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="painel-linha">
			<a href="<?php echo BASEURL; ?>customers/" class="painel-card">
				<span class="icone"><i class="fa-solid fa-address-book"></i></span>
				<span><strong>Clientes</strong><small>Consultar cadastros de clientes</small></span>
			</a>
			<?php if (esta_logado()): ?>
				<a href="<?php echo BASEURL; ?>customers/add.php" class="painel-card novo">
					<span class="icone"><i class="fa-solid fa-user-tag"></i></span>
					<span><strong>Novo cliente</strong><small>Adicionar um novo cliente</small></span>
				</a>
			<?php endif; ?>
		</div>

		<?php if (eh_admin()): ?>
			<div class="painel-linha">
				<a href="<?php echo BASEURL; ?>usuarios/" class="painel-card">
					<span class="icone"><i class="fa-solid fa-users-gear"></i></span>
					<span><strong>Usuários</strong><small>Gerenciar acessos ao sistema</small></span>
				</a>
				<a href="<?php echo BASEURL; ?>usuarios/add.php" class="painel-card novo">
					<span class="icone"><i class="fa-solid fa-user-shield"></i></span>
					<span><strong>Novo usuário</strong><small>Criar acesso ao sistema</small></span>
				</a>
			</div>
		<?php endif; ?>

	</div>

<?php else: ?>
	<div class="alert alert-danger" role="alert">
		<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.<br>
		<?= h($erro) ?>
	</div>
<?php endif; ?>

<?php include FOOTER_TEMPLATE; ?>