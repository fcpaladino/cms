<?php

define('EamApp', 1 );

error_reporting(0); // Set E_ALL for debuging

session_start();

define(DS , DIRECTORY_SEPARATOR );

include_once dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'config.php';
include_once dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'core'.DS.'sessao.php';

// Defini a url raiz do site, retirando o caminho desta pasta, pois se não acaba ficando a raiz como esta pasta
$raizSite = str_replace('core/elfinder/php/', '', AppConfig::$SiteUrl);

include_once dirname(__FILE__).DS.'elFinderConnector.class.php';
include_once dirname(__FILE__).DS.'elFinder.class.php';
include_once dirname(__FILE__).DS.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DS.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$opts = array(
	// 'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
			'path'          => '../../../media/uploads/',         // path to files (REQUIRED)
			'URL'           => $raizSite.'media/uploads/', // URL to files (REQUIRED)
			'accessControl' => 'access'             // disable and hide dot starting files (OPTIONAL)
		)
	)
);

// Se esta logado e se existir o Id do usuario e se o usuario  é o grupo 3 (Admin),
// Caso mude o id do admin ai será preciso alterar aqui tambem
if ( Sessao::Get('Logado') == 1 and Sessao::Get('usuarioId') and Sessao::Get('grupo_id') == '3' ) {

	// run elFinder
	$connector = new elFinderConnector(new elFinder($opts));
	$connector->run();

} else {

	exit(json_encode(array('error' => 'Você não tem permissão de acessar diretamente o gerenciador de arquivos!')));

}
