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
	if (!GetDroit("AccesConfigFormations")) { FatalError("Accès non autorisé (AccesConfigFormations)"); }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Vérifie les variables
	$form_data=checkVar("form_data","array");

	$tmpl_x->assign("url",geturl("admin","references",""));

// ---- Enregistre les modifications
	if (($fonc=="Enregistrer") && (GetDroit("ModifFormation")) && (is_array($form_data)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
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
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
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
		"action"=>array(
			"aff"=>"",
			"width"=>24
		),
	);
	$order=checkVar("order","varchar",10,"refffa");	
	$trie=checkVar("trie","varchar",1,"d");
	

	$lst=ListFormation($sql);
	
	$tabValeur=array();
	foreach($lst as $i=>$d)
	{
		$ref=new formation_class($d["id"],$sql);

		$tabValeur[$i]["id"]["val"]=$d["id"];
		$tabValeur[$i]["description"]["val"]=$ref->val("description");
		$tabValeur[$i]["description"]["aff"]=$ref->aff("description","form","form_data[".$d["id"]."]");

		$tabValeur[$i]["action"]["val"]=$d["id"];
		$tabValeur[$i]["action"]["aff"]="<a id='del_".$d["id"]."' href='".geturl("admin","formations","fonc=delete&id=".$d["id"])."' style='display:none;'><i class='mdi mdi-close'></i></a>";
	}

	$ref=new formation_class(0,$sql);	
	$tabValeur[0]["id"]["val"]=0;
	$tabValeur[0]["description"]["val"]="";
	$tabValeur[0]["description"]["aff"]=$ref->aff("description","form","form_data[0]");

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,-1,0,"del"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
