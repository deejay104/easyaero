<?
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
	"actualites" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"titre" => Array("Type" => "varchar(150)", "Default" => "Titre" ),
		"message" => Array("Type" => "text", ),
		"dte_mail" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"mail" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_modif" => Array("Type" => "int(11)", ),
		"dte_modif" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"bapteme" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"num" => Array("Type" => "varchar(20)", ),
		"nom" => Array("Type" => "varchar(50)", ),
		"telephone" => Array("Type" => "varchar(14)", ),
		"mail" => Array("Type" => "varchar(100)", ),
		"nb" => Array("Type" => "tinyint(3) unsigned", ),
		"dte" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"status" => Array("Type" => "tinyint(3) unsigned", "Index"=>1 ),
		"type" => Array("Type" => "enum('btm','vi')", "Default" => "btm", ),
		"paye" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"id_pilote" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"id_avion" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"id_resa" => Array("Type" => "int(10) unsigned", "Index"=>1),
		"description" => Array("Type" => "text", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_maj" => Array("Type" => "int(10) unsigned", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"calendrier" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"description" => Array("Type" => "text", ),
		"dte_deb" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"dte_fin" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_pilote" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"uid_debite" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"uid_instructeur" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"uid_avion" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index" => "1", ),
		"destination" => Array("Type" => "varchar(50)", ),
		"nbpersonne" => Array("Type" => "tinyint(3) unsigned", "Default" => "1", ),
		"invite" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"accept" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"temps" => Array("Type" => "int(11)", "Default" => "0", ),
		"tarif" => Array("Type" => "char(2)", ),
		"prix" => Array("Type" => "float", "Default" => "0", ),
		"tpsestime" => Array("Type" => "int(11)", ),
		"tpsreel" => Array("Type" => "int(11)", ),
		"horadeb" => Array("Type" => "varchar(10)", "Default" => "0", ),
		"horafin" => Array("Type" => "varchar(10)", "Default" => "0", ),
		"idmaint" => Array("Type" => "int(10) unsigned", "Default" => 0),
		"potentiel" => Array("Type" => "int(10) unsigned", "Default" => 0),
		"carburant" => Array("Type" => "varchar(8)", ),
		"carbavant" => Array("Type" => "int(10) unsigned", "Default" => 0),
		"carbapres" => Array("Type" => "int(10) unsigned", "Default" => 0),
		"prixcarbu" => Array("Type" => "varchar(8)" ),
		"reel" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"edite" => Array("Type" => "enum('oui','non')", "Default" => "non", "Index"=>1),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
	),
	"compte" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"mid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"tiers" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"montant" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"mouvement" => Array("Type" => "varchar(100)", "Index" => "1", ),
		"commentaire" => Array("Type" => "tinytext", ),
		"date_valeur" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"dte" => Array("Type" => "varchar(6)", "Index" => "1", ),
		"compte" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"pointe" => Array("Type" => "char(1)", "Default" => ""),
		"facture" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"rembfact" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"signature" => Array("Type" => "varchar(64)", "Index" => "1", ),
		"precedent" => Array("Type" => "varchar(64)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"date_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"comptetemp" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"deb" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"cre" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
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
	"config" => Array
	(
		"param" => Array("Type" => "varchar(20)", ),
		"value" => Array("Type" => "varchar(20)", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00" ),
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
	"cron" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"description" => Array("Type" => "varchar(40)", ),
		"module" => Array("Type" => "varchar(20)", ),
		"script" => Array("Type" => "varchar(20)", ),
		"schedule" => Array("Type" => "int(10) unsigned", ),
		"lastrun" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"nextrun" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"txtretour" => Array("Type" => "varchar(20)", ),
		"txtlog" => Array("Type" => "text", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", ),
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
	"document" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"name" => Array("Type" => "varchar(100)", ),
		"filename" => Array("Type" => "varchar(20)", ),
		"uid" => Array("Type" => "int(10) unsigned", "Default" => 0, "Index" => "1", ),
		"type" => Array("Type" => "varchar(10)", "Index" => "1", ),
		"dossier" => Array("Type" => "tinytext", ),
		"droit" => Array("Type" => "varchar(3)", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"uid_creat" => Array("Type" => "int(10) unsigned","Default" => 0, ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"droits" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"groupe" => Array("Type" => "varchar(5)", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Default" => 0, "Index" => "1", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => 0, ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"echeance" => Array
	(
		"id" => Array("Type" => "bigint(20) unsigned", "Index" => "PRIMARY", ),
		"typeid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"dte_echeance" => Array("Type" => "date", ),
		"paye" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1 ),
		"dte_create" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_create" => Array("Type" => "int(10) unsigned", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_maj" => Array("Type" => "int(10) unsigned", ),
	),
	"echeancetype" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"description" => Array("Type" => "varchar(100)", ),
		"poste" => Array("Type" => "int(11)", "Index" => "1", ),
		"cout" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"resa" => Array("Type" => "enum('obligatoire','instructeur','facultatif')", ),
		"droit" => Array("Type" => "varchar(3)", ),
		"multi" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"notif" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"delai" => Array("Type" => "tinyint(3) unsigned", "Default" => "30", ),
	),
	"export" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"nom" => Array("Type" => "varchar(50)", ),
		"description" => Array("Type" => "text", ),
		"requete" => Array("Type" => "text", ),
		"param" => Array("Type" => "varchar(50)", ),
		"droit_r" => Array("Type" => "char(3)", ),
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
	"forums" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"fid" => Array("Type" => "mediumint(8) unsigned", "Default" => "0", "Index" => "1", ),
		"fil" => Array("Type" => "mediumint(8) unsigned", "Default" => "0", "Index" => "1", ),
		"titre" => Array("Type" => "varchar(104)", ),
		"message" => Array("Type" => "text", ),
		"pseudo" => Array("Type" => "varchar(104)", ),
		"mail_diff" => Array("Type" => "varchar(104)", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index" => "1", ),
		"droit_r" => Array("Type" => "char(3)", ),
		"droit_w" => Array("Type" => "char(3)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"mailing" => Array("Type" => "int(11)", "Default" => "0", ),
	),
	"forums_lus" => Array
	(
		"forum_id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"forum_msg" => Array("Type" => "mediumint(8) unsigned", "Index" => "1", ),
		"forum_usr" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"forum_date" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"groupe" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"groupe" => Array("Type" => "varchar(5)" ),
		"description" => Array("Type" => "varchar(200)", ),
	),
	"historique" => Array
	(
		"id" => Array("Type" => "bigint(20) unsigned", "Index" => "PRIMARY", ),
		"class" => Array("Type" => "varchar(20)", ),
		"table" => Array("Type" => "varchar(20)", "Index" => "1", ),
		"idtable" => Array("Type" => "bigint(20) unsigned", "Index" => "1", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"type" => Array("Type" => "varchar(3)", ),
		"comment" => Array("Type" => "text", ),
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
	"login" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"username" => Array("Type" => "varchar(100)", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"header" => Array("Type" => "varchar(200)", ),
	),
	"maintatelier" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"nom" => Array("Type" => "varchar(200)", ),
		"mail" => Array("Type" => "varchar(200)", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"maintenance" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"uid_ressource" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index" => "1", ),
		"uid_atelier" => Array("Type" => "mediumint(8) unsigned", "Default" => "0", "Index"=>1),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"status" => Array("Type" => "enum('planifie','confirme','effectue','cloture','supprime')", "Default" => "planifie", "Index"=>1),
		"dte_deb" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"dte_fin" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"potentiel" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"commentaire" => Array("Type" => "text" ),
		"uid_lastresa" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"maintfiche" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"uid_avion" => Array("Type" => "smallint(5) unsigned", "Default" => "0", "Index"=>1),
		"description" => Array("Type" => "text", ),
		"uid_valid" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_valid" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"traite" => Array("Type" => "enum('oui','non','ann','ref')", "Default" => "non", ),
		"uid_planif" => Array("Type" => "mediumint(8) unsigned", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"manips" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"titre" => Array("Type" => "varchar(100)", ),
		"comment" => Array("Type" => "text", ),
		"type" => Array("Type" => "varchar(100)", ),
		"cout" => Array("Type" => "decimal(10,2)", "Default" => "0.00", ),
		"facture" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index"=>1),
		"dte_manip" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"dte_limite" => Array("Type" => "date", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00", ),
	),
	"masses" => Array
	(
		"id" => Array("Type" => "mediumint(8) unsigned", "Index" => "PRIMARY", ),
		"uid_vol" => Array("Type" => "mediumint(8) unsigned", "Index" => "1", ),
		"uid_pilote" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid_place" => Array("Type" => "tinyint(3) unsigned", ),
		"poids" => Array("Type" => "char(3)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", ),
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
		"uid_creat" => Array("Type" => "int(10) unsigned", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_modif" => Array("Type" => "int(10) unsigned", ),
		"dte_modif" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"navpoints" => Array
	(
		"nom" => Array("Type" => "varchar(20)", "Index" => "PRIMARY", ),
		"description" => Array("Type" => "varchar(200)", ),
		"lat" => Array("Type" => "varchar(10)", ),
		"lon" => Array("Type" => "varchar(10)", ),
		"icone" => Array("Type" => "varchar(20)", ),
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
	"ressources" => Array
	(
		"id" => Array("Type" => "smallint(6)", "Index" => "PRIMARY", ),
		"nom" => Array("Type" => "varchar(20)", ),
		"immatriculation" => Array("Type" => "varchar(6)", ),
		"marque" => Array("Type" => "varchar(20)", ),
		"modele" => Array("Type" => "varchar(20)", ),
		"couleur" => Array("Type" => "varchar(6)", ),
		"actif" => Array("Type" => "enum('oui','non','off')", "Default" => "oui", "Index"=>1),
		"poste" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"maxpotentiel" => Array("Type" => "int(10) unsigned", "Default" => 50 ),
		"alertpotentiel" => Array("Type" => "int(10) unsigned", "Default" => 45 ),
		"tarif" => Array("Type" => "varchar(6)", "Default" => "0", ),
		"tarif_reduit" => Array("Type" => "varchar(6)", "Default" => "0", ),
		"tarif_double" => Array("Type" => "varchar(6)", "Default" => "0", ),
		"tarif_inst" => Array("Type" => "varchar(6)", "Default" => "0", ),
		"tarif_nue" => Array("Type" => "varchar(6)", "Default" => "0", ),
		"typehora" => Array("Type" => "varchar(3)", "Default" => "min", ),
		"description" => Array("Type" => "text", ),
		"places" => Array("Type" => "tinyint(3) unsigned", "Default" => "0", ),
		"puissance" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"charge" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"massemax" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"vitesse" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"autonomie" => Array("Type" => "smallint(5) unsigned", "Default" => "0", ),
		"tolerance" => Array("Type" => "tinytext", ),
		"centrage" => Array("Type" => "text", ),
		"maintenance" => Array("Type" => "varchar(200)", ),
		"uid_creat" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"uid_maj" => Array("Type" => "int(10) unsigned", "Default" => "0", ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
	),
	"rex" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"titre" => Array("Type" => "varchar(40)" ),
		"status" => Array("Type" => "enum('new','inprg','close','cancel')","Index"=>1 ),
		"description" => Array("Type" => "text" ),
		"commentaire" => Array("Type" => "text" ),
		"synthese" => Array("Type" => "text" ),
		"planaction" => Array("Type" => "text" ),
		"categorie" => Array("Type" => "varchar(30)" ),
		"nature" => Array("Type" => "varchar(30)" ),
		"mto" => Array("Type" => "varchar(30)" ),
		"environnement" => Array("Type" => "varchar(30)" ),
		"phase" => Array("Type" => "varchar(30)" ),
		"typevol" => Array("Type" => "varchar(30)" ),
		"typeevt" => Array("Type" => "varchar(30)" ),
		"uid_avion" => Array("Type" => "int(10) unsigned", "Index"=>1 ),
		"risque" => Array("Type" => "varchar(2)" ),
		"actif" => Array("Type" => "enum('oui','non')","Index"=>1, "Default"=>"oui" ),
		"dte_rex" => Array("Type" => "date", "Default" => "0000-00-00" ),
		"uid_creat" => Array("Type" => "int(10) unsigned","Index"=>1 ),
		"dte_creat" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00" ),
		"uid_maj" => Array("Type" => "int(10) unsigned","Index"=>1 ),
		"dte_maj" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00" ),
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
		"defaut_pil" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
		"defaut_ins" => Array("Type" => "enum('oui','non')", "Default" => "non", ),
	),
	"type" => Array
	(
		"id" => Array("Type" => "varchar(2)", "Index" => "PRIMARY", ),
		"nom" => Array("Type" => "varchar(50)", ),
		"libelle" => Array("Type" => "varchar(50)", ),
	),
	"utildonnees" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"did" => Array("Type" => "int(10) unsigned", "Index" => "1", ),
		"uid" => Array("Type" => "int(11)", "Index" => "1", ),
		"valeur" => Array("Type" => "varchar(255)", ),
	),
	"utildonneesdef" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"ordre" => Array("Type" => "tinyint(3) unsigned", ),
		"nom" => Array("Type" => "varchar(20)", ),
		"type" => Array("Type" => "varchar(10)", ),
		"actif" => Array("Type" => "enum('oui','non')", "Default" => "oui", "Index" => "1"),
	),
	"utilisateurs" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"sexe" => Array("Type" => "enum('M','F','NA')", "Default" => "NA", ),
		"pere" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1"),
		"mere" => Array("Type" => "int(10) unsigned", "Default" => "0", "Index" => "1"),
		"disponibilite" => Array("Type" => "enum('dispo','occupe')", "Default" => "dispo", ),
		"tel_fixe" => Array("Type" => "varchar(20)", ),
		"tel_portable" => Array("Type" => "varchar(20)", ),
		"tel_bureau" => Array("Type" => "varchar(20)", ),
		"adresse1" => Array("Type" => "varchar(255)", ),
		"adresse2" => Array("Type" => "varchar(255)", ),
		"ville" => Array("Type" => "varchar(100)", ),
		"codepostal" => Array("Type" => "varchar(10)", ),
		"zone" => Array("Type" => "varchar(3)", ),
		"profession" => Array("Type" => "varchar(50)", ),
		"commentaire" => Array("Type" => "text", ),
		"avatar" => Array("Type" => "varchar(50)", ),
		"droits" => Array("Type" => "varchar(2)", ),
		"lache" => Array("Type" => "varchar(2)", ),
		"type" => Array("Type" => "enum('pilote','eleve','instructeur','invite','membre','parent','enfant','employe')", "Default" => "pilote", "Index" => "1", ),
		"decouvert" => Array("Type" => "smallint(6)", "Default" => "0", ),
		"tarif" => Array("Type" => "smallint(6)", "Default" => "0", ),
		"dte_naissance" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"dte_inscription" => Array("Type" => "date", "Default" => "0000-00-00"),
		"dte_login" => Array("Type" => "datetime", "Default" => "0000-00-00 00:00:00"),
		"poids" => Array("Type" => "tinyint(3) unsigned", "Default" => "75", ),
		"aff_rapide" => Array("Type" => "char(1)", "Default" => "n", ),
		"aff_mois" => Array("Type" => "char(1)", ),
		"aff_jour" => Array("Type" => "date", "Default" => "0000-00-00", ),
		"aff_msg" => Array("Type" => "tinyint(3) unsigned", "Default" => "0", ),
	),
	"vacances" => Array
	(
		"id" => Array("Type" => "int(10) unsigned", "Index" => "PRIMARY", ),
		"dtedeb" => Array("Type" => "date", ),
		"dtefin" => Array("Type" => "date", ),
		"comment" => Array("Type" => "text", ),
	),

);



?>