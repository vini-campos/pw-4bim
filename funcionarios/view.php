<?php
include('function.php');
view($_GET['id'] ?? null);
include(HEADER_TEMPLATE);
?>

<h2>Moto <?php echo (int) $customer['Id']; ?></h2>
<hr>

<?php exibir_mensagem(); ?>

<div class="detail-panel">
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Modelo:</span>
            <span class="detail-value"><?php echo h($customer['Modelo']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Marca:</span>
            <span class="detail-value"><?php echo h($customer['Marca']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Motor:</span>
            <span class="detail-value"><?php echo h($customer['Motor']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data de Cadastro:</span>
            <span class="detail-value"><?php echo formatData($customer['DataCad'], "d/m/Y - H:i:s"); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Imagem:</span>
            <span class="detail-value">
                <?php if (!empty($customer['Imagem'])): ?>
                    <img src="<?php echo h(BASEURL . 'uploads/' . rawurlencode(basename($customer['Imagem']))); ?>"
                        alt="Imagem da moto" class="detail-image">
                <?php else: ?>
                    &mdash;
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<div id="actions" class="row">
    <div class="col-md-12">
        <?php if (esta_logado()): ?>
            <a href="edit.php?id=<?php echo (int) $customer['Id']; ?>" class="btn btn-secondary">
                <i class="fa-solid fa-pen-to-square"></i> Editar</a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
    </div>
</div>

<?php include FOOTER_TEMPLATE; ?>