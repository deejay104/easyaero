<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = new XTemplate (MyRep("menu.htm"));
	$tmpl_menu->assign("path_module","$module/$mod");

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");
	$tmpl_x->assign("class_".$rub,"class='pageTitleSelected'");

	
// ---- Affiche les menus

	if ((GetDroit("AccesConfigComptes")) || (GetDroit("AccesConfigPostes")) || (GetDroit("AccesConfigTarifs")) || (GetDroit("AccesConfigPrevisions")))
	{
		$tmpl_menu->parse("infos.comptabilite");
	}

	if (GetDroit("AccesConfigComptes"))
	{
		$tmpl_x->parse("corps.comptes");
	}
	if (GetDroit("AccesConfigPostes"))
	{
		$tmpl_x->parse("corps.postes");
	}
	if (GetDroit("AccesConfigTarifs"))
	{
		$tmpl_x->parse("corps.tarifs");
	}
	if (GetDroit("AccesConfigPrevisions"))
	{
		$tmpl_x->parse("corps.previsions");
	}
	
	if (GetDroit("AccesConfigNavigation"))
	{
		$tmpl_menu->parse("infos.navigation");
	}
	if (GetDroit("AccesConfigInstruction"))
	{
		$tmpl_menu->parse("infos.exercices");
	}

	$tmpl_menu->parse("infos");
	$aff_menu.=$tmpl_menu->text("infos");
	
?>