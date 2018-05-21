<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = new XTemplate (MyRep("menu.htm"));
	$tmpl_menu->assign("path_module","$module/$mod");

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");

	
// ---- Affiche les menus
	if ((GetDroit("AccesSuiviMouvements")) && ($theme!="phone"))
	{
		$tmpl_menu->parse("infos.mouvement");
	}
	if (GetDroit("AccesSuiviEcheances"))
	{
		$tmpl_menu->parse("infos.echeances");
	}
	if ((GetDroit("AccesSuiviVols")) && ($theme!="phone"))
	{
		$tmpl_menu->parse("infos.vols");
	}	
	if (GetDroit("AccesSuiviSuivi"))
	{
		$tmpl_menu->parse("infos.suivi");
	}
	if (GetDroit("AccesSuiviListeComptes"))
	{
		$tmpl_menu->parse("infos.liste");
	}
	if (GetDroit("AccesSuiviTableauBord"))
	{
		$tmpl_menu->parse("infos.tableaubord");
	}
	if (GetDroit("AccesSuiviBilan"))
	{
		$tmpl_menu->parse("infos.bilan");
	}
 
	$tmpl_menu->parse("infos");
	$aff_menu=$tmpl_menu->text("infos");
	
?>