<?php
/**
 * Created by PhpStorm.
 * User: rodrigo
 * Date: 5/6/15
 * Time: 8:34 AM
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class veiculosnovosControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome				= "Veículos Novos";
        $this->config->componenteUrl				= BASE . 'veiculos-novos';
        $this->config->componenteTitulo				= $this->config->componenteNome;
        $this->config->componenteSubTitulo			= '';
        $this->config->componenteTabela				= Sistema::Table("veiculos_novos");
        $this->config->componenteTabelaFotos		= Sistema::Table("veiculos_novos_fotos");
        $this->config->componenteTabelaRelacao		= Sistema::Table("veiculos_novos_rel_modelos");
        $this->config->componenteTabelaModelos		= Sistema::Table("veiculos_modelos");

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
        ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
        ,'#galeria' 			=> array( 'name'=> 'Foto', 'size'=>'100px')
        ,'item.nome'    		=> array( 'name'=> 'Nome')
        ,'item.resumo'			=> array( 'name'=> 'Descrição')
        ,'item.ordem'			=> array( 'name'=> 'Ordem')
        ,'item._status'			=> array( 'size'=>'30px')
        ,'#acoes' 				=> array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('nome', 'modelo_id', 'resumo', 'ordem', 'url');


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
													 item.nome
													,item.resumo
													,item.ordem
													,GROUP_CONCAT(itemModelos.nome SEPARATOR ', ') as modelos
												FROM
													" . $this->config->componenteTabela . " as item
												INNER JOIN
													". $this->config->componenteTabelaRelacao ." as itemRelacao
												ON (itemRelacao.veiculo_id = item.id)
												INNER JOIN
													". $this->config->componenteTabelaModelos ." as itemModelos
												ON (itemRelacao.modelo_id = itemModelos.id)
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

        $resposta['Nome'] = $Item->nome;
        $resposta['Descrição'] = html_entity_decode($Item->resumo);
        $resposta['Modelos'] = $Item->modelos;
        $resposta['Fotos'] = $imagens;


        $this->Modelo->ver($resposta);
    }
}