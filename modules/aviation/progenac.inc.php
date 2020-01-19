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

// ---- Charge le template
	$tmpl_x->assign("path_module",$module."/".$mod);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

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
	addSubMenu("","Synthèses",geturl("aviation","syntheses"),"",false);
	addSubMenu("","Exercices",geturl("aviation","exercices"),"",false);
	addSubMenu("","Compétences",geturl("aviation","competences"),"",false);
	addSubMenu("","Progression",geturl("aviation","progenac"),"",true);
	addSubMenu("","Pannes",geturl("aviation","pannes"),"",false);
	affSubMenu();

// ---- Change membre
	$tmpl_x->assign("url",geturl("aviation","progenac",""));

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
	$lst=ListProgressionEnac($sql,$uid,"panne");

	$tabTitre=array(
		"id" => array("aff"=>"#","width"=>40),
		"module" => array("aff"=>"Module","width"=>150),
		"phase" => array("aff"=>"Phase","width"=>300),
		"description" => array("aff"=>"Description","width"=>500),
		"dte" => array("aff"=>"Date","width"=>120),
		"prog" => array("aff"=>"Progression","width"=>100),
	);
	$tabValeur=array();
	foreach($lst as $fid=>$d)
	{	
		$exo = new refenac_class($fid,$sql);

		$tabValeur[$fid]["id"]["val"]=$exo->val("refenac");
		$tabValeur[$fid]["module"]["val"]=$exo->val("module");
		$tabValeur[$fid]["module"]["aff"]=$exo->aff("module");
		$tabValeur[$fid]["phase"]["val"]=$exo->val("phase");
		$tabValeur[$fid]["phase"]["aff"]=$exo->aff("phase");
		$tabValeur[$fid]["description"]["val"]=$exo->val("description");
		$tabValeur[$fid]["description"]["aff"]=$exo->aff("description");

		// $tabValeur[$fid]["progression"]["val"]=$d["progression"];
		// $tabValeur[$fid]["progression"]["aff"]=($d["progression"]=="A") ? "<div style='padding-left:50px;'>A</div>" : "E";
		// $tabValeur[$fid]["progref"]["val"]=$d["progref"];
		// $tabValeur[$fid]["progref"]["aff"]=($d["progref"]=="A") ? "Acquis" : "Etude";

		// $tabValeur[$fid]["nbenac"]["val"]=$d["nbenac"];
		// $tabValeur[$fid]["nbprog"]["val"]=$d["nbprog"];
		
		if (($d["nbprog"]>=$d["nbenac"]) && ($d["nbenac"]>0))
		{
			// $tabValeur[$fid]["progression"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundError"];
			// $tabValeur[$fid]["progref"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundError"];

			$tabValeur[$fid]["prog"]["val"]="A";
			$tabValeur[$fid]["prog"]["align"]="center";
			$tabValeur[$fid]["prog"]["aff"]="<img src='".$MyOpt["host"]."/".$module."/".$mod."/img/icn16_ok.png' style='background-color:#".$MyOpt["styleColor"]["msgboxBackgroundOk"]."'>";

			$tabValeur[$fid]["dte"]["val"]=(strtotime($d["dteprog"])>0) ? strtotime($d["dteprog"]) : 99999999999 ;
			$tabValeur[$fid]["dte"]["aff"]=(strtotime($d["dteprog"])>0) ? sql2date($d["dteprog"],"jour") : " ";
		}
		else if ($d["nbenac"]==0)
		{
			$tabValeur[$fid]["prog"]["val"]="-";
			$tabValeur[$fid]["prog"]["align"]="center";
		}
		else
		{
			$tabValeur[$fid]["prog"]["val"]="E";
			$tabValeur[$fid]["prog"]["align"]="center";
			$tabValeur[$fid]["prog"]["aff"]="<img src='".$MyOpt["host"]."/".$module."/".$mod."/img/icn16_nc.png'>";
		}
	}

	if ((!isset($order)) || ($order=="")) { $order=""; }
	if ((!isset($trie)) || ($trie=="")) { $trie=""; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,"",0,""));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>