<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_header extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

        $this->addCSS( CSS	. 'default.css' );

        $this->addJS( JS	. 'jquery-1.11.1.min.js' );
        $this->addJS( JS	. 'jquery-ui.min.js' );
        $this->addJS( JS	. 'imagesloaded.pkgd.min.js' );
        $this->addJS( JS	. 'isotope.pkgd.min.js' );
        $this->addJS( JS	. 'slick.js' );
        $this->addJS( JS	. 'inputPlaceholders.js' );
        $this->addJS( JS	. 'animsition.min.js' );
        $this->addJS( JS	. 'main.js' );


        $this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );

        $this->tpl->atribuir('MenuInstitucional',   $this->Modulos->carrega('menuinstitucional'));
        $this->tpl->atribuir('MenuLinks',           $this->Modulos->carrega('menulinks'));
        $this->tpl->atribuir('MenuNavegacao',       $this->Modulos->carrega('menunavegacao'));

		return $this->tpl->salva();

	}

}

?>