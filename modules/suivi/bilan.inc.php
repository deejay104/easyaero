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
// ---- Vérifie les droits d'accès
	if (!GetDroit("AccesSuiviBilan")) { FatalError("Accès non autorisé (AccesSuiviBilan)"); }

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Vérifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");
	$dte=checkVar("dte","varchar",4);

	if (!preg_match("/[0-9]{4}/",$dte))
	{
		$dte=date("Y");
	}

// ---- Liste des années

	$query = "SELECT MIN(date_valeur) AS dtedeb FROM ".$MyOpt["tbl"]."_compte";
	$res=$sql->QueryRow($query);

	$dte1=date("Y",strtotime($res["dtedeb"]));
	if ($dte1<1970)
	  { $dte1=1970; }

	for($i=$dte1; $i<=date("Y"); $i++)
	  { 
			$tmpl_x->assign("dte_annee", $i);
			$tmpl_x->assign("chk_annee", ($i==$dte) ? "selected" : "") ;	
			$tmpl_x->parse("corps.lst_annee");
	  }

// ---- Affiche les infos
	$tabTitre=array();
	$tabTitre["compte"]["aff"]="Compte";
	$tabTitre["compte"]["width"]=150;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=250;
	$tabTitre["total"]["aff"]="Total";
	$tabTitre["total"]["width"]=150;
	
// ---- Récupère la liste
	$query = "SELECT num.description, cpt.compte,SUM(cpt.montant) AS total FROM ".$MyOpt["tbl"]."_compte AS cpt ";
	$query.= "LEFT JOIN ".$MyOpt["tbl"]."_numcompte AS num ON num.numcpt=cpt.compte ";
	$query.= "WHERE uid=".$MyOpt["uid_club"]." AND date_valeur>='".$dte."-01-01' AND date_valeur<'".($dte+1)."-01-01' GROUP BY compte ORDER BY compte";

	$total=0;

	$sql->Query($query);
	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

		$tabValeur[$i]["compte"]["val"]=$sql->data["compte"];
		$tabValeur[$i]["compte"]["aff"]=$sql->data["compte"];
		$tabValeur[$i]["description"]["val"]=$sql->data["description"];
		$tabValeur[$i]["description"]["aff"]=$sql->data["description"];
		$tabValeur[$i]["total"]["val"]=$sql->data["total"];
		$tabValeur[$i]["total"]["aff"]=AffMontant($sql->data["total"]);
		$total=$total+$sql->data["total"];
	}


	$tabTitre["compte"]["bottom"]="";
	$tabTitre["description"]["bottom"]="Résultat d'exploitation";
	$tabTitre["total"]["bottom"]=AffMontant($total);

	if ($order=="") { $order="compte"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"dte=".$dte));

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