<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class enderecoControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Endereços";
        $this->config->componenteUrl				= BASE . 'endereco';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("enderecos_rodape");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				    => array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'item.nome'			    => array( 'name'=> 'Nome')
        ,'item.endereco'			=> array( 'name'=> 'Endereço')
        ,'item.cidade'			    => array( 'name'=> 'Cidade')
        ,'item.estado'			    => array( 'name'=> 'Estado')
        ,'item.telefone'			=> array( 'name'=> 'Telefone')
        ,'item.ordem'			    => array( 'name'=> 'Ordem')
        ,'item._status'			    => array( 'size'=>'30px')
        ,'#acoes' 				    => array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('nome', 'endereco', 'cidade', 'estado', 'telefone', 'ordem');


        // Executa o __construct da classe extendida
        parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
    }

    public function ver(){

        // Pega o id do item
        $id = (int) $this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													 item.nome
													,item.endereco
													,item.cidade
													,item.estado
													,item.telefone
													,item.ordem
												FROM
													".$this->config->componenteTabela." as item
												WHERE
														item.id = '".$id."'
													AND item._deletado IS NULL
											")->fetch( PDO::FETCH_OBJ );

        if( !$Item ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }


        $resposta = array(); #$resposta[ NomeDoItem ] = ValorDoItem // Caso for campo editor usar a função html_entity_decode()

        $resposta['Nome'] = $Item->nome;
        $resposta['Endereço'] = $Item->endereco;
        $resposta['Cidade'] = $Item->cidade;
        $resposta['Estado'] = $Item->estado;
        $resposta['Telefone'] = $Item->telefone;
        $resposta['Ordem'] = $Item->ordem;

        $this->Modelo->ver( $resposta );
    }
}