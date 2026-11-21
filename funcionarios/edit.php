<?php
include 'function.php';
edit();
include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <p class="eyebrow">Equipe</p>
        <h2>Editar funcionário</h2>
        <p class="page-description">Atualize as informações de <?php echo h($funcionario['Nome']); ?>.</p>
    </div>
</header>

<?php exibir_mensagem(); ?>

<form action="edit.php?id=<?php echo (int) $funcionario['Id']; ?>" method="post" enctype="multipart/form-data" class="record-form">
    <div class="row">
        <div class="form-group col-md-8">
            <label for="nome">Nome completo</label>
            <input type="text" class="form-control" id="nome" name="funcionario[Nome]" maxlength="50" required
                value="<?php echo h($funcionario['Nome']); ?>">
        </div>
        <div class="form-group col-md-8">
            <label for="endereco">Endereço</label>
            <input type="text" class="form-control" id="endereco" name="funcionario[Endereco]" maxlength="50" required
                value="<?php echo h($funcionario['Endereco']); ?>">
        </div>
        <div class="form-group col-md-4">
            <label for="data-nasc">Data de nascimento</label>
            <input type="date" class="form-control" id="data-nasc" name="funcionario[DataNasc]" max="<?php echo date('Y-m-d'); ?>" required
                value="<?php echo h(formatData($funcionario['DataNasc'], 'Y-m-d')); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="foto">Foto</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="image/png, image/jpeg, image/gif, image/webp">
            <small class="rl-hint">JPG, PNG, GIF ou WEBP, até 2MB.</small>
            <?php if (!empty($funcionario['Foto'])): ?>
                <div class="rl-current-image">
                    <img src="<?php echo h(BASEURL . 'uploads/funcionarios/' . rawurlencode(basename($funcionario['Foto']))); ?>"
                        alt="Foto atual de <?php echo h($funcionario['Nome']); ?>">
                    <span>Foto atual</span>
                </div>
            <?php endif; ?>
            <div id="foto-preview-wrapper" class="rl-current-image" hidden>
                <img id="foto-preview" src="" alt="Prévia da nova foto selecionada">
                <span>Prévia da nova foto</span>
            </div>
        </div>
    </div>

    <div id="actions" class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary mb-2"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button>
            <a href="index.php" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
        </div>
    </div>
</form>

<script>
    document.getElementById('foto').addEventListener('change', function (event) {
        var wrapper = document.getElementById('foto-preview-wrapper');
        var preview = document.getElementById('foto-preview');
        var arquivo = event.target.files[0];
        if (!arquivo) {
            wrapper.hidden = true;
            preview.src = '';
            return;
        }
        var leitor = new FileReader();
        leitor.onload = function (e) {
            preview.src = e.target.result;
            wrapper.hidden = false;
        };
        leitor.readAsDataURL(arquivo);
    });
</script>

<?php include FOOTER_TEMPLATE; ?>
