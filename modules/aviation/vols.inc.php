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
	if (!GetDroit("AccesVols")) { FatalError("Accès non authorisé (AccesVols)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$order=checkVar("order","varchar",10,"dte_deb");
	$trie=checkVar("trie","varchar",1,"i");
	$ts=checkVar("ts","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste des membres
	if (GetDroit("ListeVols"))
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
	$tabTitre["dte_deb"]["aff"]="Date";
	$tabTitre["dte_deb"]["width"]=($theme=="phone") ? 120 : 250 ;
	$tabTitre["immat"]["aff"]="Immat";
	$tabTitre["immat"]["width"]=80;
	$tabTitre["tpsreel"]["aff"]="Bloc";
	$tabTitre["tpsreel"]["width"]=70;
	$tabTitre["temps"]["aff"]="Temps";
	$tabTitre["temps"]["width"]=70;
	$tabTitre["cout"]["aff"]="Cout";
	$tabTitre["cout"]["width"]=100;
	if ($theme!="phone")
	{
		$tabTitre["instructeur"]["aff"]="Instructeur";
		$tabTitre["instructeur"]["width"]=270;
		$tabTitre["instructeur"]["mobile"]="no";
	}


// ---- Affiche le tableau
	$tmpl_x->assign("tab_liste",AfficheTableauRemote($tabTitre,geturlapi($mod,"vols","mesvols","id=".$id."&theme=".$theme),$order,$trie,false,($theme=="phone") ? 16 : 25));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
