/**
 * Passa os dados do cliente para o Modal, e atualiza o link para exclusão
 */
$('#delete-modal').on('show.bs.modal', function (event) {

  var button = $(event.relatedTarget);
  var id = button.data('customer');
  var rotulo = button.data('rotulo') || 'Item';

  var modal = $(this);
  modal.find('.modal-title').text('Excluir ' + rotulo + ' #' + id);
  modal.find('#confirm').attr('href', 'delete.php?id=' + id);
})
/**
 * Máscara de CPF (000.000.000-00) / CNPJ (00.000.000/0000-00),
 * aplicada conforme a quantidade de dígitos digitados.
 */
function maskCpfCnpj(valor) {
  valor = valor.replace(/\D/g, '').slice(0, 14);

  if (valor.length <= 11) {
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  } else {
    valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1/$2');
    valor = valor.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
  }

  return valor;
}

/**
 * Aplica a máscara de CPF/CNPJ em tempo real
 */
$('#cpf_cnpj').on('input', function () {
  $(this).val(maskCpfCnpj($(this).val()));
});

/**
 * Bloqueia letras/símbolos em campos que só podem ter número
 * (CEP, Telefone, Celular, Inscrição Estadual)
 */
$('#zip_code, #phone, #mobile, #ie').on('input', function () {
  $(this).val($(this).val().replace(/\D/g, ''));
});

/**
 * Ao carregar a página (ex.: tela de edição), formata o que já
 * estiver preenchido, sem exigir que o usuário digite de novo.
 */
$(function () {
  $('#cpf_cnpj').trigger('input');
  $('#zip_code, #phone, #mobile, #ie').trigger('input');
});
