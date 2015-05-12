<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class Controle{

	public $Modelo;
    public $Modulos;
    public $Plugins;
    public $Sistema;
    public $App;

    public $_listagemColunas 	= null;



    public function __construct(){

		$this->App		= Registro::getInstance();
		$this->Request	= $this->App->Request;

        $this->Modulos  = new Modulos();
        $this->Plugins  = new Plugins();
        $this->Sistema  = new Sistema();

        $this->router	= $this->Sistema->Url();



        if( isset($this->config->listagemColunas) ) {
            foreach ($this->config->listagemColunas as $c => $t) {
                if ( is_numeric($c) ) {
                    $this->_listagemColunas[] = $t;
                }else{
                    $this->_listagemColunas[] = $c;
                }
            }
        }

        
        // Defini o complemento da url
		$this->config->url_compl = (isset($this->config->url_compl)) ? $this->config->url_compl : '';


        // Verifica se existe o arquivo modelo
        if( file_exists(PATH_MODELO . DS . $this->App->nomeControle . '.php') ){
            $classModelo	= $this->App->nomeControle;
        } else {
            $classModelo	= 'paginas';
        }

        $this->classModelo	= $classModelo . 'Modelo';
        $this->Modelo		= new $this->classModelo($this->config);

	}


	/*
	 * Escapa aspas
	 */
	protected function _addslashes($value)
	{
	    if(is_array($value)) {
			return array_map(array($this,'_addslashes'), $value);
		} else {
			return addslashes($value);
		}
	}


    /**
     * Cadastrar
     */
    public function cadastrar( ) {

        $this->Modelo->cadastrar();
    }

    /**
     * Editar
     */
    public function editar( ) {

        // Pega o id do item
        $id = (int) $this->router[3];

        // Portugues
        $Item = $this->App->BancoDeDados->query("SELECT
													item.*
												FROM
													".$this->config->componenteTabela." as item
												WHERE
														item.id         = '".$id."'
													AND item._deletado  IS NULL
											")->fetch( PDO::FETCH_OBJ );

        if( !$Item ) {
            $this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }

        $this->Modelo->editar( $Item );

    }

    /**
     * Ver
     */
    public function ver( ) {

        // Pega o id do item
        $id = (int) $this->router[3];

        // Valida o item
        $Item = $this->App->BancoDeDados->query("SELECT
													item.*
												FROM
													".$this->config->componenteTabela." as item
												WHERE
														item.id = '".$id."'
													AND item._deletado IS NULL
											")->fetch( PDO::FETCH_OBJ );

        if( !$Item ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }

        $this->Modelo->ver( $Item );

    }


    /*
     * Valida o item pai
     */
    function item_pai($_cod, $_retorno){
        $_item_pai = Registro::getInstance()->BancoDeDados->query("SELECT item.*
												FROM
												  ".$this->config->componenteTabelaItemPai." as item
												WHERE
														item.id         = '".$_cod."'
													AND item._deletado  is null
											")->fetch( PDO::FETCH_OBJ );
        if( !$_item_pai ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrlItemPai, '', 'Item não encontrado.' );
        }

        return $_item_pai->$_retorno;
    }

    /*
	 * Index do controle
	 */
	public function index( ) {
		return $this->Modelo->index( );
	}

	/*
	 * Listagem dos itens em json, para a tabela inicial
	 */
	public function jsonlistagem( ) {

		// Saida como json
		header('Content-type: application/json');

		/* Coluna index p/ calcular todos os itens da consulta */
		$ColunaIndex = "item.id";


        $id_pai = isset($this->config->id_pai) ? " AND id_item = '" . $this->config->id_pai . "'" : "";

        $Where = "WHERE item._deletado IS NULL " . $id_pai;

		/*
		 * SQL query
		 * Pega os itens para mostrar
		 */
        foreach( $this->_listagemColunas as $Coluna ) {
            // Se nao for um campo de banco
            if( substr($Coluna, 0, 1) != "#" ) {
                $camposConsulta[] = $Coluna.' as `'.$Coluna.'`';
            }
        }

		$camposConsulta = str_replace(" , ", " ", implode(", ", $camposConsulta));

        $Query = "
        SELECT
            SQL_CALC_FOUND_ROWS ".$camposConsulta."
        FROM
            ".$this->config->listagemQueryFrom."

        ".$Where."

        ";

		$Itens = $this->App->BancoDeDados->query($Query)->fetchAll( PDO::FETCH_OBJ );

		/* Data set length after filtering */
		$Query = "
			SELECT FOUND_ROWS() as total
		";
		$ResultadoFiltroTotal = $this->App->BancoDeDados->query($Query)->fetch( PDO::FETCH_OBJ );

		/* Total data set length */
        $Query = "
			SELECT COUNT(".$ColunaIndex.") as total
			FROM ".$this->config->listagemQueryFrom."
			WHERE item._deletado IS NULL
		";
		$ResultadoTotal = $this->App->BancoDeDados->query($Query)->fetch( PDO::FETCH_OBJ );

		/*
		 * Output
		 */
		$resposta = array(
			"sEcho"					=> intval($this->Request->get('sEcho')),
			"iTotalRecords"			=> $ResultadoTotal->total,
			"iTotalDisplayRecords"	=> $ResultadoFiltroTotal->total,
			"aaData"				=> array()
		);

		// Se tem itens
		if( $Itens ) {

			foreach( $Itens as $Item ) {

				$Linha = array();

				for( $i=0 ; $i<count($this->_listagemColunas); $i++ ) {

					$Linha[] = $this->jsonlistagemLinha($Item, $i);

				} // for

				$resposta['aaData'][] = $Linha;

			} // foreach

		}

		// retorna a saida em json
		echo json_encode($resposta); exit;

	}


	/*
	 * Trata a linha da listagem dos itens em json, para a tabela inicial
	 */
	protected function jsonlistagemLinha($Item, $i) {


        $ItemID = $Item->{'item.id'};
        $coluna = $this->_listagemColunas[$i];
        $valor  = isset($Item->{$this->_listagemColunas[$i]}) ? $Item->{$this->_listagemColunas[$i]} : '';

        $arr_status         = array('A'=>'Ativo', 'I'=>'Inativo', 1=>'Sim', 0=>'Não', ' '=>'Não');
        $arr_status_class   = array('A'=>'label-success', 'I'=>'label-inverse', 1=>'label-success', 0=>'label-inverse', ' '=>'label-inverse');

        $Linha = null;

        switch($coluna){


            case "#checkbox":
                $Linha = '<input type="checkbox" class="checkboxes checkboxitens" value="'.$ItemID.'">';
                break;

            case "item._status":
            case "item.destaque":
                $Linha = '<span class="label '.$arr_status_class[$valor].'">'.$arr_status[$valor].'</span>';
                break;

            case "#acoes":
                $acoes = $this->Modulos->carrega('acoes', array('codigo'=>$ItemID) );

                if( !empty($acoes) ){
                    $Linha = $acoes;
                }
                break;

            case "item.data":
                $Linha = $this->Sistema->ConverterDataHora($valor, 0);
                break;

            case "item.arquivo":

                $imagem = 'http://www.placehold.it/100x100/f3f3f3/919191&text=Sem+imagem';
                $link   = 'http://www.placehold.it/100x100/f3f3f3/919191&text=Sem+imagem';

                if( $valor ){
                    $imagem = '../miniatura/'.  $valor .'&w=100&bg=ffffff';
                    $link   = '../'. $valor;
                }



                $Linha = '<a target="_blank" class="fancybox thumbnail" href="'. $link .'"><img src="'.$imagem.'" width="100"></a>';
                break;


            default:
                $Linha = $this->jsonlistagemLinhaAlt($Item, $i);
                break;
        }

		return $Linha;
	}


	/*
	 * Trata a linha da listagem dos itens em json que sobraram, assim em cada controle não é preciso substituir a funcao jsonlistagemLinha inteira
	 */
	protected function jsonlistagemLinhaAlt($Item, $i) {
		$Linha = html_entity_decode($Item->{$this->_listagemColunas[$i]});
		return $Linha;
	}


	/*
	 * Ativar selecionados
	 */
	public function ativar() {

		// Pega o id do item
		$ids = $this->Request->get('ids');

		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				$sql = "UPDATE ".$this->config->componenteTabela."
							SET
								_status = 'A', _id_usuario = ".Sessao::Get('usuarioId')."
							WHERE
									id = '".$id."'
								AND _deletado IS NULL
						";

				if( $this->App->BancoDeDados->exec($sql) !== false ) {
                    $total++;
				} else {
					$erroCount++;
				}

			}

            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Ativar'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" ativou o(s) registro(s).'
            ,'item'     => implode(', ', $ids)
            ));

			// Inicia a mensagem de retorno
			$mensagem = $total . ' itens ativados com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}

	/*
	 * Desativar selecionados
	 */
	public function desativar() {

		// Verifica se esta habilitado a ação
		//$this->acaoAtiva('desativar');

		// Pega o id do item
		$ids = $this->Request->get('ids');

		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				$sql = "UPDATE ".$this->config->componenteTabela."
							SET
								_status = 'I'
							WHERE
									id = '".$id."'
								AND _deletado IS NULL
						";

				if( $this->App->BancoDeDados->exec($sql) !== false ) {
					$total++;
				} else {
					$erroCount++;
				}

			}
            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Desativar'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" desativou o(s) registro(s).'
            ,'item'     => implode(', ', $ids)
            ));
			// Inicia a mensagem de retorno
			$mensagem = $total . ' itens desativados com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}

	/*
	 * Excluir selecionados
	 */
	public function excluir() {

		// Verifica se esta habilitado a ação
		#$this->acaoAtiva('excluir');

		// Pega o id do item
		$ids = $this->Request->get('ids');


		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				$sql = "UPDATE ".$this->config->componenteTabela."
							SET
								_deletado = NOW()
							WHERE
									id = '".$id."'
								AND _deletado IS NULL
						";

				if( $this->App->BancoDeDados->exec($sql) !== false ) {
					$total++;
				} else {
					$erroCount++;
				}

			}
            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Excluir'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" excluiu o(s) registro(s).'
            ,'item'     => implode(', ', $ids)
            ));
			// Inicia a mensagem de retorno
			$mensagem = $total . ' itens excluídos com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}

	/*
	 * Com Destaque selecionados
	 */
	public function comdestaque() {

		// Pega o id do item
		$ids = $this->Request->get('ids');

		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				$sql = "UPDATE ".$this->config->componenteTabela."
							SET
								destaque = '1'
								, _id_usuario = ".Sessao::Get('usuarioId')."
							WHERE
									id = '".$id."'
								AND _deletado IS NULL
						";

				if( $this->App->BancoDeDados->exec($sql) !== false ) {
					$total++;
				} else {
					$erroCount++;
				}

			}

            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Comdestaque'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" destacou o(s) registro(s).'
            ,'item'     => implode(', ', $ids)
            ));
			// Inicia a mensagem de retorno
			$mensagem = $total . ' itens destacados com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}

	/*
	 * Sem Destaque selecionados
	 */
	public function semdestaque() {

		// Pega o id do item
		$ids = $this->Request->get('ids');

		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				$sql = "UPDATE ".$this->config->componenteTabela."
							SET
								destaque = '0'
								,_id_usuario = ".Sessao::Get('usuarioId')."
							WHERE
									id = '".$id."'
								AND _deletado IS NULL
						";

				if( $this->App->BancoDeDados->exec($sql) !== false ) {
					$total++;
				} else {
					$erroCount++;
				}

			}
            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Semdestaque'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" removeu destaque do(s) registro(s).'
            ,'item'     => implode(', ', $ids)
            ));
			// Inicia a mensagem de retorno
			$mensagem =  $total . ' itens removido destaque com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}

	/*
	 * Duplicar selecionados
	 */
	public function duplicar() {

		// Pega o id do item
		$ids = $this->Request->get('ids');

		// Se veio o array dos ids
		if(is_array($ids)) {

			// Erros
			$erroCount	= 0;

			// Total afetados
			$total = 0;

			// Faz o loop nos ids fazendo a acao
			foreach( $ids as $id ) {

				// ID precisa ser inteiro
				$id = (int) $id;

				// Busca o item no banco
				$Item = $this->App->BancoDeDados->query("SELECT
																*
															FROM
																".$this->config->componenteTabela."
															WHERE
																	id = '".$id."'
																AND _deletado IS NULL
															LIMIT 1
														")->fetch( PDO::FETCH_OBJ );
				#echo '<pre>'; print_r($Item); echo '</pre>'; exit;

				// Se o item existe
				if( $Item ) {

					$QueryColunas = array();
					$QueryValores = array();
					$num = 1;

					// Faz o foreach para pegar separado a coluna e seu valor
					foreach( $Item as $coluna => $valor ) {

						// Pula a primeira coluna que normalmente é o autoincremente
                        if( $num != 1 ) {
                            if($coluna != '_deletado'){
                                switch($coluna){
                                    case "_criado":
                                    case "_modificado":
                                        $QueryColunas[] = $coluna;
                                        $QueryValores[] = "NOW()";
                                        break;

                                    case "_id_usuario":
                                        $QueryColunas[] = $coluna;
                                        $QueryValores[] = "'".Sessao::Get('usuarioId')."'";
                                        break;

                                    default:
                                        $QueryColunas[] = $coluna;
                                        $QueryValores[] = "'".$this->_addslashes($valor)."'";
                                        break;
                                }
                            }
                        }

						$num++;

					}

					$sql = "INSERT INTO ".$this->config->componenteTabela."
								(".implode(",", $QueryColunas). "
							) VALUES (
								".implode(",", $QueryValores)."
							)";
					#echo $sql;

					if( $this->App->BancoDeDados->exec($sql) !== false ) {
						$total++;
					} else {
						$erroCount++;
					}

				} // se o item existe

			} // foreach

			// Inicia a mensagem de retorno
			$mensagem = $total . ' itens duplicados com sucesso.';

			// Se teve erros
			if( $erroCount > 0 ) {
				$mensagem .= ' Porem houve algum erro.';
			}

			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', '', $mensagem);

		} else {
			$this->Sistema->Redirecionar($this->config->componenteUrl.$this->config->url_compl, '', 'Você precisa selecionar ao menos 1 item.');
		}

	}




    /************************************************
     * Cadastrar Post
     ***********************************************/
    public function cadastrarpost( ) {

        ////////////////////////////////////////////////////
        // Pega as configurações de cada campo do formulario
        $base_formulario = $this->App->BancoDeDados->query("
													SELECT
                                                    adm.campo
                                                    ,adm.tipo
                                                    ,adm.formato
                                                    ,adm.crop
                                                    ,adm.tabela
                                                    ,adm.tabelaRelacao
                                                    ,adm.coluna_relacao_pai
                                                    ,adm.coluna_relacao_filho
                                                    ,adm.coluna_filho
                                                    ,adm.tabelafilho
													FROM base_formulario as adm
													WHERE adm.tabela = '".str_replace( array('base_', AppConfig::$PrefixoDB), '', $this->config->componenteTabela)."'
													AND ( adm.campo = '" . implode( "' OR adm.campo ='", $this->config->campos) . "' )
										")->fetchAll(PDO::FETCH_OBJ);

        $ArquivosFiles    = null;

        $ArrayComboMulti  = null;
        $ArrayMultiAtiva  = false;

        $ArrayFilesMulti  = null;
        $FilesMultiAtiva  = false;

        $num = 1;


        $_colunas = array();
        $_valores = array();

        foreach($base_formulario as $_base){
            $_campo         = 'frm_' . $_base->campo;
            $_tipo          = $_base->tipo;
            $_crop          = $_base->crop;


            switch($_tipo){

                case "combomultiple":
                    $ArrayComboMulti[$num]['tabela']            = $this->config->componenteTabela;
                    $ArrayComboMulti[$num]['tabela_filho']      = Sistema::Table($_base->tabelafilho);
                    $ArrayComboMulti[$num]['tabela_relacao']    = Sistema::Table($_base->tabelaRelacao);
                    $ArrayComboMulti[$num]['col_rel_pai']       = $_base->coluna_relacao_pai;
                    $ArrayComboMulti[$num]['col_rel_filho']     = $_base->coluna_relacao_filho;
                    $ArrayComboMulti[$num]['col_filho']         = $_base->coluna_filho;
                    $ArrayComboMulti[$num]['valor']             = $this->App->Request->post($_campo);
                    $ArrayMultiAtiva = true;
                    break;

                case "editor":
                    $_colunas[]     = $_base->campo;
                    $_valores[]     = "'" . htmlentities($this->App->Request->getHTML('post', $_campo), ENT_QUOTES, 'UTF-8') . "'";
                    break;

                case "datahora":
                    $_colunas[]     = $_base->campo;
                    $_valores[]     = "'" . $this->Sistema->ConverterDataHora($this->App->Request->post($_campo), 0) . "'";
                    break;

                case "data":
                    $_colunas[]     = $_base->campo;
                    $_valores[]     = "'" . $this->Sistema->ConverterData($this->App->Request->post($_campo)) . "'";
                    break;

                case "intervalodata":
                    $col = explode('_to_', $_base->campo);

                    $_colunas[]     = $col[0];
                    $_valores[]     = "'" . $this->Sistema->ConverterData($this->App->Request->post('frm_' . $col[0])) . "'";

                    $_colunas[]     = $col[1];
                    $_valores[]     = "'" . $this->Sistema->ConverterData($this->App->Request->post($col[1])) . "'";

                    break;

                case "intervalodatahora":
                    $col = explode('_to_', $_base->campo);

                    $_colunas[]     = $col[0];
                    $_valores[]     = "'" . $this->Sistema->ConverterDataHora($this->App->Request->post('frm_' . $col[0]), 0) . "'";

                    $_colunas[]     = $col[1];
                    $_valores[]     = "'" . $this->Sistema->ConverterDataHora($this->App->Request->post($col[1]), 0) . "'";

                    break;

                case "intervalohora":
                    $col = explode('_to_', $_base->campo);

                    $_colunas[]     = $col[0];
                    $_valores[]     = "'" . $this->App->Request->post('frm_' . $col[0]) . "'";

                    $_colunas[]     = $col[1];
                    $_valores[]     = "'" . $this->App->Request->post($col[1]) . "'";

                    break;


                case "file":
                    $_colunas[]     = $_base->campo;

                        if ( $_FILES[$_campo]['tmp_name'] ) {

                            $pasta = isset($this->config->pastaImagens) ? $this->config->pastaImagens : $this->App->nomeControle;
                            $ArquivoFinal = $this->UploadImagem( $_FILES[$_campo], $pasta );

                            if( $_crop ){
                                $this->croparImagem( $ArquivoFinal, $_crop );
                            }

                            $_valores[] = "'" . $ArquivoFinal . "'";

                        } else {
                            $_valores[] = "''";

                        }

                    break;


                case "filemultiple":
                    $TotalUploads = count($_FILES[$_campo]['tmp_name']);

                    if( $_FILES[$_campo]['tmp_name'][0] == '' ){ break; }

                    for ($i = 0; $i <= ($TotalUploads - 1); $i++) { // -1 pq o array começa no 0 ( zero )
                        // Monta o arquivo
                        $ArrayFilesMulti[$i]['campo']           = $_base->campo;
                        $ArrayFilesMulti[$i]['crop']            = $_crop;

                        $ArrayFilesMulti[$i]['tabela']          = $this->config->componenteTabela;
                        $ArrayFilesMulti[$i]['tabela_filho']    = Sistema::Table($_base->tabelafilho);
                        $ArrayFilesMulti[$i]['col_filho']       = $_base->coluna_filho;

                        $ArrayFilesMulti[$i]['name']            = $_FILES[$_campo]['name'][$i];
                        $ArrayFilesMulti[$i]['type']            = $_FILES[$_campo]['type'][$i];
                        $ArrayFilesMulti[$i]['tmp_name']        = $_FILES[$_campo]['tmp_name'][$i];
                        $ArrayFilesMulti[$i]['error']           = $_FILES[$_campo]['error'][$i];
                        $ArrayFilesMulti[$i]['size']            = $_FILES[$_campo]['size'][$i];
                    }

                    $FilesMultiAtiva = true;

                    break;


                case "senha":
                    if( $this->App->Request->post($_campo) != '' ) {
                        $_colunas[] = $_base->campo;
                        $_valores[] = "'" . md5($this->App->Request->post($_campo)) . "'";
                    }
                    break;

                case "confirma_senha":
                case "confirma_campo":
                    break;

                default:
                    $_colunas[]     = $_base->campo;
                    $_valores[]     = "'" . $this->App->Request->post($_campo) . "'";
                    break;

            }

            $num++;
        }


		if( isset($this->config->idpai) && $this->config->idpai ){
			$_colunas[] = 'item_id';
			$_valores[] = $this->config->idpai;
		}


        $_colunas[] = '_criado';
        $_valores[] = 'NOW()';

        $_colunas[] = '_modificado';
        $_valores[] = 'NOW()';

        $_colunas[] = '_id_usuario';
        $_valores[] = Sessao::Get('usuarioId');

        $sql = "INSERT INTO ".$this->config->componenteTabela." (".implode(",", $_colunas). ") VALUES (".implode(",", $_valores).")";

        if( $FilesMultiAtiva ){

            $pasta = isset($this->config->pastaImagens) ? $this->config->pastaImagens : $this->App->nomeControle;
            $diretorio = PATH_UPLOADS . DS . $pasta;
            if(!is_dir($diretorio)){ mkdir( $diretorio, 0777, true ); }

            for( $i = 0; $i < count($ArrayFilesMulti); $i++ ){
                $crop_file      = $ArrayFilesMulti[$i]['crop'];
                $file           = $ArrayFilesMulti[$i];

                unset($file['campo']);
                unset($file['tabela']);
                unset($file['tabela_filho']);
                unset($file['col_filho']);

                $Upload = new Upload($file, 'pt_BR');
                if ($Upload->uploaded) {
                    $Upload->file_safe_name = true;
                    $Upload->file_overwrite = false;
                    $Upload->file_auto_rename = true;
                    $Upload->Process( $diretorio );
                    if ($Upload->processed) {

                        $arquivo = "media/uploads/". $pasta ."/". $Upload->file_dst_name;
                        $sqlFile = "INSERT INTO ".$this->config->componenteTabela." (".implode(",", $_colunas). ", arquivo) VALUES (".implode(",", $_valores).", '".$arquivo."' )";

                        if( $crop_file ){ $this->croparImagem( $arquivo, $crop_file ); }

                        $InsertSQL = $this->App->BancoDeDados->exec($sqlFile);

                        $_lastInsertId = $this->App->BancoDeDados->lastInsertId();
                        if( $ArrayMultiAtiva && $_lastInsertId ) {
                            foreach ($ArrayComboMulti as $a) {
                                $_c_r_p = $a['col_rel_pai'];
                                $_c_r_f = $a['col_rel_filho'];
                                $_t_r   = $a['tabela_relacao'];
                                $_v     = $a['valor'];
                                foreach ($_v as $k) {
                                    $sql = "INSERT INTO ".$_t_r." (".$_c_r_p.", ".$_c_r_f.") VALUES (".$_lastInsertId.", ".$k.")";
                                    $this->App->BancoDeDados->exec($sql);
                                }
                            }
                        }


                        if( !$InsertSQL ){
                            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Algum erro ocorreu. Por favor tente denovo.' );
                        }

                        $Upload->Clean();

                    }
                }

            }

            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Cadastrar post'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" cadastrou um novo registro.'
            ));
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', '', 'Cadastrado com sucesso.' );

        } else {


            if ( $this->App->BancoDeDados->exec($sql) ) {
                $_lastInsertId = $this->App->BancoDeDados->lastInsertId();

                // Ação caso tenha campo multi opção no formulario
                if( $ArrayMultiAtiva && $_lastInsertId ) {
                    foreach ($ArrayComboMulti as $a) {
                        $_c_r_p = $a['col_rel_pai'];
                        $_c_r_f = $a['col_rel_filho'];
                        $_t_r   = $a['tabela_relacao'];
                        $_v     = $a['valor'];
                        foreach ($_v as $k) {
                            $sql = "INSERT INTO ".$_t_r." (".$_c_r_p.", ".$_c_r_f.") VALUES (".$_lastInsertId.", ".$k.")";
                            $this->App->BancoDeDados->exec($sql);
                        }
                    }

                }



                Log::salvar(array(
                    'link'      => BASE . Registro::getInstance()->Request->get('url')
                ,'metodo'   =>'Cadastrar post'
                ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" cadastrou um novo registro.'
                ));

                $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', '', 'Cadastrado com sucesso.' );
            } else {

                Log::salvar(array(
                    'link'      => BASE .Registro::getInstance()->Request->get('url')
                ,'metodo'   =>'Cadastrar post'
                ,'dados'    =>' Usúario "'.Sessao::Get('usuarioId').'" tentou cadastrar um novo registro.'
                ));
                $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '','Algum erro ocorreu. Por favor tente denovo.' );
            }

        }

    }


    /************************************************
     * Editar Post
     ***********************************************/
    public function editarpost( ) {

        #echo '<pre>'; print_r($_POST); echo '</pre>';
        #echo '<pre>'; print_r($_FILES); echo '</pre>'; die();

        $frm_id						= (int) $this->App->Request->post('frm_id');

        ///////////////////////////////////////////////////////////////////////////////////////
        // Valida o Registro
        ///////////////////////////////////////////////////////////////////////////////////////
        $Item = $this->App->BancoDeDados->query("SELECT item.*
												FROM ".$this->config->componenteTabela." as item
												WHERE
														item.id = '".$frm_id."'
													AND item._deletado IS NULL
											")->fetch( PDO::FETCH_OBJ );
        if( !$Item ) {
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', 'Item não encontrado.' );
        }

        ///////////////////////////////////////////////////////////////////////////////////////
        // Pega as configurações de cada campo do formulario
        ///////////////////////////////////////////////////////////////////////////////////////
        $base_formulario = $this->App->BancoDeDados->query("
													SELECT
                                                    adm.campo
                                                    ,adm.tipo
                                                    ,adm.formato
                                                    ,adm.crop
                                                    ,adm.tabela
                                                    ,adm.tabelarelacao
                                                    ,adm.coluna_relacao_pai
                                                    ,adm.coluna_relacao_filho
                                                    ,adm.coluna_filho
                                                    ,adm.tabelafilho
													FROM base_formulario as adm
													WHERE adm.tabela = '".str_replace( array('base_', AppConfig::$PrefixoDB), '', $this->config->componenteTabela)."'
													AND ( adm.campo = '" . implode( "' OR adm.campo ='", $this->config->campos) . "' )
										")->fetchAll(PDO::FETCH_OBJ);



        $ArquivosFiles    = null;

        $ArrayComboMulti  = null;
        $ArrayMultiAtiva  = false;

        $ArrayFilesMulti  = null;
        $FilesMultiAtiva  = false;

        $num = 1;

        $_col_val = array();

        foreach($base_formulario as $_base){
            $_campo         = 'frm_' . $_base->campo;
            $_tipo          = $_base->tipo;
            $_crop          = $_base->crop;

            #'campo','editor','combobox','combomultiple','file','filemultiple','hidden','senha','confirma_senha','confirma','email','datahora','data','hora','intervalodatahora','intervalodata','intervalohora'

            switch($_tipo){

                case "combomultiple":

                    $ArrayComboMulti[$num]['tabela']            = $this->config->componenteTabela;
                    $ArrayComboMulti[$num]['tabela_filho']      = Sistema::Table($_base->tabelafilho);
                    $ArrayComboMulti[$num]['tabela_relacao']    = Sistema::Table($_base->tabelarelacao);
                    $ArrayComboMulti[$num]['col_rel_pai']       = $_base->coluna_relacao_pai;
                    $ArrayComboMulti[$num]['col_rel_filho']     = $_base->coluna_relacao_filho;
                    $ArrayComboMulti[$num]['col_filho']         = $_base->coluna_filho;

                    $ArrayComboMulti[$num]['valor']             = $this->App->Request->post($_campo);
                    $ArrayMultiAtiva = true;
                    break;

                case "editor":
                    $_col_val[] = $_base->campo . " = '" . htmlentities($this->App->Request->getHTML('post', $_campo), ENT_QUOTES, 'UTF-8') . "'";
                    break;

                case "datahora":
                    $_col_val[] = $_base->campo . " = '" . $this->Sistema->ConverterDataHora($this->App->Request->post($_campo), 0) . "'";
                    break;

                case "data":
                    $_col_val[] = $_base->campo . " = '" . $this->Sistema->ConverterData($this->App->Request->post($_campo)) . "'";
                    break;

                case "intervalodata":
                    $col = explode('_to_', $_base->campo);

                    $_col_val[] = $col[0] . " = '" . $this->Sistema->ConverterData($this->App->Request->post('frm_' . $col[0])) . "'";
                    $_col_val[] = $col[1] . " = '" . $this->Sistema->ConverterData($this->App->Request->post($col[1])) . "'";

                    break;

                case "intervalodatahora":
                    $col = explode('_to_', $_base->campo);

                    $_col_val[] = $col[0] . " = '" . $this->Sistema->ConverterDataHora($this->App->Request->post('frm_' . $col[0]) ,0) . "'";
                    $_col_val[] = $col[1] . " = '" . $this->Sistema->ConverterDataHora($this->App->Request->post($col[1]) ,0) . "'";

                    break;

                case "intervalohora":
                    $col = explode('_to_', $_base->campo);

                    $_col_val[] = $col[0] . " = '" . $this->App->Request->post('frm_' . $col[0]) . "'";
                    $_col_val[] = $col[1] . " = '" . $this->App->Request->post($col[1]) . "'";

                    break;

                case "filemultiple":
                case "file":
                    $_remover_arquivo   = $this->App->Request->post('remover_arquivo_' . $_campo);
                    $_atualizar_arquivo = $this->App->Request->post('atualizar_arquivo_' . $_campo);

                    if($_remover_arquivo == 1 && !$_atualizar_arquivo == 1 ){
                        $_col_val[] = $_base->campo . "= '' ";
                        break;
                    }

                    if( $_atualizar_arquivo == 1 && $_FILES[$_campo]['tmp_name'] ) {

                        $pasta = isset($this->config->pastaImagens) ? $this->config->pastaImagens : $this->App->nomeControle;
                        $ArquivoFinal = '';
                        if ($_FILES[$_campo]['tmp_name']) {
                            $Upload = new Upload($_FILES[$_campo], 'pt_BR');
                            if ($Upload->uploaded) {
                                $Upload->file_overwrite = false;
                                $Upload->file_safe_name = true;
                                $Upload->file_auto_rename = true;
                                $Upload->mime_check = false;
                                $Upload->Process(PATH_UPLOADS . DS . $pasta);
                                if ($Upload->processed) {

                                    $ArquivoFinal = 'media/uploads/' . $pasta . '/' . $Upload->file_dst_name;

                                    if( $_crop ){ $this->croparImagem( $ArquivoFinal, $_crop ); }

                                    $Upload->Clean();
                                } else {
                                    $this->Sistema->Redirecionar($this->config->componenteUrl . '/cadastrar/' . $this->config->url_compl, '', $Upload->error);
                                }
                            }

                            $_col_val[] = $_base->campo . " = '" . $ArquivoFinal . "'";

                        } else {
                            $_col_val[] = $_base->campo . "=''";
                        }

                    }
                    break;

                case "senha":
                    if( $this->App->Request->post($_campo) != '' ) {
                        $_col_val[] = $_base->campo . " = '" . md5($this->App->Request->post($_campo)) . "'";
                    }
                    break;

                case "confirma_senha":
                case "confirma_campo":
                    break;

                default:
                    $_col_val[] = $_base->campo . " = '" . $this->App->Request->post($_campo) . "'";
                    break;

            }

            $num++;
        }

        if( isset($this->config->id_pai) && $this->config->id_pai ){
            $_col_val = 'item_id' . " = '" . $this->config->id_pai . "'";
        }

        $_col_val[] = "_modificado = NOW() ";
        $_col_val[] = "_id_usuario = ".Sessao::Get('usuarioId')." ";


        $sql = "UPDATE ".$this->config->componenteTabela." SET ".implode(",", $_col_val). " WHERE  id = '".$Item->id."' ";

        if ( $this->App->BancoDeDados->exec($sql) ) {


            $_lastInsertId = $frm_id;

            // Ação caso tenha campo multi opção no formulario
            if( $ArrayMultiAtiva && $_lastInsertId ) {

                foreach ($ArrayComboMulti as $a) {
                    $_c_r_p = $a['col_rel_pai'];
                    $_c_r_f = $a['col_rel_filho'];
                    $_t_r   = $a['tabela_relacao'];
                    $_v     = $a['valor'];

                    $this->App->BancoDeDados->exec("DELETE FROM ".$_t_r." WHERE ".$_c_r_p." = '".$_lastInsertId."' ");

                    foreach ($_v as $k) {
                        $sql = "INSERT INTO ".$_t_r." (".$_c_r_p.", ".$_c_r_f.") VALUES (".$_lastInsertId.", ".$k.")";
                        $this->App->BancoDeDados->exec($sql);
                    }
                }

            }


            // Ação caso tenha campo mutiplo arquivos no formulario
            if( $FilesMultiAtiva && $_lastInsertId ){

                $pasta = isset($this->config->pastaImagens) ? $this->config->pastaImagens : $this->App->nomeControle;
                $diretorio = PATH_UPLOADS . DS . $pasta;
                if(!is_dir($diretorio)){ mkdir( $diretorio, 0777, true ); }

                foreach ($ArrayFilesMulti as $file) {

                    $_table_file    = $file['tabela_filho'];
                    $_col_file      = $file['col_filho'];

                    // Novo upload
                    $Upload = new Upload($file, 'pt_BR');

                    // Se upload
                    if ($Upload->uploaded) {

                        // Salva a imagem na pasta temp
                        $Upload->file_safe_name = true;
                        $Upload->file_overwrite = false;
                        $Upload->file_auto_rename = true;
                        $Upload->Process( $diretorio );
                        if ($Upload->processed) {

                            $arquivo = "'media/uploads/".$pasta."/".$Upload->file_dst_name ."'";

                            $sql = "INSERT INTO ".$_table_file." ( id_item ,".$_col_file.") VALUES (".$_lastInsertId.", ".$arquivo.")";
                            $this->App->BancoDeDados->exec($sql);

                            $Upload->Clean();
                        }
                    }
                }


            }

            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Editar post'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" editou um registro.'
            ,'item'     => $frm_id
            ));
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', '', 'Alterado com sucesso.' );
        } else {

            Log::salvar(array(
                'link'      => BASE . Registro::getInstance()->Request->get('url')
            ,'metodo'   =>'Editar post'
            ,'dados'    =>' Usúario "'.Sessao::Get('Nome').'" tentou editar o registro.'
            ,'item'     => $frm_id
            ));
            $this->Sistema->Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '','Algum erro ocorreu. Por favor tente denovo.' );
        }

        ///////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////

    }








    /************************************************
     * Upload Imagens/Arquivos
     ***********************************************/
    private function UploadImagem( $file, $pasta ){

        $ArquivoFinal = '';

        $diretorio = PATH_UPLOADS . DS . $pasta;
        if(!is_dir($diretorio)){ mkdir( $diretorio, 0777, true ); }

        $Upload = new Upload( $file, 'pt_BR' );
        if ( $Upload->uploaded ) {
            $Upload->file_overwrite = false;
            $Upload->file_safe_name = true;
            $Upload->file_auto_rename = true;
            $Upload->mime_check = false;
            $Upload->Process( PATH_UPLOADS . DS . $pasta );
            if ( $Upload->processed ) {
                $ArquivoFinal = 'media/uploads/'.$pasta.'/'.$Upload->file_dst_name;
                $Upload->Clean();
            }
        }

        return $ArquivoFinal;
    }



    /************************************************
     * Cropar imagem
     ***********************************************/
    private function croparImagem( $imagem, $crop ){
        $dimensao = explode(',', $crop);

        $namefime = array_reverse( explode('/', $imagem) );
        $namefime = $namefime[0];

        foreach ($dimensao as $value) {
            $_d = explode('x', $value);

            if( is_array($_d) ) {

                $_w = (int)$_d[0];
                $_h = (int)$_d[1];

                $new_dir_w_h = PATH_UPLOADS . DS . $this->config->pastaImagens . DS . $value;

                if (!is_dir($new_dir_w_h)) {
                    mkdir($new_dir_w_h, 0777, true);
                }

                $this->App->Crop->carrega($imagem);
                $this->App->Crop->resizeImage($_w, $_h, 'crop');
                $this->App->Crop->saveImage('media/uploads/' . $this->config->pastaImagens . '/' . $value . '/' . $namefime, 100);
            }
        }

    }

}

?>
