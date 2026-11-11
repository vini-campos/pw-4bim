<?php

$mysqli = new mysqli_driver();
$mysqli->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;
//report_mode -> é um atributo;

function open_database()
{
	try {
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$conn->set_charset("utf8"); // metodo;
		return $conn;
	} catch (Exception $e) {
		throw new Exception("Erro ao tentar conectar ao banco de dados\n {$e->getMessage()}");
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
 *  Pesquisa um Registro pelo ID em uma Tabela
 */
function find($table = null, $id = null)
{
	$found = null;

	try {
		$database = open_database();

		if ($id) {
			$sql = "SELECT * FROM $table WHERE id = $id";
			$result = $database->query($sql);

			if ($result->num_rows > 0) {
				$found = $result->fetch_assoc();
			}

		} else {

			$sql = "SELECT * FROM $table";
			$result = $database->query($sql);

			if ($result->num_rows > 0) {
				$found = $result->fetch_all(MYSQLI_ASSOC);

				/* Metodo alternativo
				$found = [];
				while ($row = $result->fetch_assoc()) {
				  array_push($found, $row);
				} */
			}
		}
	} catch (Exception $e) {
		$_SESSION['message'] = $e->GetMessage();
		$_SESSION['type'] = 'danger';
	}

	close_database($database);
	return $found;
}

/**
 *  Pesquisa Todos os Registros de uma Tabela
 */
function find_all($table)
{
	return find($table);
}

/**
 *  Insere um registro no BD
 */
function save($table = null, $data = null)
{

	$database = open_database();

	$columns = null;
	$values = null;

	//print_r($data);

	foreach ($data as $key => $value) {
		$columns .= trim($key, "'") . ","; //.= -> concatena
		$values .= "'$value',";
		/*
			'$value' -> mesmo que o campo seja passado entre apostrofo, o bd interpreta corretamente caso o campo seja do tipo int ou outros
		*/
	}

	// remove a ultima virgula
	$columns = rtrim($columns, ',');
	$values = rtrim($values, ',');

	$sql = "INSERT INTO $table ($columns) VALUES ($values);";

	try {
		$database->query($sql); // executa a query

		$_SESSION["message"] = "Registro cadastrado com sucesso.";
		$_SESSION["type"] = "success";

	} catch (Exception $e) {

		$_SESSION["message"] = "Nao foi possivel realizar a operacao.";
		$_SESSION["type"] = "danger";
	}

	close_database($database);
}

/**
 *  Atualiza um registro em uma tabela, por ID
 */
function update($table = null, $id = 0, $data = null)
{

	$database = open_database();

	$items = null;

	foreach ($data as $key => $value) {
		$items .= trim($key, "'") . "='$value',";
	}

	// remove a ultima virgula
	$items = rtrim($items, ',');

	// $sql = "UPDATE " . $table;
	// $sql .= " SET $items";
	// $sql .= " WHERE id=" . $id . ";";

	$sql = "UPDATE $table SET $items WHERE id=$id;";

	try {
		$database->query($sql);

		$_SESSION["message"] = "Registro atualizado com sucesso.";
		$_SESSION["type"] = "success";

	} catch (Exception $e) {

		$_SESSION["message"] = "Nao foi possivel realizar a operacao.";
		$_SESSION["type"] = "danger";
	}

	close_database($database);
}

function remove( $table = null, $id = null ) {

  $database = open_database();
	
  try {
    if ($id) {

      $sql = "DELETE FROM $table WHERE id = $id";
      $result = null; //$database->query($sql);

      if ($result = $database->query($sql)) {   	
        $_SESSION["message"] = "Registro Removido com Sucesso.";
        $_SESSION["type"] = "success";
      }
    }
  } catch (Exception $e) { 

    $_SESSION["message"] = "Não foi possível realizar a operação:<br>{$e->GetMessage()}";
    $_SESSION["type"] ="danger";
  }

  close_database($database);
}
?>