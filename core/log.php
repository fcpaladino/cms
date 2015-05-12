<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Log
{
	static
	private $app;

    static
	private $instance;

	public function __construct(){
        self::$app = Registro::getInstance();
	}


	public static function salvar( $dados ) {

        $dados = (Object) $dados;

        if( isset($dados->link) ){
            $col[] = 'link';
            $val[] = "'".$dados->link."'";
        }
        if( isset($dados->metodo) ){
            $col[] = 'metodo';
            $val[] = "'".$dados->metodo."'";
        }
        if( isset($dados->dados) ){
            $col[] = 'dados';
            $val[] = "'".$dados->dados."'";
        }
        if( isset($dados->item) ){
            $col[] = 'item_id';
            $val[] = "'".$dados->item."'";
        }

        $col[] = 'ip';
        $val[] = "'".self::$app->Request->server('REMOTE_ADDR')."'";

        $col[] = 'usuario';
        $val[] = "'".Sessao::Get('usuarioId')."'";

        $col[] = 'data';
        $val[] = "'".date('Y-m-d H:i:s')."'";


        $sql = "INSERT INTO base_log (".implode(",", $col). ") VALUES (".implode(",", $val).")";
        self::$app->BancoDeDados->exec($sql);

	}

    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new Log();
        }

        return self::$instance;
    }
}

?>