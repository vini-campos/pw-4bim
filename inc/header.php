<?php
$rotaAtual = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($rotaAtual, '/funcionarios/') !== false) {
    $secaoAtual = 'funcionarios';
} elseif (strpos($rotaAtual, '/customers/') !== false) {
    $secaoAtual = 'clientes';
} elseif (strpos($rotaAtual, '/usuarios/') !== false) {
    $secaoAtual = 'usuarios';
} else {
    $secaoAtual = 'painel';
}
$navAtivo = function ($nome) use ($secaoAtual) {
    return $secaoAtual === $nome ? ' class="ativo" aria-current="page"' : '';
};
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Gerenciador funcionários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gerenciador: cadastro de funcionários, clientes e usuários.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/style.css?v=4">
</head>

<body class="app-body">
    <nav class="navbar navbar-expand-lg topnav" aria-label="Menu principal">
        <div class="topnav-inner">
            <a class="brand" href="<?php echo BASEURL; ?>index.php">
                <span class="brand-name">Gerenciador</span>
            </a>

            <button class="menu-toggle d-lg-none ms-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#menu-principal" aria-controls="menu-principal" aria-expanded="false"
                aria-label="Abrir menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="menu-principal">
                <ul class="nav-topo">
                    <li><a href="<?php echo BASEURL; ?>index.php"<?php echo $navAtivo('painel'); ?>>
                            <i class="fa-solid fa-house"></i> Painel</a></li>
                    <li><a href="<?php echo BASEURL; ?>funcionarios/"<?php echo $navAtivo('funcionarios'); ?>>
                            <i class="fa-solid fa-users"></i> Funcionários</a></li>
                    <li><a href="<?php echo BASEURL; ?>customers/"<?php echo $navAtivo('clientes'); ?>>
                            <i class="fa-solid fa-address-book"></i> Clientes</a></li>
                    <?php if (eh_admin()): ?>
                        <li><a href="<?php echo BASEURL; ?>usuarios/"<?php echo $navAtivo('usuarios'); ?>>
                                <i class="fa-solid fa-user-shield"></i> Usuários</a></li>
                    <?php endif; ?>
                </ul>

                <div class="topnav-user">
                    <?php if (esta_logado()): ?>
                        <?php $fotoMini = foto_usuario_url($_SESSION['usuario']['foto'] ?? null); ?>
                        <button type="button" class="user-chip" data-bs-toggle="modal" data-bs-target="#perfil-modal">
                            <?php if ($fotoMini): ?>
                                <img src="<?php echo h($fotoMini); ?>" alt="">
                            <?php else: ?>
                                <span class="avatar-vazio" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
                            <?php endif; ?>
                            <span class="info">
                                <strong><?php echo h($_SESSION['usuario']['login']); ?></strong>
                                <small><?php echo h($_SESSION['usuario']['perfil']); ?></small>
                            </span>
                        </button>
                    <?php else: ?>
                        <div class="topnav-guest">
                            <a class="link-cadastro" href="<?php echo BASEURL; ?>cadastro.php">Criar acesso</a>
                            <a class="btn btn-secondary" href="<?php echo BASEURL; ?>login.php">
                                <i class="fa-solid fa-right-to-bracket"></i> Entrar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if (esta_logado()):
    $usuarioLogado = $_SESSION['usuario'];
    $ehAdmin = eh_admin();
?>
    <div class="modal fade" id="perfil-modal" tabindex="-1" aria-labelledby="perfil-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="perfil-titulo">
                        <i class="fa-solid fa-circle-user"></i> Meu perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <?php $fotoPerfil = foto_usuario_url($usuarioLogado['foto'] ?? null); ?>
                        <?php if ($fotoPerfil): ?>
                            <img src="<?php echo h($fotoPerfil); ?>" alt="Foto de <?php echo h($usuarioLogado['login']); ?>"
                                class="perfil-foto">
                        <?php else: ?>
                            <i class="fa-solid fa-circle-user fa-4x"></i>
                        <?php endif; ?>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-sm-4">Usuário</dt>
                        <dd class="col-sm-8"><?php echo h($usuarioLogado['login']); ?></dd>

                        <dt class="col-sm-4">Perfil</dt>
                        <dd class="col-sm-8">
                            <span class="badge <?php echo $ehAdmin ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                                <?php echo h($usuarioLogado['perfil']); ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8">#<?php echo (int) $usuarioLogado['id']; ?></dd>

                        <dt class="col-sm-4">Acesso</dt>
                        <dd class="col-sm-8">
                            <?php echo $ehAdmin
                                ? 'Funcionários, clientes e usuários (acesso total).'
                                : 'Funcionários e clientes (cadastrar, editar e excluir).'; ?>
                        </dd>
                    </dl>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <a href="<?php echo BASEURL; ?>logout.php" class="btn btn-dark">
                        <i class="fa-solid fa-right-from-bracket"></i> Sair
                    </a>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>

    <div class="app-main">
    <main class="page">
