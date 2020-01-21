<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	// $tmpl_menu = new XTemplate (MyRep("menu.htm"));
	// $tmpl_menu->assign("path_module","$module/$mod");
	// $tmpl_menu->assign("path_root",$MyOpt["host"]);

// ---- Sélectionne le menu courant
	// $tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");


	if (GetDroit("AccesAvions"))
	{
		// $tmpl_menu->parse("infos.listeavions");
		addPageMenu("",$mod,"Avions",geturl("ressources","",""),"icn32_liste.png",(($rub=="index") || ($rub=="detail")) ? true : false);
	}	
	if (GetDroit("AccesSuiviHorametre"))
	{
		addPageMenu("",$mod,"Suivi Horamètres",geturl("ressources","horametre",""),"icn32_carnetvols.png",($rub=="horametre") ? true : false);
	}
	if (GetDroit("AccesFichesMaintenance"))
	{
		// $tmpl_menu->parse("infos.fiche");
		addPageMenu("",$mod,"Fiches de maintenance",geturl("ressources","fiche",""),"icn32_ficheajout.png",(($rub=="fiche") || ($rub=="fichemaint")) ? true : false);
	}	
	
	if (GetDroit("AccesFichesValidation"))
	{
		// $tmpl_menu->parse("infos.validation");
		addPageMenu("",$mod,"Fiches à valider",geturl("ressources","validation",""),"icn32_ficheok.png",($rub=="validation") ? true : false);
	}	
	if (GetDroit("AccesMaintenances"))
	{
		// $tmpl_menu->parse("infos.maintenance");
		addPageMenu("",$mod,"Maintenances",geturl("ressources","liste",""),"icn32_maintenance.png",(($rub=="liste") || ($rub=="detailmaint")) ? true : false);
	}
	if (GetDroit("AccesRex"))
	{
		// $tmpl_menu->parse("infos.rex");
		addPageMenu("",$mod,"REX",geturl("ressources","rex",""),"icn32_rex.png",(($rub=="rex") || ($rub=="rexdetail")) ? true : false);
	}

?>