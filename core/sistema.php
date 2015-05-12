<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
defined('Application') || die('<h1>Sem acesso direto</h1>');

class Sistema
{

	static
	private $app;

	static
	private $instance;

	public function __construct(){

		self::$app = Registro::getInstance();

	}

	public static function Redirecionar( $url=null, $SessaoInfo=null, $SessaoErro=null, $SessaoSucesso=null ) {

		$url = $url ? $url : '/';
		if ( $SessaoInfo ) Sessao::set('SESSAO_INFO', $SessaoInfo );
		if ( $SessaoErro ) Sessao::set('SESSAO_ERRO', $SessaoErro );
		if ( $SessaoSucesso ) Sessao::set('SESSAO_SUCESSO', $SessaoSucesso );
		header("Location: {$url}");
		exit;

	}

	/*public function addINFO( $info ){

		$this->App->addINFO .= $info;

	}

	public function addERRO( $erro ){

		$this->App->addERRO .= $erro;

	}

	public function addSUCESSO( $sucesso ){

		$this->App->addSUCESSO .= $sucesso;

	}*/

	public function Url( ){
		$url = explode('/', self::$app->Request->Get('url'));
		$url = array_filter($url, 'strlen'); // apaga itens vazios do array
		return($url);

	}


	public function getNumero($variavel) // retorna variavel se for numérica
	{
		return is_numeric($variavel) ? floatval( $variavel ) : null;
	}

	public function ConverterData( $data, $separadorData = '/' ) { // aaaa-mm-dd 00:00:00 para dd-mm-aaaa
		$data = substr( $data, 0, 10 ); // pega a data
		return implode( !strstr($data, $separadorData) ? $separadorData : "-", array_reverse( explode(!strstr($data, $separadorData) ? "-" : $separadorData, $data) ) );
	}

	public function ConverterDataHora( $dataHora, $comAS = 1 ) { // aaaa-mm-dd hh:mm:ss para dd-mm-aaaa hh:mm:ss
		$data = substr( $dataHora, 0, 10 ); // pega a data
		$Hora = substr( $dataHora, 11, 19 ); // pega a hora

		if( $comAS == 1 ) {
			return implode( !strstr($data, '/') ? "/" : "-", array_reverse( explode(!strstr($data, '/') ? "-" : "/", $data) ) ) . ' às ' . $Hora;
		} else {
			return implode( !strstr($data, '/') ? "/" : "-", array_reverse( explode(!strstr($data, '/') ? "-" : "/", $data) ) ) . ' ' . $Hora;
		}
	}

	public function ConverterUrl( $valor ) {

		$slug = iconv('UTF-8', 'ASCII//TRANSLIT', $valor);
		$slug = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $slug);
		$slug = strtolower(trim($slug, '-'));
		$slug = preg_replace("/[\/_| -]+/", '-', $slug);

		return $slug;

	}

    public static function RemoverTags($texto){
        return strip_tags($texto, '<(.*?)>');
    }



	/*
	 * Retorna o nome do mês pelo número
	 */
	public function NomeMes( $mes ) {

        $nomeMeses = array(
			1  => 'Janeiro',
			2  => 'Fevereiro',
			3  => 'Março',
			4  => 'Abril',
			5  => 'Maio',
			6  => 'Junho',
			7  => 'Julho',
			8  => 'Agosto',
			9  => 'Setembro',
			10 => 'Outubro',
			11 => 'Novembro',
			12 => 'Dezembro'
		);

		return $nomeMeses[$mes];

	}


	public function LimitaCaracteres($texto, $limite, $quebra = true) {

		$tamanho = strlen($texto);

		// Verifica se o tamanho do texto é menor ou igual ao limite
		if ($tamanho <= $limite) {
			$novo_texto = $texto;
			// Se o tamanho do texto for maior que o limite
		} else {
			// Verifica a opção de quebrar o texto
			if ($quebra == true) {
				$novo_texto = trim(substr($texto, 0, $limite));
				// Se não, corta $texto na última palavra antes do limite
			} else {
				// Localiza o útlimo espaço antes de $limite
				$ultimo_espaco = strrpos(substr($texto, 0, $limite), ' ');
				// Corta o $texto até a posição localizada
				$novo_texto = trim(substr($texto, 0, $ultimo_espaco));
			}
		}

		// Retorna o valor formatado
		return $novo_texto;

	}






    public function ValidarColuna( $keys, $table ){
        $colunas = $this->App->BancoDeDados->query("SHOW COLUMNS FROM ".$table)->fetchAll( PDO::FETCH_OBJ );

        foreach($colunas as $c){
            if($c->Field == $keys){ return true; }
        }
        return false;
    }


    public function ValidarTabela( $table ){
        $showtables = self::$app->BancoDeDados->query('show tables')->fetchAll(PDO::FETCH_OBJ);

        foreach ($showtables as $key) {
            $_name_table = 'Tables_in_'.AppConfig::$DbConfig['database'];
            if( $key->$_name_table == $table ){
                return true;
            }
        }

        return false;
    }

    public static function Table( $table ){
        $table_base     = 'base_' . $table;
        $table_webee    = AppConfig::$PrefixoDB . $table;

        return self::ValidarTabela( $table_webee ) ? $table_webee : $table_base;
    }


    function FileSize($filesize , $pfx = 1){

        if(is_numeric($filesize)){
            $decr = 1024; $step = 0;
            $prefix = array('Byte','KB','MB','GB','TB','PB');

            while(($filesize / $decr) > 0.9){
                $filesize = $filesize / $decr;
                $step++;
            }

            if($pfx == 1)
                return round($filesize,2).' '.$prefix[$step];
            else
                return round($filesize,2);

        } else {
            return 'NaN';
        }

    }


    public function DiasEntreDatas($inicio, $fim){
        $i          = strtotime($inicio);
        $f          = strtotime($fim);
        $diferenca  = $f - $i;
        $dias       = (int)floor( $diferenca / (60 * 60 * 24));
        return $dias;
    }

    function DiffDate($d1, $d2, $type='', $sep='-')
    {
        $d1 = explode($sep, $d1);
        $d2 = explode($sep, $d2);
        switch ($type){
            case 'A':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'MI':
                $X = 60;
                break;
            default:
                $X = 1;
        }

        $v1 = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]);
        $v2 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);

        return floor( ($v1 - $v2) / $X );
    }


    public function Token($tamanho = 8, $maiusculas = TRUE, $minusculas = TRUE, $numeros = TRUE, $simbolos = FALSE)
    {
        # Biblioteca
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $num  = '0123456789';
        $simb = '!@#$%*-';

        $retorno = '';
        $caracteres = '';

        if ($maiusculas) {
            $caracteres .= $lmai;
        }

        if ($minusculas) {
            $caracteres .= $lmin;
        }

        if ($numeros) {
            $caracteres .= $num;
        }

        if ($simbolos) {
            $caracteres .= $simb;
        }

        # Calculamos o total de caracteres possíveis
        $len = strlen($caracteres);

        for ($n = 1; $n <= $tamanho; $n++) {
            # Criamos um número aleatório de 1 até $len para pegar um dos caracteres
            $rand = mt_rand(1, $len);
            # Concatenamos um dos caracteres na variável $retorno
            $retorno .= $caracteres[$rand - 1];
        }

        return $retorno;
    }


    static
	public function init()
	{
		if (self::$instance === null) {
			self::$instance = new Sistema();
		}

		return self::$instance;
	}

}

?>