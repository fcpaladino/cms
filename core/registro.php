<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Registro extends ArrayObject
{

	private static $instance = null;

	public function __construct( array $array = array() ) {

		parent::__construct($array);

	}

	private static function _init() {

		self::$instance = new Registro();

	}

	public static function getInstance() {

		if (self::$instance === null) {
			self::_init();
		}

		return self::$instance;

	}

	public function set($index, $value)
	{

		$instance = self::getInstance();
		$instance->offsetSet($index, $value);

	}

	public function __get($index) {

		$instance = self::getInstance();

		if ($instance->offsetExists($index)) {
			return $instance->offsetGet($index);
		}

		return null;

	}

}