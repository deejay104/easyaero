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
	if (!GetDroit("AccesEvents")) { FatalError("Accès non autorisé (AccesEvents)"); }
	
	require_once ($appfolder."/class/evenement.inc.php");

// ---- Vérification des données
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche le sous-menu
	if (GetDroit("CreeEvent"))
	{
		addPageMenu($corefolder,$mod,"Créer",geturl("securite","eventdetail","id=0"),"",false);
	}

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["dte"]["aff"]="Date";
	$tabTitre["dte"]["width"]=150;
	$tabTitre["title"]["aff"]="Titre";
	$tabTitre["title"]["width"]=350;
	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=100;
	$tabTitre["risk"]["aff"]="Risque";
	$tabTitre["risk"]["width"]=150;

	$lst=ListEvents($sql,array());

	$tabValeur=array();

	foreach($lst as $i=>$d)
	{
		$event=new event_class($d["id"],$sql);
		$tabValeur[$i]["dte"]["val"]=strtotime($event->val("dte_event"));
		$tabValeur[$i]["dte"]["aff"]=$event->aff("dte_event");
		$tabValeur[$i]["title"]["val"]=$event->val("title");
		$tabValeur[$i]["title"]["aff"]=$event->aff("title");
		$tabValeur[$i]["status"]["val"]=$event->val("status");
		$tabValeur[$i]["status"]["aff"]=$event->aff("status");
		$tabValeur[$i]["risk"]["val"]=$event->val("risk");
		$tabValeur[$i]["risk"]["aff"]=$event->aff("risk","list");
	}

	if ($order=="") { $order="dte"; }
	if ($trie=="") { $trie="i"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>