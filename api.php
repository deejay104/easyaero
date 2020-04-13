<?
	if (file_exists("config/config.inc.php"))
	{
		require ("config/config.inc.php");
	}
	if (file_exists("static/cache/config/variables.inc.php"))
	{
		require ("static/cache/config/variables.inc.php");
	}

	$appfolder="..";
	$corefolder="core";

	chdir($corefolder);
	require("api.php");
?>
