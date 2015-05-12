<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class indexModelo extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function index( $Banner ) {

		$this->addDESCRIPTION( $this->App->config->site_seo_descricao );
		$this->addKEYWORDS( $this->App->config->site_seo_palavrachave );

		$this->tpl = $this->CarregarTemplate( 'index.tpl.php' );
		$this->tpl->atribuir('ModuloHeader',	        $this->Modulos->carrega('header') );
		$this->tpl->atribuir('ModuloEnderecosRodape',	$this->Modulos->carrega('enderecosrodape') );
		$this->tpl->atribuir('ModuloBuscaHomeSeminovos',$this->Modulos->carrega('buscahomeseminovos') );


        for( $i = 0; $i < count($Banner); $i++){
            $this->tpl->atribuir('legenda',             $Banner[$i]->legenda);
            $this->tpl->atribuir('imagem',              BASE . $Banner[$i]->arquivo);

            $this->tpl->block('BANNER');

            $this->tpl->limpa('legenda');
            $this->tpl->limpa('imagem');
        }


        $this->tpl->Renderizar();

	}

}

?>