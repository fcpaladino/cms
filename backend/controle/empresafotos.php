<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class empresafotosControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Empresa Fotos";
		$this->config->componenteUrl				= BASE . 'empresa-fotos';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela     		= Sistema::Table("empresa_fotos");
        $this->config->componenteTabelaItemPai		= Sistema::Table("empresa");
        $this->config->componenteUrlItemPai			= BASE . 'empresa';

        $this->config->pastaImagens                 = 'empresa';

        // Pega o id pai dos itens passado por GET
        $this->config->idpai = (int) $_GET['idpai'];

        // Por ser uma janela que lista itens de um item pai então passamos o id dele como complemento na url
        $this->config->url_compl = "&idpai=".$this->config->idpai;

        // Valida o item pai
        $_t = self::item_pai($this->config->idpai, 'titulo');

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
            'ordem', 'legenda', 'arquivo'
        );

		// Executa o __construct da classe extendida
		parent::__construct();


        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->config->componenteUrlItemPai) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
	}

}

?>