<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Affiche le menu

	addPageMenu("",$mod,"Mes vols",geturl("aviation","vols",""),"icn32_listevols.png",($rub=="vols") ? true : false);
	addPageMenu("",$mod,"Carnet de route",geturl("aviation","carnet",""),"icn32_carnetvols.png",($rub=="carnet") ? true : false);
	if (GetDroit("AccesSuiviVolsMembres"))
	{
		addPageMenu("",$mod,"Suivi membres",geturl("aviation","suivivols",""),"icn32_suivivols.png",($rub=="suivivols") ? true : false);
	}
	addPageMenu("",$mod,"Suivi annuel",geturl("aviation","suiviannuel",""),"icn32_suivivols.png",($rub=="suiviannuel") ? true : false);

	$sel=false;
	if ($rub=="syntheses")
	{
		$sel=true;
	}
	else if ($rub=="exercices")
	{
		$sel=true;
	}
	else if ($rub=="competences")
	{
		$sel=true;
	}
	else if ($rub=="progenac")
	{
		$sel=true;
	}
	else if ($rub=="pannes")
	{
		$sel=true;
	}
	addPageMenu("","aviation","Livret de progression",geturl("aviation","syntheses",""),"icn32_synthese.png",$sel);

?>