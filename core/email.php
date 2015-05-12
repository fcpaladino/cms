<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

class email extends PHPMailer {

	public function __construct(){

		$this->App = Registro::getInstance();

		$this->CharSet = 'UTF-8';

		// Envio por smtp autenticado através do PHPMailer
		$this->IsSMTP();
		$this->Host       = $this->App->config->smtp_servidor;
		$this->Port       = $this->App->config->smtp_porta;
		$this->SMTPAuth   = true;
		$this->SMTPSecure = $this->App->config->smtp_seguranca;
		$this->Username   = $this->App->config->smtp_email;
		$this->Password   = $this->App->config->smtp_senha;

		$this->AddReplyTo( $this->App->config->smtp_responder_para );

		$this->SetFrom( $this->App->config->smtp_email, $this->App->config->nome_empresa );

		// Erros com Exceções (try e catch)
		parent::__construct(true);

	}

}

?>