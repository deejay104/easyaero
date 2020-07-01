<?
// ---- Desktop
	$tabMenu["reservations"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/reservations/img/icn32_titre.png",
		"nom"=>"Réservations",
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","","start=today"),
	);
	$tabMenu["bapteme"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_baptemes.png",
		"nom"=>"Baptèmes",
		"droit"=>"AccesBaptemes",
		"url"=>geturl("aviation","baptemes",""),
	);
	$tabMenu["manifestattions"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/manifestations/img/icn32_titre.png",
		"nom"=>"Manifestations",
		"droit"=>"AccesManifestations",
		"url"=>geturl("manifestations","",""),
	);
	$tabMenu["comptes"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/comptes/img/icn32_titre.png",
		"nom"=>"Comptes",
		"droit"=>"AccesCompte",
		"url"=>geturl("comptes","",""),
	);
	$tabMenu["suivi"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/suivi/img/icn32_titre.png",
		"nom"=>"Suivi Club",
		"droit"=>"AccesSuivi",
		"url"=>geturl("suivi","",""),
	);
	$tabMenu["suivivols"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_suivivols.png",
		"nom"=>"Suivi des Vols",
		"droit"=>"AccesVols",
		"url"=>geturl("aviation","vols",""),
	);
	$tabMenu["suiviavions"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/ressources/img/icn32_titre.png",
		"nom"=>"Suivi des Avions",
		"droit"=>"AccesAvions",
		"url"=>geturl("ressources","",""),
	);
	$tabMenu["rex"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/ressources/img/icn32_rex.png",
		"nom"=>"REX",
		"droit"=>"AccesRex",
		"url"=>geturl("ressources","rex",""),
	);
	$tabMenu["indicateurs"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn32_indicateurs.png",
		"nom"=>"Indicateurs",
		"droit"=>"AccesIndicateurs",
		"url"=>geturl("aviation","indicateurs",""),
	);
	
// ---- Phone
	// $tabMenuPhone["logs"]["icone"]="static/modules/logs/img/icn48_titre.png";
	// $tabMenuPhone["logs"]["nom"]="";
	// $tabMenuPhone["logs"]["droit"]="AccesVols";
	// $tabMenuPhone["logs"]["url"]="mod=logs&rub=search";
	
	$tabMenuPhone["reservations"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/reservations/img/icn48_titre.png",
		"nom"=>"Réservations",
		"droit"=>"AccesReservations",
		"url"=>geturl("reservations","","start=today"),
	);
	$tabMenuPhone["suivivols"]=array(
		"icone"=>$MyOpt["host"]."/"."static/modules/aviation/img/icn48_suivivols.png",
		"nom"=>"Suivi des Vols",
		"droit"=>"AccesVols",
		"url"=>geturl("aviation","vols",""),
	);

?>