<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class slidesControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Slides";
		$this->config->componenteUrl				= BASE . 'slides';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela				= Sistema::Table("slides");
		$this->config->componenteTabelaRelacao		= Sistema::Table("slides_rel_paginas");
		$this->config->componenteTabelaPaginas		= Sistema::Table("pagians");

        $this->config->pastaImagens                 = 'slides';

		// Tabelas para consulta da listagem
		$this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
            ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
            ,'item.arquivo'			=> array( 'name' => 'Imagem', 'size' => '100px' )
            ,'item.legenda'			=> array( 'name' => 'legenda')
            ,'item._status'			=> array( 'size'=>'21px' )
            ,'#acoes' 				=> array( 'name'=>'', 'size'=>'20px' )
        );

        $this->config->campos = array(
            'legenda', 'data_inicio_to_data_fim', 'ordem', 'arquivo'
        );


        /*Acoes::set( array(
            'cadastrar'		 	 => 1
            ,'editar'		   	 => 1
            ,'excluir'			 => 1
            ,'ativar'			 => 1
            ,'desativar'		 => 1
            ,'comdestaque'		 => 1
            ,'semdestaque'		 => 1
            ,'duplicar'			 => 0
            ,'ver'				 => 0
        ) );*/


		// Executa o __construct da classe extendida
		parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }

	}




    protected function jsonlistagemLinhaAlt($Item, $i) {

        if( $this->_listagemColunas[$i] != ' ' ) {

            $Linha = html_entity_decode($Item->{$this->_listagemColunas[$i]});

        }

        return $Linha;

    }

    public function ver(){

        // Pega o id do item
        $id = (int) $this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													 item.legenda
													,item.arquivo
													,item.data_inicio
													,item.data_fim
													,item.ordem
												FROM
													".$this->config->componenteTabela." as item
												WHERE
														item.id = '".$id."'
													AND item._deletado IS NULL
											")->fetch( PDO::FETCH_OBJ );

        $imagem = '<div class="thumbnail" style="width: 200px; height: 150px;">
                    <img width="200" height="150" src="'.FRONDEND . $Item->arquivo.'" alt="">
        </div>';

        if( !$Item ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }


        $resposta = array(); #$resposta[ NomeDoItem ] = ValorDoItem // Caso for campo editor usar a função html_entity_decode()

        $resposta['Nome'] = $Item->legenda;
        $resposta['Data início'] = $this->Sistema->ConverterDataHora($Item->data_inicio);
        $resposta['Data final'] = $this->Sistema->ConverterDataHora($Item->data_fim);
        $resposta['Ordem'] = $Item->ordem;
        $resposta['Foto'] = $imagem;

        $this->Modelo->ver( $resposta );
    }
}
