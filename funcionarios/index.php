<?php
    include("function.php");
    index();
    include HEADER_TEMPLATE;
?>

<header>
    <div class="row">
        <div class="col-sm-6">
            <h2>Motos</h2>
        </div>
        <div class="col-sm-6 text-end h2">
            <?php if (esta_logado()): ?>
                <a class="btn btn-secondary mb-2" href="add.php"><i class="fa-solid fa-bicycle"></i> Nova Moto</a>
            <?php endif; ?>
            <a class="btn btn-light" href="index.php"><i class="fa-solid fa-refresh"></i> Atualizar</a>
        </div>
    </div>
</header>

<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo h($_SESSION['type']); ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php clear_messages(); ?>
<?php endif; ?>

<hr>

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th width="30%">Modelo</th>
            <th>Marca</th>
            <th>Motor</th>
            <th>Atualizado em</th>
            <th>Opções</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($customers): ?>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo (int) $customer['Id']; ?></td>
                    <td><?php echo h($customer['Modelo']); ?></td>
                    <td><?php echo h($customer['Marca']); ?></td>
                    <td><?php echo h($customer['Motor']); ?></td>
                    <td><?php echo formatData($customer['DataCad'], "d/m/Y - H:i:s"); ?></td>
                    <td class="actions text-end">
                        <a href="view.php?id=<?php echo (int) $customer['Id']; ?>" class="btn btn-sm btn-light">
                            <i class="fa fa-eye"></i> Visualizar</a>

                        <?php if (esta_logado()): ?>
                            <a href="edit.php?id=<?php echo (int) $customer['Id']; ?>"  class="btn btn-sm btn-secondary">
                                <i class="fa fa-pencil"></i> Editar</a>
                            <a href="#" class="btn btn-sm btn-dark mt-2" data-bs-toggle="modal"
                                data-bs-target="#delete-modal" data-customer="<?php echo (int) $customer['Id']; ?>" data-rotulo="Moto">
                                <i class="fa fa-trash" ></i> Excluir
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nenhum registro encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include "modal.php"; ?>
<?php include FOOTER_TEMPLATE; ?>