<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class plg_fancybox extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.fancybox.pack.js' );
		$this->addCSS( PLUGINS	. get_class($this) . '/css/jquery.fancybox.css' );

		// Defini as variaveis
		$thumbs = '';
		$buttons = '';
		$media = '';

		if ( isset($config->thumbs) AND $config->thumbs == '1' ){
			$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.fancybox-thumbs.js' );
			$this->addCSS( PLUGINS	. get_class($this) . '/css/jquery.fancybox-thumbs.css' );
			$thumbs = '
					,thumbs	: {
						width	: 100,
						height	: 70
					}
			';
		}

		if ( isset($config->buttons) AND $config->buttons == '1' ){
			$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.fancybox-buttons.js' );
			$this->addCSS( PLUGINS	. get_class($this) . '/css/jquery.fancybox-buttons.css' );
			$buttons = '
					,buttons	: {}
			';
		}

		if ( isset($config->media) AND $config->media == '1' ){
			$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.fancybox-media.js' );
			$media = '
					,media	: {}
			';
		}

		$this->addJS( PLUGINS	. get_class($this) . '/js/jquery.mousewheel-3.0.6.pack.js' );

		$this->addJQUERY("
			jQuery('.fancybox').fancybox({
				openEffect	: 'elastic',
				closeEffect	: 'elastic',
				prevEffect	: 'elastic',
				nextEffect	: 'elastic',
				helpers	: {
					title	: {
						type: 'outside'
					}
					".$thumbs."
					".$buttons."
					".$media."
				}
			});
		");

	}

}

?>