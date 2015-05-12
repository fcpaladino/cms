<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_mail extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        $titulo = isset($config->titulo) ? $config->titulo : '';
        $this->tpl->atribuir('titulo',             $titulo);
        
        if( isset($config->dados) && $config->dados ){


            foreach ($config->dados as $key => $value) {

                $this->tpl->atribuir('title',             $key);
                $this->tpl->atribuir('value',             $value);
                $this->tpl->block('LISTA');
                $this->tpl->limpa('title');
                $this->tpl->limpa('value');
            }


        }
        

		return $this->tpl->salva();

	}

}

?>