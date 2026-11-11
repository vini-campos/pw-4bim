<?php
/** O nome do banco de dados*/
const DB_NAME = "pw4bim";

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** caminho absoluto para a pasta do sistema **/
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/'); // não pode const aqui dentro
	
/** caminho no server para o sistema **/
if ( !defined('BASEURL') )
	define('BASEURL', '/pw4bim/'); //raiz do projeto. trocar para "/" quando hospedado
	
/** caminho do arquivo de banco de dados **/
if ( !defined('DBAPI') )
	define("DBAPI", ABSPATH . 'inc/database.php');

const HEADER_TEMPLATE = ABSPATH . "includes/header.php";
const FOOTER_TEMPLATE = ABSPATH . "includes/footer.php";