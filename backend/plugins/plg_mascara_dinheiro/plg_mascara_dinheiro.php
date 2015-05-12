<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class plg_mascara_dinheiro extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->addJS( URL_PLUGINS	. get_class($this) . '/js/jquery.maskMoney.js' );

	}

}

?>