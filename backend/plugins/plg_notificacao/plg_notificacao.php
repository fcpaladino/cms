<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class plg_notificacao extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->addJS( PLUGINS	. get_class($this) . '/js/snarl.min.js' );
		$this->addJS( PLUGINS	. get_class($this) . '/js/main.js' );
        $this->addCSS( PLUGINS	. get_class($this) . '/css/font-awesome.min.css' );
        $this->addCSS( PLUGINS	. get_class($this) . '/css/snarl.css' );

	}

}

?>