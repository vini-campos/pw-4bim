<?php
include 'function.php';
index();
include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <h2>Funcionários</h2>
        <p class="page-description">Consulte e mantenha os dados da equipe atualizados.</p>
    </div>
    <div class="page-actions">
        <?php if (esta_logado()): ?>
            <a class="btn btn-primary" href="add.php"><i class="fa-solid fa-user-plus"></i> Novo funcionário</a>
        <?php endif; ?>
        <a class="btn btn-light" href="index.php"><i class="fa-solid fa-rotate"></i> Atualizar</a>
    </div>
</header>

<?php exibir_mensagem(); ?>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Funcionário</th>
                <th>Endereço</th>
                <th>Idade</th>
                <th>Nascimento</th>
                <th class="text-end">Opções</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($funcionarios): ?>
                <?php foreach ($funcionarios as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['Foto'])): ?>
                                <img src="<?php echo h(BASEURL . 'uploads/funcionarios/' . rawurlencode(basename($item['Foto']))); ?>"
                                    alt="Foto de <?php echo h($item['Nome']); ?>" class="employee-thumb">
                            <?php else: ?>
                                <span class="employee-thumb employee-placeholder" aria-hidden="true">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?php echo h($item['Nome']); ?></td>
                        <td><?php echo h($item['Endereco']); ?></td>
                        <td><?php echo (int) $item['Idade']; ?> anos</td>
                        <td><?php echo h(formatData($item['DataNasc'], 'd/m/Y')); ?></td>
                        <td class="actions text-end text-nowrap">
                            <a href="view.php?id=<?php echo (int) $item['Id']; ?>" class="btn btn-sm btn-action btn-light" title="Visualizar" aria-label="Visualizar funcionário">
                                <i class="fa-solid fa-eye"></i><span>Visualizar</span>
                            </a>
                            <?php if (esta_logado()): ?>
                                <a href="edit.php?id=<?php echo (int) $item['Id']; ?>" class="btn btn-sm btn-action btn-secondary" title="Editar" aria-label="Editar funcionário">
                                    <i class="fa-solid fa-pen"></i><span>Editar</span>
                                </a>
                                <a href="#" class="btn btn-sm btn-action btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete-modal" data-customer="<?php echo (int) $item['Id']; ?>" data-rotulo="Funcionário"
                                    data-nome="<?php echo h($item['Nome']); ?>"
                                    title="Excluir" aria-label="Excluir funcionário">
                                    <i class="fa-solid fa-trash"></i><span>Excluir</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">Nenhum funcionário cadastrado ainda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'modal.php'; ?>
<?php include FOOTER_TEMPLATE; ?>
