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

<?php if ($funcionarios): ?>
    <div class="cracha-grid">
        <?php foreach ($funcionarios as $item): ?>
            <?php $fotoFunc = !empty($item['Foto']) ? BASEURL . 'uploads/funcionarios/' . rawurlencode(basename($item['Foto'])) : null; ?>
            <article class="cracha">
                <div class="cracha-faixa">
                    <span class="cracha-num">Nº <?php echo str_pad((int) $item['Id'], 3, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="cracha-corpo">
                    <?php if ($fotoFunc): ?>
                        <img src="<?php echo h($fotoFunc); ?>" alt="Foto de <?php echo h($item['Nome']); ?>" class="cracha-foto">
                    <?php else: ?>
                        <span class="cracha-foto cracha-foto-vazia" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
                    <?php endif; ?>
                    <h3 class="cracha-nome"><?php echo h($item['Nome']); ?></h3>
                    <dl class="cracha-dados">
                        <div><dt>Idade</dt><dd><?php echo (int) $item['Idade']; ?> anos</dd></div>
                        <div><dt>Nascimento</dt><dd><?php echo h(formatData($item['DataNasc'], 'd/m/Y')); ?></dd></div>
                        <div><dt>Endereço</dt><dd><?php echo h($item['Endereco']); ?></dd></div>
                    </dl>
                </div>
                <div class="cracha-acoes">
                    <a href="view.php?id=<?php echo (int) $item['Id']; ?>" class="btn btn-sm btn-light">
                        <i class="fa-solid fa-eye"></i> Ver
                    </a>
                    <?php if (esta_logado()): ?>
                        <a href="edit.php?id=<?php echo (int) $item['Id']; ?>" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#delete-modal" data-customer="<?php echo (int) $item['Id']; ?>" data-rotulo="Funcionário"
                            aria-label="Excluir funcionário">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="empty-state">Nenhum funcionário cadastrado ainda.</p>
<?php endif; ?>

<?php include 'modal.php'; ?>
<?php include FOOTER_TEMPLATE; ?>
