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
            <th>Opções</th>
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
                        <a href="view.php?id=<?php echo (int) $c['Id_Cli']; ?>" class="btn btn-sm btn-light">
                            <i class="fa fa-eye"></i> Visualizar</a>

                        <?php if (esta_logado()): ?>
                            <a href="edit.php?id=<?php echo (int) $c['Id_Cli']; ?>" class="btn btn-sm btn-secondary">
                                <i class="fa fa-pencil"></i> Editar</a>
                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" data-customer="<?php echo (int) $c['Id_Cli']; ?>"
                                data-rotulo="Cliente">
                                <i class="fa fa-trash"></i> Excluir
                            </a>
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