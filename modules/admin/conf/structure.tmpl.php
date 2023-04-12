<?php
$tabCustom=Array
(
	"abo_ligne" => Array (
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"abonum" => Array("Type" => "varchar(8)", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"mouvid" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"montant" => Array("Type" => "decimal(10,2)", ),
	),
	"abonnement" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"abonum" => Array("Type" => "varchar(8)", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => 1),
		"dtedeb" => Array("Type" => "date", ),
		"dtefin" => Array("Type" => "date", ),
		"jour_num" => Array("Type" => "tinyint(3) unsigned", "Default" => "0", ),
		"jour_sem" => Array("Type" => "char(1)", "Default" => "-", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index" => "1", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"compte" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"mid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"tiers" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"montant" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"mouvement" => Array("Type" => "varchar(100)", "Index" => "1", ),
		"commentaire" => Array("Type" => "tinytext", ),
		"date_valeur" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"dte" => Array("Type" => "varchar(6)", "Index" => "1", ),
		"compte" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"pointe" => Array("Type" => "char(1)", ),
		"facture" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"rembfact" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"hash" => Array("Type" => "varchar(64)", ),
		"signature" => Array("Type" => "varchar(172)", ),
		"precedent" => Array("Type" => "int(10) unsigned", ),
		"clepublic" => Array("Type" => "varchar(280)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"date_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"comptetemp" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"deb" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"cre" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"tiers" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"ventilation" => Array("Type" => "text", ),
		"montant" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"poste" => Array("Type" => "int(10)", "Default" => "0", ),
		"commentaire" => Array("Type" => "tinytext", ),
		"date_valeur" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"compte" => Array("Type" => "varchar(10)", ),
		"facture" => Array("Type" => "varchar(10)", ),
		"rembfact" => Array("Type" => "varchar(10)", ),
		"status" => Array("Type" => "varchar(10)", "Default" => "0", "Index"=>1),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"date_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"conso" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"idvol" => Array("Type" => "mediumint(8) unsigned", "Default" => "0", "Index" => "1", ),
		"idavion" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index" => "1", ),
		"quantite" => Array("Type" => "float", "Default" => "0", ),
		"prix" => Array("Type" => "float", "Default" => "0", ),
		"tiers" => Array("Type" => "varchar(100)", ),
		"uid_creat" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_modif" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"dte_modif" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"disponibilite" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"dte_deb" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"dte_fin" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => 0, ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"factures" => Array
	(
		"id" => Array("Type" => "varchar(10)", "Index" => "PRIMARY", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"dteid" => Array("Type" => "varchar(6)", "Index" => "1", ),
		"dte" => Array("Type" => "date", ),
		"total" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"paid" => Array("Type" => "varchar(1)", ),
		"email" => Array("Type" => "char(1)", "Default" => "N", ),
		"comment" => Array("Type" => "varchar(200)", ),
	),
	"lache" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"id_avion" => Array("Type" => "smallint(5) unsigned", "Index" => "1", ),
		"uid_pilote" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"masses" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"uid_vol" => Array("Type" => "mediumint(8) unsigned", "Index" => "1", ),
		"uid_pilote" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid_place" => Array("Type" => "tinyint(3) unsigned", ),
		"poids" => Array("Type" => "char(3)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0"),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_modif" => Array("Type" => "int(10) unsigned", ),
		"dte_modif" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"mouvement" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"ordre" => Array("Type" => "varchar(4)", ),
		"description" => Array("Type" => "varchar(100)", ),
		"compte" => Array("Type" => "varchar(10)", ),
		"debiteur" => Array("Type" => "char(3)", "Default" => "0", ),
		"crediteur" => Array("Type" => "char(3)", "Default" => "0", ),
		"montant" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", ),
		"j0" => Array("Type" => "char(1)", ),
		"j1" => Array("Type" => "char(1)", "Default" => "N", ),
		"j2" => Array("Type" => "char(1)", "Default" => "N", ),
		"j3" => Array("Type" => "char(1)", ),
		"j4" => Array("Type" => "char(1)", "Default" => "N", ),
		"j5" => Array("Type" => "char(1)", "Default" => "N", ),
		"j6" => Array("Type" => "char(1)", ),
		"j7" => Array("Type" => "char(1)", ),
		"vac" => Array("Type" => "char(1)", ),
	),
	"navigation" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"titre" => Array("Type" => "varchar(40)", ),
		"vitesse" => Array("Type" => "int(10) unsigned", ),
		"dirvent" => Array("Type" => "int(10) unsigned", ),
		"vitvent" => Array("Type" => "int(10) unsigned", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0"),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_modif" => Array("Type" => "int(10) unsigned", ),
		"dte_modif" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"navroute" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"idnav" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"ordre" => Array("Type" => "int(10) unsigned", ),
		"nom" => Array("Type" => "varchar(20)", "Index" => "1", ),
	),
	"numcompte" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"numcpt" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"description" => Array("Type" => "varchar(40)"),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", ),
	),
	"participants" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"idmanip" => Array("Type" => "mediumint(8) unsigned", "Default" => "0", "Index" => "1", ),
		"idusr" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index"=>1),
		"participe" => Array("Type" => "enum('Y','N')", "Default" => "Y", "Index"=>1),
		"nb" => Array("Type" => "tinyint(3) unsigned", "Default" => "1", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"plage" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY"),
		"jour" => Array("Type" => "char(1)", ),
		"plage" => Array("Type" => "char(1)", ),
		"titre" => Array("Type" => "varchar(20)", ),
		"nom" => Array("Type" => "varchar(50)", ),
		"libelle" => Array("Type" => "varchar(50)", ),
		"deb" => Array("Type" => "int(10) unsigned", ),
		"fin" => Array("Type" => "int(10) unsigned", ),
	),
	"presence" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"dte" => Array("Type" => "date", "Index" => "1", ),
		"type" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"dtedeb" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"dtefin" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"zone" => Array("Type" => "char(3)", ),
		"regime" => Array("Type" => "varchar(3)", ),
		"tpspaye" => Array("Type" => "int(11)", "Default" => "0", ),
		"tpsreel" => Array("Type" => "int(11)", "Default" => "0", ),
		"age" => Array("Type" => "tinyint(3) unsigned", "Default" => "0", ),
		"handicap" => Array("Type" => "varchar(3)", "Default" => "non", ),
	),
	"prevision" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"annee" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index" => "1", ),
		"mois" => Array("Type" => "tinyint(3) unsigned", "Default" => "0"),
		"avion" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index"=>1),
		"heures" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
	),
	"tarifs" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"ress_id" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"code" => Array("Type" => "varchar(2)", "Index" => "1", ),
		"nom" => Array("Type" => "varchar(20)", ),
		"reservation" => Array("Type" => "varchar(20)", ),
		"pilote" => Array("Type" => "varchar(6)", ),
		"instructeur" => Array("Type" => "varchar(6)", ),
		"reduction" => Array("Type" => "int(11)", ),
		"poste" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"defaut_pil" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"defaut_ins" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
	),
	"type" => Array
	(
		"id" => Array("Type" => "varchar(2)", "Index" => "PRIMARY", ),
		"nom" => Array("Type" => "varchar(50)", ),
		"libelle" => Array("Type" => "varchar(50)", ),
	),
	"vacances" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"dtedeb" => Array("Type" => "date", ),
		"dtefin" => Array("Type" => "date", ),
		"comment" => Array("Type" => "text", ),
	),

);

	require_once ($appfolder."/class/user.inc.php");
	$obj=new user_class(0,$sql);
	$obj->genSqlTab($tabTmpl);

	require_once ($appfolder."/class/bapteme.inc.php");
	$obj=new bapteme_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/navigation.inc.php");
	$obj=new navpoint_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/manifestation.inc.php");
	$obj=new manip_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/reservation.inc.php");
	$obj=new resa_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/ressources.inc.php");
	$obj=new ress_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/maintenance.inc.php");
	$obj=new maint_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new fichemaint_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new atelier_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/echeance.inc.php");
	$obj=new echeance_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new echeancetype_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/rex.inc.php");
	$obj=new rex_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	require_once ($appfolder."/class/synthese.inc.php");

	$obj=new formation_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new livret_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	$obj=new synthese_class(0,$sql);
	$obj->genSqlTab($tabCustom);

	$obj=new exercice_conf_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new exercice_prog_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new exercice_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new reference_class(0,$sql);
	$obj->genSqlTab($tabCustom);
	$obj=new refenac_class(0,$sql);
	$obj->genSqlTab($tabCustom);
?>