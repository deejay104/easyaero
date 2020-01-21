<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Affiche les menus

	if ((GetDroit("AccesSuiviMouvements")) && ($theme!="phone"))
	{
		addPageMenu("",$mod,"Mouvement",geturl("suivi","mouvement",""),"icn32_mouvement.png",($rub=="mouvement") ? true : false);
	}

	if (GetDroit("AccesSuiviEcheances"))
	{
		addPageMenu("",$mod,"Echéances",geturl("suivi","echeances",""),"icn32_echeances.png",($rub=="echeances") ? true : false);
	}

	if ((GetDroit("AccesSuiviVols")) && ($theme!="phone"))
	{
		addPageMenu("",$mod,"Vols",geturl("suivi","vols",""),"icn32_vols.png",($rub=="vols") ? true : false);
	}	

	if ((GetDroit("AccesSuiviTaxeAT")) && ($theme!="phone"))
	{
		addPageMenu("",$mod,"Taxe AT",geturl("suivi","taxeat",""),"icn32_taxeat.png",($rub=="taxeat") ? true : false);
	}	

	if (GetDroit("AccesSuiviSuivi"))
	{
		addPageMenu("",$mod,"Suivi",geturl("suivi","suivi",""),"icn32_suivi.png",($rub=="suivi") ? true : false);
	}

	if (GetDroit("AccesSuiviListeComptes"))
	{
		addPageMenu("",$mod,"Liste des comptes",geturl("suivi","liste",""),"icn32_comptes.png",($rub=="liste") ? true : false);
	}

	if (GetDroit("AccesSuiviTableauBord"))
	{
		addPageMenu("",$mod,"Tableau de bord",geturl("suivi","tableaubord",""),"icn32_tabbord.png",($rub=="tableaubord") ? true : false);
	}

	if (GetDroit("AccesSuiviBilan"))
	{
		addPageMenu("",$mod,"Bilan",geturl("suivi","bilan",""),"icn32_bilan.png",($rub=="bilan") ? true : false);
	}
 

	
?>