<?
	$tmpl_custom = new XTemplate (MyRep("custom.htm"));
	$tmpl_x->assign("path_module",$corefolder."/".$module."/".$mod);

		  
$MyOpt["module"]["compta"]="on";
$MyOpt["module"]["aviation"]="on";

// ---- Charge l'utilisateur
	require_once ($appfolder."/class/user.inc.php");
	$usrcus = new user_class($id,$sql,true);
  	if (GetModule("aviation"))
	{
		$usrcus->LoadLache();
	}

// ---- Sauvegarde
	// Sauvegarde les infos spécifiques
	if (($fonc=="Enregistrer") && ((GetMyId($id)) || (GetDroit("ModifUserSauve"))))
	{
		// Sauvegarde les données
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$usrcus->Valid($k,$v);
		  	}
		}
		$usrcus->Save();		
	}
	// Sauvegarde le lache
	if (($fonc=="Enregistrer") && ($id>0) && (GetDroit("ModifUserLache")))
	{
		if (!is_array($form_lache))
		{
			$form_lache=array();
		}
		
		$msg_erreur.=$usrcus->SaveLache($form_lache);
		$usrcus->LoadLache();
	}
	
// ---- Données Utilisateurs

	foreach($usrcus->data as $k=>$v)
	  { $tmpl_custom->assign("form_$k", $usrcus->aff($k,$typeaff)); }

	$tmpl_custom->parse("left.type");

	if (((GetDroit("ModifUserDecouvert")) || (GetMyId($id))) && (GetModule("compta")))
	  { $tmpl_custom->parse("left.decouvert"); }

	if ((((GetDroit("ModifUserTarif")) || (GetMyId($id))) && (GetModule("compta"))) && (GetModule("aviation")))
	  { $tmpl_custom->parse("left.tarif"); }
  
  	if (GetModule("aviation"))
	{
	  	$tmpl_custom->parse("left.mod_aviation_lache");
	}

// ---- Affiche la page
	$tmpl_custom->parse("left");
	$left=$tmpl_custom->text("left");

	$tmpl_custom->parse("right");
	$right=$tmpl_custom->text("right");

?>