<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
  	$tmpl_menu = LoadTemplate("menu");
	$tmpl_menu->assign("path_module","$module/$mod");

// ---- Sélectionne le menu courant
	$tmpl_menu->assign("class_".$rub,"class='pageTitleSelected'");

	require_once ($appfolder."/class/user.inc.php");
	$usrcus = new user_class($id,$sql,true);
	$usrcus->LoadRoles();

	$tmpl_menu->assign("form_id", $id);
	if (($usrcus->TstDroit("TypeInstructeur")) && ((GetMyId($id)) || (GetDroit("ModifUserDisponibilite"))))
	{
	  	$tmpl_menu->parse("infos.disponibilite");
	}

// ---- Affiche le menu	
	$tmpl_menu->parse("infos");
	$aff_menu.=$tmpl_menu->text("infos");


?>