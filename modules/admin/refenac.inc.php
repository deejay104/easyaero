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

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("refenac.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Vérifie les variables
	$checktime=checkVar("checktime","numeric");
	$form_data=checkVar("form_data","array");

// ---- Enregistre les modifications
	if (($fonc=="Enregistrer") && (is_array($form_data)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
	  	foreach($form_data as $id=>$d)
	  	{
			if ($d["description"]!="")
			{
				$ref=new refenac_class($id,$sql);
				$ref->Valid("refenac",$d["refenac"]);
				$ref->Valid("description",$d["description"]);
				$ref->Valid("module",$d["module"]);
				$ref->Valid("phase",$d["phase"]);
				
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
			$ref=new refenac_class($id,$sql);
			$ref->Delete();
		}
	}


// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

	$tabTitre=array(
		"module"=>array(
			"aff"=>"Module",
			"width"=>200
		),
		"phase"=>array(
			"aff"=>"Phase",
			"width"=>310
		),
		"description"=>array(
			"aff"=>"Description",
			"width"=>500
		),
		"refenac"=>array(
			"aff"=>"Référence",
			"width"=>100
		),
		"action"=>array(
			"aff"=>"",
			"width"=>24
		),
	);
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar",1);
	

	$lst=ListRefEnac($sql);
	
	$tabValeur=array();
	foreach($lst as $i=>$d)
	{
		$ref=new refenac_class($d["id"],$sql);

		$tabValeur[$i]["id"]["val"]=$d["id"];
		$tabValeur[$i]["module"]["val"]=$ref->val("module");
		$tabValeur[$i]["module"]["aff"]=$ref->aff("module","form","form_data[".$d["id"]."]");
		$tabValeur[$i]["phase"]["val"]=$ref->val("phase");
		$tabValeur[$i]["phase"]["aff"]=$ref->aff("phase","form","form_data[".$d["id"]."]");
		$tabValeur[$i]["description"]["val"]=$ref->val("description");
		$tabValeur[$i]["description"]["aff"]=$ref->aff("description","form","form_data[".$d["id"]."]");
		$tabValeur[$i]["refenac"]["val"]=$ref->val("refenac");
		$tabValeur[$i]["refenac"]["aff"]=$ref->aff("refenac","form","form_data[".$d["id"]."]");

		$tabValeur[$i]["action"]["val"]=$d["id"];
		$tabValeur[$i]["action"]["aff"]="<a id='del_".$d["id"]."' href='index.php?mod=admin&rub=refenac&fonc=delete&id=".$d["id"]."' class='imgDelete' style='display:none;'><img src='".$corefolder."/".$module."/".$mod."/img/icn16_supprimer.png'></a></div>";
	}

	$ref=new refenac_class(0,$sql);
	$tabValeur[0]["id"]["val"]=0;
	$tabValeur[0]["module"]["val"]=$ref->val("module");
	$tabValeur[0]["module"]["aff"]=$ref->aff("module","form","form_data[0]");
	$tabValeur[0]["phase"]["val"]=$ref->val("phase");
	$tabValeur[0]["phase"]["aff"]=$ref->aff("phase","form","form_data[0]");
	$tabValeur[0]["description"]["val"]=$ref->val("description");
	$tabValeur[0]["description"]["aff"]=$ref->aff("description","form","form_data[0]");
	$tabValeur[0]["refenac"]["val"]=$ref->val("refenac");
	$tabValeur[0]["refenac"]["aff"]=$ref->aff("refenac","form","form_data[0]");

	if ((!isset($order)) || ($order=="")) { $order="refenac"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,"",0,"del"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
