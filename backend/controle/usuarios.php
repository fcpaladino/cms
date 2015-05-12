<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class usuariosControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Usúarios";
		$this->config->componenteUrl				= BASE . 'usuarios';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela				= Sistema::Table("usuario");
		$this->config->componenteTabelaGrupos		= Sistema::Table("usuario_grupo");
		$this->config->componenteTabelaSessao		= Sistema::Table("sessao");

		// Tabelas para consulta da listagem
		$this->config->listagemQueryFrom = $this->config->componenteTabela." as item
		    INNER JOIN ".$this->config->componenteTabela." as temp
		    ON  item.id = temp.id
		    AND temp.id not in(1,2)

		";

        $this->config->listagemColunas = array(
            '#checkbox'
            ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
            ,'item.nome'            => array( 'name'=>'Nome')
            ,'item.email'           => array( 'name'=>'E-mail')
            ,'item.usuario'         => array( 'name'=>'Usúario')
            ,'item.ultimo_login'    => array( 'visible'=>false)
            ,'item._status'			=> array( 'size'=>'21px')
            ,'#acoes' 				=> array('name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('nome', 'email', 'usuario', 'grupo_id', 'senha', 'avatar');

        // Ações disponiveis no componente
        Acoes::set( array(
            'cadastrar'		 	 => 1
            ,'editar'		   	 => 1
            ,'excluir'			 => 1
            ,'ativar'			 => 1
            ,'desativar'		 => 1
            ,'comdestaque'		 => 0
            ,'semdestaque'		 => 0
            ,'duplicar'			 => 0
            ,'ver'				 => 0
        ) );

		// Executa o __construct da classe extendida
		parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
	}



    protected function jsonlistagemLinhaAlt($Item, $i) {

        if( $this->_listagemColunas[$i] == "sessao.usuario_id" ) {

            // Defini nome e cor de status do item
            if( $Item->{$this->_listagemColunas[$i]} > 0 ) {
                $ItemConectado		= 'Ativo';
                $ItemConectadoCor	= 'label-success';
            } else {
                $ItemConectado		= 'Inativo';
                $ItemConectadoCor	= 'label-inverse';
            }

            $Linha = '<span class="label '.$ItemConectadoCor.'">'.$ItemConectado.'</span>';

        } elseif( $this->_listagemColunas[$i] == '#ultimologin' ) {

            $data   = $this->Sistema->ConverterData($this->Sistema->ConverterData($Item->{'item.ultimo_login'}));

            $minuto = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'MI');
            $horas  = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'H');
            $dias   = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'D');

            if( (int)$minuto <= 60 && (int)$minuto != 0 ){
                $msg_data = $minuto . 'min atrás';

            }elseif( (int)$horas <= 2 && (int)$horas != 0 ){
                $msg_data = $horas . 'h atrás';

            }else if( (int)$dias <= 28 && (int)$dias != 0 ) {
                $msg_data = $dias . ' dias atrás';

            } else {
                $msg_data = $this->Sistema->ConverterDataHora($Item->{'item.ultimo_login'});
            }

            $Linha = $msg_data;


        }elseif( $this->_listagemColunas[$i] != ' ' ) {

            $Linha = $Item->{$this->_listagemColunas[$i]};

        }

        return $Linha;

    }

}

?>