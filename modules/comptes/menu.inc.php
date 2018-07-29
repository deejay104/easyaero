<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = new XTemplate (MyRep("menu.htm"));
	$tmpl_menu->assign("path_module","$module/$mod");

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");
	$tmpl_menu->assign("form_id", $id);

	if (GetDroit("AccesSuiviListeComptes"))
	{
		$tmpl_menu->parse("infos.liste_compte");
	}
	if (GetDroit("AccesTransfert"))
	{
		$tmpl_menu->parse("infos.transfert");
	}
	if (GetDroit("AccesCredite"))
	{
		$tmpl_menu->parse("infos.credite");
	}

	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone") && ($rub=="index"))
	{
		$tmpl_menu->parse("infos.affiche_signature");
	}

// ---- Affiche les menus
	$tmpl_menu->parse("infos");
	$aff_menu=$tmpl_menu->text("infos");
	
?>