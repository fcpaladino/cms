<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Controle
{    

    public $App;
    public $Request;
    public $config;
    public $router;

    public $Modulos;
    public $Plugins;
    public $Sistema;

    public $Modelo;

    public function __construct(){

		$this->App = Registro::getInstance();
		$this->Request = $this->App->Request;

        $this->config   = new stdClass();
        $this->Modulos  = new Modulos();
        $this->Plugins  = new Plugins();
        $this->Sistema  = new Sistema();

        $this->router	= $this->Sistema->Url();


	}

}

?>