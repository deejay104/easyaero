<?php
    if (!GetDroit("AccesFormations")) { FatalError("Accès non autorisé (AccesFormations)"); }

	require_once ($appfolder."/class/synthese.inc.php");
	require_once ($appfolder."/class/user.inc.php");


// ---- Récupère la liste des formations
    $lst=ListeObjets($sql,"livret",array("id"),array("actif"=>"oui"),array("dte_deb"));

	$tabTitre=array(
		"name" => array("aff"=>"Nom"),
		"surname" => array("aff"=>"Prénom"),
		"formation" => array("aff"=>"Formation"),
		"instructeur" => array("aff"=>"Instructeur Référent"),
		"status" => array("aff"=>"Status"),
		"dte_test" => array("aff"=>"Date Test"),
		"phase" => array("aff"=>"Phase"),
		"theory" => array("aff"=>"E-Learning"),
		"dte_theory" => array("aff"=>"Date Théorique"),
		"ffa" => array("aff"=>"FFA"),
//		"status" => array("aff"=>"Status","width"=>100, "mobile"=>"no"),
	);

	$tabValeur=array();
	foreach($lst as $fid=>$d)
	{
		$livret = new livret_class($fid,$sql);
		$pil = new user_class($livret->val("iduser"),$sql);
		$inst = new user_class($livret->val("idinstructeur"),$sql);

        if ($pil->actif!="non")
        {
            $tabValeur[$fid]["name"]["val"]=$pil->val("nom");
            $tabValeur[$fid]["name"]["aff"]=$pil->aff("nom");
            $tabValeur[$fid]["surname"]["val"]=$pil->val("prenom");
            $tabValeur[$fid]["surname"]["aff"]=$pil->aff("prenom");
            $tabValeur[$fid]["formation"]["val"]=$livret->val("idformation");
            $tabValeur[$fid]["formation"]["aff"]=$livret->displayDescription();
            $tabValeur[$fid]["instructeur"]["val"]=$inst->val("fullname");
            $tabValeur[$fid]["instructeur"]["aff"]=$inst->aff("fullname");
            $tabValeur[$fid]["status"]["val"]=$livret->val("status");
            $tabValeur[$fid]["status"]["aff"]=$livret->aff("status");
            $tabValeur[$fid]["dte_test"]["val"]=$livret->val("dte_test_practice");
            $tabValeur[$fid]["dte_test"]["aff"]=$livret->aff("dte_test_practice");
            $tabValeur[$fid]["dte_theory"]["val"]=$livret->val("dte_test_theory");
            $tabValeur[$fid]["dte_theory"]["aff"]=$livret->aff("dte_test_theory");
            $tabValeur[$fid]["phase"]["val"]=$livret->val("phase");
            $tabValeur[$fid]["phase"]["aff"]=$livret->aff("phase");
            $tabValeur[$fid]["theory"]["val"]=$livret->val("theory");
            $tabValeur[$fid]["theory"]["aff"]=$livret->aff("theory");
            $tabValeur[$fid]["ffa"]["val"]=$pil->val("licence");
            $tabValeur[$fid]["ffa"]["aff"]=$pil->aff("licence");
        }
	}

	$order="dte";
	$trie="i";

	// $tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"type=".$type."&dte=".$dte));

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>