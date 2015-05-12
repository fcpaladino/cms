<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class institucionalControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Institucional";
        $this->config->componenteUrl				= BASE . 'institucional';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("institucional");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'item.titulo'			=> array( 'name'=> 'Titulo')
        ,'item.resumo'			=> array( 'name'=> 'Resumo')
        ,'item._status'			=> array( 'size'=>'30px')
        ,'#acoes' 				=> array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('titulo','ordem', 'resumo');


        // Executa o __construct da classe extendida
        parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
    }


}