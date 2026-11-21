<?php
ob_start();

include('../config.php');
include(DBAPI);
include_once('../inc/auth.php');
include_once('../inc/upload.php');

const USUARIOS_TABELA = 'Usuarios';
const USUARIOS_PK = 'Id_Usu';

$usuarios = null;
$usuario = null;

function definir_erro($mensagens)
{
	$_SESSION['message'] = is_array($mensagens) ? implode('<br>', $mensagens) : $mensagens;
	$_SESSION['type'] = 'danger';
}

function nomePerfilUsuario($nivel)
{
	return (string) $nivel === '1' ? 'Admin' : ((string) $nivel === '0' ? 'Comum' : '—');
}

function dadosUsuario($origem)
{
	return [
		'Login_usu' => trim($origem['Login_usu'] ?? ''),
		'Ativo_usu' => !empty($origem['Ativo_usu']) ? 1 : 0,
		'Senha' => (string) ($origem['Senha'] ?? ''),
	];
}

function validarUsuario($dados, $idAtual = 0)
{
	$erros = [];

	if ($dados['Login_usu'] === '') {
		$erros[] = 'O campo Usuário é obrigatório.';
	} elseif (strlen($dados['Login_usu']) < 3 || strlen($dados['Login_usu']) > 50) {
		$erros[] = 'O Usuário deve ter de 3 a 50 caracteres.';
	} elseif (!preg_match('/^[A-Za-z0-9._ -]+$/', $dados['Login_usu'])) {
		$erros[] = 'O Usuário aceita letras, números, espaços, ponto, hífen e sublinhado.';
	} else {
		$outro = find_by(USUARIOS_TABELA, 'Login_usu', $dados['Login_usu']);
		if ($outro && (int) $outro[USUARIOS_PK] !== (int) $idAtual) {
			$erros[] = 'Já existe um usuário com esse login.';
		}
	}

	if ((int) $idAtual === 0 && $dados['Senha'] === '') {
		$erros[] = 'O campo Senha é obrigatório.';
	}
	if ($dados['Senha'] !== '') {
		if (strlen($dados['Senha']) < 6) {
			$erros[] = 'A senha deve ter pelo menos 6 caracteres.';
		} elseif (strlen($dados['Senha']) > 72) {
			$erros[] = 'A senha aceita no máximo 72 caracteres.';
		}
	}

	return $erros;
}

function index()
{
	global $usuarios;
	exigir_admin();
	$usuarios = find_all(USUARIOS_TABELA, 'Login_usu');
}

function view($id = null)
{
	global $usuario;
	exigir_admin();

	$id = (int) $id;
	$usuario = $id > 0 ? find(USUARIOS_TABELA, $id, USUARIOS_PK) : null;
	if (!$usuario) {
		$_SESSION['message'] = 'Usuário não encontrado.';
		$_SESSION['type'] = 'warning';
		header('Location: index.php');
		exit;
	}
}

function add()
{
	global $usuario;
	exigir_admin();

	if (empty($_POST['usuario'])) {
		return;
	}

	$dados = dadosUsuario($_POST['usuario']);
	$usuario = $dados;
	$erros = validarUsuario($dados);
	if ($erros) {
		definir_erro($erros);
		return;
	}

	$foto = upload_foto_usuario();
	if ($foto === false) {
		return;
	}

	$registro = [
		'Login_usu' => $dados['Login_usu'],
		'Senha_usu' => password_hash($dados['Senha'], PASSWORD_DEFAULT),
		'Nivel_usu' => '0',
		'Ativo_usu' => 1,
	];
	if ($foto) {
		$registro['Foto_usu'] = $foto;
	}

	if (save(USUARIOS_TABELA, $registro)) {
		header('Location: index.php');
		exit;
	}
	apagar_foto_usuario($foto);
}

function edit()
{
	global $usuario;
	exigir_admin();

	$id = (int) ($_GET['id'] ?? 0);
	if ($id <= 0) {
		header('Location: index.php');
		exit;
	}

	$atual = find(USUARIOS_TABELA, $id, USUARIOS_PK);
	if (!$atual) {
		$_SESSION['message'] = 'Usuário não encontrado.';
		$_SESSION['type'] = 'warning';
		header('Location: index.php');
		exit;
	}

	$ehEu = $id === (int) $_SESSION['usuario']['id'];
	if (isset($_POST['usuario'])) {
		$dados = dadosUsuario($_POST['usuario']);
		if ($ehEu) {
			$dados['Ativo_usu'] = 1;
		}

		$erros = validarUsuario($dados, $id);
		if ($erros) {
			definir_erro($erros);
			$usuario = array_merge($atual, $dados);
			return;
		}

		$foto = upload_foto_usuario($atual['Foto_usu'] ?? null);
		if ($foto === false) {
			$usuario = array_merge($atual, $dados);
			return;
		}

		$registro = [
			'Login_usu' => $dados['Login_usu'],
			'Nivel_usu' => (string) $atual['Nivel_usu'],
			'Ativo_usu' => $dados['Ativo_usu'],
		];
		if ($dados['Senha'] !== '') {
			$registro['Senha_usu'] = password_hash($dados['Senha'], PASSWORD_DEFAULT);
		}
		if ($foto) {
			$registro['Foto_usu'] = $foto;
		}

		$fotoMudou = $foto && $foto !== ($atual['Foto_usu'] ?? null);
		if (update(USUARIOS_TABELA, $id, $registro, USUARIOS_PK)) {
			if ($fotoMudou) {
				apagar_foto_usuario($atual['Foto_usu'] ?? null);
			}
			if ($ehEu) {
				$_SESSION['usuario']['login'] = $dados['Login_usu'];
				$_SESSION['usuario']['foto'] = $foto;
			}
			header('Location: index.php');
			exit;
		}

		if ($fotoMudou) {
			apagar_foto_usuario($foto);
		}
		$usuario = array_merge($atual, $dados);
		return;
	}

	$usuario = $atual;
}

function delete($id = null)
{
	exigir_admin();

	$id = (int) $id;
	if ($id > 0) {
		if ($id === (int) $_SESSION['usuario']['id']) {
			definir_erro('Você não pode excluir a sua própria conta.');
		} else {
			$atual = find(USUARIOS_TABELA, $id, USUARIOS_PK);
			if ($atual) {
				if (remove(USUARIOS_TABELA, $id, USUARIOS_PK)) {
					apagar_foto_usuario($atual['Foto_usu'] ?? null);
				}
			} else {
				$_SESSION['message'] = 'Usuário não encontrado.';
				$_SESSION['type'] = 'warning';
			}
		}
	}

	header('Location: index.php');
	exit;
}
?>
