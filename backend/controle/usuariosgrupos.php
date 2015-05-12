<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class usuariosgruposControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Grupos";
		$this->config->componenteUrl				= BASE . 'usuarios-grupos';
		$this->config->componenteTitulo				= $this->config->componenteNome;
		$this->config->componenteSubTitulo			= 'Usúarios';
		$this->config->componenteTabela				= Sistema::Table("usuario_grupo");

        $this->config->componenteTabelaMenu    	    = Sistema::Table("menu");
        $this->config->componenteTabelaRegra    	= Sistema::Table("usuario_regra");
        $this->config->componenteTabelaGrupoRegra  	= Sistema::Table("usuario_grupo_regra");

		// Tabelas para consulta da listagem
		$this->config->listagemQueryFrom = $this->config->componenteTabela." as item
		INNER JOIN ".$this->config->componenteTabela." as temp
		ON  item.id  = temp.id
		AND temp.id != '1'
		";

        $this->config->listagemColunas = array(
            '#checkbox'
            ,'item.id' 				=> array( 'name'=>'#', 'order'=>'asc', 'size'=>'15px', 'visible' => false )
            ,'item.titulo'          => array( 'name'=>'Grupo')
            ,'item._status'			=> array( 'size'=>'21px')
            ,'#acoes' 				=> array('name'=>'', 'size'=>'20px')
        );

        $this->config->campos = array('titulo', 'regra', 'menu');

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
        ) );

		// Executa o __construct da classe extendida
		parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
	}



    protected function jsonlistagemLinhaAlt($Item, $i) {

        if( $this->_listagemColunas[$i] != ' ' ) {

            $Linha = $Item->{$this->_listagemColunas[$i]};

        }

        return $Linha;

    }





    public function cadastrarpost(){


        #echo '<pre>'; print_r($_POST); echo '</pre>'; die();

        $grupo = $this->App->Request->post('nome_grupo');
        $menu  = $this->App->Request->post('menu');
        $regra = $this->App->Request->post('regra');

        $CadastrarGRupo = $this->App->BancoDeDados->exec("INSERT INTO ".$this->config->componenteTabela." (titulo) VALUES ('".$grupo."') ");

        if( $CadastrarGRupo ){
            $grupoID = $this->App->BancoDeDados->lastInsertId();

            for( $i = 0; $i < count($menu); $i++){
                $menuId = $menu[$i];

                $Pai = $this->App->BancoDeDados->query("
                                                        SELECT
                                                              item.id_pai
                                                        FROM
                                                            base_menu as item
                                                        WHERE
                                                            item._status          = 'A'
                                                            AND item.id           = '".$menuId."'
                                                    ")->fetch( PDO::FETCH_OBJ );

                $ValidaRegistro = $this->App->BancoDeDados->query("SELECT id FROM ".$this->config->componenteTabelaGrupoRegra." WHERE grupo_id = '".$grupoID."' AND regra_id = '4' AND menu_id = '".$Pai->id_pai."'  ");

                if( $Pai->id_pai ){

                    $this->App->BancoDeDados->exec("INSERT INTO ".$this->config->componenteTabelaGrupoRegra."
                                            (grupo_id, regra_id, menu_id)
                                            VALUES
                                            ('".$grupoID."', '4', '".$Pai->id_pai."')
                                          ");

                }



                if( isset($regra[$menuId]) && count($regra[$menuId]) > 0 ){

                    for( $j = 0; $j < count($regra[$menuId]); $j++){
                        $this->App->BancoDeDados->exec("INSERT INTO ".$this->config->componenteTabelaGrupoRegra."
                                            (grupo_id, regra_id, menu_id)
                                            VALUES
                                            ('".$grupoID."', '".$regra[$menuId][$j]."', '".$menuId."')
                                          ");
                    }


                }

            }

            $this->Sistema->Redirecionar( $this->config->componenteUrl, '', '', 'Cadastrado com sucesso.' );
        } else {
            $this->Sistema->Redirecionar( $this->config->componenteUrl, '','Algum erro ocorreu. Por favor tente denovo.' );
        }

    }


    public function editarpost(){
        $id = $this->App->Request->post('id');

        $Item = $this->App->BancoDeDados->query("SELECT item.*
												FROM ".$this->config->componenteTabela." as item
												WHERE
														item.id = '".$id."'
													AND item._deletado IS NULL
											")->fetch( PDO::FETCH_OBJ );
        if( !$Item ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }

        $grupo = $this->App->Request->post('nome_grupo');
        $menu  = $this->App->Request->post('menu');
        $regra = $this->App->Request->post('regra');

        $this->App->BancoDeDados->exec("UPDATE ".$this->config->componenteTabela." SET titulo = '".$grupo."' WHERE id = '".$id."' ");

        $this->App->BancoDeDados->exec("DELETE FROM ".$this->config->componenteTabelaGrupoRegra." WHERE grupo_id = '".$id."' ");

        for( $i = 0; $i < count($menu); $i++){
            $menuId = $menu[$i];

            if( isset($regra[$menuId]) && count($regra[$menuId]) > 0 ){

                $Pai = $this->App->BancoDeDados->query("
                                                        SELECT
                                                              item.id_pai
                                                        FROM
                                                            base_menu as item
                                                        WHERE
                                                            item._status          = 'A'
                                                            AND item.id           = '".$menuId."'
                                                    ")->fetch( PDO::FETCH_OBJ );

                #echo '<pre>'; print_r($Pai); echo '</pre>'; die();

                if( $Pai->id_pai ){

                    $this->App->BancoDeDados->exec("INSERT INTO ".$this->config->componenteTabelaGrupoRegra."
                                            (grupo_id, regra_id, menu_id)
                                            VALUES
                                            ('".$id."', '4', '".$Pai->id_pai."')
                                          ");

                }

                for( $j = 0; $j < count($regra[$menuId]); $j++){
                    $sql = "INSERT INTO  ".$this->config->componenteTabelaGrupoRegra."
                                                    (grupo_id, regra_id, menu_id)
                                                    VALUES
                                                    ('".$id."', '".$regra[$menuId][$j]."', '".$menuId."')
                                                   ";

                    $Insert = $this->App->BancoDeDados->exec($sql);
                    if(!$Insert){
                        $this->Sistema->Redirecionar( $this->config->componenteUrl, '','Algum erro ocorreu. Por favor tente denovo.' );
                    }

                }

            }

        }

        $this->Sistema->Redirecionar( $this->config->componenteUrl, '', '', 'Alterado com sucesso.' );

    }




}

?>