<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

defined('Application') || die('<h1>Sem acesso direto</h1>');

/**
 * Mecanismo de Template para PHP5
 * 
 * Mecanismos de Template permitem manter o código HTML em arquivos externos
 * que ficam completamente livres de código PHP. Dessa forma, consegue-se manter 
 * a lógica de programação (PHP) separada da estrutura visual (HTML ou XML, CSS, etc).
 *
 * Se você já é familiar ao uso de mecanismos de template PHP, esta classe inclui 
 * algumas melhorias: suporte à objetos, automaticamente detecta blocos, mantém uma 
 * lista interna das variáveis que existem, limpa automaticamente blocos "filhos", 
 * avisando quando tentamos chamar blocos ou variáveis que são existem, avisando quando 
 * criamos blocos "mal formados", e outras pequenas ajudas.
 * 
 * @author Rael G.C. (rael.gc@gmail.com)
 * @version 1.9
 */
class Template {

	/**
	 * A list of existent document variables.
	 *
	 * @var       array
	 */
	private $vars = array();
	
	/**
	 * A hash with vars and values setted by the user.
	 *
	 * @var       array
	 */
	private $values = array();
	
	/**
	 * A hash of existent object properties variables in the document.
	 *
	 * @var       array
	 */
	private $properties = array();
	
	/**
	 * A hash of the object instances setted by the user.
	 *
	 * @var       array
	 */
	private $instances = array();
	
	/**
	 * A list of all automatic recognized blocks.
	 *
	 * @var       array
	 */
	private $blocks = array();
	
	/**
	 * A list of all blocks that contains at least a "child" block.
	 *
	 * @var       array
	 */
	private $parents = array();
	
	/**
	 * Describes the replace method for blocks. See the Template::setFile() 
	 * method for more details.
	 *
	 * @var       boolean
	 */
	private $accurate;

	protected $App;
	
	/**
	 * Regular expression to find var and block names. 
	 * Only alfa-numeric chars and the underscore char are allowed.
	 *
	 * @var		string
	 */
	private static $REG_NAME = "([[:alnum:]]|_)+";
	
	/**
	 * Cria um novo template, usando $filename como arquivo principal
	 * 
	 * Quando o parâmetro $accurate é true, a substituição dos blocos no arquivo   
	 * final será perfeitamente fiel ao arquivo original, isto é, todas as tabulações 
	 * serão removidas. Isso vai ter um pequeno prejuízo na performance, que pode variar 
	 * de acordo com a versão do PHP em uso. Mas é útil quando estamos usando tags HTML 
	 * como &lt;pre&gt; ou &lt;code&gt;. Em outros casos, ou melhor, quase sempre, 
	 * nunca se mexe no valor de $accurate.
	 *
	 * @param     string $filename		caminho do arquivo que será lido
	 * @param     booelan $accurate		true para fazer substituição fiel das tabulações
	 */
	public function __construct($filename, $accurate = false){

		$this->App = Registro::getInstance();

		$this->accurate = $accurate;

		$this->loadfile(".", $filename);

	}
	
	/**
	 * Adiciona o conteúdo do arquivo identificado por $filename na variável de template 
	 * identificada por $varname
	 *
	 * @param     string $varname		uma variável de template existente
	 * @param     string $filename		arquivo a ser carregado
	 */
	public function addArquivo($varname, $filename){
		if(!$this->exists($varname)) throw new InvalidArgumentException("addArquivo: var $varname não existe");
		$this->loadfile($varname, $filename);
	}
	
	/**
	 * Não use este método, ele serve apenas para podemos acessar as variáveis 
	 * de template diretamente.
	 *
	 * @param	string	$varname	template var name
	 * @param	mixed	$value		template var value
	 */
	public function __set($varname, $value){
		if(!$this->exists($varname)) throw new RuntimeException("var $varname não existe");
		$stringValue = $value;
		if(is_object($value)){
			$this->instances[$varname] = $value;
			if(!array_key_exists($varname, $this->properties)) $this->properties[$varname] = array();
			if(method_exists($value, "__toString")) $stringValue = $value->__toString();
			else $stringValue = "Object";
		} 
		$this->setValue($varname, $stringValue);
		return $value;
	}
	
	/**
	 * Não use este método, ele serve apenas para podemos acessar as variáveis 
	 * de template diretamente.
	 *
	 * @param	string	$varname	template var name
	 */
	public function __get($varname){
		if (isset($this->values["{".$varname."}"])) return $this->values["{".$varname."}"];
		throw new RuntimeException("var $varname não existe");
	}

	/**
	 * Verifica se uma variável de template existe.
	 *
	 * Retorna true se a variável existe. Caso contrário, retorna false.
	 *
	 * @param	string	$varname	template var name
	 */
	public function exists($varname){
		return in_array($varname, $this->vars);
	}

	/**
	* Verifica se um bloco de template existe.
	*
	* Retorna true se o bloco existe. Caso contrário, retorna false.
	*
	* @param string $blockname template block name
	*/
	public function blockexists($blockname){
		return in_array($blockname, $this->blocks);
	}

	/**
	 * Loads a file identified by $filename.
	 * 
	 * The file will be loaded and the file's contents will be assigned as the 
	 * variable's value.
	 * Additionally, this method call Template::recognize() that identifies 
	 * all blocks and variables automatically.
	 *
	 * @param     string $varname		contains the name of a variable to load
	 * @param     string $filename		file name to be loaded
	 * 
	 * @return    void
	 */
	private function loadfile($varname, $filename) {
		if (!file_exists($filename)) throw new InvalidArgumentException("arquivo $filename não existe");
		// Reading file and hiding comments
		$str = preg_replace("/<!---.*?--->/smi", "", file_get_contents($filename));
		$blocks = $this->recognize($str, $varname);
		if (empty($str)) throw new InvalidArgumentException("arquivo $filename está vazio");
		$this->setValue($varname, $str);
		$this->createBlocks($blocks);
	}
	
	/**
	 * Identify all blocks and variables automatically and return them.
	 * 
	 * All variables and blocks are already identified at the moment when 
	 * user calls Template::setFile(). This method calls Template::identifyVars() 
	 * and Template::identifyBlocks() methods to do the job.
	 *
	 * @param     string	$content		file content
	 * @param     string	$varname		contains the variable name of the file
	 * 
	 * @return    array		an array where the key is the block name and the value is an 
	 * 						array with the children block names.
	 */
	private function recognize(&$content, $varname){
		$blocks = array();
		$queued_blocks = array();
		foreach (explode("\n", $content) as $line) {
			if (strpos($line, "{")!==false) $this->identifyVars($line);
			if (strpos($line, "<!--")!==false) $this->identifyBlocks($line, $varname, $queued_blocks, $blocks);
		}
		return $blocks;
	}

	/**
	 * Identify all user defined blocks automatically.
	 *
	 * @param     string $line				contains one line of the content file
	 * @param     string $varname			contains the filename variable identifier
	 * @param     string $queued_blocks		contains a list of the current queued blocks
	 * @param     string $blocks			contains a list of all identified blocks in the current file
	 * 
	 * @return    void
	 */
	private function identifyBlocks(&$line, $varname, &$queued_blocks, &$blocks){
		$reg = "/<!--\s*BEGIN\s+(".self::$REG_NAME.")\s*-->/sm";
		preg_match($reg, $line, $m);
		if (1==preg_match($reg, $line, $m)){
			if (0==sizeof($queued_blocks)) $parent = $varname;
			else $parent = end($queued_blocks);
			if (!isset($blocks[$parent])){
				$blocks[$parent] = array();
			}
			$blocks[$parent][] = $m[1];
			$queued_blocks[] = $m[1];
		}
		$reg = "/<!--\s*END\s+(".self::$REG_NAME.")\s*-->/sm";
		if (1==preg_match($reg, $line)) array_pop($queued_blocks);
	}
	
	/**
	 * Identifies all variables defined in the document.
	 *
	 * @param     string $line				contains one line of the content file
	 */
	private function identifyVars(&$line){
		$r = preg_match_all("/{(".self::$REG_NAME.")((\-\>(".self::$REG_NAME."))*)?}/", $line, $m);
		if ($r){
			for($i=0; $i<$r; $i++){
				// Object var detected
				if($m[3][$i] && (!array_key_exists($m[1][$i], $this->properties) || !in_array($m[3][$i], $this->properties[$m[1][$i]]))){
					$this->properties[$m[1][$i]][] = $m[3][$i];
				}
				if(!in_array($m[1][$i], $this->vars)) $this->vars[] = $m[1][$i];
			}
		}
	}
	
	/**
	 * Create all identified blocks given by Template::identifyBlocks().
	 *
	 * @param     array $blocks		contains all identified block names
	 * @return    void
	 */
	private function createBlocks(&$blocks) {
		$this->parents = array_merge($this->parents, $blocks);
		foreach($blocks as $parent => $block){
			foreach($block as $chield){
				if(in_array($chield, $this->blocks)) throw new UnexpectedValueException("bloco duplicado: $chield");
				$this->blocks[] = $chield;
				$this->setBlock($parent, $chield);
			}
		}
	}
	
	/**
	 * A variable $parent may contain a variable block defined by:
	 * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;. 
	 * 
	 * This method removes that block from $parent and replaces it with a variable 
	 * reference named $block. The block is inserted into the varKeys and varValues 
	 * hashes. 
	 * Blocks may be nested.
	 *
	 * @param     string $parent	contains the name of the parent variable
	 * @param     string $block		contains the name of the block to be replaced
	 * @return    void
	 */
	private function setBlock($parent, $block) {
		$name = "B_".$block;
		$str = $this->getVar($parent);
		if($this->accurate){
			$str = str_replace("\r\n", "\n", $str);
			$reg = "/\t*<!--\s*BEGIN\s+$block\s+-->\n*(\s*.*?\n?)\t*<!--\s+END\s+$block\s*-->\n?/sm";
		} 
		else $reg = "/<!--\s*BEGIN\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+END\s+$block\s*-->\s*/sm";
		if(1!==preg_match($reg, $str, $m)) throw new UnexpectedValueException("bloco $block está mal formado");
		$this->setValue($name, '');
		$this->setValue($block, $m[1]);
		$this->setValue($parent, preg_replace($reg, "{".$name."}", $str));
	}

	/**
	 * Internal setValue() method.
	 *
	 * The main difference between this and Template::__set() method is this 
	 * method cannot be called by the user, and can be called using variables or 
	 * blocks as parameters.
	 *
	 * @param     string $varname		constains a varname
	 * @param     string $value        constains the new value for the variable
	 * @return    void
	 */
	private function setValue($varname, $value) {
		$this->values["{".$varname."}"] = $value;
	}
	
	/**
	 * Returns the value of the variable identified by $varname.
	 *
	 * @param     string	$varname	the name of the variable to get the value of
	 * @return    string	the value of the variable passed as argument
	 */
	private function getVar($varname) {
		return $this->values['{'.$varname.'}'];
	}
	
	/**
	 * Limpa o valor de uma variável
	 * 
 	 * O mesmo que $this->setValue($varname, "");
	 *
	 * @param     string $varname	nome da variável
	 */
	public function limpa($varname) {
		$this->setValue($varname, "");
	}
	
	/**
	 * Fill in all the variables contained within the variable named
	 * $varname. The resulting value is returned as the function result and the
	 * original value of the variable varname is not changed. The resulting string
	 * is not "finished", that is, the unresolved variable name policy has not been
	 * applied yet.
	 *
	 * @param     string 	$varname      the name of the variable within which variables are to be substituted
	 * @return    string	the value of the variable $varname with all variables substituted.
	 */
	private function subst($varname) {
		$s = $this->getVar($varname);
		// Common variables replacement
		$s = str_replace(array_keys($this->values), $this->values, $s);
		// Object variables replacement
		foreach($this->instances as $var => $instance){
			foreach($this->properties[$var] as $properties){
				if(false!==strpos($s, "{".$var.$properties."}")){
					$pointer = $instance;
					$property = explode("->", $properties);
					for($i = 1; $i < sizeof($property); $i++){
						$obj = str_replace('_', '', $property[$i]);
						// Se é variavel no objeto, comeca com "var_"
						if(substr($property[$i], 0, 4) == "var_"){
							$pointer = $pointer->{substr($property[$i], 4)};
						}
						// Non boolean accessor
						elseif(method_exists($pointer, "get$obj")){
							$pointer = $pointer->{"get$obj"}();
						}
						// Boolean accessor
						elseif(method_exists($pointer, "is$obj")){
							$pointer = $pointer->{"is$obj"}();
						}
						// Magic __get accessor
						elseif(method_exists($pointer, "__get")){
							$pointer = $pointer->__get($property[$i]);
						}
						// Accessor dot not exists: throw Exception
						else {
							$className = $property[$i-1] ? $property[$i-1] : get_class($instance);
							$class = is_null($pointer) ? "NULL" : get_class($pointer);
							throw new BadMethodCallException("não existe método na classe ".$class." para acessar ".$className."->".$property[$i]);
						}
					}
					// Checking if final value is an object
					if(is_object($pointer)){
						if(method_exists($pointer, "__toString")){
							$pointer = $pointer->__toString();
						} else {
							$pointer = "Object";
						}
					}
					// Replace
					$s = str_replace("{".$var.$properties."}", $pointer, $s);
				}
			}
		}
		return $s;
	}
	
	/**
	 * Clear all child blocks of a given block.
	 *
	 * @param     string $block	a block with chield blocks.
	 */
	private function limpaBlock($block) {
		if (isset($this->parents[$block])){
			$chields = $this->parents[$block];
			foreach($chields as $chield){
				$this->limpa("B_".$chield);
			}
		}
	}
	
	/**
	 * Mostra um bloco.
	 * 
	 * Esse método deve ser chamado quando um bloco deve ser mostrado.
	 * Sem isso, o bloco não irá aparecer no conteúdo final.
	 *
	 * Se o parâmetro $append for true, o conteúdo do bloco será 
	 * adicionado ao conteúdo que já existia antes. Ou seja, use true 
	 * quando quiser que o bloco seja duplicado.
	 *
	 * @param     string $block		nome do bloco que deve ser mostrado
	 * @param     boolean $append		true se o conteúdo anterior deve ser mantido (ou seja, para duplicar o bloco)
	 */
	public function block($block, $append = true) {
		if(!in_array($block, $this->blocks)) throw new InvalidArgumentException("bloco $block não existe");
		if ($append) $this->setValue("B_".$block, $this->getVar("B_".$block) . $this->subst($block));
		else $this->setValue("B_".$block, $this->subst($block));
		$this->limpaBlock($block);
	}

	/**
	 * Atribui um valor a um campo
	 */
	public function atribuir( $campo, $valor, $escapar = 0 ) {

		// Escapa a variavel trocando aspas simples e duplas
		if( $escapar === 1 ) {
			$valor = str_replace('"',	'&#34;',	 $valor);
			$valor = str_replace("'",	"&#39;",	 $valor);
		}

		if( $this->exists($campo) ) $this->$campo	= $valor;

	}

	/**
	 * Funcao que trocar variaveis definidas por valores pre definidos, chamada antes de renderizar.
	 */
	public function PreperaDados() {

		if( $this->exists('BASE'		    ) ) $this->BASE 			= BASE;
		if( $this->exists('FRONDEND'	    ) ) $this->FRONDEND	        = FRONDEND;
		if( $this->exists('IMG'		        ) ) $this->IMG			    = IMG;
		if( $this->exists('JS'			    ) ) $this->JS			    = JS;
		if( $this->exists('CSS'		        ) ) $this->CSS			    = CSS;
		if( $this->exists('UPLOADS'		    ) ) $this->UPLOADS	    	= UUPLOADS;
		if( $this->exists('PLUGINS'		    ) ) $this->PLUGINS  		= PLUGINS;

		if( $this->exists('sitetitulo'		) ) $this->sitetitulo		= $this->App->NomeSite;
		if( $this->exists('addCSS'			) ) $this->addCSS			= $this->App->addCSS;
		if( $this->exists('addJS'			) ) $this->addJS			= $this->App->addJS;
		if( $this->exists('addJSRODAPE'		) ) $this->addJSRODAPE		= $this->App->addJSRODAPE;
		if( $this->exists('addKEYWORDS'		) ) $this->addKEYWORDS		= $this->App->addKEYWORDS;
		if( $this->exists('addDESCRIPTION'	) ) $this->addDESCRIPTION	= $this->App->addDESCRIPTION;
		if( $this->exists('addClasseBody'	) ) $this->addClasseBody	= $this->App->addClasseBody;
		if( $this->exists('addIdBody'	    ) ) $this->addIdBody	    = $this->App->addIdBody;


		// SEO Canonical
		if( $this->exists('canonicalURL') ) {
			if( $this->App->canonicalURL ) {
				$this->canonicalURL		= $this->App->canonicalURL;
			} else {
				$this->canonicalURL		= BASE . $this->App->Request->get('url');
			}
		}

		// OpenGraph
		if( $this->exists('ogTITULO') ) {
			if( $this->App->ogTITULO ) {
				$this->ogTITULO		= $this->App->ogTITULO;
			} else {
				$this->ogTITULO		= $this->App->NomeSite;
			}
		}
		if( $this->exists('ogIMG') ) {
			if( $this->App->ogIMG ) {
				$this->ogIMG		= $this->App->ogIMG;
			} else {
				$this->ogIMG		= 'template/img/logo.png';
			}
		}
		if( $this->exists('ogDESCRICAO') ) {
			if( $this->App->ogDESCRICAO ) {
				$this->ogDESCRICAO		= $this->App->ogDESCRICAO;
			} else {
				$this->ogDESCRICAO		= $this->App->addDESCRIPTION;
			}
		}
		if( $this->exists('ogURL') ) {
			if( $this->App->ogURL ) {
				$this->ogURL		= $this->App->ogURL;
			} else {
				$this->ogURL		= BASE . $this->App->Request->get('url');
			}
		}
		if( $this->exists('ogTIPO') ) {
			if( $this->App->ogTIPO ) {
				$this->ogTIPO		= $this->App->ogTIPO;
			} else {
				$this->ogTIPO		= 'website';
			}
		}

		if( $this->App->SESSAO_SUCESSO != '' && $this->exists('SESSAO_SUCESSO') ) {
			$this->block("BLOCO_SESSAO_SUCESSO");
			$this->SESSAO_SUCESSO = $this->App->SESSAO_SUCESSO;
		}

		if( $this->App->SESSAO_INFO != '' && $this->exists('SESSAO_INFO') ) {
			$this->block("BLOCO_SESSAO_INFO");
			$this->SESSAO_INFO = $this->App->SESSAO_INFO;
		}

		if( $this->App->SESSAO_ERRO != '' && $this->exists('SESSAO_ERRO') ) {
			$this->block("BLOCO_SESSAO_ERRO");
			$this->SESSAO_ERRO = $this->App->SESSAO_ERRO;
		}

		if( $this->App->addSCRIPT != '' && $this->exists('addSCRIPT') ) {
			$this->block("BLOCO_HEADSCRIPT");
			$this->addSCRIPT = $this->App->addSCRIPT;
		}

		if( $this->App->addSCRIPTRODAPE != '' && $this->exists('addSCRIPTRODAPE') ) {
			$this->block("BLOCO_SCRIPTRODAPE");
			$this->addSCRIPTRODAPE = $this->App->addSCRIPTRODAPE;
		}

		if( $this->App->addSTYLE != '' && $this->exists('addSTYLE') ) {
			$this->block("BLOCO_HEADSTYLE");
			$this->addSTYLE = $this->App->addSTYLE;
		}

		if( $this->App->addJQUERY != '' && $this->exists('addJQUERY') ) {
			$this->block("BLOCO_JQUERY");
			$this->addJQUERY = $this->App->addJQUERY;
		}

		// GEO
		if( $this->exists('geo_region') )	{ $this->geo_region		= $this->App->config->geo_region; }
		if( $this->exists('geo_placename') ){ $this->geo_placename	= $this->App->config->geo_placename; }
		if( $this->exists('geo_position') )	{ $this->geo_position	= $this->App->config->geo_position; }

	}

	/**
	 * Mostra na tela o conteúdo final.
	 */
	public function show() {
		#echo $this->salva();
		eval("?>" . $this->salva()); // com essa linha é possivel add php dentro do template
	}

	/**
	* Retorna o conteúdo final, sem mostrá-lo na tela. 
	* Se você quer mostrá-lo na tela, use o método Template::show().
	* 
	* @return    string	
	*/
	public function salva() {

		// Substitui algumas variaveis do sistema
		$this->PreperaDados();

		// After subst, remove empty vars
		return preg_replace("/{(".self::$REG_NAME.")((\-\>(".self::$REG_NAME."))*)?}/", "", $this->subst("."));
	}

	/**
	 * Mostra na tela o conteúdo final adcionando cabecalho.
	 */
	public function Renderizar() {

		$this->show();

	}

}

?>