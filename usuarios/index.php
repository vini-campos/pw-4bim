<?php
    include("function.php");
    index();
    include HEADER_TEMPLATE;
?>

<header>
    <div class="row">
        <div class="col-sm-6">
            <h2>Usuários</h2>
        </div>
        <div class="col-sm-6 text-end h2">
            <a class="btn btn-secondary mb-2" href="add.php"><i class="fa-solid fa-user-plus"></i> Novo Usuário</a>
            <a class="btn btn-light" href="index.php"><i class="fa-solid fa-refresh"></i> Atualizar</a>
        </div>
    </div>
</header>

<?php exibir_mensagem(); ?>

<hr>

<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Foto</th>
            <th>ID</th>
            <th>Usuário</th>
            <th>Perfil</th>
            <th>Status</th>
            <th>Opções</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($usuarios): ?>
            <?php foreach ($usuarios as $u): ?>
                <?php
                    $foto = foto_usuario_url($u['Foto_usu'] ?? null);
                    $perfil = nomePermissao($u['Id_Permi']);
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
                        <a href="view.php?id=<?php echo (int) $u['Id_Usu']; ?>" class="btn btn-sm btn-light">
                            <i class="fa fa-eye"></i> Visualizar</a>
                        <a href="edit.php?id=<?php echo (int) $u['Id_Usu']; ?>" class="btn btn-sm btn-secondary">
                            <i class="fa fa-pencil"></i> Editar</a>
                        <?php if (!$ehEu): ?>
                            <a href="#" class="btn btn-sm btn-dark mt-2" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" data-customer="<?php echo (int) $u['Id_Usu']; ?>"
                                data-rotulo="Usuário">
                                <i class="fa fa-trash"></i> Excluir
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nenhum usuário encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include "modal.php"; ?>
<?php include FOOTER_TEMPLATE; ?>