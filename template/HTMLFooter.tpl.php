	{addJSRODAPE}
	<!-- BEGIN BLOCO_SCRIPTRODAPE -->
	<script type="text/javascript">
		{addSCRIPTRODAPE}
	</script>
	<!-- END BLOCO_SCRIPTRODAPE -->

	<?php
		// Código do google analytics
		echo html_entity_decode($this->App->config->analises_google_analytics, ENT_QUOTES);
	?>

</body>
</html>