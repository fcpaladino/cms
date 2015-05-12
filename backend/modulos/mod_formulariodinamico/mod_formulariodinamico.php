<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_formulariodinamico extends Modelo {

	// Config Link
	private $_compt_url 	= null;
	private $_param_url 	= null;

	// Connfig formulario
	private $_action_form   = null;
	private $_tp_form 		= null;
	private $_campo_form 	= array();
	private $_tabela_form 	= null;

    private $titulo         = null;
    private $ajax           = null;

	// Geral
	private $_codigo		= null;
	private $_idioma		= 1;
	private $_tabela_db		= null;


	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

		// Config Link
		$this->_param_url          	= Registro::getInstance()->componenteUrlPai;
		$this->_compt_url      		= $this->App->componenteUrl;

        $this->titulo         		= $config->titulo;
        $this->ajax         		= $config->ajax;

		// Connfig formulario
		$this->_tp_form				= isset($config->tipo) ? $config->tipo        : 'cadastrar' ;
		$this->_action_form			= $this->_compt_url . $this->_tp_form . '-post' . $this->_param_url;

        $this->_campo_form			= isset($config->campos) ? $config->campos    : '' ;
        $this->_tabela_form			= isset($config->tabela) ? str_replace(array('base_', AppConfig::$PrefixoDB), '', $config->tabela)    : '' ;

		// Geral
		$this->_tabela_db			= $this->Sistema->Table($this->_tabela_form);
		$this->_codigo				= isset($config->codigo) ? $config->codigo : '';

		if( $this->_tp_form == 'editar' ){
			$this->tpl->atribuir('hiddenID', $this->_codigo);
		}

		$this->tpl->atribuir('btnAction',       $this->_action_form);
		$this->tpl->atribuir('btnCancel',       $this->_compt_url . $this->_param_url);

		$this->listacampoform();

		$this->tpl->atribuir('conteudo',  Form::Display());

		return $this->tpl->salva();

	}



	//////////////////////////////////////////////////////////////////////////////////
	// Pega o valor dos campos na tabela
	/////////////////////////////////////////////////////////////////////////////////
	private function getValues( ){

        $array_consulta = array();
        foreach ($this->_campo_form as $value) {
            if( !in_array($value, array('confirma_senha', 'confirma_campo')) ){
                $array_consulta[] = $value;
            }
        }


        if( !$this->_codigo ){ return ''; }

        $select_campo_consulta  = array();
        $campos_intervalos      = array();

        $x = 0;
        foreach ($array_consulta as $consulta) {
            if( strpos($consulta, '_to_') !== false ){
                $a = explode('_to_', $consulta);
                $select_campo_consulta[] = $a[0];
                $select_campo_consulta[] = $a[1];

                $campos_intervalos[$a[0]][] = $a[0];
                $campos_intervalos[$a[0]][] = $a[1];
            } else {
                $select_campo_consulta[] = $consulta;
            }
            $x++;
        }

        $_w = " id  = '" . $this->_codigo . "' ";
		$_sql 			= "SELECT ".implode( ", ", $select_campo_consulta)." FROM ".$this->_tabela_db." WHERE ".$_w;
		$_resultado  	= $this->App->BancoDeDados->query($_sql)->fetch(PDO::FETCH_OBJ);

        $retorno = array();
        foreach ($array_consulta as $consulta) {
            if( strpos($consulta, '_to_') !== false ){
                $a = explode('_to_', $consulta);
                $retorno[$consulta] = $_resultado->$a[0].'_to_'.$_resultado->$a[1];
            } else {
                $retorno[$consulta] = $_resultado->$consulta;
            }
        }
        $retorno = (Object) $retorno;

		return $retorno;
	}

	//////////////////////////////////////////////////////////////////////////////////
	// Pega o valor dos campos na tabela
	//////////////////////////////////////////////////////////////////////////////////
	private function getConfigCampos( ){
		$_r = '';

		$_resultado = $this->App->BancoDeDados->query("
													SELECT
													 adm.*
													FROM base_formulario as adm
													WHERE adm.tabela = '".$this->_tabela_form."'
													AND ( adm.campo = '" . implode( "' OR adm.campo ='", $this->_campo_form) . "' )
										")->fetchAll(PDO::FETCH_ASSOC); # PDO::FETCH_OBJ

		if( $_resultado ){
			$_r = $_resultado;
		}

        return $_r;
	}


	private function listaCampos(){
		$config_campos = $this->getConfigCampos();
		$lista_1  = array();

        if( !$config_campos ){ return false; }

		foreach ($config_campos as $k => $v) {
			$key = $v['campo'];
			foreach ($v as $a => $b) {
				$lista_1[$key][$a] = $b;
			}
		}

		$_cmp = $this->_campo_form;
		$lista = array();

		$cont = 0;
		foreach ($_cmp as $campo) {
			foreach ($lista_1[$campo] as $_kk => $_vv) {
				$lista[$cont][$_kk] = $_vv;
			}
			$lista[$cont] = (object) $lista[$cont];
			$cont++;
		}

		return $lista;
	}


	//////////////////////////////////////////////////////////////////////////////////
	// Função Lista campo form
	/////////////////////////////////////////////////////////////////////////////////
	private function listacampoform(){


        // Variaveis para carregar cada jquery e css
        $AtivaPluginDataHora        = false;
        $AtivaPluginEditor          = false;
        $AtivaPluginComboMultiple   = false;
        $AtivaPluginMaxLength       = false;
        $AtivaPluginValidation      = false;
        $AtivaPluginMascara         = false;
        $AtivaPluginFile            = false;
        $AtivaPluginTags            = false;


		//////////////////////////////////////////////////////////////////////////////////
		// Cria as variaveis
		$_campo_valor		= $this->getValues();
		$_config_form		= $this->listaCampos();

		if( $_config_form ) {

			Form::open($this->_action_form, 'post', array('class'=>'form-horizontal', 'id'=>'formPrincipal', 'data-titulo'=>$this->titulo), array('base'=>$this->_compt_url . $this->_param_url, 'tipoform'=>$this->_tp_form));

			if( $this->_tp_form == 'editar' ){
				Form::Input('hidden', $this->_codigo, '', '', array('name'=>'frm_id'));
			}

			foreach ($_config_form as $_base) {

				$_campo         	= isset($_base->campo)          	    ? $_base->campo         	: '';
				$_titulo        	= isset($_base->titulo)         	    ? $_base->titulo        	: '';
				$_descricao     	= isset($_base->descricao)      	    ? $_base->descricao     	: '';
				$_tipo          	= isset($_base->tipo)           	    ? $_base->tipo          	: '';
				$_query         	= isset($_base->query)          	    ? $_base->query         	: '';
				$_validacao     	= isset($_base->validacao)      	    ? $_base->validacao     	: '';
				$_class         	= isset($_base->class)          	    ? $_base->class         	: '';
				$_tamanho       	= isset($_base->tamanho)        	    ? $_base->tamanho       	: '';
				$_equalTo        	= isset($_base->equalto)			    ? $_base->equalto			: '';
				$_placeholder      	= isset($_base->placeholder)		    ? $_base->placeholder		: '';
				$_info            	= isset($_base->info)		            ? $_base->info		        : '';
				$_equalTo        	= isset($_base->equalto)			    ? $_base->equalto			: '';
                $_maxlenght       	= isset($_base->maxlength)			    ? $_base->maxlength 		: '';
				$_form_valor       	= isset($_base->valor)			        ? $_base->valor 			: '';
				$_mascara       	= isset($_base->mascara)		        ? $_base->mascara 			: '';


				$_tabela_filho      = isset($_base->tabelafilho)          ? $_base->tabelafilho         : '';
				$_tabela_relacao    = isset($_base->tabelarelacao)        ? $_base->tabelarelacao       : '';
				$_coluna_rel_pai    = isset($_base->coluna_relacao_pai)   ? $_base->coluna_relacao_pai  : '';
				$_coluna_rel_filho  = isset($_base->coluna_relacao_filho) ? $_base->coluna_relacao_filho: '';
				$_coluna_filho      = isset($_base->coluna_filho)         ? $_base->coluna_filho        : '';

                $_valor         	= isset($_campo_valor->$_campo) 	  ? $_campo_valor->$_campo	    : '';

				$_sfx_name_multi_f  = $_tipo == 'filemultiple' && $this->_tp_form != 'editar'  ? '[]' : '';
                $_sfx_name_multi_c  = $_tipo == 'combomultiple' ? '[]' : '';

                $_pfx_name      = 'frm_';
				$_sfx_name      = $_sfx_name_multi_c . $_sfx_name_multi_f;
                $_class_        = $_class . ' form-control ';


                // Cria os parametros para cada campo
                $_parametros = array();
                $_parametros['name']                = $_pfx_name . $_campo . $_sfx_name;
                $_parametros['tamanho']		        = $_tamanho > 8 && $_info ? 8 : $_tamanho;


                if ($_validacao == 1) { $_parametros['required'] = 'required'; $AtivaPluginValidation = true; }

                if ( !empty($_placeholder) ) { $_parametros['placeholder'] = $_placeholder; }
                if ( !empty($_info) ) { $_parametros['ajuda'] = $_info; }

                if( !empty($_maxlenght) ){
                    $_class_ .= ' maxlength-handler ';
                    $_parametros['maxlength'] = $_maxlenght;


                    $AtivaPluginMaxLength = true;
                }


                if( !empty($_mascara) ){
                    $_class_ .= ' mascara ';
                    $_parametros['data-mask'] = $_mascara;

                    $AtivaPluginMascara = true;
                }


                if( $_tipo == 'tags'){ $AtivaPluginTags = true; }

				switch ($_tipo) {

					case "combobox":
						$_parametros['class']  = $_class_ . ' select2_category ';

                        $_sql_min   = explode('from', strtolower($_query));
                        $_sql_field = explode(',', str_replace('select','', $_sql_min[0]) );

						$_options   = array();

                        if( !empty($_query)){
                            $_list      = $this->App->BancoDeDados->query($_query)->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($_list as $item) {
                                $chave = $item[trim($_sql_field[0])];
                                $texto = $item[trim($_sql_field[1])];

                                $_options[ $chave ] =  $texto ;
                            }
                        }

						Form::Select($_titulo, $_descricao, $_options, $_valor, $_parametros);


						break;

					case "combomultiple":
                        $AtivaPluginComboMultiple = true;
						$_parametros['class']       = 'multiSelect ' . $_class_;
						$_parametros['id']          = 'selectbasic_' . $_campo;
						$_parametros['multiple']    = 'multiple';

						// Valida as tabelas
						$_t_p = Sistema::Table($this->_tabela_form);
						$_t_f = Sistema::Table($_tabela_filho);
						$_t_r = Sistema::Table($_tabela_relacao);

						$_filtroItem = $this->_codigo ? " AND r." . $_coluna_rel_pai . " = " . $this->_codigo . " " : "";

						$sql1 = "SELECT id, " . $_coluna_filho . " FROM " . $_t_f . " WHERE _status = 'A' AND _deletado is null ";
						$sql2 = "
                        SELECT t2.id, t2." . $_coluna_filho . "
                        FROM " . $_t_p . " as t1
                        INNER JOIN " . $_t_r . " as r ON t1.id = r." . $_coluna_rel_pai . " " . $_filtroItem . "
                        INNER JOIN " . $_t_f . " as t2 ON r." . $_coluna_rel_filho . " = t2.id
                        ";

						$_array_1 = $this->App->BancoDeDados->query($sql1)->fetchAll(PDO::FETCH_OBJ);

						$selected = '';
						if ($this->_codigo) {
							$_array_2 = $this->App->BancoDeDados->query($sql2)->fetchAll(PDO::FETCH_OBJ);

							foreach ($_array_2 as $op) {
								$selected[] = $op->id;
							}

						} else {
							$selected = '';
						}

						$_options = null;
						foreach ($_array_1 as $option) {
							$_options[$option->id] = $option->$_coluna_filho;
						}

						Form::Select($_titulo, $_descricao, $_options,  $selected, $_parametros);
						break;

					case "editor":
                        $AtivaPluginEditor = true;
						$_parametros['class'] = 'editor ' . $_class_;

						Form::Textarea($_titulo, $_descricao, $_valor, $_parametros);
						break;

					case 'senha':
						$_parametros['class']   = $_class_;
                        $_parametros['equalTo'] = $_equalTo;

                        if( $this->_tp_form == 'editar' ){
                            $_valor = '';
                        }

						Form::Input('password', $_valor, $_titulo, $_descricao, $_parametros);
						break;

					case 'confirma_senha':
						$_parametros['class']   = $_class_;
                        $_parametros['equalTo'] = $_equalTo;

						Form::Input('password', $_valor, $_titulo, $_descricao, $_parametros);
						break;

					case "filemultiple":
                            $AtivaPluginFile = true;
                            $_parametros['class']   = str_replace('form-control', '', $_class_);

                            if( $this->_tp_form != 'editar' ) {
                                $_parametros['multiple'] = 'multiple';
                            }
                            Form::File( $_valor, $_titulo, $_descricao, $_parametros, $this->_tp_form);
                        break;

                    case "file":
                        $AtivaPluginFile = true;
                        $_parametros['class']   = str_replace('form-control', '', $_class_);
                        Form::File( $_valor, $_titulo, $_descricao, $_parametros, $this->_tp_form);
                        break;

					case "hidden":
						$_parametros['class'] = $_class_;
						Form::Input('hidden', ( $_form_valor? $_form_valor : $_valor), $_titulo, $_descricao, $_parametros);
						break;

                    case "data":
                    case "hora":
                    case "datahora":

                        $AtivaPluginDataHora = true;

                        $_v = '';

                        if( $this->_tp_form == 'editar' ){

                            if( $_tipo == 'data' ){
                                $_v = $this->Sistema->ConverterData($_valor);

                            } else if( $_tipo == 'datahora'){
                                $_v = $this->Sistema->ConverterDataHora($_valor, 0);

                            } else if( $_tipo == 'hora' ){
                                $_v = $_valor;
                            }

                        }

                        if( $_tipo == 'data' ){
                            $c = 'date';

                        } else if( $_tipo == 'datahora'){
                            $c = 'datetime';

                        } else if( $_tipo == 'hora' ){
                            $c = 'time';
                        }

                        Form::Input('text', $_v, $_titulo, $_descricao, $_parametros);
                        break;

                    case "intervalodata":
					case "intervalodatahora":
					case "intervalohora":

                        $AtivaPluginDataHora = true;

                        $_v = array('','');
                        $valor = explode('_to_', $_valor);

                        if( $this->_tp_form == 'editar' ){

                            if( $_tipo == 'intervalodata' ){
                                $_v[0] = $this->Sistema->ConverterData($valor[0]);
                                $_v[1] = $this->Sistema->ConverterData($valor[1]);

                            } else if( $_tipo == 'intervalodatahora'){
                                $_v[0] = $this->Sistema->ConverterDataHora($valor[0], 0);
                                $_v[1] = $this->Sistema->ConverterDataHora($valor[1], 0);

                            } else if( $_tipo == 'intervalohora' ){
                                $_v[0] = $valor[0];
                                $_v[1] = $valor[1];
                            }

                        }

                        if( $_tipo == 'intervalodata' ){
                            $c = 'date';

                        } else if( $_tipo == 'intervalodatahora'){
                            $c = 'datetime';

                        } else if( $_tipo == 'intervalohora' ){
                            $c = 'time';
                        }

                        $_parametros['class'] = $c.' '.$_class_;
						Form::IntervaloDatas( $_v, $_titulo, $_descricao, $_parametros );
						break;


                    case "label":
                        $_parametros['class']   = $_class_;
                        Form::InputLabel($_valor, $_titulo, $_descricao, $_parametros);
                        break;


					default:
						$_parametros['class']   = $_class_;
                        $_parametros['equalTo'] = $_equalTo;
						Form::Input('text', $_valor, $_titulo, $_descricao, $_parametros);
						break;


				}

			}

			Form::close();

            $this->Plugins->carrega('carregacomponentes', array(
                 'datahora'  => $AtivaPluginDataHora
                ,'editor'    => $AtivaPluginEditor
                ,'combomult' => $AtivaPluginComboMultiple
                ,'maxlength' => $AtivaPluginMaxLength
                ,'validation'=> $AtivaPluginValidation
                ,'mascara'   => $AtivaPluginMascara
                ,'file'      => $AtivaPluginFile
                ,'tags'      => $AtivaPluginTags
            ));
		}

	}

    # $AtivaPluginDataHora / $AtivaPluginEditor / $AtivaPluginComboMultiple / $AtivaPluginMaxLength / $AtivaPluginValidation

	/////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////


}

?>