<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class erroControle extends Controle {

	public function __construct(){

        $this->config = new stdClass();
        $this->config->componenteNome				= "Administradores";
        $this->config->componenteUrl				= BASE . 'administradores';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';

        parent::__construct();

    }

	public function rotear( ) {

		$Erro =  isset($this->router[2]) ? $this->router[2] : '';

		return $this->Modelo->index( $Erro );

	}

}

?>