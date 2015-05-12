<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class empresaControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Empresa";
		$this->config->componenteUrl				= BASE . 'empresa';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela				= Sistema::Table("empresa");
		$this->config->componenteTabelaFotos		= Sistema::Table("empresa_fotos");

		// Tabelas para consulta da listagem
		$this->config->listagemQueryFrom = $this->config->componenteTabela." as item";

        $this->config->listagemColunas = array(
            '#checkbox'
            ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
            ,'#galeria'             => array( 'name'=> 'Imagens', 'size'=>'80px')
            ,'item.titulo'			=> array( 'name'=> 'Titulo')
            ,'item._status'			=> array( 'size'=>'30px')
            ,'#acoes' 				=> array('name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('conteudo');


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

            $Linha = $Item->{$this->_listagemColunas[$i]};

        }

        return $Linha;

    }

}

?>