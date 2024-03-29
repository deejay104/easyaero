<?php
/*
    Easy-Aero
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
	if (!GetDroit("AccesConfigExercices")) { FatalError("Accès non autorisé (AccesConfigExercices)"); }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Vérifie les variables
	$form_data=checkVar("form_data","array");
	$fid=checkVar("fid","numeric");

	$tmpl_x->assign("url",geturl("admin","formations","page=exercices"));

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Supprime un poste
	if (($fonc=="delete") && (GetDroit("SupprimeExercice")))
	{
		$id=checkVar("id","numeric");
		if ($id>0)
		{
			$exo=new exercice_conf_class($id,$sql);
			$exo->Delete();
		}
	}


// ---- Liste des formations
			// addPageMenu("",$mod,"Ajouter","#","",false);

// ---- Liste des formations
	$lst=ListFormation($sql);

	foreach($lst as $i=>$tmp)
	{
		$res=new formation_class($tmp["id"],$sql);

		if ($fid==0)
		{
			$fid=$res->id;
		}

		$tmpl_x->assign("id_formation", $res->id);
		$tmpl_x->assign("chk_formation", ($res->id==$fid) ? "selected" : "") ;
		$tmpl_x->assign("nom_formation", $res->val("description"));
		$tmpl_x->parse("corps.lst_formation");
	}
	
// ---- Affiche la page demandée
	$tabTitre=array(
		"id"=>array(
			"aff"=>"#id",
			"width"=>40
		),
		"description"=>array(
			"aff"=>"Description",
			"width"=>400
		),
		"type"=>array(
			"aff"=>"Type",
			"width"=>100
		),
		"refffa"=>array(
			"aff"=>"Ref",
			"width"=>70
		),
		"progression"=>array(
			"aff"=>"Progression",
			"width"=>200
		),
		"refenac"=>array(
			"aff"=>"ENAC",
			"width"=>50
		),
		"compcat"=>array(
			"aff"=>"Catégorie",
			"width"=>200
		),
		"competence"=>array(
			"aff"=>"Compétence",
			"width"=>200
		),
		"action"=>array(
			"aff"=>"",
			"width"=>40
		),
	);
	$order=checkVar("order","varchar",15,"refffa");
	$trie=checkVar("trie","varchar",1,"d");
	$ts=checkVar("ts","numeric");
	$tabsearch=checkVar("tabsearch","array");
	
	if ($order=="") { $order="refffa"; }

	$search=array("actif"=>"oui","idformation"=>$fid);
	$lst=ListExercicesConf($sql,$search,array($order));
	$totligne=count($lst);
	$tl=$totligne;
	$tabValeur=array();

	foreach($lst as $i=>$d)
	{
		$exo=new exercice_conf_class($d["id"],$sql);

		$tabValeur[$i]["id"]["val"]=$d["id"];
		$tabValeur[$i]["description"]["val"]=$exo->val("description");
		$tabValeur[$i]["type"]["val"]=$exo->val("type");
		$tabValeur[$i]["type"]["aff"]=$exo->aff("type");
		$tabValeur[$i]["refffa"]["val"]=$exo->val("refffa");
		$tabValeur[$i]["refenac"]["val"]=$exo->val("refenac");
		$tabValeur[$i]["compcat"]["val"]=$exo->val("compcat");
		$tabValeur[$i]["competence"]["val"]=$exo->val("competence");

		$tabValeur[$i]["action"]["val"]=$sql->data["id"];
		$tabValeur[$i]["action"]["aff"] ="";
		
		if (GetDroit("ModifExercice"))
		{
			$tabValeur[$i]["action"]["aff"].="<div id='action_".$d["id"]."' style='display:none;'>";
			$tabValeur[$i]["action"]["aff"].="<a id='edit_".$d["id"]."' href='#'><i class='mdi mdi-pencil'></i></a>";
			// $tabValeur[$i]["action"]["aff"].="<a href='index.php?mod=admin&rub=exercices&fonc=delete&id=".$d["id"]."'><index.php?mod=admin&rub=exercices&fonc=delete&id=".$d["id"]."'><i class='mdi mdi-close'></i></a>";
			$tabValeur[$i]["action"]["aff"].="<a id='del_".$d["id"]."' href='#'><i class='mdi mdi-close'></i></a>";
			$tabValeur[$i]["action"]["aff"].="</div>";
		}

		$lstp=ListProgression($sql,$d["id"]);
		$tabValeur[$i]["progression"]["val"]="";
		foreach($lstp as $ii=>$dd)
		{
			$prog=new exercice_prog_class($dd["id"],$sql);

			$tabValeur[$i]["progression"]["val"].=$prog->val("refffa").": ".$prog->val("progression")." / ";
		}
		$tmpl_x->assign("lst_id",$d["id"]);
		$tmpl_x->parse("corps.lst_edit");
	}

// function AfficheTableau($tabValeur,$tabTitre=array(),$order="",$trie="",$url="",$start=0,$limit=-1,$nbline=0,$showicon="")
	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,-1,$totligne,"action"));



	$exo=new exercice_conf_class(0,$sql);
	$exo->Render("form","form");
	$prog=new exercice_prog_class(0,$sql);
	$prog->Render("form_1","form","form_prog_1","1_");
	$prog->Render("form_2","form","form_prog_2","2_");
	$prog->Render("form_3","form","form_prog_3","3_");
	$prog->Render("form_4","form","form_prog_4","4_");


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
