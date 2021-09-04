<?
// ---- Desktop
	$tabMenu["reservations"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/reservations/img/icn32_titre.png",
		"nom"=>$tabLang["lang_schedule"],
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","","start=today"),
	);
	$tabMenu["bapteme"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_baptemes.png",
		"nom"=>$tabLang["lang_bapteme"],
		"droit"=>"AccesBaptemes",
		"url"=>geturl("aviation","baptemes",""),
	);
	$tabMenu["manifestattions"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/manifestations/img/icn32_titre.png",
		"nom"=>$tabLang["lang_meeting"],
		"droit"=>"AccesManifestations",
		"url"=>geturl("manifestations","",""),
	);
	if ($MyOpt["module"]["compta"]=="on")
	{
		$tabMenu["comptes"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/comptes/img/icn32_titre.png",
			"nom"=>$tabLang["lang_account"],
			"droit"=>"AccesCompte",
			"url"=>geturl("comptes","",""),
		);
	}
	if ($MyOpt["module"]["facture"]=="on")
	{
		$tabMenu["facturation"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/facturation/img/icn32_titre.png",
			"nom"=>$tabLang["lang_facturation"],
			"droit"=>"AccesFactures",
			"url"=>geturl("facturation","",""),
		);
	}
	$tabMenu["suivi"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/suivi/img/icn32_titre.png",
		"nom"=>$tabLang["lang_club"],
		"droit"=>"AccesSuivi",
		"url"=>geturl("suivi","",""),
	);
	if ($MyOpt["module"]["aviation"]=="on")
	{
		$tabMenu["suivivols"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_suivivols.png",
			"nom"=>$tabLang["lang_flight"],
			"droit"=>"AccesVols",
			"url"=>geturl("aviation","vols",""),
		);
		$tabMenu["suiviavions"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/ressources/img/icn32_titre.png",
			"nom"=>$tabLang["lang_plane"],
			"droit"=>"AccesAvions",
			"url"=>geturl("ressources","",""),
		);
		$tabMenu["rex"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/ressources/img/icn32_rex.png",
			"nom"=>$tabLang["lang_rex"],
			"droit"=>"AccesRex",
			"url"=>geturl("ressources","rex",""),
		);
		$tabMenu["indicateurs"]=array(
			"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_indicateurs.png",
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
		"nom"=>$tabLang["lang_schedule"],
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","","start=today"),
	);
	$tabMenuPhone["suivivols"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn48_suivivols.png",
		"nom"=>$tabLang["lang_flight"],
		"droit"=>"AccesVols",
		"url"=>geturl("aviation","vols",""),
	);

?>