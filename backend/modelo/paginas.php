<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class paginasModelo extends Modelo {

	public function index( ) {

		$this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, $this->config->componenteSubTitulo);

        $this->Plugins->carrega('fancybox');

        $this->tpl = $this->CarregarTemplate( 'index.tpl.php' );

        $this->tpl->atribuir('ModuloAcao',      $this->Modulos->carrega('acoesmassa',       array('componentePai'=>(isset($this->config->componenteUrlItemPai)?$this->config->componenteUrlItemPai:'') )));
        $this->tpl->atribuir('ModeloTabela',    $this->Modulos->carrega('tabelalistagem',   array('colunas'=>$this->config->listagemColunas) ));


        $this->tpl->Renderizar();
	}


	/**
	 * Cadastrar
	 */
	public function cadastrar() {

		// Carrega as dependencias js e css
		$this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Cadastrar');


        $this->tpl = $this->CarregarTemplate( 'formulario.tpl.php' );

        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform'));

        $this->Plugins->carrega('notificacao');

        $parametros = array(
             'tabela'		=> $this->config->componenteTabela
            ,'tipo'			=> 'cadastrar'
            ,'campos'		=> $this->config->campos
            ,'codigo'		=> ''
            ,'titulo'		=> $this->config->componenteNome
            ,'ajax'	    	=> true
        );
        $this->tpl->atribuir('ModuloFormulario',  Modulos::carrega('formulariodinamico', $parametros));

        $this->tpl->Renderizar();


	}


	/**
	 * Editar
	 */
	public function editar( $Item ) {

        // Carrega as dependencias js e css
        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Editar');

        $this->tpl = $this->CarregarTemplate( 'formulario.tpl.php' );
        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform'));
        $this->Plugins->carrega('notificacao');
        $parametros = array(
             'tabela'		=> $this->config->componenteTabela
            ,'tipo'			=> 'editar'
            ,'campos'		=> $this->config->campos
            ,'codigo'		=> $Item->id
            ,'titulo'		=> $this->config->componenteNome
            ,'ajax'	    	=> true
        );
        $this->tpl->atribuir('ModuloFormulario',  $this->Modulos->carrega('formulariodinamico', $parametros));

        $this->Plugins->carrega('fancybox');

		$this->tpl->Renderizar();
	}


    public function ver( $Item ) {

        // Carrega as dependencias js e css
        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Ver');

        $this->tpl = $this->CarregarTemplate( 'ver.tpl.php' );
        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform', array('action'=>false)));


        foreach ($Item as $key => $value) {

            $this->tpl->atribuir('ver_titulo',             $key);
            $this->tpl->atribuir('ver_valor',              $value);

            $this->tpl->block('LISTA');

            $this->tpl->limpa('ver_titulo');
            $this->tpl->limpa('ver_valor');

        }




        $this->tpl->Renderizar();
    }

}

?>