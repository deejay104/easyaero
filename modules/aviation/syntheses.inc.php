<?php
/*
	Easy Aero
    Copyright (C) 2018 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php
	// if (!GetDroit("AccesSynthese")) { FatalError("Accès non autorisé (AccesSynthese)"); }

	require_once ($appfolder."/class/synthese.inc.php");
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Initialise les variables
	$uid=checkVar("uid","numeric");

	if ($uid==0)
	{
		$uid=$gl_uid;
	}
	if ((!GetDroit("AccesSynthese")) && ($uid!=$gl_uid))
	{
		$uid=$gl_uid;
	}

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);
	
// ---- Affiche le sous menu
	if ($theme!="phone")
	{
		addSubMenu("","Synthèses",geturl("aviation","syntheses","uid=".$uid),"",true);
		addSubMenu("","Exercices Pédagogique",geturl("aviation","exercices","uid=".$uid),"",false);
		addSubMenu("","Pannes",geturl("aviation","pannes","type=panne&uid=".$uid),"",false);
		addSubMenu("","Exercices",geturl("aviation","pannes","type=exercice&uid=".$uid),"",false);
		addSubMenu("","Compétences",geturl("aviation","competences","uid=".$uid),"",false);
		addSubMenu("","Progression ENAC",geturl("aviation","progenac","uid=".$uid),"",false);
		affSubMenu();
	}
// ---- Change membre
	$tmpl_x->assign("url",geturl("aviation","syntheses",""));
	
// ---- Liste des membres
	if (GetDroit("AccesSynthese"))
	{
			$lst=ListActiveUsers($sql,"std");
		
			foreach($lst as $i=>$tmpuid)
			{
			  	$resusr=new user_class($tmpuid,$sql);
	
				$tmpl_x->assign("id_compte", $resusr->id);
				$tmpl_x->assign("chk_compte", ($resusr->id==$uid) ? "selected" : "") ;
				$tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
				$tmpl_x->parse("corps.users.lst_users");
			}
			$tmpl_x->parse("corps.users");
	}

// ---- Information sur la formation
	$pil=new user_class($uid,$sql);
	$tmpl_x->assign("dte_deb",sql2date($pil->DebFormation(),"jour"));
	$tmpl_x->assign("total_heure_dc",$pil->AffNbHeuresSynthese(date("Y-m-d"),"dc"));
	$tmpl_x->assign("total_heure_solo",$pil->AffNbHeuresSynthese(date("Y-m-d"),"solo"));
	$tmpl_x->assign("total_att_dc",$pil->NbAtt("dc"));
	$tmpl_x->assign("total_att_solo",$pil->NbAtt("solo"));
	$tmpl_x->assign("total_rmg_dc",$pil->NbRmg("dc"));
	$tmpl_x->assign("total_rmg_solo",$pil->NbRmg("solo"));
	
// ---- Affiche la liste	
	$lst=ListMySynthese($sql,$uid);

	$tabTitre=array(
		"ress" => array("aff"=>"Avion","width"=>80),
		"dte" => array("aff"=>"Date","width"=>100),
		"inst" => array("aff"=>"Instructeur","width"=>200, "mobile"=>"no"),
		"module" => array("aff"=>"Module","width"=>100, "mobile"=>"no"),
		"refffa" => array("aff"=>"Reférence","width"=>100),
		"conclusion" => array("aff"=>"Conclusion","width"=>100),
		"status" => array("aff"=>"Status","width"=>100),
	);

	$tabValeur=array();
	foreach($lst as $fid=>$d)
	{
		$fiche = new synthese_class($fid,$sql);
		$resa=new resa_class($fiche->val("idvol"),$sql);
		$ress=new ress_class($fiche->val("uid_avion"),$sql);
		$inst=new user_class($fiche->val("uid_instructeur"),$sql);
		
		$tabValeur[$fid]["ress"]["val"]=$ress->val("immatriculation");
		$tabValeur[$fid]["ress"]["aff"]="<a href='".$MyOpt["host"]."/aviation/synthese?id=".$fid."&uid=".$uid."'>".$ress->val("immatriculation")."</a>";
		$tabValeur[$fid]["dte"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$fid]["dte"]["aff"]="<a href='".$MyOpt["host"]."/aviation/synthese?id=".$fid."&uid=".$uid."'>".sql2date($resa->dte_deb,"jour")."</a>";
		
		$tabValeur[$fid]["inst"]["val"]=$inst->val("fullname");
		$tabValeur[$fid]["inst"]["aff"]="<a href='".$MyOpt["host"]."/aviation/synthese?id=".$fid."&uid=".$uid."'>".$inst->val("fullname")."</a>";
	

		$tabValeur[$fid]["module"]["val"]=$fiche->val("module");
		$tabValeur[$fid]["module"]["val"]=$fiche->aff("module");
		$tabValeur[$fid]["refffa"]["val"]=$fiche->val("refffa");
		$tabValeur[$fid]["refffa"]["val"]=$fiche->aff("refffa");
		$tabValeur[$fid]["conclusion"]["val"]=$fiche->val("conclusion");
		$tabValeur[$fid]["conclusion"]["val"]=$fiche->aff("conclusion");
		$tabValeur[$fid]["status"]["val"]=$fiche->val("status");
		$tabValeur[$fid]["status"]["val"]=$fiche->aff("status");
	}

	if ((!isset($order)) || ($order=="")) { $order="dte"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="i"; }

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