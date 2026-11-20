<?php
include('function.php');
view($_GET['id'] ?? null);
include(HEADER_TEMPLATE);
?>

<h2>Cliente #<?php echo (int) $cliente['Id_Cli']; ?></h2>
<hr>

<?php exibir_mensagem(); ?>

<div class="detail-panel">
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Nome:</span>
            <span class="detail-value"><?php echo h($cliente['Nome_Cli']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">CPF:</span>
            <span class="detail-value"><?php echo h($cliente['CPF_Cli']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Telefone:</span>
            <span class="detail-value"><?php echo h(formatTelefone($cliente['Tel_Cli'])); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">E-mail:</span>
            <span class="detail-value"><?php echo h($cliente['Email_Cli'] ?: '—'); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Endereço:</span>
            <span class="detail-value"><?php echo h($cliente['Endereco_Cli'] ?: '—'); ?></span>
        </div>
    </div>
</div>

<div id="actions" class="row">
    <div class="col-md-12">
        <?php if (esta_logado()): ?>
            <a href="edit.php?id=<?php echo (int) $cliente['Id_Cli']; ?>" class="btn btn-secondary">
                <i class="fa-solid fa-pen-to-square"></i> Editar</a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
    </div>
</div>

<?php include FOOTER_TEMPLATE; ?>