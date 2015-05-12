<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class acessoriosControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Acessórios";
        $this->config->componenteUrl				= BASE . 'acessorios';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("acessorios");
        $this->config->componenteTabelaFotos		= Sistema::Table("acessorios_fotos");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				            => array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'#galeria'         	    		=> array( 'name'=> 'Foto', 'size'=>'100px')
        ,'item.nome'    		            => array( 'name'=> 'Nome')
        ,'item.resumo'			            => array( 'name'=> 'Descrição')
        ,'item.familia_veiculo'		    	=> array( 'name'=> 'Família do Veículo')
        ,'item._status'			            => array( 'size'=>'30px')
        ,'#acoes' 				            => array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('nome', 'resumo', 'familia_veiculo');

        //views: familia do veiculo, acessorios relacionados


        // Executa o __construct da classe extendida
        parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
    }

    protected function jsonlistagemLinhaAlt($Item, $i) {

        if( $this->_listagemColunas[$i] == "#galeria" ) {

            $imagem = 'media/uploads/sem-imagem.jpg';


            $Imagem = $this->App->BancoDeDados->query("
                                                    SELECT
                                                          item.arquivo
                                                    FROM
                                                        ".$this->config->componenteTabelaFotos." as item
                                                    WHERE
                                                        item._status          = 'A'
                                                        AND item._deletado   is null
                                                        AND item.item_id        = '".$Item->{'item.id'}."'
                                                    ORDER BY
                                                        RAND()
                                                    LIMIT 1
                                                ")->fetch( PDO::FETCH_OBJ );

            if( isset($Imagem->arquivo) && $Imagem->arquivo ){
                $imagem = $Imagem->arquivo;
            }

            $Linha = '<a class="thumbnail" href="'.$this->config->componenteUrl.'-fotos/?idpai='.$Item->{'item.id'}.'" title="Clique editar as imagens"><img src="../miniatura/'.$imagem.'&w=80&h=80&bg=ffffff"></a>';

        } elseif( $this->_listagemColunas[$i] != ' ' ) {

            $Linha = html_entity_decode($Item->{$this->_listagemColunas[$i]});

        }

        return $Linha;
    }

    public function ver()
    {

        // Pega o id do item
        $id = (int)$this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													 item.nome
													,item.resumo
													,item.familia_veiculo
												FROM
													" . $this->config->componenteTabela . " as item
												WHERE
														item.id = '" . $id . "'
													AND item._deletado IS NULL
											")->fetch(PDO::FETCH_OBJ);


        $Fotos = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.arquivo
                                                      ,item.legenda
                                                FROM
                                                    ".$this->config->componenteTabelaFotos." as item
                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado   is null
                                                    AND item.item_id      = '".$id."'

                                                ORDER BY
                                                    item.ordem ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );

        if (!$Item) {
            $this->Sistema->Redirecionar($this->config->componenteUrl . $this->config->url_compl, '', 'Item não encontrado.');
        }

        $imagens = '';
        for( $i = 0; $i < count($Fotos); $i++){
            $imagens .= '<div class="thumbnail" style="width: 200px; height: 150px; float: left; margin: 0 5px;">
                            <img width="200" height="150" src="'.FRONDEND . $Fotos[$i]->arquivo .'" alt="">
                        </div>';

        }

        $resposta = array(); #$resposta[ NomeDoItem ] = ValorDoItem // Caso for campo editor usar a função html_entity_decode()

        $resposta['Nome'] = $Item->nome;
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Família do veículo'] = $Item->familia_veiculo;
        $resposta['Foto'] = $imagens;

        $this->Modelo->ver($resposta);
    }
}