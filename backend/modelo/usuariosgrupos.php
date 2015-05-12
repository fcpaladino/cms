<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class usuariosgruposModelo extends Modelo {

	public function index( ) {

		$this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, $this->config->componenteSubTitulo);

        $this->Plugins->carrega('fancybox');

        $this->tpl = $this->CarregarTemplate( 'index.tpl.php' );

        $this->tpl->atribuir('ModuloAcao',      $this->Modulos->carrega('acoesmassa',       array('componentePai'=>(isset($this->config->componenteUrlItemPai)?$this->config->componenteUrlItemPai:'') )));
        $this->tpl->atribuir('ModeloTabela',    $this->Modulos->carrega('tabelalistagem',   array('colunas'=>$this->config->listagemColunas) ));


        $this->tpl->Renderizar();
	}


	/**
	 * Cadastrar
	 */
	public function cadastrar() {

		// Carrega as dependencias js e css
		$this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Cadastrar');

        $this->tpl = $this->CarregarTemplate( 'cadastrar.tpl.php' );

        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform'));

        $this->Plugins->carrega('notificacao');


        $this->tpl->atribuir('componenteUrl',             $this->App->componenteUrl . '/cadastrar-post');



        $this->addJQUERY("
            /*$(document).on('change', '.group-checkbox', function(){

                var set     = $(this).attr('data-set');
                var checked = $(this).is(':checked');

                $(set).each(function () {
                    if (checked) {
                        $(this).parent().addClass('checked');
                    } else {
                        $(this).parent().removeClass('checked');
                    }
                });

            });

            $(document).on('change', '.select-menu-checkbox', function(){

                var set     = $(this).attr('data-set');
                var checked = $(this).is(':checked');


                $(set).each(function () {
                    if (checked) {
                        $(this).parent().addClass('checked');
                    } else {
                        $(this).parent().removeClass('checked');
                    }
                });


                if(checked){

                    if( $(set).is(':checked') ){

                    }

                }






            });*/
        ");



        $Regras = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.titulo
                                                      ,item.id
                                                FROM
                                                    ".$this->config->componenteTabelaRegra." as item
                                                WHERE
                                                    item._status          = 'A'
                                                ORDER BY
                                                    item.titulo ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );
        foreach ($Regras as $regra) {
            $this->tpl->atribuir('RegraTitulo',          $regra->titulo);
            $this->tpl->block('REGRA_TITULO');
            $this->tpl->limpa('RegraTitulo');
        }





        $Menu = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.titulo
                                                      ,item.url
                                                      ,item.id
                                                      ,(
                                                        SELECT GROUP_CONCAT(mn.titulo,'@',mn.url,'@',mn.id SEPARATOR '|')
                                                        FROM  base_menu as mn
                                                        WHERE mn._status  = 'A' AND mn.id_pai = item.id
                                                        ORDER BY mn.ordem ASC
                                                       ) as listagem_submenu

                                                       ,( IF( ( SELECT GROUP_CONCAT(mn.titulo,'@',mn.url SEPARATOR '|')
                                                            FROM  base_menu as mn
                                                            WHERE mn._status  = 'A' AND mn.id_pai = item.id
                                                            ORDER BY mn.ordem ASC
                                                        ) != '', 'submenu', 'menu') ) as tipo

                                                FROM
                                                    ".$this->config->componenteTabelaMenu." as item
                                                WHERE
                                                    item._status        = 'A'
                                                    AND item.id_pai         is null
                                                ORDER BY
                                                    item.titulo ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );

        foreach ($Menu as $menu) {


            if( $menu->tipo == 'menu' ){

                $this->tpl->atribuir('MenuTitulo',          $menu->titulo);
                $this->tpl->atribuir('MenuUrl',             $menu->url);
                $this->tpl->atribuir('MenuId',              $menu->id);

                foreach ($Regras as $regra) {
                    $this->tpl->atribuir('RegraId',              $regra->id);
                    $this->tpl->block('REGRA_LISTA');
                    $this->tpl->limpa('RegraId');
                }

                $this->tpl->block('LISTA');

                $this->tpl->limpa('MenuTitulo');
                $this->tpl->limpa('MenuUrl');
                $this->tpl->limpa('MenuId');



            } else if( $menu->tipo == 'submenu' ){

                $listagem = explode('|', $menu->listagem_submenu);

                foreach ($listagem as $sub) {

                    $item = explode('@', $sub);

                    $this->tpl->atribuir('MenuTitulo',          $menu->titulo . ' - ' . $item[0]);
                    $this->tpl->atribuir('MenuUrl',             $item[1]);
                    $this->tpl->atribuir('MenuId',              $item[2]);

                    foreach ($Regras as $regra) {
                        $this->tpl->atribuir('RegraId',              $regra->id);
                        $this->tpl->block('REGRA_LISTA');
                        $this->tpl->limpa('RegraId');
                    }

                    $this->tpl->block('LISTA');

                    $this->tpl->limpa('MenuTitulo');
                    $this->tpl->limpa('MenuUrl');
                    $this->tpl->limpa('MenuId');

                }




            }


        }



        $this->tpl->Renderizar();


	}


	/**
	 * Editar
	 */
	public function editar( $Item ) {

        // Carrega as dependencias js e css
        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Editar');

        $this->tpl = $this->CarregarTemplate( 'editar.tpl.php' );
        $this->tpl->atribuir('ButtonsForm',  $this->Modulos->carrega('buttonform'));

        $this->addJQUERY("
            /*$(document).on('change', '.group-checkbox', function(){

                var set     = $(this).attr('data-set');
                var checked = $(this).is(':checked');

                $(set).each(function () {
                    if (checked) {
                        $(this).parent().addClass('checked');
                        $(this).attr('checked', true);
                    } else {
                        $(this).parent().removeClass('checked');
                        $(this).attr('checked', false);
                    }
                });

            });*/
        ");


        $menus_ids  = array();
        $regras_ids = array();
        $Menus = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.menu_id
                                                      ,item.regra_id
                                                FROM
                                                    ".$this->config->componenteTabelaGrupoRegra." as item
                                                WHERE
                                                    item.grupo_id = '".$Item->id."'
                                            ")->fetchAll( PDO::FETCH_OBJ ); #FETCH_OBJ
        foreach ($Menus as $item) {
            $menus_ids[]                    = $item->menu_id;
            $regras_ids[$item->menu_id][]   = $item->regra_id;
        }


        $this->tpl->atribuir('componenteUrl',           $this->App->componenteUrl . 'editar-post');
        $this->tpl->atribuir('id',                      $Item->id);
        $this->tpl->atribuir('NomeGrupo',               $Item->titulo);

        $Regras = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.titulo
                                                      ,item.id
                                                FROM
                                                    ".$this->config->componenteTabelaRegra." as item
                                                WHERE
                                                    item._status          = 'A'
                                                ORDER BY
                                                    item.titulo ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );
        foreach ($Regras as $regra) {
            $this->tpl->atribuir('RegraTitulo',          $regra->titulo);
            $this->tpl->block('REGRA_TITULO');
            $this->tpl->limpa('RegraTitulo');
        }


        $Menu = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.titulo
                                                      ,item.url
                                                      ,item.id
                                                      ,(
                                                        SELECT GROUP_CONCAT(mn.titulo,'@',mn.url,'@',mn.id SEPARATOR '|')
                                                        FROM  base_menu as mn
                                                        WHERE mn._status  = 'A' AND mn.id_pai = item.id
                                                        ORDER BY mn.ordem ASC
                                                       ) as listagem_submenu

                                                       ,( IF( ( SELECT GROUP_CONCAT(mn.titulo,'@',mn.url SEPARATOR '|')
                                                            FROM  base_menu as mn
                                                            WHERE mn._status  = 'A' AND mn.id_pai = item.id
                                                            ORDER BY mn.ordem ASC
                                                        ) != '', 'submenu', 'menu') ) as tipo

                                                FROM
                                                    ".$this->config->componenteTabelaMenu." as item
                                                WHERE
                                                    item._status        = 'A'
                                                    AND item.id_pai         is null
                                                ORDER BY
                                                    item.titulo ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );

        /*$Menu = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.titulo
                                                      ,item.url
                                                      ,item.id
                                                FROM
                                                    ".$this->config->componenteTabelaMenu." as item
                                                WHERE
                                                    item._status          = 'A'
                                                ORDER BY
                                                    item.titulo ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );*/
        foreach ($Menu as $menu) {

            if( $menu->tipo == 'menu' ){

                $this->tpl->atribuir('MenuTitulo',          $menu->titulo);
                $this->tpl->atribuir('MenuUrl',             $menu->url);
                $this->tpl->atribuir('MenuId',              $menu->id);
                $this->tpl->atribuir('MenuSelected',        ( in_array($menu->id, $menus_ids) ? 'checked' : '' )  );

                $contChecked = 0;
                for( $i = 0; $i < count($Regras); $i++){
                    $regra = $Regras[$i];

                    $this->tpl->atribuir('RegraId',              $regra->id);

                    $checked = '';
                    if( isset($regras_ids[$menu->id]) ){
                        if( in_array($regra->id, $regras_ids[$menu->id]) ){
                            $checked = 'checked';
                            $contChecked++;
                        }
                    }

                    $this->tpl->atribuir('RegraSelected',        $checked );
                    $this->tpl->block('REGRA_LISTA');
                    $this->tpl->limpa('RegraId');
                    $this->tpl->limpa('RegraSelected');
                }

                if( $i == $contChecked ){
                    $this->tpl->atribuir('MarcarTodos',             'checked');
                } else {
                    $this->tpl->atribuir('MarcarTodos',             '');
                }


                $this->tpl->block('LISTA');

                $this->tpl->limpa('MenuTitulo');
                $this->tpl->limpa('MenuUrl');
                $this->tpl->limpa('MenuId');
                $this->tpl->limpa('MenuSelected');
                $this->tpl->limpa('MarcarTodos');



            } else if( $menu->tipo == 'submenu' ){

                $listagem = explode('|', $menu->listagem_submenu);

                foreach ($listagem as $sub) {

                    $item = explode('@', $sub);

                    $this->tpl->atribuir('MenuTitulo',          $menu->titulo . ' - ' . $item[0]);
                    $this->tpl->atribuir('MenuUrl',             $item[1]);
                    $this->tpl->atribuir('MenuId',              $item[2]);
                    $this->tpl->atribuir('MenuSelected',        ( in_array($item[2], $menus_ids) ? 'checked' : '' )  );

                    $contChecked = 0;
                    for( $i = 0; $i < count($Regras); $i++){
                        $regra = $Regras[$i];

                        $this->tpl->atribuir('RegraId',              $regra->id);

                        $checked = '';
                        if( isset($regras_ids[$item[2]]) ){
                            if( in_array($regra->id, $regras_ids[$item[2]]) ){
                                $checked = 'checked';
                                $contChecked++;
                            }
                        }

                        $this->tpl->atribuir('RegraSelected',        $checked );
                        $this->tpl->block('REGRA_LISTA');
                        $this->tpl->limpa('RegraId');
                        $this->tpl->limpa('RegraSelected');
                    }

                    if( $i == $contChecked ){
                        $this->tpl->atribuir('MarcarTodos',             'checked');
                    } else {
                        $this->tpl->atribuir('MarcarTodos',             '');
                    }

                    $this->tpl->block('LISTA');

                    $this->tpl->limpa('MenuTitulo');
                    $this->tpl->limpa('MenuUrl');
                    $this->tpl->limpa('MenuId');

                }

            }








/*
            $this->tpl->atribuir('MenuTitulo',          $menu->titulo);
            $this->tpl->atribuir('MenuUrl',             $menu->url);
            $this->tpl->atribuir('MenuId',              $menu->id);
            $this->tpl->atribuir('MenuSelected',        ( in_array($menu->id, $menus_ids) ? 'checked' : '' )  );

            $contChecked = 0;
            for( $i = 0; $i < count($Regras); $i++){
                $regra = $Regras[$i];

                $this->tpl->atribuir('RegraId',              $regra->id);

                $checked = '';
                if( isset($regras_ids[$menu->id]) ){
                    if( in_array($regra->id, $regras_ids[$menu->id]) ){
                        $checked = 'checked';
                        $contChecked++;
                    }
                }

                $this->tpl->atribuir('RegraSelected',        $checked );
                $this->tpl->block('REGRA_LISTA');
                $this->tpl->limpa('RegraId');
                $this->tpl->limpa('RegraSelected');
            }

            if( $i == $contChecked ){
                $this->tpl->atribuir('MarcarTodos',             'checked');
            } else {
                $this->tpl->atribuir('MarcarTodos',             '');
            }


            $this->tpl->block('LISTA');

            $this->tpl->limpa('MenuTitulo');
            $this->tpl->limpa('MenuUrl');
            $this->tpl->limpa('MenuId');
            $this->tpl->limpa('MenuSelected');
            $this->tpl->limpa('MarcarTodos');*/
        }



        $this->tpl->Renderizar();
	}


    public function ver( $Item ) {

        // Carrega as dependencias js e css
        $this->carregarDepencias();
        $this->addClasseBody('page-header-fixed page-quick-sidebar-over-content');

        Titulo::set($this->config->componenteTitulo, 'Ver');

        $this->tpl = $this->CarregarTemplate( 'ver.tpl.php' );

        $this->tpl->Renderizar();
    }

}

?>