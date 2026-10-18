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


	if (!GetDroit("AccesConfigFormations")) { FatalError("Accès non autorisé (AccesConfigFormations)"); }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Vérifie les variables
	$form_data=checkVar("form_data","array");

	$tmpl_x->assign("url",geturl("admin","formations","page=formationslst"));

// ---- Enregistre les modifications
	if ( ($fonc=="Enregistrer") && (GetDroit("ModifFormation")) && (is_array($form_data)) )
	{
	  	foreach($form_data as $id=>$d)
	  	{
			if ($d["description"]!="")
			{
				$ref=new formation_class($id,$sql);
				$ref->Valid("description",$d["description"]);				
				$ref->Save();
			}
		}
		header('Location: /admin/formations?page=formationslst', true, 303);
    	exit;
	}

// ---- Supprime une formation
	if (($fonc=="delete") && (GetDroit("SupprimeFormation")))
	{
		$id=checkVar("id","numeric");
		if ($id>0)
		{
			$exo=new formation_class($id,$sql);
			$exo->Delete();
		}
		header('Location: /admin/formations?page=formationslst', true, 303);
    	exit;
	}

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste
	$tabTitre=array(
		"description"=>array(
			"aff"=>"Description",
		),
		"version"=>array(
			"aff"=>"Version",
		),
		"status"=>array(
			"aff"=>"Status",
		),
		"encours"=>array(
			"aff"=>"En cours",
		),
	);
	$order=checkVar("order","varchar",10,"refffa");	
	$trie=checkVar("trie","varchar",1,"d");
	

	$tmpl_x->assign("aff_tableau",AfficheTableauRemote($tabTitre,geturlapi($mod,"formationlst","",""),$order,$trie,"",25,"lstformation"));


	$formation=new formation_class(0,$sql);
	$formation->render("form","form");

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
