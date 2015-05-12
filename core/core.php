<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');


class App {

    static
    private $instance;
    protected $App;

    public function __construct() {

        define('APP'				, 'frondend');
        define('BASE'				, AppConfig::$SiteUrl);
        define('BACKEND'			, AppConfig::$SiteUrl . 'backend/');

        define('PATH_CORE'		    , BASE_PATH . DS . 'core' );
        define('PATH_CONTROLE'		, BASE_PATH . DS . 'controle' );
        define('PATH_MODELO'		, BASE_PATH . DS . 'modelo' );
        define('PATH_TEMPLATE'		, BASE_PATH . DS . 'template' );
        define('PATH_MODULOS'		, BASE_PATH . DS . 'modulos' );
        define('PATH_PLUGINS'		, BASE_PATH . DS . 'plugins' );
        define('PATH_UPLOADS'		, BASE_PATH . DS . 'media' . DS . 'uploads' );

        define('JS'			        , BASE 	   . 'template/assets/js/' );
        define('CSS'			    , BASE	   . 'template/assets/css/' );
        define('IMG'			    , BASE 	   . 'template/assets/img/' );
        define('CORE'   			, BASE     . 'core/' );

        define('UPLOAD'		        , BASE	   . 'media/uploads/' );
        define('MODULOS'		    , BASE	   . 'modulos/' );
        define('PLUGINS'		    , BASE	   . 'plugins/' );


        require PATH_CORE . DS . 'sessao.php';
        require PATH_CORE . DS . 'acl.php';
        require PATH_CORE . DS . 'registro.php';
        require PATH_CORE . DS . 'config.php';
        require PATH_CORE . DS . 'sistema.php';
        require PATH_CORE . DS . 'controle.php';
        require PATH_CORE . DS . 'modelo.php';
        require PATH_CORE . DS . 'modulos.php';
        require PATH_CORE . DS . 'plugins.php';
        require PATH_CORE . DS . 'request.php';
        require PATH_CORE . DS . 'bancodedados.php';
        require PATH_CORE . DS . 'template.php';
        require PATH_CORE . DS . 'login.php';
        require PATH_CORE . DS . 'upload'		. DS . 'class.upload.php';
        require PATH_CORE . DS . 'paginacao'	. DS . 'paginacao.class.php';
        require PATH_CORE . DS . 'phpmailer'	. DS . 'class.phpmailer.php';
        require PATH_CORE . DS . 'email.php';

        require PATH_CORE . DS . 'form.php';
        require PATH_CORE . DS . 'titulopagina.php';
        require PATH_CORE . DS . 'acoes.php';
        require PATH_CORE . DS . 'cropimage.php';

        $this->App =& Registro::getInstance();

        // Inicia classe de sessao
        Sessao::init();

        $sistema = new Sistema();


        $this->App->set('SESSAO_SUCESSO', Sessao::Get('SESSAO_SUCESSO')); Sessao::Apaga('SESSAO_SUCESSO');
        $this->App->set('SESSAO_ERRO'   , Sessao::Get('SESSAO_ERRO')); Sessao::Apaga('SESSAO_ERRO');
        $this->App->set('SESSAO_INFO'   , Sessao::Get('SESSAO_INFO')); Sessao::Apaga('SESSAO_INFO');

        $this->App->set('SESSAO_ID'	    , session_id() );

        $this->App->set('BancoDeDados'	, ConexaoBD::singleton( AppConfig::$DbConfig ) );
        $this->App->set('Request'		, new Request());


        Config::init();

        $url = @explode( '/', $this->App->Request->get('url') );

        $Controle	= isset($url[0]) ? ( !empty($url[0]) ? $url[0] : 'index' ) : 'index';
        $Acao		= isset($url[1]) ? ( !empty($url[1]) ? $url[1] : 'index' ) : 'index';


        $this->App->set('nomeControle'	, strtolower( str_replace('-', '', $Controle) ));
        $this->App->set('urlControle'	, $Controle);


        $FileControle   = PATH_CONTROLE . DS . $this->App->nomeControle . '.php';
        $FileModelo     = PATH_MODELO   . DS . $this->App->nomeControle . '.php';
        $FileModeloPag  = PATH_MODELO   . DS . 'paginas.php';


        if( file_exists($FileControle) ){

            require $FileControle;

            if( file_exists($FileModelo) ){
                require $FileModelo;

            } else if( file_exists($FileModeloPag) ) {
                require $FileModeloPag;
            }

            $ClassControle = $this->App->nomeControle . 'Controle';
            $this->App->set('Controle'	, new $ClassControle());


            $Acao = @preg_replace( '/\.|\+|-|__construct|__destruct/', '', $Acao );

            if ( in_array(strtolower( $Acao ), array_map( "strtolower", get_class_methods($ClassControle) ) ) ) {

                $this->App->Controle->$Acao();

            } elseif( in_array( 'rotear', array_map( "strtolower", get_class_methods($ClassControle) ) ) ) {
                $this->App->Controle->rotear();

            } else {
                $sistema->Redirecionar( BASE . 'erro/404' );
            }


        } else {
            $sistema->Redirecionar( BASE . 'erro/404' );
        }


    }

    static
    public function init(array $params = array())
    {
        if (self::$instance === null) {
            self::$instance = new App($params);
        }

        return self::$instance;
    }

}

?>