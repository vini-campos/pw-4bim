<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Gestão de Pessoas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>assets/css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top mb-3" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASEURL; ?>index.php"><i class="fa-solid fa-people-group"></i>
                <span class="rl-tag">GESTÃO</span> DE PESSOAS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCrud"
                aria-controls="navbarCrud" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCrud">
                
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-people-group"></i> Funcionários
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASEURL; ?>funcionarios/">
                                <i class="fa-solid fa-users"></i> Ver equipe</a></li>
                        <?php if (esta_logado()): ?>
                            <li><a class="dropdown-item" href="<?php echo BASEURL; ?>funcionarios/add.php">
                                    <i class="fa-solid fa-user-plus"></i> Novo funcionário</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-users"></i> Clientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASEURL; ?>customers/">
                                <i class="fa-solid fa-users"></i> Gerenciar Clientes</a></li>
                        <?php if (esta_logado()): ?>
                            <li><a class="dropdown-item" href="<?php echo BASEURL; ?>customers/add.php">
                                    <i class="fa-solid fa-user-plus"></i> Novo Cliente</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if (eh_admin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-user-shield"></i> Usuários
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASEURL; ?>usuarios/">
                                    <i class="fa-solid fa-users-gear"></i> Gerenciar Usuários</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASEURL; ?>usuarios/add.php">
                                    <i class="fa-solid fa-user-plus"></i> Novo Usuário</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>
                
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (esta_logado()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#perfil-modal">
                                <?php $fotoMini = foto_usuario_url($_SESSION['usuario']['foto'] ?? null); ?>
                                <?php if ($fotoMini): ?>
                                    <img src="<?php echo h($fotoMini); ?>" alt="" class="perfil-mini">
                                <?php else: ?>
                                    <i class="fa-solid fa-circle-user"></i>
                                <?php endif; ?>
                                <?php echo h($_SESSION['usuario']['login']); ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASEURL; ?>login.php">
                                <i class="fa-solid fa-right-to-bracket"></i> Entrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
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

    <main class="container">