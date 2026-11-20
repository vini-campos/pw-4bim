<?php
include('function.php');
view($_GET['id'] ?? null);
include(HEADER_TEMPLATE);

$foto = foto_usuario_url($usuario['Foto_usu'] ?? null);
$perfil = nomePermissao($usuario['Id_Permi']);
?>

<h2>Usuário #<?php echo (int) $usuario['Id_Usu']; ?></h2>
<hr>

<?php exibir_mensagem(); ?>

<div class="detail-panel">
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Usuário:</span>
            <span class="detail-value"><?php echo h($usuario['Login_usu']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Perfil:</span>
            <span class="detail-value"><?php echo h($perfil); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status:</span>
            <span class="detail-value"><?php echo $usuario['Ativo_usu'] ? 'Ativo' : 'Inativo'; ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Foto:</span>
            <span class="detail-value">
                <?php if ($foto): ?>
                    <img src="<?php echo h($foto); ?>" alt="Foto de <?php echo h($usuario['Login_usu']); ?>"
                        class="detail-image">
                <?php else: ?>
                    &mdash;
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<div id="actions" class="row">
    <div class="col-md-12">
        <a href="edit.php?id=<?php echo (int) $usuario['Id_Usu']; ?>" class="btn btn-secondary">
            <i class="fa-solid fa-pen-to-square"></i> Editar</a>
        <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
    </div>
</div>

<?php include FOOTER_TEMPLATE; ?>