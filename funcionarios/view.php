<?php
include 'function.php';
view($_GET['id'] ?? null);
include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <h2><?php echo h($funcionario['Nome']); ?></h2>
        <p class="page-description">Registro #<?php echo (int) $funcionario['Id']; ?></p>
    </div>
</header>

<?php exibir_mensagem(); ?>

<div class="perfil-layout">
    <article class="detail-panel perfil-card">
        <?php if (!empty($funcionario['Foto'])): ?>
            <img src="<?php echo h(BASEURL . 'uploads/funcionarios/' . rawurlencode(basename($funcionario['Foto']))); ?>"
                alt="Foto de <?php echo h($funcionario['Nome']); ?>" class="perfil-foto">
        <?php else: ?>
            <span class="perfil-foto perfil-foto-vazia" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
        <?php endif; ?>
        <h3 class="perfil-nome"><?php echo h($funcionario['Nome']); ?></h3>
    </article>

    <div>
        <section class="detail-panel">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Idade</span>
                    <span class="detail-value"><?php echo (int) $funcionario['Idade']; ?> anos</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Data de nascimento</span>
                    <span class="detail-value"><?php echo h(formatData($funcionario['DataNasc'], 'd/m/Y')); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Endereço</span>
                    <span class="detail-value"><?php echo h($funcionario['Endereco']); ?></span>
                </div>
            </div>
        </section>

        <div id="actions" class="row">
            <div class="col-md-12">
                <?php if (esta_logado()): ?>
                    <a href="edit.php?id=<?php echo (int) $funcionario['Id']; ?>" class="btn btn-secondary">
                        <i class="fa-solid fa-pen-to-square"></i> Editar funcionário
                    </a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Voltar à equipe</a>
            </div>
        </div>
    </div>
</div>

<?php include FOOTER_TEMPLATE; ?>
