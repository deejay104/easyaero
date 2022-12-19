<?php

	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Comptes","url"=>geturl("admin","comptes",""),"droit"=>"AccesConfigComptes");
	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Comptabilité","url"=>geturl("admin","postes",""),"droit"=>"AccesConfigPostes");
	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Tarifs","url"=>geturl("admin","tarifs",""),"droit"=>"AccesConfigTarifs");
	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Prévisions","url"=>geturl("admin","previsions",""),"droit"=>"AccesConfigPrevisions");
	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Navigation","url"=>geturl("admin","navigation",""),"droit"=>"AccesConfigNavigation");
	$tabMenu["configuration"]["submenu"][]=array("nom"=>"Formations","url"=>geturl("admin","formations","page=formationslst"),"droit"=>"AccesConfigInstruction");
	
	// $tabMenu["configuration"]["submenu"][]=array("nom"=>"Vols","url"=>geturl("admin","references",""),"droit"=>"AccesConfigInstruction");
	// $tabMenu["configuration"]["submenu"][]=array("nom"=>"Exercices","url"=>geturl("admin","exercices",""),"droit"=>"AccesConfigInstruction");
	// $tabMenu["configuration"]["submenu"][]=array("nom"=>"Prog ENAC","url"=>geturl("admin","refenac",""),"droit"=>"AccesConfigInstruction");

?>