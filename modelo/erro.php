<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class erroModelo extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function index( $erro = null ) {

        switch( $erro ) {

            case '403';
                header('HTTP/1.1 403 Forbidden');
                $params = array(
                    'erro' => '403',
                    'nome' => 'Acesso Proibido',
                    'conteudo' => 'Você não tem permissão para acessar o diretório requisitado.<br />Pode não existir o arquivo de índice ou o diretório pode estar protegido contra leitura.'
                );
                break;


            case '401':
                $params = array(
                    'erro' => '401',
                    'nome' => 'Acesso Negado',
                    'conteudo' => 'Você não tem permissão para acessar esta página.'
                );
                break;

            default;
                header('HTTP/1.1 404 Not Found');
                $params = array(
                    'erro' => '404',
                    'nome' => 'Página Não Encontrada',
                    'conteudo' => 'A página que você está tentando acessar não existe ou foi movida.'
                );
                break;

        }

        $Info = (object) $params;

        $this->addCSS( 'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all' );

        $this->addCSS( BACKEND	.'template/js/bootstrap/css/bootstrap.min.css');
        $this->addCSS( BACKEND . 'template/css/components.css' );
        $this->addCSS( BACKEND . 'template/css/error.css' );

        $this->addClasseBody('page-404-full-page');


        $this->tpl = $this->CarregarTemplate( 'index.tpl.php' );
        $this->tpl->atribuir('numero',             $Info->erro);
        $this->tpl->atribuir('nome',               $Info->nome);
        $this->tpl->atribuir('conteudo',           $Info->conteudo);


		$this->tpl->Renderizar();

	}

}

?>