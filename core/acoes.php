<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

class Acoes {

    static private $instance;
    static private $app;

    public function __construct(){
        self::$app = Registro::getInstance();
    }


    public static function set( $array = null){

        if( !isset( self::$app->Acoes ) ) self::$app->Acoes = array();

        self::$app->Acoes = $array;

    }


    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new Acoes();
        }

        return self::$instance;
    }


}