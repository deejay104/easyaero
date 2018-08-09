<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = new XTemplate (MyRep("menu.htm"));
	$tmpl_menu->assign("path_module","$module/$mod");

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");

	
	if (GetDroit("AccesAvions"))
	{
		$tmpl_menu->parse("infos.listeavions");
	}	
	if (GetDroit("AccesFichesMaintenance"))
	{
		$tmpl_menu->parse("infos.fiche");
	}	
	
	if (GetDroit("AccesFichesValidation"))
	{
		$tmpl_menu->parse("infos.validation");
	}	
	if (GetDroit("AccesMaintenances"))
	{
		$tmpl_menu->parse("infos.maintenance");
	}
	if (GetDroit("AccesRex"))
	{
		$tmpl_menu->parse("infos.rex");
	}
		
// ---- Affiche les menus


	$tmpl_menu->parse("infos");
	$aff_menu=$tmpl_menu->text("infos");
	
?>