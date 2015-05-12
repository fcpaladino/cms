<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class User
{
	static
	private $app;

    static
	private $instance;

	public function __construct(){
        self::$app = Registro::getInstance();
	}



    public static function Nome(){
        return self::$app->Usuario->Nome;
    }

    public static function Id(){
        return self::$app->Usuario->usuarioId;
    }

    public static function Get(){
        return self::$app->Usuario->Usuario;
    }

    public static function Grupo(){
        return self::$app->Usuario->Grupo;
    }



    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new User();
        }

        return self::$instance;
    }
}

?>