<?php
    include("function.php");
    index();
    include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <h2>Clientes</h2>
        <p class="page-description">Consulte e mantenha os cadastros de clientes.</p>
    </div>
    <div class="page-actions">
        <?php if (esta_logado()): ?>
            <a class="btn btn-primary" href="add.php"><i class="fa-solid fa-user-plus"></i> Novo cliente</a>
        <?php endif; ?>
        <a class="btn btn-light" href="index.php"><i class="fa-solid fa-rotate"></i> Atualizar</a>
    </div>
</header>

<?php exibir_mensagem(); ?>

<div class="table-responsive">
<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th width="25%">Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th class="text-end">Opções</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($clientes): ?>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?php echo (int) $c['Id_Cli']; ?></td>
                    <td><?php echo h($c['Nome_Cli']); ?></td>
                    <td><?php echo h($c['CPF_Cli']); ?></td>
                    <td><?php echo h(formatTelefone($c['Tel_Cli'])); ?></td>
                    <td><?php echo h($c['Email_Cli'] ?: '—'); ?></td>
                    <td class="actions text-end">
                        <a href="view.php?id=<?php echo (int) $c['Id_Cli']; ?>" class="btn btn-sm btn-action btn-light" title="Visualizar" aria-label="Visualizar">
                            <i class="fa-solid fa-eye"></i><span>Visualizar</span></a>

                        <?php if (esta_logado()): ?>
                            <a href="edit.php?id=<?php echo (int) $c['Id_Cli']; ?>" class="btn btn-sm btn-action btn-secondary" title="Editar" aria-label="Editar">
                                <i class="fa-solid fa-pen"></i><span>Editar</span></a>
                            <a href="#" class="btn btn-sm btn-action btn-outline-danger" title="Excluir" aria-label="Excluir" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" data-customer="<?php echo (int) $c['Id_Cli']; ?>"
                                data-rotulo="Cliente" data-nome="<?php echo h($c['Nome_Cli']); ?>">
                                <i class="fa-solid fa-trash"></i><span>Excluir</span></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="empty-state">Nenhum cliente encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php include "modal.php"; ?>
<?php include FOOTER_TEMPLATE; ?>