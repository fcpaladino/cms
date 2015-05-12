<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class modelosnovosControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Modelos Novos";
        $this->config->componenteUrl				= BASE . 'modelos-novos';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela     		= Sistema::Table("veiculos_modelos");
        $this->config->pastaImagens                 = 'modelos-novos';

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'item.arquivo'			=> array( 'name'=> 'Foto', 'size'=>'100px')
        ,'item.nome'    		=> array( 'name'=> 'Nome')
        ,'item.resumo'			=> array( 'name'=> 'Descrição')
        ,'item.ordem'			=> array( 'name'=> 'Ordem')
        ,'item._status'			=> array( 'size'=>'30px')
        ,'#acoes' 				=> array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('nome', 'resumo', 'link_conheca', 'link_monte_seu', 'arquivo', 'url');


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
													 item.nome
													,item.resumo
													,item.link_conheca
													,item.link_monte_seu
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

        $resposta['Nome'] = $Item->nome;
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Link Conheça'] = $Item->link_conheca;
        $resposta['Link Monte o seu'] = $Item->link_monte_seu;
        $resposta['Foto'] = $imagem;

        $this->Modelo->ver($resposta);
    }
}