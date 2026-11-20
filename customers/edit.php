<?php
include "function.php";
edit();
include HEADER_TEMPLATE;
?>

<h2>Atualizar Cliente</h2>

<?php exibir_mensagem(); ?>

<form action="edit.php?id=<?php echo (int) $cliente['Id_Cli']; ?>" method="post">
    <hr>
    <div class="row">
        <div class="form-group col-md-8">
            <label for="nome">Nome</label>
            <input type="text" class="form-control" id="nome" name="cliente[Nome_Cli]" maxlength="100" required
                value="<?php echo h($cliente['Nome_Cli']); ?>">
        </div>

        <div class="form-group col-md-4">
            <label for="cpf_cnpj">CPF</label>
            <input type="text" class="form-control" id="cpf_cnpj" name="cliente[CPF_Cli]" maxlength="14"
                placeholder="000.000.000-00" required value="<?php echo h($cliente['CPF_Cli']); ?>">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label for="phone">Telefone</label>
            <input type="text" class="form-control" id="phone" name="cliente[Tel_Cli]" maxlength="11"
                placeholder="DDD + número" value="<?php echo h($cliente['Tel_Cli']); ?>">
        </div>

        <div class="form-group col-md-8">
            <label for="email">E-mail</label>
            <input type="email" class="form-control" id="email" name="cliente[Email_Cli]" maxlength="100"
                value="<?php echo h($cliente['Email_Cli']); ?>">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <label for="endereco">Endereço</label>
            <input type="text" class="form-control" id="endereco" name="cliente[Endereco_Cli]" maxlength="200"
                value="<?php echo h($cliente['Endereco_Cli']); ?>">
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

<?php include FOOTER_TEMPLATE; ?>