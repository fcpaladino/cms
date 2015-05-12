<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

class Titulo {

    static private $instance;
    static private $app;

    public function __construct(){
        self::$app = Registro::getInstance();
    }


    public static function set( $titulo = null, $subtitulo = null){

        if( !isset( self::$app->TituloPagina ) ) self::$app->TituloPagina = array();

        self::$app->TituloPagina = array(
            'titulo' => $titulo,
            'subtitulo' => $subtitulo
        );

    }


    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new Titulo();
        }

        return self::$instance;
    }


}