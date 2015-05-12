<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_iecompatibility extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

        $this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        if($this->App->config->ativa_compatibilidade == '1'){

            $this->addCSS( MODULOS . get_class($this) . '/css/default.css' );

            if($this->App->config->tipo_compatibilidade == '1'){
                $this->tpl->atribuir('FixoNaoFixo',       'class="fixed"');
            } else {
                $this->tpl->atribuir('FixoNaoFixo',       '');
            }

            $this->tpl->atribuir('url',     MODULOS . get_class($this) . '/img/' );

            $this->tpl->block('MOSTRAR');
        } else {
            $this->tpl->block('OCULTAR');
        }


        return $this->tpl->salva();

	}

}

?>