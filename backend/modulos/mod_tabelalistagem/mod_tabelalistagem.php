<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_tabelalistagem extends Modelo {


	private $_colunas			= array();
	private $_coluna_nome		= array();
	private $_coluna_size		= array();
	private $_coluna_visible	= array();
	private $_coluna_sorting	= array();
	private $_col_tratadas		= array();

	private $_compUrl			= null;
	private $_paramUrl			= null;


	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );


		///////////////////////////////////////////////////////////
		// Inicialize as variaveis
		//////////////////////////////////////////////////////////
		$this->_colunas			  	= isset($config->colunas) 			? $config->colunas			: '';

        $acoes                      = $this->Modulos->carrega('acoes', array('codigo'=>'1') );
        if( empty($acoes) ){
            foreach ($this->_colunas as $key => $value) {
                if( $key == '#acoes' or $value == '#acoes' ){
                    unset($this->_colunas['#acoes']);
                    break;
                }
            }
        }



		$this->_compUrl           	= $this->App->componenteUrl . '/';
		$this->_paramUrl          	= $this->App->componenteUrlPai;

		$this->_col_tratadas		= $this->_trataArrayColunas();



		///////////////////////////////////////////////////////////
		// Monta os arrays
		//////////////////////////////////////////////////////////
		$this->_sortoutColumns();

		///////////////////////////////////////////////////////////
		// Monta o Jquery
		//////////////////////////////////////////////////////////
        $this->addJQUERY('
        var table = $("#tableListagem");

        table.dataTable({
            "ajax": "'.$this->_compUrl.'jsonlistagem'.$this->_paramUrl.'"
				,'.$this->_aoColumns().'
				'.$this->_aaSorting().'
        });

        var tableWrapper = jQuery("#tableListagem_wrapper");

        table.find(".group-checkable").change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                } else {
                    $(this).attr("checked", false);
                }
            });
            jQuery.uniform.update(set);
        });

        tableWrapper.find(".dataTables_length select").select2(); // initialize select2 dropdown


        ');


		for($i = 0; $i < count($this->_col_tratadas); $i++){

			$_col = $this->_col_tratadas[$i];

			if($i == 0) { continue; }

			$this->tpl->atribuir('css',     $this->getSize($_col) );
			$this->tpl->atribuir('titulo',  $this->getTitle($_col) );

			$this->tpl->block('BLOCO_TITULOS');

			$this->tpl->limpa('titulo');
			$this->tpl->limpa('css');


		}



		return $this->tpl->salva();

	}


	private function ConverterString($texto){

		$texto = array_reverse( explode('.', str_replace('#','',$texto)) );
		$texto = $texto[0] ? $texto[0] : $texto[1];

		$_temp          = explode('_',$texto);
		$_novo_texto    = null;
		for($x=0; $x < count($_temp); $x++){
			$_novo_texto .= ucfirst($_temp[$x]) . ' ';
		}

		return $_novo_texto;
	}

	private function _sortoutColumns(){

		foreach ($this->_colunas as $key => $value) {

			if ( isset($value) && is_array($value) ) {
				$this->_coluna_nome[$key] 		= isset($value['name']) 	? $value['name'] 	: self::ConverterString($key);
				$this->_coluna_size[$key] 		= isset($value['size'])		? $value['size'] 	: '';

				$this->_coluna_visible[$key]	= isset($value['visible'])  ? $value['visible'] : true;

				if( isset($value['order']) ) {
					$this->_coluna_sorting[$key] = $value['order'];
				}

			}else {
				$this->_coluna_nome[$value] 	= self::ConverterString($value);
				$this->_coluna_visible[$value]  = true;
			}

		}
	}

	private function _trataArrayColunas(){
		$_l = null;

		if( isset($this->_colunas) ) {
			foreach ($this->_colunas as $c => $t) {
				$tmp = '';
				if ( is_numeric($c) ) {
					$tmp = $t;
				}else{
					$tmp = $c;
				}
				$_l[] = $tmp;
			}
		}

		return $_l;
	}

	private function _aoColumns(){

		$_r = null;

		$_v = $this->_coluna_visible;
		$_c = $this->_col_tratadas;


		$_v_t = null;
		for($i = 0; $i < count($_c); $i++){
			$virgula = $i != 0 ? ',' : '';

			if( $_v )
				if( $_v[$_c[$i]] ){ $_v_t = ',"bVisible":true'; } else { $_v_t = ',"bVisible":false'; }
			else
				$_v_t = '';

			if( $i == 0 OR ( $i + 1 ) == count($_c) ){
				$_r .= $virgula . '{ "sClass": "", "bSortable": false '.$_v_t.' }' . "\n";
			}else {
				$_r .= $virgula . '{ "sClass": "" '.$_v_t.' }' . "\n";
			}
		}

		$_r = " 'aoColumns' : [ ".$_r." ] ";

		return $_r;
	}

	private function _aaSorting(){
		$_r = null;

		$_s = $this->_coluna_sorting;


		if( $_s ) {
			foreach ($_s as $k => $v) {
				$num = array_search($k, $this->_col_tratadas);

				$_aa[] = '[' . $num . ', "' . $v . '"]';
			}

			$_r = ',"aaSorting": ['.implode(',', $_aa).']';
		} else {
			$_r = '';
		}

		return $_r;
	}


	private function getTitle( $coluna ){
		return isset($this->_coluna_nome[$coluna]) ? $this->_coluna_nome[$coluna] : '';
	}

	private function getSize( $coluna ){
		return  isset($this->_coluna_size[$coluna]) ? ' style=" width:'. $this->_coluna_size[$coluna] .' !important;" ' : '';
	}
}

?>