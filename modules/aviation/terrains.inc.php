<?
/*
    Easy-Aero
    Copyright (C) 2025 Matthieu Isorez

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
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/navigation.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$order=checkVar("order","varchar",10,"nb");
	$trie=checkVar("trie","varchar",1,"i");
	$ts=checkVar("ts","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste des membres
	if (GetDroit("ListeTerrains"))
	{
		if ($id==0)
		  { $id=$gl_uid; }

		$tmpl_x->assign("form_lstuser", AffListeMembres($sql,$id,"form_uid_user","","","std","non",array()));
		$tmpl_x->parse("corps.listeVols");
	}
	else
	{
		$id=$gl_uid;
	}

// ---- Titre

	$tabTitre=array();
	$tabTitre["nom"]["aff"]="OACI";
	$tabTitre["nom"]["width"]=100 ;
	$tabTitre["description"]["aff"]="Terrain";
	$tabTitre["description"]["width"]=150 ;
	$tabTitre["nb"]["aff"]="Nombre";
	$tabTitre["nb"]["width"]=80;
	$tabTitre["last"]["aff"]="Dernière visite";
	$tabTitre["last"]["width"]=150;

// ---- Récupère la liste des terrains
    $usr=new user_class($id,$sql);
    $lst=$usr->ListeTerrains();

    $tabValeur=array();

    $i=0;
    foreach ($lst as $id=>$d)
    {
        $tabValeur[$i]["nom"]["val"]=$d["nom"];
        $tabValeur[$i]["description"]["val"]=$d["description"];
        $tabValeur[$i]["nb"]["val"]=$d["nb"];
        $tabValeur[$i]["last"]["val"]=$d["last"];
        $i++;
    }

// ---- Affiche le tableau
    $tmpl_x->assign("tab_liste",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	$tmpl_x->assign("tileLayer_url",'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png');
	$tmpl_x->assign("map_lat",$MyOpt["terrain"]["latitude"]);
	$tmpl_x->assign("map_lon",$MyOpt["terrain"]["longitude"]);
	

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
