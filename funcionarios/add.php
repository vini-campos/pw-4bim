<?php
include "function.php";
add();
include HEADER_TEMPLATE;
?>

<h2>Nova Moto</h2>

<?php exibir_mensagem(); ?>

<form action="add.php" method="post" enctype="multipart/form-data">
    <hr>
    <div class="row">
        <div class="form-group col-md-7">
            <label for="modelo">Modelo</label>
            <input type="text" class="form-control" id="modelo" name="customer[Modelo]" maxlength="50" required
                value="<?php echo h($customer['Modelo'] ?? ''); ?>">
        </div>

        <div class="form-group col-md-3">
            <label for="marca">Marca</label>
            <input type="text" class="form-control" id="marca" name="customer[Marca]" maxlength="30" required
                value="<?php echo h($customer['Marca'] ?? ''); ?>">
        </div>

        <div class="form-group col-md-2">
            <label for="motor">Motor</label>
            <input type="text" class="form-control" id="motor" name="customer[Motor]" maxlength="20" required
                value="<?php echo h($customer['Motor'] ?? ''); ?>">
        </div>

        <div class="form-group col-md-2">
            <label for="created">Data de Cadastro</label>
            <input type="date" class="form-control" id="created" name="customer[DataCad]" disabled>
        </div>

        <div class="form-group col-md-4">
            <label for="imagem">Imagem</label>
            <input type="file" class="form-control" id="imagem" name="customer[imagem]"
                accept="image/png, image/jpeg, image/gif, image/webp">
            <small class="rl-hint">JPG, PNG, GIF ou WEBP, até 2MB.</small>

            <div id="imagem-preview-wrapper" class="rl-current-image" style="display: none;">
                <img id="imagem-preview" src="" alt="Prévia da imagem selecionada">
                <span>Prévia da imagem selecionada</span>
            </div>
        </div>
    </div>

    <div id="actions" class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-secondary mb-2">
                <i class="fa-solid fa-floppy-disk"></i> Salvar
            </button>
            <a href="index.php" class="btn btn-light">
                <i class="fa-solid fa-rotate-left"></i> Cancelar
            </a>
        </div>
    </div>
</form>

<script>
    document.getElementById('imagem').addEventListener('change', function (event) {
        var wrapper = document.getElementById('imagem-preview-wrapper');
        var preview = document.getElementById('imagem-preview');
        var arquivo = event.target.files[0];

        if (!arquivo) {
            wrapper.style.display = 'none';
            preview.src = '';
            return;
        }

        var leitor = new FileReader();
        leitor.onload = function (e) {
            preview.src = e.target.result;
            wrapper.style.display = 'flex';
        };
        leitor.readAsDataURL(arquivo);
    });
</script>

<?php include FOOTER_TEMPLATE; ?>