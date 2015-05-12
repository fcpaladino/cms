<?php

class Paginacao {

	var $itens_por_pagina;
	var $itens_total;
	var $pagina_atual;
	var $numero_de_paginas;
	var $media_paginas;
	var $num_parametro;
	var $minimo;
	var $SQLLimite;
	var $retorno;
	var $padrao_itens_por_pagina;
	var $ipp_array;
	var $url_base;

	public function __construct(){

		$this->App		= Registro::getInstance();
		$this->Request	= $this->App->Request;

		$this->url_base	= '';

		$this->pagina_atual		= 1;
		$this->media_paginas	= 7;
		$this->ipp_array		= array(5,10,25,50,100);


		$this->itens_por_pagina	=  $this->padrao_itens_por_pagina;

	}


	function Paginar() {

		if( !isset($this->padrao_itens_por_pagina) ) $this->padrao_itens_por_pagina = 25;

		if( !is_numeric( $this->itens_por_pagina ) OR $this->itens_por_pagina <= 0 ) {
			$this->itens_por_pagina = $this->padrao_itens_por_pagina;
		}

		$this->numero_de_paginas = ceil($this->itens_total/$this->itens_por_pagina);

		$this->pagina_atual = $this->pagina_atual > 1 ? (int) $this->pagina_atual : 1 ; // must be numeric > 0
		$prev_page = $this->pagina_atual-1;
		$next_page = $this->pagina_atual+1;

		// Começa o html do numero de paginas
		$this->retorno = '<div id="pagination" class="col-lg-12 col-md-12"> <ul>';

		$this->retorno .= ($this->pagina_atual > 1 AND $this->itens_total >= 10) ? '<li class=""><a class="pagina" href="'.$this->url_base.$prev_page.'">&laquo;</a></li>' : '';

		if( $this->numero_de_paginas > 10 ) {

			$this->start_range	= $this->pagina_atual - floor($this->media_paginas/2);
			$this->end_range	= $this->pagina_atual + floor($this->media_paginas/2);

			if( $this->start_range <= 0 ) {

				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;

			}

			if( $this->end_range > $this->numero_de_paginas ) {

				$this->start_range	-= $this->end_range - $this->numero_de_paginas;
				$this->end_range	 = $this->numero_de_paginas;

			}

			$this->range = range($this->start_range, $this->end_range);

			for( $i=1; $i<=$this->numero_de_paginas; $i++ ) {

				if($this->range[0] > 2 AND $i == $this->range[0]) $this->retorno .= "<li><span>...</span></li>";
				// loop through all pages. if first, last, or in range, display
				if( $i==1 Or $i==$this->numero_de_paginas OR in_array($i,$this->range) ) {

					$this->retorno .= ( $i == $this->pagina_atual ) ? '<li class=" active"><a class="pagina" title="Ir para página '.$i.' de '.$this->numero_de_paginas.'" href="javascript:void(0);">'.$i.'</a></li>' : '<li class=""><a class="pagina" title="Ir para página '.$i.' de '.$this->numero_de_paginas.'" href="'.$this->url_base.$i.'">'.$i.'</a></li>';

				}

				if($this->range[$this->media_paginas-1] < $this->numero_de_paginas-1 AND $i == $this->range[$this->media_paginas-1]) $this->retorno .= "<li class=''><span>...</span></li>";

			}

		}
		else
		{

			for( $i=1; $i <= $this->numero_de_paginas; $i++ ) {

				$this->retorno .= ($i == $this->pagina_atual) ? '<li class=" active"><a class="pagina" title="Ir para página '.$i.' de '.$this->numero_de_paginas.'" href="javascript:void(0);">'.$i.'</a></li>' : '<li class=""><a class="pagina" title="Ir para página '.$i.' de '.$this->numero_de_paginas.'" href="'.$this->url_base.$i.'">'.$i.'</a></li>';

			}

		}

		$this->retorno .= (($this->pagina_atual < $this->numero_de_paginas AND $this->itens_total >= 10) AND $this->pagina_atual > 0) ? '<li class=""><a class="pagina" href="'.$this->url_base.$next_page.'">&raquo;</a></li>' : '';

		$this->retorno .= '</ul> </div>';

		$this->minimo = ($this->pagina_atual <= 0) ? 0 : ($this->pagina_atual-1) * $this->itens_por_pagina;

		if($this->pagina_atual <= 0) $this->itens_por_pagina = 0;

		$this->SQLLimite = ' LIMIT '.$this->minimo.', '.$this->itens_por_pagina;

	}


	function mostrar_itens_por_pagina() {

		$items = '';

		if( !$this->Request->get('ipp') ) {
			$this->itens_por_pagina = $this->padrao_itens_por_pagina;
		}

		foreach( $this->ipp_array as $ipp_opt ) {

			$items .= ( $ipp_opt == $this->itens_por_pagina ) ? '<option selected value="'.$ipp_opt.'">'.$ipp_opt.'</option>' : '<option value="'.$ipp_opt.'">'.$ipp_opt.'</option>';

		}

		return '<div><span>Mostrar </span><select class="form-control" onchange="window.location=\''.$this->url_base.'1\">'.$items.'</select> por página</div>';

	}


	function mostrar_menu_paginas() {

		for( $i = 1; $i <= $this->numero_de_paginas; $i++ ) {

			$option .= ( $i == $this->pagina_atual ) ? '<option value="'.$i.'" selected>'.$i.'</option>' : '<option value="'.$i.'">'.$i.'</option>';

		}

		return '<span>Página: </span><select class="form-control" onchange="window.location=\''.$this->url_base.'\' + this[this.selectedIndex].value">'.$option.'</select>';

	}


	function mostrar_paginas() {

		return $this->retorno;

	}


}