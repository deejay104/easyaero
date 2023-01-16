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
	if (!GetDroit("AccesConfigReferences")) { FatalError("Accès non autorisé (AccesConfigReferences)"); }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Vérifie les variables
	$form_data=checkVar("form_data","array");
	$fid=checkVar("fid","numeric");

	$tmpl_x->assign("url",geturl("admin","formations","page=references"));

// ---- Enregistre les modifications
	if (($fonc=="Enregistrer") && (is_array($form_data)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
	  	foreach($form_data as $id=>$d)
	  	{
			if ($d["refffa"]!="")
			{
				$ref=new reference_class($id,$sql);
				$ref->Valid("idformation",$fid);
				$ref->Valid("refffa",$d["refffa"]);
				$ref->Valid("theme",$d["theme"]);
				
				$ref->Save();
			}
		}
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Supprime une référence
	if ($fonc=="delete")
	{
		$id=checkVar("id","numeric");
		if ($id>0)
		{
			$ref=new reference_class($id,$sql);
			$ref->Delete();
		}
	}

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
	
// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste
	$tabTitre=array(
		"refffa"=>array(
			"aff"=>"Référence",
			"width"=>120
		),
		"theme"=>array(
			"aff"=>"Thème",
			"width"=>500
		),
		"action"=>array(
			"aff"=>"",
			"width"=>24
		),
	);
	$order=checkVar("order","varchar",10,"refffa");
	$trie=checkVar("trie","varchar",1,"d");
	

	$lst=ListReference($sql,$fid);
	
	$tabValeur=array();
	foreach($lst as $i=>$d)
	{
		$ref=new reference_class($d["id"],$sql);

		$tabValeur[$i]["id"]["val"]=$d["id"];
		$tabValeur[$i]["refffa"]["val"]=$ref->val("refffa");
		$tabValeur[$i]["refffa"]["aff"]=$ref->aff("refffa","form","form_data[".$d["id"]."]");
		$tabValeur[$i]["theme"]["val"]=$ref->val("theme");
		$tabValeur[$i]["theme"]["aff"]=$ref->aff("theme","form","form_data[".$d["id"]."]");

		$tabValeur[$i]["action"]["val"]=$d["id"];
		$tabValeur[$i]["action"]["aff"]="<a id='del_".$d["id"]."' href='".$MyOpt["host"]."/admin/references?fonc=delete&id=".$d["id"]."' style='display:none;'><i class='mdi mdi-close' style='font-size:20px;'></i></a>";
	}

	$ref=new reference_class(0,$sql);	
	$tabValeur[0]["id"]["val"]=0;
	$tabValeur[0]["refffa"]["val"]="";
	$tabValeur[0]["refffa"]["aff"]=$ref->aff("refffa","form","form_data[0]");
	$tabValeur[0]["theme"]["val"]="";
	$tabValeur[0]["theme"]["aff"]=$ref->aff("theme","form","form_data[0]");

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,-1,0,"del"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
