<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class plg_mascara extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.maskedinput-1.3.min.js' );

	}

}

?>