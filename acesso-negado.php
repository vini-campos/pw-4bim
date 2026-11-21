<?php
include 'config.php';
include_once ABSPATH . 'inc/auth.php';

if (!esta_logado()) {
	header('Location: ' . BASEURL . 'login.php');
	exit;
}

if (eh_admin()) {
	header('Location: ' . BASEURL . 'index.php');
	exit;
}

http_response_code(403);
include HEADER_TEMPLATE;
?>

<section class="text-center py-4">
    <span class="welcome-icon mx-auto mb-4" aria-hidden="true">
        <i class="fa-solid fa-lock"></i>
    </span>
    <p class="eyebrow">Erro 403</p>
    <h1>Acesso não autorizado</h1>
    <p class="page-description mb-4">
        Seu perfil não tem o nível necessário para acessar esta página.
    </p>
    <a href="<?php echo BASEURL; ?>index.php" class="btn btn-primary">
        <i class="fa-solid fa-house"></i> Voltar ao painel
    </a>
</section>

<?php include FOOTER_TEMPLATE; ?>
