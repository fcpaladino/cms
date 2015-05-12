<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class textosControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Textos";
        $this->config->componenteUrl				= BASE . 'textos';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("textos");
        $this->config->componenteTabelaPaginas		= Sistema::Table("paginas");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				            => array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'item.titulo'			            => array( 'name'=> 'Título')
        ,'item.resumo'			            => array( 'name'=> 'Resumo')
        ,'item._status'			            => array( 'size'=>'30px')
        ,'#acoes' 				            => array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('pagina_id', 'titulo', 'resumo', 'resumo_lateral');


        // Executa o __construct da classe extendida
        parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
    }

    public function ver()
    {

        // Pega o id do item
        $id = (int)$this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													 itemPaginas.titulo as paginas
													,item.titulo
													,item.resumo
													,item.resumo_lateral
													,item.ordem
												FROM
													" . $this->config->componenteTabela . " as item
													INNER JOIN
													". $this->config->componenteTabelaPaginas ." as itemPaginas
												ON (itemPaginas.id = item.pagina_id)
												WHERE
														item.id = '" . $id . "'
													AND item._deletado IS NULL
											")->fetch(PDO::FETCH_OBJ);

        if (!$Item) {
            $this->Sistema->Redirecionar($this->config->componenteUrl . $this->config->url_compl, '', 'Item não encontrado.');
        }


        $resposta = array(); #$resposta[ NomeDoItem ] = ValorDoItem // Caso for campo editor usar a função html_entity_decode()

        $resposta['Titulo'] = $Item->paginas;
        $resposta['Titulo'] = $Item->titulo;
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Descrição lateral'] = html_entity_decode($Item->resumo_lateral);

        $this->Modelo->ver($resposta);
    }
}