<?php
include 'function.php';
view($_GET['id'] ?? null);
include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <p class="eyebrow">Perfil da equipe</p>
        <h2><?php echo h($funcionario['Nome']); ?></h2>
        <p class="page-description">Registro #<?php echo (int) $funcionario['Id']; ?></p>
    </div>
</header>

<?php exibir_mensagem(); ?>

<section class="detail-panel">
    <div class="employee-profile">
        <?php if (!empty($funcionario['Foto'])): ?>
            <img src="<?php echo h(BASEURL . 'uploads/funcionarios/' . rawurlencode(basename($funcionario['Foto']))); ?>"
                alt="Foto de <?php echo h($funcionario['Nome']); ?>" class="employee-photo">
        <?php else: ?>
            <span class="employee-photo employee-placeholder" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
        <?php endif; ?>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Nome</span>
                <span class="detail-value"><?php echo h($funcionario['Nome']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Idade</span>
                <span class="detail-value"><?php echo (int) $funcionario['Idade']; ?> anos</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Endereço</span>
                <span class="detail-value"><?php echo h($funcionario['Endereco']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Data de nascimento</span>
                <span class="detail-value"><?php echo h(formatData($funcionario['DataNasc'], 'd/m/Y')); ?></span>
            </div>
        </div>
    </div>
</section>

<div id="actions" class="row">
    <div class="col-md-12">
        <?php if (esta_logado()): ?>
            <a href="edit.php?id=<?php echo (int) $funcionario['Id']; ?>" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Editar funcionário
            </a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Voltar à equipe</a>
    </div>
</div>

<?php include FOOTER_TEMPLATE; ?>
