<?php

$mysqli = new mysqli_driver();
$mysqli->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;

function open_database()
{
	try {
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$conn->set_charset("utf8mb4");
		return $conn;
	} catch (Exception $e) {
		throw new Exception("Erro ao conectar no banco de dados\n {$e->getMessage()}");
	}
}

function close_database($conn)
{
	try {
		$conn->close();
	} catch (Exception $e) {
		throw new Exception("Erro ao encerrar conexão com o banco de dados\n {$e->getMessage()}");
	}
}

/**
 *  Nome de tabela/coluna não pode ser parâmetro do prepared statement,
 *  então só aceita letras, números e "_" e coloca entre crases.
 */
function identificador($nome)
{
	if (!is_string($nome) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $nome)) {
		throw new InvalidArgumentException("Nome inválido de tabela/coluna.");
	}
	return "`$nome`";
}

/**
 *  Registra o erro real no log do servidor e mostra uma mensagem amigável.
 */
function erro_banco($e)
{
	error_log($e->getMessage());

	if ($e instanceof mysqli_sql_exception && (int) $e->getCode() === 1062) {
		$_SESSION['message'] = "Já existe um registro com esses dados.";
	} else {
		$_SESSION['message'] = "Não foi possível realizar a operação.";
	}
	$_SESSION['type'] = 'danger';
}

/**
 *  Pesquisa um registro pelo ID. $pk = nome da coluna de ID da tabela.
 *  Sem $id, devolve todos os registros.
 */
function find($table = null, $id = null, $pk = 'Id')
{
	if (!$id) {
		return find_all($table);
	}

	$found = null;
	$database = null;

	try {
		$database = open_database();
		$sql = "SELECT * FROM " . identificador($table) . " WHERE " . identificador($pk) . " = ? LIMIT 1";
		$stmt = $database->prepare($sql);
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$found = $stmt->get_result()->fetch_assoc() ?: null;
		$stmt->close();
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $found;
}

/**
 *  Pesquisa o primeiro registro em que uma coluna tem determinado valor
 *  (usado para checar CPF duplicado, por exemplo).
 */
function find_by($table, $column, $value)
{
	$found = null;
	$database = null;

	try {
		$database = open_database();
		$sql = "SELECT * FROM " . identificador($table) . " WHERE " . identificador($column) . " = ? LIMIT 1";
		$stmt = $database->prepare($sql);
		$stmt->bind_param('s', $value);
		$stmt->execute();
		$found = $stmt->get_result()->fetch_assoc() ?: null;
		$stmt->close();
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $found;
}

/**
 *  Todos os registros de uma tabela, opcionalmente ordenados por uma coluna.
 */
function find_all($table, $orderBy = null)
{
	$found = null;
	$database = null;

	try {
		$database = open_database();
		$sql = "SELECT * FROM " . identificador($table);
		if ($orderBy) {
			$sql .= " ORDER BY " . identificador($orderBy);
		}
		$result = $database->query($sql);

		if ($result->num_rows > 0) {
			$found = $result->fetch_all(MYSQLI_ASSOC);
		}
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $found;
}

/**
 *  Insere um registro. Retorna true se deu certo.
 */
function save($table = null, $data = null)
{
	$database = null;
	$ok = false;

	try {
		$database = open_database();

		$colunas = [];
		$marcas = [];
		$valores = [];
		foreach ($data as $coluna => $valor) {
			$colunas[] = identificador($coluna);
			$marcas[] = '?';
			$valores[] = $valor;
		}

		$sql = "INSERT INTO " . identificador($table)
			. " (" . implode(',', $colunas) . ") VALUES (" . implode(',', $marcas) . ")";
		$stmt = $database->prepare($sql);
		$stmt->bind_param(str_repeat('s', count($valores)), ...$valores);
		$stmt->execute();
		$stmt->close();

		$_SESSION["message"] = "Registro cadastrado com sucesso.";
		$_SESSION["type"] = "success";
		$ok = true;
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $ok;
}

/**
 *  Atualiza um registro por ID. Retorna true se deu certo.
 */
function update($table = null, $id = 0, $data = null, $pk = 'Id')
{
	$database = null;
	$ok = false;

	try {
		$database = open_database();

		$sets = [];
		$valores = [];
		foreach ($data as $coluna => $valor) {
			$sets[] = identificador($coluna) . " = ?";
			$valores[] = $valor;
		}
		$valores[] = $id;

		$sql = "UPDATE " . identificador($table) . " SET " . implode(', ', $sets)
			. " WHERE " . identificador($pk) . " = ?";
		$stmt = $database->prepare($sql);
		$stmt->bind_param(str_repeat('s', count($valores)), ...$valores);
		$stmt->execute();
		$stmt->close();

		$_SESSION["message"] = "Registro atualizado com sucesso.";
		$_SESSION["type"] = "success";
		$ok = true;
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $ok;
}

/**
 *  Remove um registro por ID. Retorna true se deu certo.
 */
function remove($table = null, $id = null, $pk = 'Id')
{
	$database = null;
	$ok = false;

	try {
		if ($id) {
			$database = open_database();
			$sql = "DELETE FROM " . identificador($table) . " WHERE " . identificador($pk) . " = ?";
			$stmt = $database->prepare($sql);
			$stmt->bind_param('s', $id);
			$stmt->execute();
			$stmt->close();

			$_SESSION["message"] = "Registro removido com sucesso.";
			$_SESSION["type"] = "success";
			$ok = true;
		}
	} catch (Exception $e) {
		erro_banco($e);
	}

	if ($database) {
		close_database($database);
	}
	return $ok;
}
?>