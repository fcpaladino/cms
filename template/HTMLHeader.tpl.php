<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="{BASE}">
    <meta charset="UTF-8">
    <title>{sitetitulo}</title>
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 day">
    <meta name="distribution" content="Global">
    <meta name="language" content="pt-br">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Icones -->
    <link rel="shortcut icon" href="{IMG}favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="{IMG}favicon57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="{IMG}favicon72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="{IMG}favicon114.png">
    <link rel="apple-touch-icon" sizes="144x144" href="{IMG}favicon144.png">
    <link rel="apple-touch-icon" href="{IMG}favicon144.png">

    <!-- SEO inicio -->
    <meta name="description" content="{addDESCRIPTION}">
    <meta name="keywords" content="{addKEYWORDS}">
    <link rel="canonical" href="{canonicalURL}">
    <!-- SEO fim -->

    <!-- GEO inicio -->
    <meta name="geo.region" content="{geo_region}">
    <meta name="geo.placename" content="{geo_placename}">
    <meta name="geo.position" content="{geo_position}">
    <!-- GEO fim -->

    <link rel="image_src" href="{BASE}miniatura/{ogIMG}&w=200&h=200&far=1&zc=0" />
    <link rel="apple-touch-icon" href="{BASE}miniatura/{ogIMG}&w=200&h=200&far=1&zc=0" />
    <meta property="og:image" content="{BASE}miniatura/{ogIMG}&w=200&h=200&far=1&zc=0" />

    <meta property="og:site_name" content="{sitetitulo}" />
    <meta property="og:title" content="{ogTITULO}" />
    <meta property="og:description" content="{ogDESCRICAO}" />
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:url" content="{ogURL}" />
    <meta property="og:type" content="article" />

    <script src="{JS}jquery-1.11.1.min.js"></script>

    {addCSS}{addJS}

    <!--[if !IE]><!--><link rel="stylesheet" type="text/css" href="{BASE}modulos/mod_iecompatibility/css/default.css"><!--<![endif]-->
    <!--[if !IE]><!--><script src="{BASE}modulos/mod_iecompatibility/js/ie-compatibility.js"></script><!--<![endif]-->



	<!-- BEGIN BLOCO_HEADSCRIPT -->
	<script type="text/javascript">
		{addSCRIPT}
	</script>
	<!-- END BLOCO_HEADSCRIPT -->

	<!-- BEGIN BLOCO_HEADSTYLE -->
	<style type="text/css">
	<!--
		{addSTYLE}
	-->
	</style>
	<!-- END BLOCO_HEADSTYLE -->

	<!-- BEGIN BLOCO_JQUERY -->
	<script type="text/javascript">
		jQuery(document).ready(function(){
        {addJQUERY}
		});
	</script>
	<!-- END BLOCO_JQUERY -->

</head>
<body class="{addClasseBody}" id="{addIdBody}">
