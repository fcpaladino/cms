<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Modelo
{
    public $Modulos;
    public $Plugins;
    public $Sistema;
    public $config;
	public $tpl;

	public function __construct($config = null){

		// Define algumas variaveis para acessar no controles
		$this->App		= Registro::getInstance();
		$this->Request	= $this->App->Request;

        $this->Modulos  = new Modulos();
        $this->Plugins  = new Plugins();
        $this->Sistema  = new Sistema();

        $this->router	= $this->Sistema->Url();

        // Recebe as configurações passadas pelo controle
		$this->config = $config;

	}

	/*
	 * Carrega os js e css padrao do modelo
	 */
	public function carregarDepencias(){


        // GLOBAL STYLES
        $this->addCSS('http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all');
        $this->addCSS( JS	. 'font-awesome/css/font-awesome.min.css');
        $this->addCSS( JS	. 'simple-line-icons/simple-line-icons.min.css');
        $this->addCSS( JS	. 'bootstrap/css/bootstrap.min.css');
        $this->addCSS( JS	. 'uniform/css/uniform.default.css');
        $this->addCSS( JS	. 'bootstrap-switch/css/bootstrap-switch.min.css');

        // SELECT
        $this->addCSS( JS	. 'select2/select2.css');

        // TABLE
        $this->addCSS( JS	. 'datatables/plugins/bootstrap/dataTables.bootstrap.css');

        // THEME STYLES
        $this->addCSS( CSS	. 'components.css');
        $this->addCSS( CSS	. 'plugins.css');
        $this->addCSS( CSS	. 'layout.css');
        $this->addCSS( CSS	. 'light.css');
        $this->addCSS( CSS	. 'custom.css');


        $this->addJS( JS	. 'jquery.min.js');
        $this->addJS( JS	. 'jquery-migrate.min.js');
        $this->addJS( JS	. 'jquery-ui/jquery-ui-1.10.3.custom.min.js');
        $this->addJS( JS	. 'jquery-slimscroll/jquery.slimscroll.min.js');
        $this->addJS( JS	. 'jquery.blockui.min.js');
        $this->addJS( JS	. 'jquery.cokie.min.js');
        $this->addJS( JS	. 'uniform/jquery.uniform.min.js');
        $this->addJS( JS	. 'select2/select2.min.js');
        $this->addJS( JS	. 'datatables/media/js/jquery.dataTables.min.js');
        $this->addJS( JS	. 'datatables/plugins/bootstrap/dataTables.bootstrap.js');

        $this->addJS( JS	. 'bootstrap-switch/js/bootstrap-switch.min.js');
        $this->addJS( JS	. 'bootstrap/js/bootstrap.min.js');
        $this->addJS( JS	. 'bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js');

        $this->addJS( JS	. 'metronic.js');
        $this->addJS( JS	. 'layout.js');
        $this->addJS( JS	. 'quick-sidebar.js');
        $this->addJS( JS	. 'demo.js');
        $this->addJS( JS	. 'table-managed.js');
        $this->addJS( JS	. 'form-samples.js');

        $this->addJS( JS    . 'bootbox/bootbox.min.js');
        $this->addJS( JS    . 'ui-alert-dialog-api.js');

        $this->addJS( JS    . 'datatable.js');


        $this->addJS( JS    . 'typeahead/handlebars.min.js');
        $this->addJS( JS    . 'typeahead/typeahead.bundle.min.js');
        $this->addJS( JS    . 'bootstrap-touchspin/bootstrap.touchspin.js');
        $this->addCSS( JS   . 'typeahead/typeahead.css');

        $this->addJS( JS    . 'plugins.js');
        $this->addJS( JS    . 'app.js');
        $this->addJQUERY(" App.init(); ");

		// Defini o nome e url do componente para uso em javascript
		$this->addSCRIPT('
			var componenteNome	= "'.( isset($this->config->componenteNome) ? $this->config->componenteNome : '' ).'";
			var componenteURL	= "'.( isset($this->config->componenteUrl) ? $this->config->componenteUrl : '').'";
			var url_compl		= "'.( isset($this->config->url_compl) ? $this->config->url_compl : '').'";
		');

	}

	/*
	 * Carregamento do template pelo modelo
	 */
	public function CarregarTemplate( $template ){

        if( file_exists(PATH_TEMPLATE . DS . $this->App->nomeControle . DS . $template) ){
            $arquivo_templates	= $this->App->nomeControle;
        } else {
            $arquivo_templates	= 'paginas';
        }

		$this->tpl = new Template( PATH_TEMPLATE . DS . $arquivo_templates . DS . $template );

		// Defini os nomes padroes dos arquivos
		$HTMLHeader	= 'HTMLHeader.tpl.php';
		$SiteTopo	= 'SiteTopo.tpl.php';
		$SiteRodape	= 'SiteRodape.tpl.php';
		$HTMLFooter	= 'HTMLFooter.tpl.php';

		// Inclui os arquivos caso exista os blocos
		if( $this->tpl->exists('HTMLHeader'))	$this->tpl->addArquivo('HTMLHeader',	PATH_TEMPLATE . DS . $HTMLHeader);
		if( $this->tpl->exists('SiteTopo'))		$this->tpl->addArquivo('SiteTopo',		PATH_TEMPLATE . DS . $SiteTopo);
		if( $this->tpl->exists('SiteRodape'))	$this->tpl->addArquivo('SiteRodape',	PATH_TEMPLATE . DS . $SiteRodape);
		if( $this->tpl->exists('HTMLFooter'))	$this->tpl->addArquivo('HTMLFooter',	PATH_TEMPLATE . DS . $HTMLFooter);

        if( ACL::estaLogado() ) {
            $this->tpl->atribuir('ModuloTituloPagina',          $this->Modulos->carrega('titulopagina'));
            $this->tpl->atribuir('ModuloBreadcrumb',            $this->Modulos->carrega('breadcrumb'));
            $this->tpl->atribuir('Mod_menunavegacao',			$this->Modulos->carrega('menunavegacao'));
            $this->tpl->atribuir('ModuloMensagemAviso',		    $this->Modulos->carrega('mensagensaviso'));
            $this->tpl->atribuir('ModuloCabecalho',    		    $this->Modulos->carrega('cabecalho'));
        }
        return $this->tpl;

	}

	/*
	 * Carregamento do template pelo modulo
	 */
	public function CarregarTemplateModulo( $nomeclasse, $template ){

		$this->tpl = new Template( PATH_MODULOS . DS . $nomeclasse . DS . 'tmpl' . DS . $template );

		return $this->tpl;

	}

	/*
	 * Carregamento do template pelo plugin
	 */
	public function CarregarTemplatePlugin( $nomeclasse, $template ){

		$this->tpl = new Template( PATH_PLUGINS . DS . $nomeclasse . DS . 'tmpl' . DS . $template );

		return $this->tpl;

	}

	public function addJS( $arquivo ){

		$this->App->addJS .= '	<script type="text/javascript" src="' . $arquivo . '"></script>' . "\n";

	}

	public function addJSRODAPE( $arquivo ){

		$this->App->addJSRODAPE .= '	<script type="text/javascript" src="' . $arquivo . '"></script>' . "\n";

	}

	public function addCSS( $arquivo, $media = 'screen' ){

		$this->App->addCSS .= '	<link href="' . $arquivo . '" rel="stylesheet" media="'.$media.'">' . "\n";

	}

	public function addSCRIPT( $script ){

		$this->App->addSCRIPT .= $script;

	}

	public function addSCRIPTRODAPE( $script ){

		$this->App->addSCRIPTRODAPE .= $script;

	}

	public function addSTYLE( $script ){

		$this->App->addSTYLE .= $script;

	}

	public function addJQUERY( $script ){

		$this->App->addJQUERY .= $script;

	}

	public function addClasseBody( $texto ){

		$this->App->addClasseBody .= $texto;

	}

	/* SEO*/
	public function addTITULO( $valor ){

		if ( $this->App->NomeSite ) {
			$this->App->NomeSite = $valor . AppConfig::$TituloSeparador . $this->App->NomeSite;
		} else {
			$this->App->NomeSite = $valor;
		}

	}

}

?>