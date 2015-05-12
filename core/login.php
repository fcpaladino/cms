<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
class Login {

    static
    private $instance;

    static
    private $app;

    private $sistema;

    public function __construct(){

        self::$app = Registro::getInstance();

        $this->sistema  = new Sistema();

    }

    public static function Logar( $usuario, $senha, $redirecionar = BASE, $somente_grupo = false ) {

        $nomeusuario	= $usuario;
        $senha			= md5( $senha );

        // Se definiu que nesse login só pode logar o grupo informado
        $sqlGrupo = '';

        if( $somente_grupo ) {

            if( is_array($somente_grupo) ){
                $sqlGrupo = "grupo.id = '" . implode( "' OR grupo.id ='", $somente_grupo) . "'";
            } else {
                $sqlGrupo = " grupo.id = '".$somente_grupo."'  ";
            }

            $sqlGrupo = " AND ( " . $sqlGrupo . " ) ";

        } else {

            $sqlGrupo = "";
        }



        $Usuario = self::$app->BancoDeDados->query("
                                                SELECT
                                                     usuario.id
                                                    ,usuario.nome
                                                    ,usuario.usuario
                                                    ,usuario.avatar
                                                    ,usuario.email
                                                    ,grupo.id as grupo
                                                    ,GROUP_CONCAT( DISTINCT rel_grupo.grupo_id SEPARATOR ',') as lista_grupo

                                                FROM base_usuario as usuario


                                                INNER JOIN base_usuario_rel_grupo as rel_grupo
                                                ON usuario.id = rel_grupo.usuario_id

                                                INNER JOIN base_usuario_grupo as grupo
                                                ON  grupo.id        = rel_grupo.grupo_id
                                                AND grupo._status   = 'A'
                                                AND grupo._deletado is null
                                                ".$sqlGrupo."

                                                WHERE
                                                        usuario._status         = 'A'
                                                        AND usuario._deletado   is null
                                                        AND (usuario.usuario	= '".$nomeusuario."' OR	usuario.email = '".$nomeusuario."')
                                                        AND usuario.senha	    = '".$senha."'
                                                LIMIT 1
                                            ")->fetch( PDO::FETCH_OBJ );

        if( $Usuario ) {

            Sessao::Set('Logado',			1);
            Sessao::Set('usuarioId',		$Usuario->id);
            Sessao::Set('Usuario',			$Usuario->usuario);
            Sessao::Set('Nome',				$Usuario->nome);
            Sessao::Set('email',			$Usuario->email);
            Sessao::Set('grupo_id',			$Usuario->lista_grupo);
            Sessao::Set('Avatar',			$Usuario->avatar ? $Usuario->avatar : 'media/uploads/usuarios/avatar_padrao.png');


            $sql_inner_join = "";
            $sql_coluna     = "";
            if( !in_array(1, explode(',', $Usuario->lista_grupo)) ){
                $sql_inner_join = " INNER JOIN base_menu as menu
                                    ON  item.menu_id = menu.id
                                    ";

                $sql_coluna     = ", menu.url";
            }

            $PermissoesAcoes = self::$app->BancoDeDados->query("
                                                SELECT
                                                      DISTINCT item.regra_id, regra.chave, item.id, REPLACE(menu.url, '-', '') as url
                                                FROM
                                                    base_usuario_grupo_regra as item

                                                INNER JOIN base_usuario_regra as regra
                                                    ON  item.regra_id = regra.id
                                                    AND regra._status = 'A'

                                                INNER JOIN base_menu as menu
                                                ON item.menu_id = menu.id

                                                WHERE
                                                    item.grupo_id in('".$Usuario->lista_grupo."')
                                            ")->fetchAll( PDO::FETCH_OBJ );


            $regras = array();
            $permissao_array = array();
            for( $i = 0; $i < count($PermissoesAcoes); $i++){
                $item = $PermissoesAcoes[$i];

                $regras[] = $item->id;

                $arr = explode('_', $item->chave);
                if( isset($arr[0]) && isset($arr[1]) ){
                    $permissao_array[$item->url][$arr[0]] = 1;
                    $permissao_array[$item->url][$arr[1]] = 1;

                } else {
                    $permissao_array[$item->url][$item->chave] = 1;
                }
            }

            ksort($permissao_array);
            Sessao::Set('PERMISSOESACOES', $permissao_array);

            if( count($regras) == 0 ){
                $regras[] = 1;
            }

            $Paginas = self::$app->BancoDeDados->query("
                                                SELECT
                                                     DISTINCT menu.url
                                                FROM
                                                    base_usuario_grupo_regra as item

                                                INNER JOIN base_menu as menu
                                                    ON item.menu_id = menu.id

                                                WHERE
                                                    item.id in(".implode(',', $regras).")
                                            ")->fetchAll( PDO::FETCH_OBJ );



            $permissoes_paginas = array();
            foreach ($Paginas as $pagina) {
                $permissoes_paginas[AppConfig::$PrefixoPermSessao.(str_replace('-','', $pagina->url))] = 1;
            }

            Sessao::Set('permissoespaginas', $permissoes_paginas);

            // Da um update no usuario do banco
            self::$app->BancoDeDados->exec("UPDATE
												base_usuario
											SET
												ultimo_login = '".date('Y-m-d H:i:s')."'
											WHERE
												id = '".$Usuario->id."'
											LIMIT 1
										");

            // Da um update na sessao do banco
            self::$app->BancoDeDados->exec("UPDATE
												base_sessao
											SET
												 usuario		= '".$Usuario->usuario."'
												,nome			= '".$Usuario->nome."'
												,email			= '".$Usuario->email."'
												,usuario_id		= '".$Usuario->id."'
												,grupo_id		= '".$Usuario->grupo."'
											WHERE
												session_id		= '".self::$app->SESSION_ID."'
											LIMIT 1
										");


            Log::salvar(array(
                 'link'     => BASE . Registro::getInstance()->Request->get('url')
                ,'metodo'   =>'Logar'
                ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" logou no sistema.'
            ));


            if( $redirecionar == false ) {
                return true;

            } else if($redirecionar != ''){
                return self::$sistema->Redirecionar($redirecionar);
            }

        }

        if( $redirecionar == false ) {
            return false;

        } else if($redirecionar != ''){
            return self::$sistema->Redirecionar( BASE . 'login');
        }


    }

    public static function Sair() {

        Log::salvar(array(
        'link'      => BASE . Registro::getInstance()->Request->get('url')
        ,'metodo'   =>'Sair'
        ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" saio do sistema.'
        ));

        // Deleta a sessao do usuário do banco
        self::$app->BancoDeDados->exec("DELETE FROM base_sessao WHERE session_id = '".self::$app->SESSION_ID."'");

        Sessao::Destruir();

        return Sistema::Redirecionar( BASE . 'login' );

    }

    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new Login();
        }

        return self::$instance;
    }
}

?>