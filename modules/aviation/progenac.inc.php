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
	$lid=checkVar("lid","numeric");
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
	addPageMenu("","aviation","Formations",geturl("aviation","syntheses","lid=".$lid."&uid=".$uid),"",false);
	addPageMenu("","aviation","Pédagogique",geturl("aviation","exercices","lid=".$lid."&uid=".$uid),"",false);
	addPageMenu("","aviation","Pannes",geturl("aviation","pannes","type=panne&lid=".$lid."&&uid=".$uid),"",false);
	addPageMenu("","aviation","Exercices",geturl("aviation","pannes","type=exercice&lid=".$lid."&uid=".$uid),"",false);
	addPageMenu("","aviation","Progression CBT",geturl("aviation","competences","lid=".$lid."&uid=".$uid),"",false);
	addPageMenu("","aviation","Progression ENAC",geturl("aviation","progenac","lid=".$lid."&uid=".$uid),"",true);

// ---- Change membre
	$tmpl_x->assign("url",geturl("aviation","progenac","q=1"));

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

		if (($d["nbprog"]>=$d["nbenac"]) && ($d["nbenac"]>0))
		{
			$tabValeur[$fid]["prog"]["val"]="A";
			$tabValeur[$fid]["prog"]["align"]="center";
			$tabValeur[$fid]["prog"]["aff"]="<img src='".$MyOpt["host"]."/".$module."/".$mod."/img/icn16_ok.png' style='background-color:#".$MyOpt["styleColor"]["msgboxBackgroundOk"]."'>";
			$tabValeur[$fid]["prog"]["aff"]="<i style='font-size:18px; color:green;' class='mdi mdi-checkbox-marked-outline'></i>";

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
			$tabValeur[$fid]["prog"]["aff"]="<i style='font-size:18px;color:#888888;' class='mdi mdi-checkbox-blank-outline'></i>";
		}
		
		
	}

	$tabOrder=array("module"=>"asc","phase"=>"asc");
	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$tabOrder,""));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>