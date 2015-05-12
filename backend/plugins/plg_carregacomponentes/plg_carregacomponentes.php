<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class plg_carregacomponentes extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {


        if( isset($config->datahora) && $config->datahora ){
            $this->addCSS( JS	. 'bootstrap-datepicker/css/datepicker3.css');
            $this->addCSS( JS	. 'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
            $this->addJS( JS	. 'bootstrap-datepicker/js/bootstrap-datepicker.js');
            $this->addJS( JS	. 'bootstrap-datetimepicker/js/bootstrap-datetimepicker.js');

            $this->addCSS( JS	. 'bootstrap-timepicker/css/bootstrap-timepicker.min.css');
            $this->addJS( JS	. 'bootstrap-timepicker/js/bootstrap-timepicker.js');

            $this->addJQUERY(" Plugins.datahora(); ");
        }

        if( isset($config->editor) && $config->editor ){
            $this->addJSRODAPE( CORE . 'ckeditor/ckeditor.js' );
            $this->addSCRIPTRODAPE('Plugins.editor();');
        }

        if( isset($config->combomult) && $config->combomult ){
            $this->addCSS( JS . 'multiselect/css/multi-select.css');
            $this->addJS( JS . 'multiselect/js/jquery.quicksearch.js');
            $this->addJSRODAPE( JS . 'multiselect/js/jquery.multi-select.js');
            $this->addJQUERY(" Plugins.multSelect(); ");
        }

        if( isset($config->maxlength) && $config->maxlength ){
            $this->addJS( JS . 'bootstrap-maxlength/bootstrap-maxlength.min.js');
            $this->addJQUERY(" Plugins.maxlength(); ");
        }

        if( isset($config->validation) && $config->validation ){
            $this->addJS( JS . 'jquery-validation/js/jquery.validate.js');
            $this->addJS( JS . 'form-validation.js');
            $this->addJQUERY(" Plugins.validation(); ");
        }

        if( isset($config->tags) && $config->tags ){
            $this->addCSS( JS   . 'jquery-tags-input/jquery.tagsinput.css');
            $this->addJS( JS    . 'jquery-tags-input/jquery.tagsinput.min.js');
            $this->addJQUERY(" Plugins.tags(); ");

        }

        if( isset($config->file) && $config->file ){
            $this->addCSS( JS   . 'bootstrap-fileinput/bootstrap-fileinput.css');
            $this->addJS( JS    . 'bootstrap-fileinput/bootstrap-fileinput.js');
            $this->addJQUERY(" ");

        }

        if( isset($config->mascara) && $config->mascara ){
            $this->addJS( JS    . 'jquery-inputmask/jquery.inputmask.bundle.min.js');
            $this->addJQUERY(" Plugins.inputmask(); ");
        }

        if( isset($config->senha) && $config->senha){
            $this->addJS( JS    . 'bootstrap-pwstrength/pwstrength-bootstrap.js');
            $this->addJQUERY(" Plugins.nivelsenha(); ");
        }



	}

}

?>