<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_buttonform extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        $action = isset($config->action) ? $config->action : true;
        $voltar = isset($config->voltar) ? $config->voltar : true;


        if( $action ){
            $this->tpl->block('ACTION');
        }

        if( $voltar ){
            $this->tpl->block('VOLTAR');
        }



        if( isset($this->router[2]) && strtolower($this->router[2]) == 'editar' ){
            $this->tpl->atribuir('BtnAcao',             'Atualizar');
            $this->tpl->atribuir('IconAcao',            'fa-refresh');

        } else {
            $this->tpl->atribuir('BtnAcao',             'Salvar');
            $this->tpl->atribuir('IconAcao',            'fa-check');
        }

        $this->tpl->atribuir('componenteUrl',   Registro::getInstance()->componenteUrl);
        $this->tpl->atribuir('componenteUrlPai',Registro::getInstance()->componenteUrlPai);

		return $this->tpl->salva();

	}

}

?>