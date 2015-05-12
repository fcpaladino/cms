<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_mensagensaviso extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

		return $this->tpl->salva();

	}

}

?>