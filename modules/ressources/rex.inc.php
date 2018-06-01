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
	if (!GetDroit("AccesRex")) { FatalError("Acc�s non autoris� (AccesRex)"); }
	
	require_once ($appfolder."/class/rex.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("rex.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- V�rification des donn�es
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["dte"]["aff"]="Date";
	$tabTitre["dte"]["width"]=150;
	$tabTitre["titre"]["aff"]="Titre";
	$tabTitre["titre"]["width"]=350;
	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=100;
	$tabTitre["categorie"]["aff"]="Cat�gorie";
	$tabTitre["categorie"]["width"]=150;

	$lst=ListRex($sql,array());

	$tabValeur=array();
	foreach($lst as $i=>$d)
	{
		$rex=new rex_class($d["id"],$sql);
		$tabValeur[$i]["dte"]["val"]=strtotime($rex->val("dte_rex"));
		$tabValeur[$i]["dte"]["aff"]=$rex->aff("dte_rex");
		$tabValeur[$i]["titre"]["val"]=$rex->val("titre");
		$tabValeur[$i]["titre"]["aff"]=$rex->aff("titre");
		$tabValeur[$i]["status"]["val"]=$rex->val("status");
		$tabValeur[$i]["status"]["aff"]=$rex->aff("status");
		$tabValeur[$i]["categorie"]["val"]=$rex->val("categorie");
		$tabValeur[$i]["categorie"]["aff"]=$rex->aff("categorie");
	}

	if ($order=="") { $order="dte"; }
	if ($trie=="") { $trie="i"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("CreeRex"))
	{
			$tmpl_x->parse("corps.creerex");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>