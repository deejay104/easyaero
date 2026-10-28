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


// ---- Liste des formations
			// addPageMenu("",$mod,"Ajouter","#","",false);

// ---- Liste des formations
	$lst=ListFormation($sql);

	$formation=new formation_class(0,$sql);
	foreach($lst as $i=>$tmp)
	{
		$res=new formation_class($tmp["id"],$sql);

		if ($fid==0)
		{
			$fid=$res->id;
		}
		if ($res->id==$fid)
		{
			$formation=$res;
		}
		$tmpl_x->assign("id_formation", $res->id);
		$tmpl_x->assign("chk_formation", ($res->id==$fid) ? "selected" : "") ;
		$tmpl_x->assign("nom_formation", $res->val("description")." (v".$res->val("version").")");
		$tmpl_x->parse("corps.lst_formation");
	}

	if ($formation->val("status")=="draft")
	{
		$tmpl_x->parse("corps.btn_ajouter");
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
		"focus"=>array(
			"aff"=>"Focus",
			"width"=>40
		),
	);
	$order=checkVar("order","varchar",15,"refffa");
	$trie=checkVar("trie","varchar",1,"d");

	$tmpl_x->assign("aff_tableau",AfficheTableauRemote($tabTitre,geturlapi($mod,"updexo","","fid=".$fid),$order,$trie,true,25,"lstexo"));


	$exo=new exercice_conf_class(0,$sql);
	$exo->Render("form",($formation->val("status")=="draft") ? "form" : "read");
	$tmpl_x->assign("form_focus",$exo->aff("focus","form"));

	$prog=new exercice_prog_class(0,$sql);
	$prog->Render("form_1",($formation->val("status")=="draft") ? "form" : "read","form_prog_1","1_");
	$prog->Render("form_2",($formation->val("status")=="draft") ? "form" : "read","form_prog_2","2_");
	$prog->Render("form_3",($formation->val("status")=="draft") ? "form" : "read","form_prog_3","3_");
	$prog->Render("form_4",($formation->val("status")=="draft") ? "form" : "read","form_prog_4","4_");


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
