<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class multimarcasControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Multimarcas";
        $this->config->componenteUrl				= BASE . 'multimarcas';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("veiculos_multimarcas");
        $this->config->componenteTabelaFotos		= Sistema::Table("veiculos_multimarcas_fotos");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				        => array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'#galeria' 			        => array( 'name'=> 'Foto', 'size'=>'100px')
        ,'item.codigo_veiculo'    		=> array( 'name'=> 'Código')
        ,'item.marca'			        => array( 'name'=> 'Marca')
        ,'item.placa'			        => array( 'name'=> 'Placa')
        ,'item._status'			        => array( 'size'=>'30px')
        ,'#acoes' 				        => array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('codigo_veiculo', 'marca','resumo', 'placa' ,'preco' ,'destaque');

        //views multimarcas, ativos


        // Executa o __construct da classe extendida
        parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
    }

    protected function jsonlistagemLinhaAlt($Item, $i)
    {

        if ($this->_listagemColunas[$i] == "#galeria") {

            $imagem = 'media/uploads/sem-imagem.jpg';


            $Imagem = $this->App->BancoDeDados->query("
                                                    SELECT
                                                          item.arquivo
                                                    FROM
                                                        " . $this->config->componenteTabelaFotos . " as item
                                                    WHERE
                                                        item._status          = 'A'
                                                        AND item._deletado   is null
                                                        AND item.item_id        = '" . $Item->{'item.id'} . "'
                                                    ORDER BY
                                                        RAND()
                                                    LIMIT 1
                                                ")->fetch(PDO::FETCH_OBJ);

            if (isset($Imagem->arquivo) && $Imagem->arquivo) {
                $imagem = $Imagem->arquivo;
            }

            $Linha = '<a class="thumbnail" href="' . $this->config->componenteUrl . '-fotos/?idpai=' . $Item->{'item.id'} . '" title="Clique editar as imagens"><img src="../miniatura/' . $imagem . '&w=80&h=80&bg=ffffff"></a>';

        } elseif ($this->_listagemColunas[$i] != ' ') {

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
													 item.codigo_veiculo
													,item.marca
													,item.placa
													,item.resumo
													,item.preco
													,item.destaque
													,GROUP_CONCAT(item.carimbos SEPARATOR ', ') as carimbos
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
                            <span>'.$Fotos[$i]->legenda.'</span>
                        </div>';

        }

        $resposta = array();

        $resposta['Código do veículo'] = $Item->codigo_veiculo;
        $resposta['Marca'] = $Item->marca;
        $resposta['Placa'] = $Item->placa;
        $resposta['Preço'] = $Item->preco;
        $resposta['Carimbos'] = $Item->carimbos;
        $resposta['Destaque'] = ($Item->destaque == 1) ? 'Sim' : 'Não';
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Fotos'] = $imagens;


        $this->Modelo->ver($resposta);
    }
}