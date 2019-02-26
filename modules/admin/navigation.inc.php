<?
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

<?
	require_once ($appfolder."/class/navigation.inc.php");
	if (!GetDroit("AccesConfigNavigation")) { FatalError("Accès non autorisé (AccesConfigNavigation)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("navigation.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Vérifie les variables
	$checktime=checkVar("checktime","numeric");


// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des waypoints
	$tabTitre=array();
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=120;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=350;
	$tabTitre["icone"]["aff"]="Type";
	$tabTitre["icone"]["width"]=100;
	$tabTitre["taxe"]["aff"]="Taxe";
	$tabTitre["taxe"]["width"]=100;
	
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar",1);
	$ts=checkVar("ts","numeric");
	$tabsearch=checkVar("tabsearch","array");
	$tl=20;
	
	if ($order=="") { $order="nom"; }

	// Calcul le nombre ligne totale
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_navpoints";
	$res=$sql->QueryRow($query);
	$totligne=$res["nb"];

	$q="";
	$op="";
	foreach($tabsearch as $k=>$v)
	{
		if ($v!="")
		{
			$q.=$op." ".$k." LIKE '%".addslashes($v)."%'";
			$op="AND";
		}
	}

	$query="SELECT * FROM ".$MyOpt["tbl"]."_navpoints ".(($q!="") ? "WHERE ".$q : "")." ORDER BY ".$order." ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT ".$ts.",".$tl;
	$sql->Query($query);
	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		
		$tabValeur[$i]["nom"]["val"]=$sql->data["nom"];
		$tabValeur[$i]["description"]["val"]=$sql->data["description"];
		$tabValeur[$i]["icone"]["val"]=$sql->data["icone"];
		$tabValeur[$i]["taxe"]["val"]=$sql->data["taxe"];

	}
	
	$tmpl_x->assign("aff_tableau",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,"",$ts,$tl,$totligne,true));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
