<?
// ---- Charge le template
  	// $tmpl_menu = new XTemplate("modules/membres/tmpl/menu.htm");
  	$tmpl_menu = LoadTemplate("menu","aviation");
	$tmpl_menu->assign("path_module",$module."/".$mod);

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");

// ---- Affiche les menus
	if (GetDroit("AccesSuiviVols"))
	{
		$tmpl_menu->parse("infos.suiviVols");
	}
	if (GetDroit("AccesSuiviHorametre"))
	{
		$tmpl_menu->parse("infos.horametre");
	}
	
	
// ---- Affiche le menu	
	$tmpl_menu->parse("infos");
	$aff_menu=$tmpl_menu->text("infos");

?>