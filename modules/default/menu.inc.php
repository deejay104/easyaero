<?
// ---- Desktop
	$tabMenu["reservations"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/reservations/img/icn32_titre.png",
		"icone"=>"mdi-calendar",
		"nom"=>$tabLang["lang_schedule"],
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","index","start=today"),
		"submenu"=>array(
			array("nom"=>"Calendrier","url"=>geturl("reservations","index","start=today"),"droit"=>""),
			array("nom"=>"Journée","url"=>geturl("reservations","scheduler","start=today"),"droit"=>""),
		),
	);
	$tabMenu["bapteme"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_baptemes.png",
		"icone"=>"mdi-wallet-giftcard",
		"nom"=>$tabLang["lang_bapteme"],
		"droit"=>"AccesBaptemes",
		"url"=>geturl("aviation","baptemes",""),
	);
	$tabMenu["manifestattions"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/manifestations/img/icn32_titre.png",
		"icone"=>"mdi-calendar-text",
		"nom"=>$tabLang["lang_meeting"],
		"droit"=>"AccesManifestations",
		"url"=>geturl("manifestations","index",""),
	);
	if ($MyOpt["module"]["compta"]=="on")
	{
		$tabMenu["comptes"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/comptes/img/icn32_titre.png",
			"icone"=>"mdi-wallet",
			"nom"=>$tabLang["lang_account"],
			"droit"=>"AccesCompte",
			"url"=>geturl("comptes","",""),
			"submenu"=>array(
				array("nom"=>$tabLang["lang_myaccount"],"url"=>geturl("comptes","index",""),"droit"=>""),
				array("nom"=>$tabLang["lang_transfer"],"url"=>geturl("comptes","transfert",""),"droit"=>"AccesTransfert"),
				array("nom"=>$tabLang["lang_credit"],"url"=>geturl("comptes","credite",""),"droit"=>"AccesCredite"),
			)
		);
	}
	if ($MyOpt["module"]["facture"]=="on")
	{
		$tabMenu["facturation"]=array(
			"icone"=>"mdi-receipt",
			"nom"=>$tabLang["lang_facturation"],
			"droit"=>"AccesFactures",
			"url"=>geturl("facturation","",""),
		);
	}
	$tabMenu["suivi"]=array(
		"icone"=>"mdi-book",
		"nom"=>$tabLang["lang_club"],
		"droit"=>"AccesSuivi",
		"url"=>geturl("suivi","index",""),
		"submenu"=>array(
			array("nom"=>"Mouvement","url"=>geturl("suivi","mouvement",""),"droit"=>"AccesSuiviMouvements"),
			array("nom"=>"Echéances","url"=>geturl("suivi","echeances",""),"droit"=>"AccesSuiviEcheances"),
			array("nom"=>"Echéances membres","url"=>geturl("suivi","suiviecheances",""),"droit"=>"AccesSuiviEcheancesMembres"),
			array("nom"=>"Vols","url"=>geturl("suivi","vols",""),"droit"=>"AccesSuiviVols"),
			array("nom"=>"Taxe AT","url"=>geturl("suivi","taxeat",""),"droit"=>"AccesSuiviTaxeAT"),
			array("nom"=>"Extrait Comptes","url"=>geturl("suivi","suivi",""),"droit"=>"AccesSuiviSuivi"),
			array("nom"=>"Comptes membres","url"=>geturl("suivi","liste",""),"droit"=>"AccesSuiviListeComptes"),
			array("nom"=>"Tableau de bord","url"=>geturl("suivi","tableaubord",""),"droit"=>"AccesSuiviTableauBord"),
			array("nom"=>"Bilan Comptable","url"=>geturl("suivi","bilan",""),"droit"=>"AccesSuiviBilan"),
		)
	);
	if ($MyOpt["module"]["aviation"]=="on")
	{
		$tabMenu["aviation"]=array(
			"icone"=>"mdi-airplane-takeoff",
			"nom"=>$tabLang["lang_flight"],
			"droit"=>"AccesVols",
			"url"=>geturl("aviation","vols",""),
			"submenu"=>array(
				array("nom"=>"Mes vols","url"=>geturl("aviation","vols",""),"droit"=>""),
				array("nom"=>"Carnet de route","url"=>geturl("aviation","carnet",""),"droit"=>""),
				array("nom"=>"Suivi membres","url"=>geturl("aviation","suivivols",""),"droit"=>"AccesSuiviVolsMembres"),
				array("nom"=>"Suivi annuel","url"=>geturl("aviation","suiviannuel",""),"droit"=>""),
				array("nom"=>"Livret de progression","url"=>geturl("aviation","syntheses",""),"droit"=>""),
				array("nom"=>"REX","url"=>geturl("aviation","rex",""),"droit"=>"AccesRex"),
			)
		);
		$tabMenu["suiviavions"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/ressources/img/icn32_titre.png",
			"icone"=>"mdi-airplane",
			"nom"=>$tabLang["lang_plane"],
			"droit"=>"AccesAvions",
			"url"=>geturl("ressources","index",""),
			"submenu"=>array(
				array("nom"=>"Avions","url"=>geturl("ressources","index",""),"droit"=>"AccesAvions"),
				array("nom"=>"Suivi Horamètres","url"=>geturl("ressources","horametre",""),"droit"=>"AccesSuiviHorametre"),
				array("nom"=>"Fiches de maintenance","url"=>geturl("ressources","fiche",""),"droit"=>"AccesFichesMaintenance"),
				array("nom"=>"Fiches à valider","url"=>geturl("ressources","validation",""),"droit"=>"AccesFichesValidation"),
				array("nom"=>"Maintenances","url"=>geturl("ressources","liste",""),"droit"=>"AccesMaintenances")
			)
		);


		$tabMenu["rex"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_rex.png",
			"icone"=>"mdi-alert",
			"nom"=>$tabLang["lang_rex"],
			"droit"=>"AccesRex",
			"url"=>geturl("aviation","rex",""),
		);
		$tabMenu["indicateurs"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_indicateurs.png",
			"icone"=>"mdi-trending-up",
			"nom"=>$tabLang["lang_followup"],
			"droit"=>"AccesIndicateurs",
			"url"=>geturl("aviation","indicateurs",""),
		);
	}
// ---- Phone
	// $tabMenuPhone["logs"]["icone"]="static/modules/logs/img/icn48_titre.png";
	// $tabMenuPhone["logs"]["nom"]="";
	// $tabMenuPhone["logs"]["droit"]="AccesVols";
	// $tabMenuPhone["logs"]["url"]="mod=logs&rub=search";
	
	$tabMenuPhone["reservations"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/reservations/img/icn48_titre.png",
		"icone"=>"mdi-calendar",
		"nom"=>$tabLang["lang_schedule"],
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","","start=today"),
	);
	$tabMenuPhone["suivivols"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn48_suivivols.png",
		"icone"=>"mdi-airplane-takeoff",
		"nom"=>$tabLang["lang_flight"],
		"droit"=>"AccesVols",
		"url"=>geturl("aviation","vols",""),
	);

?>