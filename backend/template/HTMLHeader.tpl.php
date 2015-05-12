<!DOCTYPE html>
<!--[if IE 8]> <html lang="pt-BR" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="pt-BR" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="pt-BR"> <!--<![endif]-->
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<base href="{BASE_URL}">
	<meta charset="UTF-8">
	<title>{sitetitulo}</title>
	<meta name="robots" content="index, follow">
	<meta name="revisit-after" content="1 day">
	<meta name="distribution" content="Global">
	<meta name="language" content="pt-br">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

{addCSS}{addJS}

	<!--[if lt IE 9]>
	<script src="{PATH_JS}excanvas.js" type="text/javascript"></script>
	<script src="{PATH_JS}respond.js" type="text/javascript"></script>
	<![endif]-->

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
<body class="{addClasseBody}">
