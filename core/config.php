<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Config
{
	static
	private $instance;

	public function __construct() {

		$this->App = Registro::getInstance();

		$Configs = $this->App->BancoDeDados->query("SELECT
														nome,
														valor
													FROM
														base_config
													WHERE
														autoload = '1'
													ORDER BY
														id ASC
												")->fetchAll( PDO::FETCH_OBJ );

		if( $Configs ) {
			// Inicia o objeto
			$Configuracoes = new stdClass();

			foreach( $Configs as $Config ) {
				// Gera a configuracao com o nome
				$Configuracoes->{$Config->nome} = $Config->valor;
			}
			// Atribui as configuracoes na variavel global
			$this->App->set('config', $Configuracoes);

		}

	}


	static
	public function init()
	{
		if (self::$instance === null) {
			self::$instance = new Config();
		}

		return self::$instance;
	}


}

?>