<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Sessao {

	static private $_begin = 0;
	static private $_instance = null;
	static private $_debug = false;

	function __construct() {

		self::$_begin = microtime(true);

	}

	static public function init( $debug=false ) {

		self::$_instance = new Sessao();
		self::$_debug = $debug;
		session_start();

	}

	static public function Set($nome, $valor) {

		$_SESSION[$nome] = $valor;

	}

	static public function Apaga($nome) {

		unset( $_SESSION[$nome] );

	}

	static public function Destruir() {

		session_destroy();
		session_unset();

	}

	static public function Get( $name, $once=false ) {

		$v = null;

		if ( isset( $_SESSION[$name] ) ) {

			$v = $_SESSION[$name];
			if ( $once ) unset( $_SESSION[$name] );

		}

		return $v;

	}

}

?>