<?php
    include("function.php");
    index();
    include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <h2>Usuários</h2>
        <p class="page-description">Gerencie quem tem acesso ao sistema.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="add.php"><i class="fa-solid fa-user-plus"></i> Novo usuário</a>
        <a class="btn btn-light" href="index.php"><i class="fa-solid fa-rotate"></i> Atualizar</a>
    </div>
</header>

<?php exibir_mensagem(); ?>

<div class="table-responsive">
<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Foto</th>
            <th>ID</th>
            <th>Usuário</th>
            <th>Perfil</th>
            <th>Status</th>
            <th class="text-end">Opções</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($usuarios): ?>
            <?php foreach ($usuarios as $u): ?>
                <?php
                    $foto = foto_usuario_url($u['Foto_usu'] ?? null);
                    $perfil = nomePerfilUsuario($u['Nivel_usu']);
                    $ehEu = ((int) $u['Id_Usu'] === (int) $_SESSION['usuario']['id']);
                ?>
                <tr>
                    <td>
                        <?php if ($foto): ?>
                            <img src="<?php echo h($foto); ?>" alt="" class="perfil-thumb">
                        <?php else: ?>
                            <i class="fa-solid fa-circle-user fa-2x"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo (int) $u['Id_Usu']; ?></td>
                    <td><?php echo h($u['Login_usu']); ?><?php echo $ehEu ? ' <small>(você)</small>' : ''; ?></td>
                    <td>
                        <span class="badge <?php echo $perfil === 'Admin' ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                            <?php echo h($perfil); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $u['Ativo_usu'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $u['Ativo_usu'] ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </td>
                    <td class="actions text-end">
                        <a href="view.php?id=<?php echo (int) $u['Id_Usu']; ?>" class="btn btn-sm btn-action btn-light" title="Visualizar" aria-label="Visualizar">
                            <i class="fa-solid fa-eye"></i><span>Visualizar</span></a>
                        <a href="edit.php?id=<?php echo (int) $u['Id_Usu']; ?>" class="btn btn-sm btn-action btn-secondary" title="Editar" aria-label="Editar">
                            <i class="fa-solid fa-pen"></i><span>Editar</span></a>
                        <?php if (!$ehEu): ?>
                            <a href="#" class="btn btn-sm btn-action btn-outline-danger" title="Excluir" aria-label="Excluir" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" data-customer="<?php echo (int) $u['Id_Usu']; ?>"
                                data-rotulo="Usuário" data-nome="<?php echo h($u['Login_usu']); ?>">
                                <i class="fa-solid fa-trash"></i><span>Excluir</span></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="empty-state">Nenhum usuário encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php include "modal.php"; ?>
<?php include FOOTER_TEMPLATE; ?>