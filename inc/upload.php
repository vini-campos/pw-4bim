<?php

/**
 *  Upload da foto do funcionário. O <input type="file"> deve se chamar "foto".
 *  Retorna: nome do arquivo novo, a foto atual (se nada foi enviado) ou false se deu erro.
 */
function upload_foto_funcionario($fotoAtual = null)
{
	if (empty($_FILES['foto']['name']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
		return $fotoAtual;
	}

	if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
		$_SESSION['message'] = "Não foi possível enviar a foto.";
		$_SESSION['type'] = "danger";
		return false;
	}

	$extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
	if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
		$_SESSION['message'] = "Formato não permitido. Use JPG, PNG, GIF ou WEBP.";
		$_SESSION['type'] = "danger";
		return false;
	}

	if ($_FILES['foto']['size'] > 2 * 1024 * 1024) { // 2MB
		$_SESSION['message'] = "A foto deve ter no máximo 2MB.";
		$_SESSION['type'] = "danger";
		return false;
	}

	if (@getimagesize($_FILES['foto']['tmp_name']) === false) {
		$_SESSION['message'] = "O arquivo enviado não é uma imagem válida.";
		$_SESSION['type'] = "danger";
		return false;
	}

	$pasta = __DIR__ . '/../uploads/funcionarios/';
	if (!is_dir($pasta)) {
		mkdir($pasta, 0755, true);
	}

	// O nome gerado cabe na coluna Foto (VARCHAR(30)).
	$nomeArquivo = substr(md5(uniqid('', true)), 0, 20) . '.' . $extensao;

	if (!move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . $nomeArquivo)) {
		$_SESSION['message'] = "Não foi possível salvar a foto no servidor.";
		$_SESSION['type'] = "danger";
		return false;
	}

	return $nomeArquivo;
}

/** Apaga o arquivo da foto antiga do funcionario */
function apagar_foto_funcionario($nomeArquivo)
{
	if (empty($nomeArquivo)) {
		return;
	}
	$caminho = __DIR__ . '/../uploads/funcionarios/' . basename($nomeArquivo);
	if (is_file($caminho)) {
		unlink($caminho);
	}
}