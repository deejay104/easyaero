<?
// ---- Refuse l'acc�s en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = new XTemplate (MyRep("menu.htm"));
	$tmpl_menu->assign("path_module","$module/$mod");
	$tmpl_menu->assign("id", $id);

// ---- S�lectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");

	
// ---- Affiche les menus
	if (GetDroit("AccesConfigComptes"))
	{
		$tmpl_menu->parse("infos.comptes");
	}
	if (GetDroit("AccesConfigPostes"))
	{
		$tmpl_menu->parse("infos.postes");
	}
	if (GetDroit("AccesConfigTarifs"))
	{
		$tmpl_menu->parse("infos.tarifs");
	}
	if (GetDroit("AccesConfigPrevisions"))
	{
		$tmpl_menu->parse("infos.previsions");
	}
	if (GetDroit("AccesConfigDonneesUser"))
	{
		$tmpl_menu->parse("infos.utildonnees");
	}
	if (GetDroit("AccesConfigCrontab"))
	{
		$tmpl_menu->parse("infos.crontab");
	}

	$tmpl_menu->parse("infos");
	$aff_menu.=$tmpl_menu->text("infos");
	
?>