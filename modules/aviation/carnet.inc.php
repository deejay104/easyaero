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
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$order=checkVar("order","varchar",20,"dte_deb");
	$trie=checkVar("trie","varchar");
	$ts=checkVar("ts","numeric");


// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Titre
	$tabTitre=array();
	if ($theme=="phone")
	{
		$tabTitre["dte_deb"]["aff"]="Date";
		$tabTitre["dte_deb"]["width"]=110;

		$tabTitre["nom"]["aff"]="Equipage";
		$tabTitre["nom"]["width"]=350;

		$tabTitre["heure"]["aff"]="Temps de vol";
		$tabTitre["heure"]["width"]=110;
		$tabTitre["carbavant"]["aff"]="Carbu";
		$tabTitre["carbavant"]["width"]=100;
		$tabTitre["potentiel"]["aff"]="Total";
		$tabTitre["potentiel"]["width"]=100;
	}
	else
	{
		$tabTitre["dte_deb"]["aff"]="Date";
		$tabTitre["dte_deb"]["width"]=110;
		$tabTitre["dte_deb"]["mobile"]="no";

		$tabTitre["nom"]["aff"]="Equipage";
		$tabTitre["nom"]["width"]=350;

		$tabTitre["tarif"]["aff"]="Tarif";
		$tabTitre["tarif"]["width"]=50;
		$tabTitre["tarif"]["mobile"]="no";
		$tabTitre["dest"]["aff"]="Lieu";
		$tabTitre["dest"]["width"]=100;
		$tabTitre["dest"]["mobile"]="no";
		$tabTitre["heure_deb"]["aff"]="Départ";
		$tabTitre["heure_deb"]["width"]=70;
		$tabTitre["heure_deb"]["mobile"]="no";
		$tabTitre["heure_fin"]["aff"]="Arrivée";
		$tabTitre["heure_fin"]["width"]=70;
		$tabTitre["heure_fin"]["mobile"]="no";

		$tabTitre["heure"]["aff"]="Temps de vol";
		$tabTitre["heure"]["width"]=110;
		$tabTitre["carbavant"]["aff"]="Carburant Avant";
		$tabTitre["carbavant"]["width"]=100;
		$tabTitre["carbapres"]["aff"]="Carburant Après";
		$tabTitre["carbapres"]["width"]=100;
		$tabTitre["potentiel"]["aff"]="Potentiel";
		$tabTitre["potentiel"]["width"]=100;
		$tabTitre["total"]["aff"]="Total heures de vol";
		$tabTitre["total"]["width"]=100;
	}
		

// ---- Liste des avions
	$lstress=ListeRessources($sql);

	foreach($lstress as $i=>$rid)
	{
		$resr=new ress_class($rid,$sql);

		// Initilialise l'id de ressouce s'il est vide
		if ($id==0)
		{
			$id=$rid;
		}

		// Rempli la liste dans le template
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", $resr->val("immatriculation"));
		if ($rid==$id)
		{
			$tmpl_x->assign("chk_avion", "selected");
		}
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	}

// ---- Affiche le tableau
	// $tmpl_x->assign("tab_liste",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne,false,false));
	$tmpl_x->assign("tab_liste",AfficheTableauRemote($tabTitre,geturlapi($mod,"vols","carnet","id=".$id."&theme=".$theme),$order,$trie,false,($theme=="phone") ? 16 : 25));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>