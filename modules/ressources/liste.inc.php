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
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$ress=checkVar("trie","numeric");
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche le sous-menu
	if (GetDroit("CreeMaintenance"))
	{
		addPageMenu("","ressources","Planifier une maintenance",geturl("ressources","detailmaint","id=0"),"");
	}


// ---- Affiche la liste des maintenances

	if (!is_numeric($ress))
	  { $ress=0; }

	$tabTitre=array();
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=70;
	$tabTitre["dte_deb"]["aff"]="Début";
	$tabTitre["dte_deb"]["width"]=100;
	$tabTitre["dte_fin"]["aff"]="Fin";
	$tabTitre["dte_fin"]["width"]=100;
	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=100;
	
	if ($theme!="phone")
	{
		$tabTitre["cout"]["aff"]="Cout";
		$tabTitre["cout"]["width"]=100;
		$tabTitre["atelier"]["aff"]="Atelier";
		$tabTitre["atelier"]["width"]=220;
	}
	
	$lstFiche=GetAllMaintenance($sql,$ress);
	$tabValeur=array();

	if (count($lstFiche)>0)
	{
		foreach($lstFiche as $i=>$id)
		{
			$maint = new maint_class($id,$sql);

			$ress = new ress_class($maint->data["uid_ressource"],$sql,false);
			$tabValeur[$i]["ress"]["val"]=$ress->val("immatriculation");
			$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
			
			$tabValeur[$i]["dte_deb"]["val"]=strtotime($maint->data["dte_deb"]);
			$tabValeur[$i]["dte_deb"]["aff"]=$maint->aff("dte_deb");
			$tabValeur[$i]["dte_fin"]["val"]=strtotime($maint->data["dte_fin"]);
			$tabValeur[$i]["dte_fin"]["aff"]=$maint->aff("dte_fin");

			$tabValeur[$i]["status"]["val"]=$maint->val("status");
			$tabValeur[$i]["status"]["aff"]=$maint->aff("status");
			$tabValeur[$i]["cout"]["val"]=$maint->val("cout");
			$tabValeur[$i]["cout"]["aff"]=$maint->aff("cout");

			$tabValeur[$i]["atelier"]["val"]=$maint->val("uid_atelier");
			$tabValeur[$i]["atelier"]["aff"]=$maint->aff("uid_atelier");
		}

		if ($order=="") { $order="dte_deb"; }
		if ($trie=="") { $trie="i"; }
		
		$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));
	}
	else
	{
		$tmpl_x->assign("aff_tableau","-Aucune maintenance de saisie-");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>