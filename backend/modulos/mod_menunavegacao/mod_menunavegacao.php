<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_menunavegacao extends Modelo {


    private $_html_menu     = null;

    private $_selected      = '<span class="selected"></span>';
    private $_arrow         = '<span class="arrow"></span>';




	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

		// Pega o controle
		$Controle = '';
		if( isset($this->router[1]) ) {
			$Controle = strtolower(trim($this->router[1]));
		}

        $temp       = explode('-', $Controle);
        $t0         = isset($temp[0]) ? $temp[0] : '';
        $t1         = isset($temp[1]) ? $temp[1] : '';
        $t2         = isset($temp[2]) ? $temp[2] : '';

        $grupo      = Sessao::Get('grupo_id');
        $MenuPermitidos = $this->App->BancoDeDados->query("
                                                SELECT
                                                      GROUP_CONCAT( DISTINCT item.menu_id SEPARATOR ',')
                                                FROM
                                                    base_usuario_grupo_regra as item
                                                WHERE
                                                    item.grupo_id in('".$grupo."')
                                            ")->fetchColumn();
        $MenuPermitidos = !empty($MenuPermitidos) ? $MenuPermitidos : 0;


        $sql_menu = '';
        $sql_submenu = '';

        if( ACL::UsuarioAdmin() !== true ){
            $sql_menu       = " AND menu.id in(".$MenuPermitidos.") ";
            $sql_submenu    = " AND mn.id in(".$MenuPermitidos.") ";
        }


        $Controle1  = null;
        $Controle2  = null;
        $Controle3  = null;

        $Controle1  = $t0;
        $Controle2  = $t0 . '-' . $t1;
        $Controle3  = $t0 . '-' . $t1 . '-' . $t2;


        $Menu = $this->App->BancoDeDados->query("
                    SELECT
                     menu.id
                    ,menu.titulo
                    ,menu.url
                    ,menu.icone
                    ,(
                        SELECT COUNT(m.id) as total
                        FROM  base_menu as m
                        WHERE
                            m._status  = 'A'
                            AND (
                                   m.id_pai = menu.id AND m.url = '".$Controle1."'
                                OR m.id_pai = menu.id AND m.url = '".$Controle2."'
                                OR m.id_pai = menu.id AND m.url = '".$Controle3."'

                                OR m.id = menu.id AND m.url = '".$Controle1."'
                                OR m.id = menu.id AND m.url = '".$Controle2."'
                                OR m.id = menu.id AND m.url = '".$Controle3."'
                            )
                     ) as menu_ativo

                    ,(
                        SELECT GROUP_CONCAT(mn.titulo,'@',mn.url ORDER BY mn.ordem ASC SEPARATOR '|')
                        FROM  base_menu as mn
                        WHERE mn._status  = 'A' AND mn.id_pai = menu.id ".$sql_submenu."
                        ORDER BY mn.ordem ASC
                     ) as listagem_submenu

                    , ( IF( ( SELECT GROUP_CONCAT(mn.titulo,'@',mn.url SEPARATOR '|')
                            FROM  base_menu as mn
                            WHERE mn._status  = 'A' AND mn.id_pai = menu.id
                            ORDER BY mn.ordem ASC
                        ) != '', 'submenu', 'menu') ) as tipo

                    FROM  base_menu as menu
                    WHERE menu._status  = 'A' AND menu.id_pai IS NULL ".$sql_menu."
                    ORDER BY menu.ordem ASC
                    ")->fetchAll(PDO::FETCH_OBJ);



        for($i = 0; $i < count($Menu); $i++){
            $menu = (Object) $Menu[$i];

            $dados = array(
                 'titulo'        => $menu->titulo
                ,'url'           => $menu->url ? BASE . $menu->url : 'javascript:;'
                ,'ativo'         => $menu->menu_ativo > 0 ? true : false
                ,'submenu'       => $menu->listagem_submenu
                ,'last'          => $i == count($Menu) ? 'last' : ''
                ,'start'         => $i == 0 ? '' : ''
                ,'icone'         => $menu->icone ? $menu->icone : 'fa-file-text-o'
            );
            $dados = (Object) $dados;

            switch( $menu->tipo ){
                case "menu":    $this->monta_menu( $dados );   break;
                case "submenu": $this->monta_submenu( $dados );  break;
            }

        }


        $this->tpl->atribuir('menu',  $this->getMenu());

        //////////////////////////////////////////////////////////////////////
		return $this->tpl->salva();

	}








    private function getMenu(){
        return $this->_html_menu;
    }


    private function monta_menu( $dados ){
        $_a = $_s = '';

        if( $dados->ativo ){
            $_a = 'active';
            $_s = $this->_selected;
        }

        $retorno = '
            <li class="'.$_a.' '.$dados->start.' '.$dados->last.'">
                <a href="'.$dados->url.'">
                    <i class="fa '.$dados->icone.'"></i>
                    <span class="title">'.$dados->titulo.'</span> '.$_s.'
                </a>
            </li>
            ';

        $this->_html_menu .= $retorno;
    }

    private function monta_submenu( $dados ){

        if( $dados->ativo ){
            $_a = 'active';
            $_s = $this->_selected;
            $_i = 'fa-folder-open-o';

        } else {
            $_a = '';
            $_s = '';
            $_i = 'fa-folder-o';

        }

        $retorno = '
        <li class="has-sub '.$_a.' '.$dados->start.' '.$dados->last.'">
                <a href="javascript:;">
                    <i class="fa '.$_i.'"></i>
                    <span class="title">'.$dados->titulo.'</span>
                    <span class="arrow "></span>
                    '.$_s.'
                </a>
                <ul class="sub-menu">';

                    $_html_sub = '';

                    if( $dados->submenu ) {
                        $submenu = explode('|', $dados->submenu);
                        #print_r($submenu); die();


                        for( $s = 0; $s < count($submenu); $s++){
                            $arr = explode('@', $submenu[$s]);
                            $_html_sub .= '<li><a href="' . BASE . $arr[1] . '">' . $arr[0] . '</a></li>';
                        }

                    }

        $retorno .= $_html_sub;
        $retorno .= ' </ul></li>';

        $this->_html_menu .= $retorno;
    }





    private function monta_titulo(){
        $retorno = '
            <li class="heading">
                <h3 class="uppercase">Features</h3>
            </li>
        ';

        $this->_html_menu = $retorno;
    }

}

?>