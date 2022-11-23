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



?>