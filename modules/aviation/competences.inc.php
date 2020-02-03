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
	$order=checkVar("order","varchar",12);
	$trie=checkVar("trie","varchar",1);
	$ts=checkVar("ts","numeric");

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
	addSubMenu("","Synthèses",geturl("aviation","syntheses","uid=".$uid),"",false);
	addSubMenu("","Exercices Pédagogique",geturl("aviation","exercices","uid=".$uid),"",false);
	addSubMenu("","Pannes",geturl("aviation","pannes","type=panne&uid=".$uid),"",false);
	addSubMenu("","Exercices",geturl("aviation","pannes","type=exercice&uid=".$uid),"",false);
	addSubMenu("","Compétences",geturl("aviation","competences","uid=".$uid),"",true);
	addSubMenu("","Progression ENAC",geturl("aviation","progenac","uid=".$uid),"",false);
	affSubMenu();

// ---- Change membre
	$tmpl_x->assign("url",geturl("aviation","competences",""));
	
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

// ---- Affiche la liste	
	$lst=ListCompetences($sql,$uid);

	$tabTitre=array(
		"competence" => array("aff"=>"Compétences","width"=>400),
		"dte" => array("aff"=>"Date","width"=>120),
		"progression" => array("aff"=>"Progression","width"=>100),
	);
	$tabValeur=array();
	foreach($lst as $fid=>$d)
	{	
		$exo = new exercice_conf_class($fid,$sql);

		$tabValeur[$fid]["competence"]["val"]=$exo->val("competence");
		$tabValeur[$fid]["competence"]["aff"]=$exo->aff("competence");
		$tabValeur[$fid]["dte"]["val"]=(strtotime($d["dte_acquis"])>0) ? strtotime($d["dte_acquis"]) : 99999999999 ;
		$tabValeur[$fid]["dte"]["aff"]=(strtotime($d["dte_acquis"])>0) ? sql2date($d["dte_acquis"],"jour") : " ";
		$tabValeur[$fid]["progression"]["val"]=$d["progression"];
		$tabValeur[$fid]["progression"]["aff"]=($d["progression"]=="A") ? "Acquis" : "Etude";
	}

	if ((!isset($order)) || ($order=="")) { $order="dte"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>