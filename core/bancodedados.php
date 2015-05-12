<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class ConexaoBD extends PDO {

	private $_info = null;

	private static $instancia;

    // O método singleton 
	public static function singleton($params) {

		if ( !isset( self::$instancia ) ) {

			$c = __CLASS__;
			self::$instancia = new $c($params);

		}

		return self::$instancia;

	}

	public function __construct($params)
	{
        $this->_info = new stdClass();

		try {

			$this->_info->driver	= $params['driver'];
			$this->_info->hostname	= $params['hostname'];
			$this->_info->port		= $params['port'];

			// Set PATH
			if ( array_key_exists( 'path', $params ) && in_array( $this->_info->driver, array('sqlite', 'obdc') ) ) {
				$this->_info->path = $params['path'];
			} else {
				$this->_info->path = null;
			}
			$this->_info->database	= $params['database'];
			$this->_info->username 	= $params['username'];
			$this->_info->password 	= $params['password'];
			$this->_info->options 	= $params['options'];

			switch ($this->_info->driver) {
				case 'sqlite':
				case 'ODBC': {
					parent::__construct($this->_prepareDSN());
				} break;
				default: {
					parent::__construct(
						$this->_prepareDSN(),
						$this->_info->username, $this->_info->password,
						$this->_info->options
					);
				} break;
			}

			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			switch ($this->_info->driver) {
				case 'mysql': {
					$this->exec("SET NAMES utf8");
					$this->exec("SET CHARACTER SET utf8");
				} break;
			}
		} catch (PDOException $e) {
			trigger_error(new Exception("Connection failed: " . $e), E_USER_ERROR);
		}
	}

	private function _prepareDSN()
	{
		switch (strtolower($this->_info->driver))
		{
			case "pgsql": {
				$_dsn = "pgsql:dbname={$this->_info->database};host={$this->_info->hostname}";
			} break;

			case "sqlite": {
				$_dsn = "sqlite:{$this->_info->path}{$this->_info->database}.sdb";
			} break;

			case "mysql": {
				$_dsn = "mysql:host={$this->_info->hostname};port={$this->_info->port};dbname={$this->_info->database}";
			} break;

			case "firebird": {
				$_dsn = "firebird:dbname={$this->_info->hostname}:{$this->_info->path}{$this->_info->database}.fdb";
			} break;

			case "informix": {
				$_dsn = "informix:DSN={$this->_info->database};host={$this->_info->hostname}";
			} break;

			case "oracle": {
				$_dsn = "OCI:dbname={$this->_info->database};charset=UTF-8";
			} break;

			case "obdc": {
				$_dsn = "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq={$this->_info->path}{$this->_info->database}.mdb;Uid={$this->_info->username}";
			} break;

			case "dllib": {
				$_dsn = "dblib:host={$this->_info->hostname}:{$this->_info->port};dbname={$this->_info->database}";
			} break;

			case "ibm": {
				$_dsn = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->_info->database}; HOSTNAME={$this->_info->hostname};PORT={$this->_info->port};PROTOCOL=TCPIP;";
			} break;
		}

		return $_dsn;
	}



}

?>