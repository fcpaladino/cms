<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */
class Request
{

	protected $_request = array();
	protected $_post = array();
	protected $_get = array();
	protected $_cookie = array();
	protected $_server = array();

	public function __construct()
	{
		$this->_request	= array_merge($this->_request,	$_REQUEST);
		$this->_get		= array_merge($this->_get,		$_GET);
		$this->_post	= array_merge($this->_post,		$_POST);
		$this->_cookie	= array_merge($this->_cookie,	$_COOKIE);
		$this->_server	= array_merge($this->_server,	$_SERVER);
		$this->_limpar();
	}

	// Cria um valor para uma variavel específica de _REQUEST ( get e post )
	public function set($key, $value)
	{
		$this->_request[$key] = $value;
	}

	// Retorna variavel específica de _REQUEST ( get e post )
	public function get($variavel)
	{
		return isset($this->_request[$variavel]) ? $this->_strip_tags($this->_request[$variavel]) : null;
	}

	// Retorna variavel específica do _SERVER
	public function server($variavel)
	{
		return isset($this->_server[$variavel]) ? $this->_strip_tags($this->_server[$variavel]) : null;
	}

	// Retorna variavel específica do _COOKIE
	public function cookie($variavel)
	{
		return isset($this->_cookie[$variavel]) ? $this->_strip_tags($this->_cookie[$variavel]) : null;
	}

	// Retorna variavel específica do _POST
	public function post($variavel)
	{
		return isset($this->_post[$variavel]) ? $this->_strip_tags($this->_post[$variavel]) : null;
	}

	public function getNumero($key) // retorna variavel se for numérica
	{
		return is_numeric($this->_request[$key]) ? floatval( $this->_request[$key] ) : null;
	}

	public function getHTML($var, $key)
	{
		switch(strtolower($var)) {
			case 'get':		$array = $this->_get;		break;
			case 'post':	$array = $this->_post;		break;
			case 'cookie':	$array = $this->_cookie;	break;
			case 'server':	$array = $this->_server;	break;
			default:		$array = array();			break;
		}
		if(isset($array[$key])) {
			return $this->_raw( $array[$key] );
		}
		return null;
	}

	protected function _raw( $value ) // Recupera a variavel sem escapar ' ou "
	{
		return stripslashes($value);
	}

	protected function _limpar()
	{
		if( !get_magic_quotes_gpc() ) { // Caso por padrao o servidor não escapa ' com \, entao ele escapa com addslashes
			$this->_request	= $this->_addslashes($this->_request);
			$this->_get		= $this->_addslashes($this->_get);
			$this->_post	= $this->_addslashes($this->_post);
			$this->_cookie	= $this->_addslashes($this->_cookie);
			$this->_server	= $this->_addslashes($this->_server);
		}
	}

	protected function _addslashes($value)
	{
	    if(is_array($value)) {
			return array_map(array($this,'_addslashes'), $value);
		} else {
			return addslashes($value);
		}
	}

	protected function _strip_tags($value)
	{
	    if(is_array($value)) {
			return array_map(array($this,'_strip_tags'), $value);
		} else {
			return strip_tags($value);
		}
	}

}