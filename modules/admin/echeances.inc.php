<?php
	require_once("class/echeance.inc.php");
	require_once($appfolder."/class/echeance.inc.php");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesConfigEcheances")) { FatalError($tabLang["lang_accessdenied"]." (AccesConfigEcheances)"); }

// ---- Variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

	
// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Sauvegarde
	if ($fonc=="Enregistrer")
	{
		$form_description=checkVar("form_description","array");
		$form_data=checkVar("form_data","array");
		foreach($form_data as $id=>$d)
		{
			if (($id>0) || ($d["description"]!=""))
			{
				$ech = new echeancetype_class($id,$sql);
				foreach($d as $k=>$v)
				{
					$err=$ech->Valid($k,$v);
					affInformation($err,"error");
				}
				$ech->Save();
				affInformation("Echéances saubegardées","ok");
			}
		}
	}

// ---- Supprime une échéance
	if (($fonc=="delete") && ($id>0))
	{
		$ech = new echeancetype_class($id,$sql);
		$ech->Delete();
	}

	
	$tabTitre=array(
		"description"=>array(
			"aff"=>$tabLang["lang_description"],
			"width"=>220
		),
		"calendar"=>array(
			"aff"=>$tabLang["lang_calendar"],
			"width"=>150
		),
		"multi"=>array(
			"aff"=>$tabLang["lang_multi"],
			"width"=>70
		),
		"poste"=>array(
			"aff"=>$tabLang["lang_poste"],
			"width"=>300
		),
		"cout"=>array(
			"aff"=>$tabLang["lang_cost"],
			"width"=>60
		),
		"right"=>array(
			"aff"=>$tabLang["lang_right"],
			"width"=>150
		),
		"notif"=>array(
			"aff"=>$tabLang["lang_notif"],
			"width"=>70
		),
		"recipient"=>array(
			"aff"=>$tabLang["lang_recipient"],
			"width"=>200
		),
		"delay"=>array(
			"aff"=>$tabLang["lang_delay"],
			"width"=>90
		),
		"document"=>array(
			"aff"=>$tabLang["lang_document"],
			"width"=>70
		),
		"context"=>array(
			"aff"=>$tabLang["lang_contexte"],
			"width"=>110
		),
		"action"=>array(
			"aff"=>"&nbsp;",
			"width"=>20
		)
	);


	$tabValeur=array();

	// $lstFiche=GetActiveFiche($sql,$uid_avion);
	$lstEcheance=ListEcheanceType($sql,"");
	$lstEcheance[]["id"]=0;

	foreach($lstEcheance as $i=>$d)
	{
		$id=$d["id"];
		$ech = new echeancetype_class($id,$sql);

		$tabValeur[$i]["description"]["val"]=$ech->val("description");
		$tabValeur[$i]["description"]["aff"]=$ech->aff("description","form","form_data[".$id."]");

		$tabValeur[$i]["calendar"]["val"]=$ech->val("resa");
		$tabValeur[$i]["calendar"]["aff"]=$ech->aff("resa","form","form_data[".$id."]");

		$tabValeur[$i]["right"]["val"]=$ech->val("droit");
		$tabValeur[$i]["right"]["aff"]=$ech->aff("droit","form","form_data[".$id."]");

		$tabValeur[$i]["multi"]["val"]=$ech->val("multi");
		$tabValeur[$i]["multi"]["aff"]=$ech->aff("multi","form","form_data[".$id."]");
		$tabValeur[$i]["notif"]["val"]=$ech->val("notif");
		$tabValeur[$i]["notif"]["aff"]=$ech->aff("notif","form","form_data[".$id."]");
		$tabValeur[$i]["recipient"]["val"]=$ech->val("recipient");
		$tabValeur[$i]["recipient"]["aff"]=$ech->aff("recipient","form","form_data[".$id."]");
		$tabValeur[$i]["delay"]["val"]=$ech->val("delai");
		$tabValeur[$i]["delay"]["aff"]=$ech->aff("delai","form","form_data[".$id."]");
		$tabValeur[$i]["document"]["val"]=$ech->val("document");
		$tabValeur[$i]["document"]["aff"]=$ech->aff("document","form","form_data[".$id."]");
		
		$tabValeur[$i]["id"]["val"]=$id;
		$tabValeur[$i]["action"]["val"]=$id;
		$tabValeur[$i]["action"]["aff"]="<div id='action_".$id."' style='display:none;'><a id='edit_".$id."' class='imgDelete' href='".geturl("admin","echeances","fonc=delete&id=".$id)."'><i class='mdi mdi-window-close'></i></a></div>";


		$tabValeur[$i]["poste"]["val"]=$ech->val("poste");
		$tabValeur[$i]["poste"]["aff"]=$ech->aff("poste","form","form_data[".$id."]");
		$tabValeur[$i]["cout"]["val"]=$ech->val("cout");
		$tabValeur[$i]["cout"]["aff"]=$ech->aff("cout","form","form_data[".$id."]");
		$tabValeur[$i]["context"]["val"]=$ech->val("context");
		$tabValeur[$i]["context"]["aff"]=$ech->aff("context","form","form_data[".$id."]");
	}

	if ($order=="") { $order="context"; }
	if ($trie=="") { $trie="d"; }

// function AfficheTableau($tabValeur,$tabTitre=array(),$order="",$trie="",$url="",$start=0,$limit=-1,$nbline=0,$showicon="")
	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,-1,0,"action"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>