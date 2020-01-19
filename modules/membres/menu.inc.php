<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Livret de progression
	addPageMenu("","aviation","Livret de progression",geturl("aviation","syntheses","uid=".$id),"icn32_synthese.png");

// ---- Disponibilité instructeur
	require_once ($appfolder."/class/user.inc.php");
	$usrcus = new user_class($id,$sql,true);
	$usrcus->LoadRoles();

	if (($usrcus->TstDroit("TypeInstructeur")) && ((GetMyId($id)) || (GetDroit("ModifUserDisponibilite"))))
	{
	  	// $tmpl_menu->parse("infos.disponibilite");
		addPageMenu($corefolder,$mod,"Disponibilités",geturl("membres","disponibilite","id=".$id),"icn32_editer.png",($rub=="disponibilite") ? true : false);
	}


?>