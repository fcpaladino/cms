<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class indexModelo extends Modelo {

	public function index( ) {

		// Carrega as dependencias js e css
        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

		$this->tpl = $this->CarregarTemplate( 'index.tpl.php' );

		$this->tpl->Renderizar();

	}

}

?>