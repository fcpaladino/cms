<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class acessoriosfotosControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Acessórios Fotos";
		$this->config->componenteUrl				= BASE . 'acessorios-fotos';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela     		= Sistema::Table("acessorios_fotos");
        $this->config->componenteTabelaItemPai		= Sistema::Table("acessorios");
        $this->config->componenteUrlItemPai			= BASE . 'acessorios';
        $this->config->pastaImagens                 = 'acessorios';

        // Pega o id pai dos itens passado por GET
        $this->config->idpai = (int) $_GET['idpai'];

        // Por ser uma janela que lista itens de um item pai então passamos o id dele como complemento na url
        $this->config->url_compl = "&idpai=".$this->config->idpai;

        // Valida o item pai
        $_t = self::item_pai($this->config->idpai, 'nome');

        $this->config->componenteNome .= ' - Fotos de '.$_t;
        $this->config->componenteTitulo .= ' - Fotos de '.$_t;

        // Tabelas para consulta da listagem
        $this->config->listagemQueryFrom = $this->config->componenteTabela." as item
			INNER JOIN
				".$this->config->componenteTabelaItemPai." as item_pai
			ON
					item_pai.id = '".$this->config->idpai."'
				AND item_pai.id = item.item_id
		";

        $this->config->listagemColunas = array(
            '#checkbox'
            ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
            ,'item.arquivo'		    => array( 'name' => 'Imagem', 'size' => '100px' )
            ,'item.legenda'		    => array( 'name' => 'Legenda')
            ,'item.ordem'		    => array( 'name' => 'Ordem')
            ,'item.destaque'		=> array( 'size'=>'30px')
            ,'item._status'			=> array( 'size'=>'30px')
            ,'#acoes' 				=> array( 'name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array(
            'ordem', 'legenda', 'arquivo', 'destaque'
        );

		// Executa o __construct da classe extendida
		parent::__construct();


        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->config->componenteUrlItemPai) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
	}

    public function ver()
    {

        // Pega o id do item
        $id = (int)$this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													 itemPai.resumo
													,item.legenda
													,item.ordem
													,item.arquivo
												FROM
													" . $this->config->componenteTabela . " as item
												INNER JOIN
													". $this->config->componenteTabelaItemPai ." as itemPai
												ON (itemPai.id = item.item_id)
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

        $resposta['Descrição do veículo'] = html_entity_decode($Item->resumo);
        $resposta['Legenda'] = $Item->legenda;
        $resposta['Ordem'] = $Item->ordem;
        $resposta['Foto'] = $imagem;

        $this->Modelo->ver($resposta);
    }

}
