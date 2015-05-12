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

	public $tpl;

	public function __construct(){

		$this->App		= Registro::getInstance();
		$this->Request	= $this->App->Request;

        $this->Modulos  = new Modulos();
        $this->Plugins  = new Plugins();
        $this->Sistema  = new Sistema();

        $this->router	= $this->Sistema->Url();


    }

	public function CarregarTemplate( $template ){

		$this->tpl = new Template( PATH_TEMPLATE . DS . $this->App->nomeControle . DS . $template );

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

		return $this->tpl;

	}

	public function CarregarTemplateModulo( $nomeclasse, $template ){

		$this->tpl = new Template( PATH_MODULOS . DS . $nomeclasse . DS . 'tmpl' . DS . $template );

		return $this->tpl;

	}

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

	public function addCSS( $arquivo, $media = 'all' ){

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

	public function addIdBody( $texto ){

		$this->App->addIdBody .= $texto;

	}

	/* SEO*/
	public function addTITULO( $valor ){

		if ( $this->App->config->nome_empresa ) {
			$this->App->NomeSite = $valor . AppConfig::$TituloSeparador . $this->App->config->nome_empresa;
		} else {
			$this->App->NomeSite = $valor;
		}

	}

	public function addDESCRIPTION( $texto ){

		$this->App->addDESCRIPTION .= $texto;

	}

	public function addKEYWORDS( $texto ){

		$this->App->addKEYWORDS .= $texto;

	}

	public function canonicalURL( $texto ){
		$this->App->canonicalURL = $texto;
	}

	public function ogTITULO( $texto ){
		$this->App->ogTITULO .= $texto;
	}

	public function ogTIPO( $texto ){
		$this->App->ogTIPO = $texto;
	}

	public function ogURL( $texto ){
		$this->App->ogURL = $texto;
	}

	public function ogIMG( $texto ){
		$this->App->ogIMG = $texto;
	}

	public function ogDESCRICAO( $texto ){
		$this->App->ogDESCRICAO = $texto;
	}

}

?>