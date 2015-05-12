<?php
 /**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com        
 */
define('Application', 2593);

#ini_set('display_errors', 0); error_reporting(0);

// Força www, se nao estiver local
//if( $_SERVER['SERVER_NAME'] != "localhost" AND $_SERVER['SERVER_NAME'] != "local" AND $_SERVER['SERVER_NAME'] != "www" ) {
//	// Se nao conter www
//	if( substr($_SERVER['HTTP_HOST'], 0, 4) != "www." ) {
//		header("Location: http".((isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on') ? 's' : '')."://www." . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
//	}
//}

define('DS'				, DIRECTORY_SEPARATOR );
define('PS'				, PATH_SEPARATOR );

$url = @explode( '/', trim( $_GET['url'] ) );

// Defini o path dos arquivos do site
define('BASE_PATH'		, realpath('.') );

// Inclui o arquivo de configuração
require BASE_PATH . DS . 'config.php';

// Se estiver acessando o backend
if( $url[0] == AppConfig::$URLbackend ) {

	// Inclui o arquivo de nucleo do backend
	include 'core' . DS . 'backend.core.php';
	AppAdministrador::init();

} else { // Se estiver acessando o frontend

	// Inclui o arquivo de nucleo do frontend
	include 'core' . DS . 'core.php';
	App::init();

}

?>