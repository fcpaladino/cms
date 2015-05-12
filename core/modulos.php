<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Modulos
{

	private $tpl;

	public function __construct(){

		$this->App = Registro::getInstance();

	}

	public function carrega( $NomeModulo, $params = null ){

		$config = (object) $params;

		// Arruma o nome do modulo
		$NomeModulo = 'mod_'.$NomeModulo;

		// Salva o caminho do modulo na variavel
		$arquivoDoModulo = PATH_MODULOS . DS . $NomeModulo . DS . $NomeModulo.'.php';

		// Se nao existir o arquivo do modulo entao retorna que nao existe
		if ( file_exists( $arquivoDoModulo ) AND is_file( $arquivoDoModulo ) ) {

			// Inclui o arquivo do modulo com as funcoes dele
			include_once $arquivoDoModulo;

			// Inicia o modulo
			$IniciaModulo = new $NomeModulo( );

			// Imprimi a saída modulo
			return $IniciaModulo->renderiza( $config );

		} else {

			return 'O Módulo ' . $NomeModulo . ' não existe';
		
		}

	}


}

?>