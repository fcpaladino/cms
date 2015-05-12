<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

#define('BASEURL', 'http://www/framework/');

class AppConfig {

	public static $SiteUrl				= 'http://www/framework/';

	// Head
	public static $SiteAdministracao	= 'Painel Administrativo';
	public static $TituloSeparador		= ' - ';

	// Sistema
	public static $URLbackend			= 'admin';
	public static $ControleInicial		= 'index';

	/* Banco de dados */
	public static $PrefixoDB			= 'webee_';
	public static $PrefixoPermSessao	= 'frEw13_';
	public static $DbConfig = array(
		'driver' 	=> 'mysql',
		'hostname' 	=> 'localhost',
		'port' 		=> '3306',
		'database' 	=> 'dev_framework',
		'username' 	=> 'root',
		'password' 	=> '',
		'options' 	=> array(PDO::ATTR_PERSISTENT => true)
	);

}
