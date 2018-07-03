<?php
	set_time_limit(0);


// ---- Dossier framework
	$corefolder="core";
	$appfolder="..";
	chdir("../".$corefolder);

// ---- Charge la config  
  	require ($appfolder."/config/config.inc.php");
	require ($appfolder."/config/variables.inc.php");

// ---- Défini les variables globales
	$prof="";
	$gl_mode="batch";
	
	require ("lib/fonctions.inc.php");


// ---- Charge le numéro de version
	require ("version.php");

// ---- Se connecte à la base MySQL
	require ("class/objet.inc.php");
	require ("class/mysql.inc.php");
	require ("class/user.inc.php");
	require ($appfolder."/class/user.inc.php");

	$sql = new mysql_core($mysqluser, $mysqlpassword, $hostname, $db);
	$sql_cron = new mysql_core($mysqluser, $mysqlpassword, $hostname, $db);

// ---- Défini l'utilisateur d'execution du batch
	if ((!is_numeric($MyOpt["uid_system"])) || ($MyOpt["uid_system"]==0))
	  { echo("Compte systeme introuvable"); exit; }

	$gl_uid=$MyOpt["uid_system"];
	$token=md5($gl_uid);

	if ($gl_uid>0)
	{
		$myuser = new user_core($gl_uid,$sql,true);
	}

// ---- Timezone d'exécution
	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }
	
// ---- Fonction des informations de l'utilisateur
	$module="modules";
	$mod="admin";
	require($appfolder."/modules/admin/signcompte.api.php");

// ---- Ferme la connexion à la base de données	
 	$sql->closedb();

?>
