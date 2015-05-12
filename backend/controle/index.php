<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class indexControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome		= "Início";
		$this->config->componenteUrl		= BASE;
		$this->config->componenteTitulo		= 'Página Inicial';
		$this->config->componenteSubTitulo	= 'resumo geral';

		// Executa o __construct da classe extendida
		parent::__construct();

		// Somente se estiver logado continua
		ACL::SomenteLogado();
	}

	public function index( ) {

		$this->Modelo->index();

	}

}

?>