<?php
include "function.php";
edit();
include HEADER_TEMPLATE;

$ehEu = ((int) $usuario['Id_Usu'] === (int) $_SESSION['usuario']['id']);
$fotoAtual = foto_usuario_url($usuario['Foto_usu'] ?? null);
?>

<h2>Atualizar Usuário</h2>

<?php exibir_mensagem(); ?>

<form action="edit.php?id=<?php echo (int) $usuario['Id_Usu']; ?>" method="post" enctype="multipart/form-data">
    <hr>
    <div class="row">
        <div class="form-group col-md-5">
            <label for="login">Usuário</label>
            <input type="text" class="form-control" id="login" name="usuario[Login_usu]" maxlength="50" required
                autocomplete="off" value="<?php echo h($usuario['Login_usu']); ?>">
        </div>

        <div class="form-group col-md-4">
            <label for="senha">Nova senha</label>
            <input type="password" class="form-control" id="senha" name="usuario[Senha]" maxlength="72"
                autocomplete="new-password">
            <small class="rl-hint">Deixe em branco para manter a senha atual.</small>
        </div>

        <div class="form-group col-md-3">
            <label for="perfil">Perfil</label>
            <select class="form-select" id="perfil" name="usuario[Id_Permi]" required
                <?php echo $ehEu ? 'disabled' : ''; ?>>
                <?php foreach ($permissoes as $p): ?>
                    <option value="<?php echo (int) $p['Id_Permi']; ?>"
                        <?php echo ((int) $usuario['Id_Permi'] === (int) $p['Id_Permi']) ? 'selected' : ''; ?>>
                        <?php echo h($p['Nome_Permi']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row mt-3">
        <div class="form-group col-md-4">
            <label for="foto">Foto</label>
            <input type="file" class="form-control" id="foto" name="foto"
                accept="image/png, image/jpeg, image/gif, image/webp">
            <small class="rl-hint">JPG, PNG, GIF ou WEBP, até 2MB.</small>

            <?php if ($fotoAtual): ?>
                <div class="rl-current-image">
                    <img src="<?php echo h($fotoAtual); ?>" alt="Foto atual">
                    <span>Foto atual</span>
                </div>
            <?php endif; ?>

            <div id="foto-preview-wrapper" class="rl-current-image" style="display: none;">
                <img id="foto-preview" src="" alt="Prévia da foto selecionada">
                <span>Prévia da foto selecionada</span>
            </div>
        </div>

        <div class="form-group col-md-4 d-flex align-items-end">
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ativo" name="usuario[Ativo_usu]" value="1"
                        <?php echo !empty($usuario['Ativo_usu']) ? 'checked' : ''; ?>
                        <?php echo $ehEu ? 'disabled' : ''; ?>>
                    <label class="form-check-label" for="ativo">Usuário ativo</label>
                </div>
                <?php if ($ehEu): ?>
                    <small class="rl-hint">Você não pode alterar o seu próprio perfil nem se desativar.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="actions" class="row mt-3">
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