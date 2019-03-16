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

	$tabTitre=array(
		"nom"=>array(
			"aff"=>"Nom",
			"width"=>120
		),
		"description"=>array(
			"aff"=>"Description",
			"width"=>350
		),
		"icone"=>array(
			"aff"=>"Type",
			"width"=>100
		),
		"taxe"=>array(
			"aff"=>"Taxe",
			"width"=>100
		),
		"action"=>array(
			"aff"=>"",
			"width"=>40
		),
	);
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar",1);
	$ts=checkVar("ts","numeric");
	$tabsearch=checkVar("tabsearch","array");
	$tl=20;
	
	if ($order=="") { $order="nom"; }
	if ($trie=="")
	{
		$tabsearch["taxe"]="*";
		$trie="d";
	}

	
	// Génération des conditions
	$q="1=1 ";
	$op="";
	foreach($tabsearch as $k=>$v)
	{
		if ($v=="*")
		{
			$q.="AND ".$k." <> ''";
		}
		else if ($v!="")
		{
			$q.="AND ".$k." LIKE '%".addslashes($v)."%'";
		}
	}

	// Calcul le nombre ligne totale
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_navpoints WHERE ".$q;
	$res=$sql->QueryRow($query);
	$totligne=$res["nb"];

	// Récupération des lignes
	$query="SELECT * FROM ".$MyOpt["tbl"]."_navpoints ".(($q!="") ? "WHERE ".$q : "")." ORDER BY ".$order." ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT ".$ts.",".$tl;
	$sql->Query($query);
	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		
		$tabValeur[$i]["id"]["val"]=$sql->data["id"];

		$tabValeur[$i]["nom"]["val"]="<div id='nom_".$sql->data["id"]."'>".$sql->data["nom"]."</div>";
		$tabValeur[$i]["description"]["val"]="<div id='description_".$sql->data["id"]."'>".$sql->data["description"]."</div>";
		$tabValeur[$i]["icone"]["val"]=$sql->data["icone"];
		$tabValeur[$i]["taxe"]["val"]="<div id='taxe_".$sql->data["id"]."'>".$sql->data["taxe"]."</div>";

		$tabValeur[$i]["action"]["val"]=$sql->data["id"];
		$tabValeur[$i]["action"]["aff"]="<div id='action_".$sql->data["id"]."' style='display:none;'><a id='edit_".$sql->data["id"]."' class='imgDelete' ><img src='".$corefolder."/".$module."/".$mod."/img/icn16_editer.png'></a></div>";

		$tmpl_x->assign("lst_id",$sql->data["id"]);
		$tmpl_x->parse("corps.lst_edit");
	}
	

	$tmpl_x->assign("aff_tableau",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,"",$ts,$tl,$totligne,true,true,"action"));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
