<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class loginModelo extends Modelo {

	public function index( ) {

		// Carrega as dependencias js e css
		$this->carregarDepencias();

		$this->addClasseBody( 'login' );

		$this->tpl = $this->CarregarTemplate( 'index.tpl.php' );


        $this->addCSS( CSS . 'login.css');
        $this->addJS( JS . 'jquery-validation/js/jquery.validate.min.js');
        $this->addJS( JS . 'login.js');

        $this->Plugins->carrega('notificacao');

        $this->addJQUERY('
            //Login.init();
            App.initLogin();
        ');

		$this->tpl->Renderizar();

	}

}

?>