<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class ACL
{
	static
	private $App;

	static
	private $instance;

    static
    public $sistema;

	/*
	 * Cria sessoes e atribui permissoes
	 */
	public function __construct(){

		$this->App			= Registro::getInstance();

        $this->sistema      = new Sistema();

		$TempoLimit			= 30; // Em minutos
		$UnixTime			= strtotime( 'NOW' );
		$SESSION_TimeNow	= $UnixTime - ( $TempoLimit * 60 );

		// Deleta os usuarios que estão ocioso
		$this->App->BancoDeDados->exec("DELETE FROM base_sessao WHERE tempo < '" . $SESSION_TimeNow . "'");

		// Verifica no banco se o usuario está na sessao
		$SessaoBanco = $this->App->BancoDeDados->query("SELECT
																*
															FROM
																base_sessao
															WHERE
																session_id = '".$this->App->SESSION_ID."'
															LIMIT 1
														")->fetch( PDO::FETCH_OBJ );

		// Se existir a sessao no banco
		if( $SessaoBanco ) {

			if( $SessaoBanco->usuario_id > 0 ) {
				Sessao::Set('Logado',	1);
			} else {
				Sessao::Set('Logado',	0);
			}

			Sessao::Set('Tempo',			$SessaoBanco->tempo);
			Sessao::Set('Nome',				$SessaoBanco->nome);
			Sessao::Set('email',			$SessaoBanco->email);
			Sessao::Set('Usuario',			$SessaoBanco->usuario);
			Sessao::Set('usuarioId',		$SessaoBanco->usuario_id);
			Sessao::Set('grupo_id',			$SessaoBanco->grupo_id);

			// Atualiza o tempo da sessao do usuario
			$this->App->BancoDeDados->exec("UPDATE base_sessao SET
												tempo = '".$UnixTime."'
											WHERE
												session_id = '".$this->App->SESSION_ID."'
											LIMIT 1
										");

		}
		else // Se nao existe a sessao no banco, entao a cria
		{

			// Caso o usuário nao tenha sua sessao no banco entao cria
			$this->App->BancoDeDados->exec("INSERT INTO base_sessao
												(
													 session_id
													,tempo
												) VALUES (
													 '".$this->App->SESSION_ID."'
													,'".$UnixTime."'
												)
											");

			Sessao::Set('Logado',			0);
			Sessao::Set('Tempo',			$UnixTime);
			Sessao::Set('Nome',				'');
			Sessao::Set('email',			'');
			Sessao::Set('Usuario',			'');
			Sessao::Set('Avatar',       	'');
			Sessao::Set('usuarioId',		0);
			Sessao::Set('grupo_id',			0);
		}


        $RegraAcao  = Sessao::Get('PermissaoAcao');

        $PermissoesAcoes    = array();


        $acoes = explode('|', $RegraAcao);
        foreach ($acoes as $acao) {
            $item   = explode('@@', $acao);
            $key    = $item[0];
            $value  = $item[1];

            $PermissoesAcoes[AppConfig::$PrefixoPermSessao.$key] = $value;
        }


        Sessao::Set('permissoesacoes',	$PermissoesAcoes);

	}


    public static function UsuarioAdmin(){
        if( in_array( 1, explode(',', Sessao::Get('grupo_id'))) ){
            return true;
        }

        return false;
    }



    public static function ValidarPagina( $pagina ){
        $permissoes = Sessao::get('permissoespaginas');

        // Verifica se tem a chave no array
        if( array_key_exists(AppConfig::$PrefixoPermSessao.$pagina, $permissoes) ) {

            // Verifica se tem a permissao
            if( $permissoes[AppConfig::$PrefixoPermSessao.$pagina] === 1 ) {
                return true;
            }

        }

        if( in_array( 1, explode(',', Sessao::Get('grupo_id'))) ){
            return true;
        }

        return false;
    }

    public static function ValidarAcao( $acao ){
        $permissoes = Sessao::get('permissoesacoes');

        // Verifica se tem a chave no array
        if( array_key_exists(AppConfig::$PrefixoPermSessao.$acao, $permissoes) ) {

            // Verifica se tem a permissao
            if( $permissoes[AppConfig::$PrefixoPermSessao.$acao] === 1 ) {
                return true;
            }

        }

        return false;
    }



	/*
	 * Verifica se o usuário está logado, se nao estiver então redireciona para o login
	 */
	public static function SomenteLogado( ) {

		if( Sessao::Get('Logado') !== 1 OR !Sessao::Get('usuarioId') ) {

			return Sistema::Redirecionar( BASE . 'login', 'Você precisa estar logado para acessar este conteúdo.');

		}

	}


	/*
	 * Retorna se o usuário está logado true ou false
	 */
	public static function estaLogado( ) {

		if ( Sessao::Get('Logado') === 1 AND Sessao::Get('usuarioId') > 0 ) {
			return true;
		}

		return false;

	}



	static public function init() {
		if (self::$instance === null) {
			self::$instance = new ACL();
		}

		return self::$instance;
	}

}

?>