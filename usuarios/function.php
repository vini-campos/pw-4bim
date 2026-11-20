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
$permissoes = [];

function definir_erro($mensagens)
{
	$_SESSION['message'] = is_array($mensagens) ? implode('<br>', $mensagens) : $mensagens;
	$_SESSION['type'] = "danger";
}

/** Carrega os perfis (Admin, Comum...) da tabela Permi */
function carregarPermissoes()
{
	global $permissoes;
	$permissoes = find_all('Permi', 'Nome_Permi') ?: [];
}

function nomePermissao($idPermi)
{
	global $permissoes;
	foreach ($permissoes as $p) {
		if ((int) $p['Id_Permi'] === (int) $idPermi) {
			return $p['Nome_Permi'];
		}
	}
	return '—';
}

/** Pega só os campos permitidos do formulário */
function dadosUsuario($origem)
{
	return [
		'Login_usu' => trim($origem['Login_usu'] ?? ''),
		'Id_Permi'  => (int) ($origem['Id_Permi'] ?? 0),
		'Ativo_usu' => !empty($origem['Ativo_usu']) ? 1 : 0,
		'Senha'     => (string) ($origem['Senha'] ?? ''),
	];
}

/** Validação. $idAtual = ID do usuário em edição (0 no cadastro) */
function validarUsuario($d, $idAtual = 0)
{
	$erros = [];

	if ($d['Login_usu'] === '') {
		$erros[] = "O campo Usuário é obrigatório.";
	} elseif (strlen($d['Login_usu']) < 3 || strlen($d['Login_usu']) > 50) {
		$erros[] = "O Usuário deve ter de 3 a 50 caracteres.";
	} elseif (!preg_match('/^[A-Za-z0-9._-]+$/', $d['Login_usu'])) {
		$erros[] = "O Usuário aceita apenas letras, números, ponto, hífen e sublinhado.";
	} else {
		$outro = find_by(USUARIOS_TABELA, 'Login_usu', $d['Login_usu']);
		if ($outro && (int) $outro[USUARIOS_PK] !== (int) $idAtual) {
			$erros[] = "Já existe um usuário com esse login.";
		}
	}

	if ((int) $idAtual === 0 && $d['Senha'] === '') {
		$erros[] = "O campo Senha é obrigatório.";
	}
	if ($d['Senha'] !== '') {
		if (strlen($d['Senha']) < 6) {
			$erros[] = "A senha deve ter pelo menos 6 caracteres.";
		} elseif (strlen($d['Senha']) > 72) {
			$erros[] = "A senha aceita no máximo 72 caracteres.";
		}
	}

	if ($d['Id_Permi'] <= 0 || !find('Permi', $d['Id_Permi'], 'Id_Permi')) {
		$erros[] = "Selecione um perfil válido.";
	}

	return $erros;
}

/** Listagem (só Admin) */
function index()
{
	global $usuarios;
	exigir_admin();
	carregarPermissoes();
	$usuarios = find_all(USUARIOS_TABELA, 'Login_usu');
}

/** Visualização (só Admin) */
function view($id = null)
{
	global $usuario;
	exigir_admin();
	carregarPermissoes();

	$id = (int) $id;
	$usuario = $id > 0 ? find(USUARIOS_TABELA, $id, USUARIOS_PK) : null;

	if (!$usuario) {
		$_SESSION['message'] = "Usuário não encontrado.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}
}

/** Cadastro (só Admin) */
function add()
{
	global $usuario;
	exigir_admin();
	carregarPermissoes();

	if (empty($_POST['usuario'])) {
		return;
	}

	$dados = dadosUsuario($_POST['usuario']);
	$usuario = $dados; // mantém o que foi digitado se houver erro

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
		'Id_Permi'  => $dados['Id_Permi'],
		'Login_usu' => $dados['Login_usu'],
		'Senha_usu' => password_hash($dados['Senha'], PASSWORD_DEFAULT),
		'Ativo_usu' => $dados['Ativo_usu'],
	];
	if ($foto) {
		$registro['Foto_usu'] = $foto;
	}

	if (save(USUARIOS_TABELA, $registro)) {
		header('location: index.php');
		exit;
	}

	apagar_foto_usuario($foto); // não salvou: não deixa a foto órfã
}

/** Edição (só Admin) */
function edit()
{
	global $usuario;
	exigir_admin();
	carregarPermissoes();

	$id = (int) ($_GET['id'] ?? 0);
	if ($id <= 0) {
		header('location: index.php');
		exit;
	}

	$atual = find(USUARIOS_TABELA, $id, USUARIOS_PK);
	if (!$atual) {
		$_SESSION['message'] = "Usuário não encontrado.";
		$_SESSION['type'] = "warning";
		header('location: index.php');
		exit;
	}

	$ehEu = ($id === (int) $_SESSION['usuario']['id']);

	if (isset($_POST['usuario'])) {
		$dados = dadosUsuario($_POST['usuario']);

		// ninguém altera o próprio perfil nem se desativa (evita ficar sem acesso)
		if ($ehEu) {
			$dados['Id_Permi'] = (int) $atual['Id_Permi'];
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
			'Id_Permi'  => $dados['Id_Permi'],
			'Login_usu' => $dados['Login_usu'],
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
			// editou a própria conta: atualiza o que aparece no menu e no perfil
			if ($ehEu) {
				$_SESSION['usuario']['login'] = $dados['Login_usu'];
				$_SESSION['usuario']['foto'] = $foto;
			}
			header('location: index.php');
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

/** Exclusão (só Admin) */
function delete($id = null)
{
	exigir_admin();

	$id = (int) $id;
	if ($id > 0) {
		if ($id === (int) $_SESSION['usuario']['id']) {
			definir_erro("Você não pode excluir a sua própria conta.");
		} else {
			$atual = find(USUARIOS_TABELA, $id, USUARIOS_PK);

			if ($atual) {
				remove(USUARIOS_TABELA, $id, USUARIOS_PK);
				if (($_SESSION['type'] ?? '') === 'success') {
					apagar_foto_usuario($atual['Foto_usu'] ?? null);
				}
			} else {
				$_SESSION['message'] = "Usuário não encontrado.";
				$_SESSION['type'] = "warning";
			}
		}
	}

	header("location: index.php");
	exit;
}
?>