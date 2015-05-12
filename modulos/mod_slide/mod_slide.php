<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_slide extends Modelo {

    public function __construct(){

        parent::__construct();

    }

    public function renderiza( $config = null ) {

        $this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        $pagina  = isset($config->pagina)  ? $config->pagina  : '';
        $produto = isset($config->produto) ? $config->produto : '';

        $Controle	= '';
        if( isset($this->router[0]) ) {
            $Controle	= str_replace('-', '', strtolower(trim($this->router[0])));
        }

        if( $Controle == '' OR $Controle == 'index' ){ $Controle = 'home'; }


        $BannerProduto = '';
        $BannerPaginas = '';

        $BannerPaginas = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.link
                                                      ,item.arquivo
                                                      ,item.nome
                                                      ,item.id
                                                FROM
                                                    ".AppConfig::$PrefixoDB."slides as item

                                                INNER JOIN ".AppConfig::$PrefixoDB."slides_rel_paginas as relacao
                                                ON item.id = relacao.id_slide

                                                INNER JOIN ".AppConfig::$PrefixoDB."paginas as pagina
                                                ON relacao.id_pagina = pagina.id AND pagina.url = '".$Controle."'

                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado    is null
                                                    AND item.data_inicio  <= '".date('Y-m-d H:i:s')."'
											        AND item.data_fim     >= '".date('Y-m-d H:i:s')."'

                                                ORDER BY item.ordem ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );


        if( isset($BannerPaginas) && $BannerPaginas ){
            for($num = 0; $num < count($BannerPaginas); $num++ ) {
                $item = $BannerPaginas[$num];

                if( isset($item->link) && $item->link ){
                    $this->tpl->atribuir('OpenTagA',             '<a href="'.$item->link.'" target="_blank"> ');
                    $this->tpl->atribuir('CloseTagA',            '</a>');

                } else {
                    $this->tpl->atribuir('OpenTagA',             '');
                    $this->tpl->atribuir('CloseTagA',            '');
                }

                $this->tpl->atribuir( 'imagem',		BASE_URL . $item->arquivo );
                $this->tpl->atribuir( 'legenda',	$item->nome );

                #$this->tpl->block('LISTA');

                $this->tpl->limpa('imagem');
                $this->tpl->limpa('legenda');

            }

            #$this->tpl->block('MOSTRAR');

        } else {
            #$this->tpl->block('OCULTAR');
        }


        return $this->tpl->salva();

    }

}

?>