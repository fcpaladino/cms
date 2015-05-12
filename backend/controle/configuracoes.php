<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class configuracoesControle extends Controle {

	public function __construct(){

		// Configurações do componente
		$this->config = new stdClass();
		$this->config->componenteNome				= "Configurações";
		$this->config->componenteUrl				= BASE . 'configuracoes';
		$this->config->componenteTitulo				= 'Configurações';
		$this->config->componenteSubTitulo			= '';
		$this->config->componenteTabela				= Sistema::Table("config");

		// Executa o __construct da classe extendida
		parent::__construct();

        // Somente se estiver logado continua
        ACL::SomenteLogado();
        // Somente se tiver permissao para este acessar o admin
        if( ACL::ValidarPagina($this->App->nomeControle) !== true ) { $this->Sistema->Redirecionar( BASE . 'erro/401' ); }
	}


    public function upload(){
        echo '<pre>'; print_r($_POST); echo '</pre>';
        echo '<pre>'; print_r($_FILES); echo '</pre>';
    }


	/*
	 * Retorna o campo e suas configurações
	 */
	protected function campo($campo){

		$campos = array(
			"nome_empresa" => array(
				"nome" => "Nome do site"
				,"obrigatorio" => true
			)
			,"contato_email" => array(
				"nome" => "Contato -> Para"
				,"obrigatorio" => true
				,"validar" => "email"
			)
			,"contato_emailcopia" => array(
				"nome" => "Contato -> Cópia"
				,"validar" => "email"
			)
			,"contato_emailcopiaoculta" => array(
				"nome" => "Contato -> Cópia oculta"
				,"validar" => "email"
			)
			,"trabalheconosco_email" => array(
				"nome" => "Trabalhe Conosco -> Para"
				,"obrigatorio" => true
				,"validar" => "email"
			)
			,"trabalheconosco_emailcopia" => array(
				"nome" => "Trabalhe Conosco -> Cópia"
				,"validar" => "email"
			)
			,"trabalheconosco_emailcopiaoculta" => array(
				"nome" => "Trabalhe Conosco -> Cópia oculta"
				,"validar" => "email"
			)
			,"depoimento_email" => array(
				"nome" => "Depoimento -> Para"
				,"obrigatorio" => true
				,"validar" => "email"
			)
			,"depoimento_emailcopia" => array(
				"nome" => "Depoimento -> Cópia"
				,"validar" => "email"
			)
			,"depoimento_emailcopiaoculta" => array(
				"nome" => "Depoimento -> Cópia oculta"
				,"validar" => "email"
			)
			,"smtp_servidor" => array(
				"nome" => "SMTP -> Servidor"
				,"obrigatorio" => true
			)
			,"smtp_porta" => array(
				"nome" => "SMTP -> Porta"
				,"obrigatorio" => true
			)
			,"smtp_seguranca" => array(
				"nome" => "SMTP -> Segurança"
			)
			,"smtp_email" => array(
				"nome" => "SMTP -> E-mail"
				,"obrigatorio" => true
				,"validar" => "email"
			)
			,"smtp_senha" => array(
				"nome" => "SMTP -> Senha"
				,"obrigatorio" => true
			)
			,"smtp_responder_para" => array(
				"nome" => "SMTP -> Responder para"
				,"obrigatorio" => true
				,"validar" => "email"
			)
			,"site_seo_titulo" => array(
				"nome" => "SEO -> Título do site"
				,"obrigatorio" => true
			)
			,"site_seo_descricao" => array(
				"nome" => "SEO -> Descrição"
				,"obrigatorio" => true
			)
			,"site_seo_palavrachave" => array(
				"nome" => "SEO -> Palavras-chave"
				,"obrigatorio" => true
			)
			,"analises_google_analytics" => array(
				"nome" => "Análises -> Google analytics"
				,"tipo" => "html"
			),
			"social_facebook" => array(
				"nome" => "Rede social -> Facebook"
			)
			,"social_twitter" => array(
				"nome" => "Rede social -> Twitter"
			)
			,"social_google_plus" => array(
				"nome" => "Rede social -> Google plus"
			)
			,"ativa_compatibilidade" => array(
				"nome" => "Ativa suporte navegadores -> Ativa suporte navegadores"
			)
			,"tipo_compatibilidade" => array(
				"nome" => "Tipo de suporte navegadores -> Tipo de suporte navegadores"
			)
		);

		return isset($campos[$campo]) ? $campos[$campo] : false;

	}


	public function index( ) {

		$this->Modelo->index( );

	}


	/**
	 * Index post
	 */
	public function indexpost( ) {

		#echo '<pre>'; print_r($_POST); echo '</pre>'; exit;

		// Pega os dados do form
		$frm_config = (array) $this->App->Request->post('frm_config');

		// Cria o array de configuracao com alguns valores padrões
		// assim campos com apenas um checkbox poderão ser pré-definidos
		$config = array_merge(
			$frm_config
		);

		// Inicia o array dos campos para atualização
		$campos_update = array();

		// Erros
		$erros = 0;
		$errosMsg = '';

		if( $config ) {
			foreach( $config as $campo => $valor ) {

				// Retira os espaços
				$valor = trim($valor);

				// Pega as configuracoes do campo
				$campo_config = $this->campo($campo);

				// Se não existir o campo
				if( !$campo_config ) {
					// Pula para o próximo campo
					continue;
				}

				// Verifica se é obrigatorio
				if( isset($campo_config["obrigatorio"]) && $campo_config["obrigatorio"] === true ) {
					if( $valor == "" ) {
						$erros++;
						$errosMsg .= $campo_config["nome"] . " é obrigatório.<br>";
					}
				}

				// Valida - email
				if( isset($campo_config["validar"]) && $campo_config["validar"] == "email" ) {
					// Se o campo está preenchido
					if( $valor != "" ) {
						if(!preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,})$/', $valor)) {

							$erros++;
							$errosMsg .= $campo_config["nome"] . " não é um e-mail válido.<br>";

							// Pula para o próximo campo
							continue;

						}
					}
				}

				// Trata - Dinheiro
				if( isset($campo_config["tipo"]) && $campo_config["tipo"] == "dinheiro" ) {
					$valor = (float) str_replace(',', '.', $valor);
				}

				// Trata - inteiro
				if( isset($campo_config["tipo"]) &&  $campo_config["tipo"] == "inteiro" ) {
					$valor = (int) $valor;
				}

				// Trata - html
				if( isset($campo_config["tipo"]) &&  $campo_config["tipo"] == "html" ) {
					// Recuperao campo do post e converte o html
					$valor = htmlentities($_POST['frm_config'][$campo], ENT_QUOTES, 'UTF-8');
				}

				// Salva o campo no array de atualização
				$campos_update[$campo] = $valor;

			}
		}

		// Se há campos para atualizar
		if( count($campos_update) > 0 ) {
			foreach( $campos_update as $campo => $valor ) {

				$this->App->BancoDeDados->exec("UPDATE base_config SET valor = '".$valor."' WHERE nome = '".$campo."' LIMIT 1");

			}
		}

		Sistema::Redirecionar( $this->config->componenteUrl.$this->config->url_compl, '', $errosMsg, 'Configuração atualizada' );

	}


	/**
	 * Testa as configurações de SMTP
	 */
	public function smtpteste( ) {

		#echo '<pre>'; print_r($_POST); echo '</pre>'; exit;

		// Dados inicias do retorno
		header('Content-type: application/json');

		// Inicia os dados de retorno padrão
		$resposta = array();
		$resposta["valido"]			= 1;
		$resposta["sucesso_msg"]	= "";
		$resposta["erro_msg"]		= "";

		// Pega os dados do post
		$smtp_servidor			= $this->App->Request->post('servidor');
		$smtp_porta				= $this->App->Request->post('porta');
		$smtp_seguranca			= $this->App->Request->post('seguranca');
		$smtp_email				= $this->App->Request->post('email');
		$smtp_senha				= $this->App->Request->post('senha');
		$smtp_responder_para	= $this->App->Request->post('responder_para');

		try {

			// Valida os campos
			if( empty($smtp_servidor) ) { throw new Exception("Erro: Informe o Servidor."); }
			if( empty($smtp_porta) ) { throw new Exception("Erro: Informe a Porta."); }
			if( empty($smtp_email) ) { throw new Exception("Erro: Informe o E-mail."); }
			if( empty($smtp_senha) ) { throw new Exception("Erro: Informe a senha."); }
			if( empty($smtp_responder_para) ) { throw new Exception("Erro: Informe o Responder para."); }

		} catch (Exception $e) {

			$resposta["valido"] = 0;
			$resposta["erro_msg"] = $e->getMessage();
			echo json_encode($resposta);
			exit;

		}

		try {

			// Inicia o PHPMailer, true para erros com Exceções (try e catch)
			$email = new PHPMailer(true);

			// Defini o charset
			$email->CharSet = 'UTF-8';

			// Envio por smtp autenticado através do PHPMailer
			$email->IsSMTP();
			$email->Host       = $smtp_servidor;
			$email->Port       = $smtp_porta;
			$email->SMTPAuth   = true;
			$email->SMTPSecure = $smtp_seguranca;
			$email->Username   = $smtp_email;
			$email->Password   = $smtp_senha;

			$email->AddReplyTo( $smtp_responder_para );
			$email->AddAddress( $smtp_responder_para );

			$email->SetFrom( $smtp_email );

			$body = "Este é um e-mail de teste confirmando as configurações de SMTP digitadas estão corretas em ".$this->App->config->nome_empresa.".";
			$email->Subject = "E-mail de teste para ".$this->App->config->nome_empresa;
			$email->IsHTML(true);
			$email->MsgHTML($body);
			$email->AltBody = $body;
			$email->Send();

			$resposta["sucesso_msg"] = "Configuração válida, uma mensagem de teste foi enviado para o e-mail " .$smtp_responder_para;

		} catch (Exception $e) {

			$resposta["valido"] = 0;
			$resposta["erro_msg"] = $e->getMessage();

		}

		echo json_encode($resposta);
		exit;

	}

}

?>