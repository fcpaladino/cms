<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_cabecalho extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );


        $this->tpl->atribuir('NomeEmpresa',             $this->App->config->nome_empresa);
        $this->tpl->atribuir('Avatar',                  FRONDEND . Sessao::Get('Avatar'));
        $this->tpl->atribuir('NomeUsuario',             Sessao::Get('Usuario'));


        $grupo = explode(',', Sessao::Get('grupo_id'));

        if( in_array(1, $grupo) ){
            $this->tpl->block('ADMINISTRADOR');
        }



		return $this->tpl->salva();

	}

}

?>