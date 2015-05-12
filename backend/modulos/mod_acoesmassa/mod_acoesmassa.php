<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_acoesmassa extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );


        $this->tpl->atribuir('componenteUrl',   Registro::getInstance()->componenteUrl . '/');
        $this->tpl->atribuir('url_compl',       Registro::getInstance()->componenteUrlPai);
        $this->tpl->atribuir('componentePai',   $config->componentePai);

        // Pega o controle
        $Controle = '';
        if( isset($this->router[1]) ) {
            $Controle = str_replace('-', '', strtolower(trim($this->router[1])));
        }

        $Acoes = isset($this->App->Acoes) ? $this->App->Acoes : ( isset(Sessao::Get('PERMISSOESACOES')[$Controle]) ? Sessao::Get('PERMISSOESACOES')[$Controle] : '' );

        if( ACL::UsuarioAdmin() ){

            $Itens = $this->App->BancoDeDados->query("
                                                    SELECT
                                                          item.chave
                                                    FROM
                                                        base_usuario_regra as item
                                                    WHERE
                                                        item._status          = 'A'
                                                ")->fetchAll( PDO::FETCH_OBJ );

            $Acoes = array();

            foreach ($Itens as $item) {
                $arr = explode('_', $item->chave);
                if( isset($arr[0]) && isset($arr[1]) ){
                    $Acoes[$arr[0]] = 1;
                    $Acoes[$arr[1]] = 1;

                } else {
                    $Acoes[$item->chave] = 1;
                }
            }

        }

        ksort($Acoes);

        $titles = array(
            'cadastrar'		 	 => "Cadastrar"
            ,'editar'		   	 => 'Editar'
            ,'excluir'			 => 'Deletar'
            ,'ativar'			 => 'Ativar'
            ,'desativar'		 => 'Desativar'
            ,'comdestaque'		 => 'Destacar'
            ,'semdestaque'		 => 'Remover destaque'
            ,'duplicar'			 => 'Duplicar'
            ,'ver'				 => 'Ver'
            ,'importar'			 => 'Importar'
            ,'exportar'			 => 'Exportar'
            ,'imprimir'          => 'Imprimir'
        );

        $icons = array(
            'cadastrar'		 	 => "plus"
            ,'editar'		   	 => 'pencil'
            ,'excluir'			 => 'trash-o'
            ,'ativar'			 => 'check'
            ,'desativar'		 => 'close'
            ,'comdestaque'		 => 'check-square-o'
            ,'semdestaque'		 => 'square-o'
            ,'duplicar'			 => 'files-o'
            ,'ver'				 => 'eye'
            ,'importar'			 => 'upload'
            ,'exportar'			 => 'download'
            ,'imprimir'			 => 'print'
        );

        $no_modal = array(
            'editar', 'ver', 'importar', 'exportar', 'imprimir'
        );

        $externo = array(
            'cadastrar', 'importar', 'exportar'
        );

        if( $this->App->Request->get('idpai') ){ $this->tpl->block('BOTAO_VOLTAR'); }



        if($Acoes){
            $AtivaBlocoAcoes = false;
            foreach ($Acoes as $key => $value) {

                $ativo  = 0;
                $icon   = '';
                $title  = '';
                $tipo   = '';
                $url    = '';
                $chave  = str_replace(AppConfig::$PrefixoPermSessao, '', $key);


                if( is_array($value) ){

                    $ativo  = isset($value['value'])    ? $value['value']   : 0;
                    $icon   = isset($value['icon'])     ? $value['icon']    : ( isset($icons[$chave])   ? $icons[$chave]  : '' );
                    $title  = isset($value['title'])    ? $value['title']   : ( isset($titles[$chave])  ? $titles[$chave] : '' );
                    $tipo   = isset($value['type'])     ? ( $value['type'] == 'ext' ? 'EXTERNO' : 'INTERNO' ) : 'INTERNO';
                    $url    = isset($value['url'])      ? $value['url']     : '';

                } else {

                    $ativo  = $value;
                    $icon   = isset($icons[$chave])   ? $icons[$chave]  : '';
                    $title  = isset($titles[$chave])  ? $titles[$chave] : '';
                    $tipo   = in_array($chave, $externo) ? 'EXTERNO' : 'INTERNO';
                    $url    = '';

                }

                if( $ativo == 0 ){ continue; }

                if( $tipo == 'INTERNO' ){
                    $AtivaBlocoAcoes = true;
                }


                $this->tpl->atribuir('class',            !empty($icon) ? 'icon-btn' : 'no-icon-btn' );
                $this->tpl->atribuir('acao',             $chave);
                $this->tpl->atribuir('value',            $value);
                $this->tpl->atribuir('icon',             !empty($icon) ? '<i class="fa fa-'.$icon.' text-muted"></i>' : '' );
                $this->tpl->atribuir('title',            $title);
                $this->tpl->atribuir('url',              $url);
                $this->tpl->atribuir('modal',            !in_array($chave, $no_modal) ? 'true' : 'false');

                $this->tpl->block($tipo);

                $this->tpl->limpa('class');
                $this->tpl->limpa('acao');
                $this->tpl->limpa('value');
                $this->tpl->limpa('icon');
                $this->tpl->limpa('title');
                $this->tpl->limpa('url');
                $this->tpl->limpa('modal');

            }

            if($AtivaBlocoAcoes){ $this->tpl->block('BLOCO_ACOES'); }
        }




		return $this->tpl->salva();
	}

}

?>