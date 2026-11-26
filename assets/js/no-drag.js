/**
 * Impede que botões, links e imagens sejam arrastados com o mouse.
 */
document.addEventListener('dragstart', function (event) {
  var alvo = event.target;
  if (alvo && alvo.closest && alvo.closest('a, button, .btn, img')) {
    event.preventDefault();
  }
});
