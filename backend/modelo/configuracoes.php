<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class configuracoesModelo extends Modelo {

	public function index( ) {

        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, $this->config->componenteSubTitulo);

        $this->tpl = $this->CarregarTemplate( 'index.tpl.php' );

		$this->tpl->atribuir('componenteUrl',				$this->config->componenteUrl );
		$this->tpl->atribuir('url_compl',					$this->config->url_compl );

        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform', array('voltar'=>false) ));

        $this->Plugins->carrega('carregacomponentes', array('maxlength'=>true, 'tags'=>true));

        $this->addJQUERY("
            App.pageConfig();
        ");



		$this->tpl->Renderizar();

	}

}

?>