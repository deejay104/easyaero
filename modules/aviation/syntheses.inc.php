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
	
// ---- Change membre
	$tmpl_x->assign("url",geturl("aviation","syntheses","q=1"));
	
// ---- Liste des membres
	if (GetDroit("AccesSynthese"))
	{
			// $lst=ListActiveUsers($sql,"std");
		
			// foreach($lst as $i=>$tmpuid)
			// {
			  	// $resusr=new user_class($tmpuid,$sql);
	
				// $tmpl_x->assign("id_compte", $resusr->id);
				// $tmpl_x->assign("chk_compte", ($resusr->id==$uid) ? "selected" : "") ;
				// $tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
				// $tmpl_x->parse("corps.users.lst_users");
			// }
		$tmpl_x->assign("form_lstuser", AffListeMembres($sql,$uid,"form_id","","","std","non",array()));
		$tmpl_x->parse("corps.users");
	}

// ---- Liste des formations
	$lst=ListeLivret($sql,$uid);

	foreach($lst as $i=>$tmp)
	{
		$res=new livret_class($tmp["id"],$sql);

		if ($lid==0)
		{
			$lid=$res->id;
		}

		$tmpl_x->assign("id_livret", $res->id);
		$tmpl_x->assign("chk_livret", ($res->id==$lid) ? "selected" : "") ;
		$tmpl_x->assign("nom_livret", $res->displayDescription());
		$tmpl_x->parse("corps.lst_livret");
	}

	$livret=new livret_class($lid,$sql);

// ---- Affiche le sous menu
	if ($theme!="phone")
	{
		addPageMenu("","aviation","Formations",geturl("aviation","syntheses","lid=".$lid."&uid=".$uid),"",true);
		addPageMenu("","aviation","Pédagogique",geturl("aviation","exercices","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Pannes",geturl("aviation","pannes","type=panne&lid=".$lid."&&uid=".$uid),"",false);
		addPageMenu("","aviation","Exercices",geturl("aviation","pannes","type=exercice&lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Progression CBT",geturl("aviation","competences","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Progression ENAC",geturl("aviation","progenac","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Terrains",geturl("aviation","terrains","lid=".$lid."&id=".$uid),"",false);

	}

	if (GetDroit("ModifLivret"))
	{
		$tmpl_x->parse("corps.aff_editer");
	}
	
// ---- Information sur la formation
	$pil=new user_class($uid,$sql);

	$tmpl_x->assign("dte_deb",$livret->Aff("dte_deb"));
	$tmpl_x->assign("dte_fin",$livret->Aff("dte_fin"));
	$tmpl_x->assign("cr",$livret->Aff("cr","form"));
	$tmpl_x->assign("total_heure_dc",$livret->AffNbHeures("now","dc"));
	$tmpl_x->assign("total_heure_solo",$livret->AffNbHeures("now","solo"));
	$tmpl_x->assign("total_att_dc",$livret->NbAtt("now","dc"));
	$tmpl_x->assign("total_att_solo",$livret->NbAtt("now","solo"));
	$tmpl_x->assign("total_rmg_dc",$livret->NbRmg("now","dc"));
	$tmpl_x->assign("total_rmg_solo",$livret->NbRmg("now","solo"));

	$livret=new livret_class(0,$sql);
	$livret->Render("form","form");

// ---- Affiche la liste des exercices importants
	$lst=ListeExercicesProg($sql,$lid,$uid,"peda","oui");

	$tabTitre=array(
		// "id" => array("aff"=>"#","width"=>40),
		"exercice" => array("aff"=>"Exercice","width"=>400),
		"dte" => array("aff"=>"Date","width"=>120),
		"progression" => array("aff"=>"Progression","width"=>100),
		"progref" => array("aff"=>"Requis","width"=>100),
	);
	$tabValeur=array();

	foreach($lst as $fid=>$d)
	{	
		$exo = new exercice_conf_class($fid,$sql);

		$tabValeur[$fid]["id"]["val"]=$fid;
		$tabValeur[$fid]["exercice"]["val"]=$exo->val("description");
		$tabValeur[$fid]["exercice"]["aff"]=$exo->aff("description");
		$tabValeur[$fid]["dte"]["val"]=(strtotime($d["dte_acquis"])>0) ? strtotime($d["dte_acquis"]) : 99999999999 ;
		$tabValeur[$fid]["dte"]["aff"]=(strtotime($d["dte_acquis"])>0) ? "<a href='".geturl("aviation","synthese","id=".$d["idsynthese"])."'>".sql2date($d["dte_acquis"],"jour")."</a>" : " ";
		// $tabValeur[$fid]["dte"]["aff"]=$d["dte_acquis"];
		$tabValeur[$fid]["progression"]["val"]=$d["progression"];
		$tabValeur[$fid]["progression"]["aff"]="<i class='mdi ".(($d["progression"]=="A") ? "mdi-checkbox-marked-outline" : "mdi-checkbox-blank-outline")."' style='font-size:18px; ".(($d["progression"]=="A") ? "color:green; " : "color:#888888;")."'>";
		$tabValeur[$fid]["progression"]["align"]="center";
		$tabValeur[$fid]["progref"]["val"]=$d["progref"];
		$tabValeur[$fid]["progref"]["aff"]=(($d["progref"]=="A") ? "Acquis" : "Etude");
		
		if ((($d["progref"]!=$d["progression"]) && ($d["progref"]=="A")))
		{
			$tabValeur[$fid]["progression"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundError"];
			$tabValeur[$fid]["progref"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundError"];
		}
		else if ((($d["progression"]=="V")|| ($d["progression"]=="")) && ($d["progref"]=="E"))
		{
			$tabValeur[$fid]["progression"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundWarning"];
			$tabValeur[$fid]["progref"]["color"]=$MyOpt["styleColor"]["msgboxBackgroundWarning"];
		}
	}

	if ((!isset($order)) || ($order=="")) { $order="dte"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_exercices",AfficheTableau($tabValeur,$tabTitre,$order,$trie));	

// ---- Affiche la liste des vols	
	$lst=ListeMySynthese($sql,$uid,$lid);

	$tabTitre=array(
		"ress" => array("aff"=>"Avion","width"=>80),
		"dte" => array("aff"=>"Date","width"=>100),
		"inst" => array("aff"=>"Instructeur","width"=>200, "mobile"=>"no"),
		"module" => array("aff"=>"Module","width"=>100, "mobile"=>"no"),
		"refffa" => array("aff"=>"Reférence","width"=>100),
		"temps" => array("aff"=>"Temps","width"=>100),
		"conclusion" => array("aff"=>"Conclusion","width"=>100),
		"status" => array("aff"=>"Status","width"=>100, "mobile"=>"no"),
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
		$tabValeur[$fid]["temps"]["val"]=$fiche->temps();
		$tabValeur[$fid]["temps"]["val"]=AffTemps($fiche->temps());
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