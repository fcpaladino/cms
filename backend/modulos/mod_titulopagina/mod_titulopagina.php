<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_titulopagina extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        $titulo = $subtitulo = '';

        $titulo     = isset($this->App->TituloPagina['titulo']) ? $this->App->TituloPagina['titulo'] : '';
        $subtitulo  = isset($this->App->TituloPagina['subtitulo']) ? $this->App->TituloPagina['subtitulo'] : '';

        $this->tpl->atribuir('titulo',  $titulo);
        $this->tpl->atribuir('subtitulo',  $subtitulo);

		return $this->tpl->salva();

	}

}

?>