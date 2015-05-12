<?php

class loginControle extends Controle {

    public function __construct(){

        // Configurações do componente
        $this->config = new stdClass();
        $this->config->componenteNome		= "Login";
        $this->config->componenteUrl		= BASE . 'login';
        $this->config->componenteTitulo		= 'Login';
        $this->config->componenteSubTitulo	= '';

        // Executa o __construct da classe extendida
        parent::__construct();

    }

    public function index( ) {

        $this->Modelo->index();
    }


    public function validar(){
        header('Content-type: application/json');

        $logado = Login::Logar( $this->Request->post('frm_login_user'), $this->Request->post('frm_login_pass'), false );


        if( $logado ){
            $type = 'success';
            $msg  = 'Bem vindo, ' . Sessao::Get('Nome');
            $red  = BASE;

        } else {
            $type = 'error';
            $msg  = 'Usúario ou/e Senha incorretos.';
            $red  = false;
        }

        $resposta = array(
            'type'         => $type
            ,'msg'          => $msg
            ,'msg'          => $msg
            ,'redirect'     => $red
        );

        echo json_encode($resposta); exit;
    }


    public function validarcodigo(){
        header('Content-type: application/json');

        $frm_codigo			= $this->Request->post('codigo');
        $frm_token			= $this->Request->post('token');

        $validaCodigo       = $this->App->BancoDeDados->query("SELECT
													item.*
												FROM
													base_recuperar_senha as item
												WHERE
													item.codigo_verificador = '".$frm_codigo."'
												AND item.token              = '".$frm_token."'
												AND item.expira             > '".date('Y-m-d H:i:s')."'
											")->fetch( PDO::FETCH_OBJ );

        if( !$validaCodigo ){
            $resposta = array( 'type' => 'error', 'msg' => 'Código incorreto.', 'email' => '' );
            echo json_encode($resposta); exit;
        }

        $resposta = array( 'type' => 'success', 'msg' => 'alterar senha.' );
        echo json_encode($resposta); exit;

    }




    public function esqueciminhasenha( ) {
        header('Content-type: application/json');

        if( $_POST ) {

            // Pega os dados do post
            $frm_email			= $this->Request->post('frm_email_codigoVerificador');

            // Valida o usuario
            $validaUsuario = $this->App->BancoDeDados->query("SELECT
													 item.id
													,item.nome
													,item.email
												FROM
													base_usuario as item
												WHERE
														item.usuario = '".$frm_email."'
													OR	item.email = '".$frm_email."'
											")->fetch( PDO::FETCH_OBJ );
            if( !$validaUsuario ) {

                $resposta = array(
                    'type'          => 'error'
                    ,'msg'          => 'Não foi possivel encontrar o email.'
                    ,'email'        => $frm_email
                    ,'action'       => ''
                    ,'token'        => ''

                ); echo json_encode($resposta); exit;

            }

            // Gera token
            $token			= Sistema::Token( 30 );
            $codVerificador	= Sistema::Token( 4, false, false );
            $data_expira	= date('Y-m-d H:i:s', strtotime('+1 day'));

            // Insere no banco
            $this->App->BancoDeDados->exec("INSERT INTO base_recuperar_senha
												(
													 usuario_id
													,token
													,codigo_verificador
													,_criado
													,expira
													,ip
												) VALUES (
													 '".$validaUsuario->id."'
													,'".$token."'
													,'".$codVerificador."'
													,'".date('Y-m-d H:i:s')."'
													,'".$data_expira."'
													,'".$this->Request->server('REMOTE_ADDR')."'
												)");

            $resposta = array(
                'type'          => 'success'
                ,'msg'          => 'link gerado com sucesso.'
                ,'email'        => $frm_email
                ,'action'       => BASE . 'login/validarcodigo'
                ,'token'        => $token

            ); echo json_encode($resposta); exit;





            // Preenche o template do email
            $tplEmail = new Template( PATH_TEMPLATE . DS . 'emails' . DS . 'recuperar_senha.html' );

            $tplEmail->atribuir('empresa_nome',				$this->App->config->nome_empresa );
            $tplEmail->atribuir('empresa_logo',				PATH_IMG . '/' . 'logo_email.png' );
            $tplEmail->atribuir('usuario_nome',				$validaUsuario->nome );
            $tplEmail->atribuir('usuario_link_senha',		BASE . 'login/alterar-senha/' . $token );
            $tplEmail->atribuir('usuario_ip',				$this->Request->server('REMOTE_ADDR') );
            $tplEmail->atribuir('data_expira',				date('d/m/Y \à\s H\:i', strtotime($data_expira)) );
            $tplEmail->atribuir('data_envio',				date('d/m/Y \à\s H\:i') );

            $body = $tplEmail->salva();

            try {

                // Envia a mensagem
                $email = new email();

                $email->AddAddress( $validaUsuario->email, $validaUsuario->nome );

                $email->Subject = "Recuperação de Senha, ".$this->App->config->nome_empresa;
                $email->IsHTML(true);
                $email->MsgHTML( $body );
                $email->AltBody = $body;
                $email->Send();

                Url::Redirecionar( BASE . 'login/esqueci-minha-senha', '', '', 'Link de recuperação enviado, por favor cheque seu e-mail.');

            } catch (Exception $e) {

                Url::Redirecionar( BASE . 'login/esqueci-minha-senha', '', $e->getMessage() );

            }

        }

    }


    public function alterarsenha( ) {
        header('Content-type: application/json');

        $frm_token			= $this->Request->post('token');


        // Valida o token
        $validaRecuperacao = $this->App->BancoDeDados->query("SELECT
													item.*
												FROM
													base_recuperar_senha as item
												WHERE
													item.token = '".$frm_token."'
												AND item.expira > '".date('Y-m-d H:i:s')."'
											")->fetch( PDO::FETCH_OBJ );


        if( !$validaRecuperacao ) {
            $resposta = array(
                'type'          => 'error'
                ,'msg'          => 'Link de recuperação de senha não encontrado.'
                ,'email'        => ''
                ,'action'       => ''
                ,'token'        => ''

            ); echo json_encode($resposta); exit;
        }

        if( $_POST ) {

            // Pega os dados do post
            $frm_senha				= $this->App->Request->post('senha');
            $frm_senha_confirmar	= $this->App->Request->post('senha_confirmar');

            // Valida a senha
            if( $frm_senha != $frm_senha_confirmar ) {
                $resposta = array(
                    'type'          => 'error'
                    ,'msg'          => 'As senhas não coincidem.'
                    ,'email'        => ''
                    ,'action'       => ''
                    ,'token'        => ''

                ); echo json_encode($resposta); exit;
            }

            // Convete a senha para md5
            $frm_senha_md5 = md5($frm_senha);

            // Altera senhano banco
            $Alteracao = $this->App->BancoDeDados->exec("UPDATE base_usuario
															SET
																 senha		    = '".$frm_senha_md5."'
																,_modificado	= 'NOW()'
															WHERE
																id = '".$validaRecuperacao->usuario_id."'
														");
            if ( $Alteracao !== false ) {

                // Apaga solicitacao de alterar senha
                $this->App->BancoDeDados->exec("DELETE FROM
														base_recuperar_senha
													WHERE
														id = '".$validaRecuperacao->id."'
													LIMIT 1
												");

                $resposta = array(
                    'type'          => 'success'
                    ,'msg'          => 'Senha alterada com sucesso.'
                    ,'email'        => ''
                    ,'action'       => ''
                    ,'token'        => ''

                ); echo json_encode($resposta); exit;

            } else {

                $resposta = array(
                    'type'          => 'error'
                    ,'msg'          => 'Algum erro ocorreu. Por favor tente denovo.'
                    ,'email'        => ''
                    ,'action'       => ''
                    ,'token'        => ''

                ); echo json_encode($resposta); exit;
            }

        }


    }






    public function Sair( ) {
        Login::Sair();
    }

}

?>