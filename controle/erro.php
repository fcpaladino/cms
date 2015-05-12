<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class erroControle extends Controle {

	public function __construct(){

		parent::__construct();

		$this->classModelo = $this->App->nomeControle . 'Modelo';
		$this->Modelo = new $this->classModelo();

	}

	public function rotear( ) {

		$Erro =  isset($this->router[1]) ? $this->router[1] : '';

		return $this->Modelo->index( $Erro );

	}

}

?>