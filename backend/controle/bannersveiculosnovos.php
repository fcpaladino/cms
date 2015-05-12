<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class bannersveiculosnovosControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Veículos Novos Banners";
        $this->config->componenteUrl				= BASE . 'banners-veiculos-novos';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("veiculos_novos_banners");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				    => array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'item.arquivo' 		    => array( 'name'=> 'Foto', 'size'=>'100px')
        ,'item.titulo'    		    => array( 'name'=> 'Título')
        ,'item.resumo'			    => array( 'name'=> 'Descrição')
        ,'item.ordem'			    => array( 'name'=> 'Ordem')
        ,'item.data_inicio'			=> array( 'name'=> 'Data início')
        ,'item.data_fim'			=> array( 'name'=> 'Data fim')
        ,'item._status'			    => array( 'size'=>'30px')
        ,'#acoes' 				    => array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('titulo', 'resumo', 'ordem', 'arquivo', 'data_inicio_to_data_fim');


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
													 item.titulo
													,item.resumo
													,item.data_inicio
													,item.data_fim
													,item.ordem
													,item.arquivo
												FROM
													" . $this->config->componenteTabela . " as item
												WHERE
														item.id = '" . $id . "'
													AND item._deletado IS NULL
											")->fetch(PDO::FETCH_OBJ);

        $imagem = '<div class="thumbnail" style="width: 200px; height: 150px;">
                    <img width="200" height="150" src="'.FRONDEND . $Item->arquivo.'" alt="">
        </div>';

        if (!$Item) {
            $this->Sistema->Redirecionar($this->config->componenteUrl . $this->config->url_compl, '', 'Item não encontrado.');
        }


        $resposta = array(); #$resposta[ NomeDoItem ] = ValorDoItem // Caso for campo editor usar a função html_entity_decode()

        $resposta['Titulo'] = $Item->titulo;
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Data início'] = $this->Sistema->ConverterDataHora($Item->data_inicio);
        $resposta['Data final'] = $this->Sistema->ConverterDataHora($Item->data_fim);
        $resposta['Ordem'] = $Item->ordem;
        $resposta['arquivo'] = $imagem;

        $this->Modelo->ver($resposta);
    }
}