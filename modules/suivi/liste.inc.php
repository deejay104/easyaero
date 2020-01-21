<?php
/*
    SoceIt v3.0
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
	if (!GetDroit("AccesSuiviListeComptes")) { FatalError("Accès non autorisé (AccesSuiviListeComptes)"); }

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Vérifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche les infos
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;
	if (($MyOpt["module"]["aviation"]=="on") && ($theme!="phone"))
	{
		$tabTitre["lastflight"]["aff"]="Vols/12mois";
		$tabTitre["lastflight"]["width"]=80;
	}
	$tabTitre["solde"]["aff"]="Solde";
	$tabTitre["solde"]["width"]=120;
	$tabTitre["soldetmp"]["aff"]="En attente";
	$tabTitre["soldetmp"]["width"]=120;

	$tabTitre["decouvert"]["aff"]="Découvert";
	$tabTitre["decouvert"]["width"]=70;

	$lstusr=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
		$tabValeur[$i]["solde"]["val"]=$usr->CalcSolde();
		$tabValeur[$i]["solde"]["aff"]=$usr->AffSolde()."&nbsp;&nbsp;";
		$tabValeur[$i]["solde"]["align"]="right";

		$s=$usr->CalcSoldeTemp();
		$tabValeur[$i]["soldetmp"]["val"]=$s;
		$tabValeur[$i]["soldetmp"]["aff"]=AffMontant($s)."&nbsp;&nbsp;";
		$tabValeur[$i]["soldetmp"]["align"]="right";

		if ($MyOpt["module"]["aviation"]=="on")
		{
			$tabValeur[$i]["lastflight"]["val"]=$usr->AffNbHeures12mois("val");
			$tabValeur[$i]["lastflight"]["aff"]=$usr->AffNbHeures12mois()."&nbsp;&nbsp;";
		}
		$tabValeur[$i]["lastflight"]["align"]="right";
		$tabValeur[$i]["decouvert"]["val"]=$usr->data["decouvert"];
		$tabValeur[$i]["decouvert"]["aff"]=$usr->data["decouvert"]."&nbsp;&nbsp;";
		$tabValeur[$i]["decouvert"]["align"]="right";
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
