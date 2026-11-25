<?php
include "function.php";
add();
include HEADER_TEMPLATE;
?>

<header class="page-heading">
    <div>
        <h2>Novo usuário</h2>
        <p class="page-description">Crie um acesso ao sistema.</p>
    </div>
</header>

<?php exibir_mensagem(); ?>

<form action="add.php" method="post" enctype="multipart/form-data" class="record-form">
    <div class="row">
        <div class="form-group col-md-4">
            <label for="login">Usuário</label>
            <input type="text" class="form-control" id="login" name="usuario[Login_usu]" maxlength="50" required
                autocomplete="off" value="<?php echo h($usuario['Login_usu'] ?? ''); ?>">
        </div>

        <div class="form-group col-md-4">
            <label for="senha">Senha</label>
            <input type="password" class="form-control" id="senha" name="usuario[Senha]" maxlength="72" required
                autocomplete="new-password">
            <small class="rl-hint">Mínimo de 6 caracteres.</small>
        </div>
    </div>

    <div class="row mt-3">
        <div class="form-group col-md-4">
            <label for="foto">Foto</label>
            <input type="file" class="form-control" id="foto" name="foto"
                accept="image/png, image/jpeg, image/gif, image/webp">
            <small class="rl-hint">JPG, PNG, GIF ou WEBP, até 2MB.</small>

            <div id="foto-preview-wrapper" class="rl-current-image" style="display: none;">
                <img id="foto-preview" src="" alt="Prévia da foto selecionada">
                <span>Prévia da foto selecionada</span>
            </div>
        </div>

    </div>

    <div id="actions" class="row mt-3">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Salvar
            </button>
            <a href="index.php" class="btn btn-light">
                <i class="fa-solid fa-rotate-left"></i> Cancelar
            </a>
        </div>
    </div>
</form>

<script>
    document.getElementById('foto').addEventListener('change', function (event) {
        var wrapper = document.getElementById('foto-preview-wrapper');
        var preview = document.getElementById('foto-preview');
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