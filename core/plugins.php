<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Plugins
{

	private $tpl;

	public function __construct(){

		$this->App = Registro::getInstance();

	}

	public function carrega( $NomePlugin, $params = null ){

		$config = (object) $params;

		// Arruma o nome do plugin
		$NomePlugin = 'plg_'.$NomePlugin;

		// Salva o caminho do modulo na variavel
		$arquivoDoPlugin = PATH_PLUGINS . DS . $NomePlugin . DS . $NomePlugin.'.php';

		// Se nao existir o arquivo do plugin entao retorna que nao existe
		if ( file_exists( $arquivoDoPlugin ) AND is_file( $arquivoDoPlugin ) ) {

			// Inclui o arquivo do plugin com as funcoes dele
			include_once $arquivoDoPlugin;

			// Inicia o plugin
			$IniciaPlugin = new $NomePlugin( );

			// Imprimi a saída plugin
			return $IniciaPlugin->renderiza( $config );

		} else {

			return 'O Plugin ' . $NomePlugin . ' não existe';
		
		}

	}


}

?>