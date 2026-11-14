<?php
require __DIR__ . "/inc/config.php";

$databaseStatus = '';
$databaseMessage = '';

try {
    require_once __DIR__ . "/inc/database.php";
    $databaseConnection = open_database();
    close_database($databaseConnection);

} catch (Throwable $error) {
    error_log('Falha na verificação do banco de dados: ' . $error->getMessage());
    $databaseStatus = 'danger';
    $databaseMessage = 'Banco de dados indisponível. Verifique se o servidor está ativo e se as configurações estão corretas.';
}

require __DIR__ . "/includes/header.php";
?>

<div class="alert alert-<?= $databaseStatus ?>" role="<?= $databaseStatus === 'danger' ? 'alert' : 'status' ?>">
    <?= htmlspecialchars($databaseMessage, ENT_QUOTES, 'UTF-8') ?>
</div>

<section class="hero-panel">
    <div>
        <p class="eyebrow text-white-50">PAINEL CORPORATIVO</p>
        <h1>Gestao de funcionarios.</h1>
        <p class="hero-copy">Consulte de registros dos funcionarios da empresa em um unico ambiente.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-light" href="funcionarios.php">Gerenciar funcionarios</a>
        </div>
    </div>
    <div class="hero-symbol" aria-hidden="true">GI</div>
</section>

<section class="row g-3 my-4">
    <div class="col-md-6">
        <a class="metric-card" href="funcionarios.php">
            <span class="metric-icon">F</span>
            <span><span class="metric-label">Funcionarios cadastrados</span><strong class="metric-number">123</strong></span>
            <span class="metric-arrow">→</span>
        </a>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>