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
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesConfigPrevisions")) { FatalError("Accès non autorisé (AccesConfigPrevisions)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("previsions.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	$dte=checkVar("dte","varchar",4);
	
// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des années

	if (($dte=="") || (!preg_match("/[0-9]{4}/",$dte)))
	{
	  	$dte=date("Y");
	}
	$tmpl_x->assign("aff_annee", $dte);

	$query="SELECT MIN(annee) AS deb FROM ".$MyOpt["tbl"]."_prevision GROUP BY annee";
	$res=$sql->QueryRow($query);
	if ($res["deb"]=="")
	{
		$res["deb"]=date("Y");
	}
	for($d=$res["deb"]; $d<=date("Y"); $d++)
	{ 
		$tmpl_x->assign("form_dte", $d);
		$tmpl_x->assign("form_selected", (($d==$dte) ? "selected" : "") );
		$tmpl_x->parse("corps.lst_annee");
	}

// ---- Liste des prévisions
	$tabPrev=array();
	$tabRess=array();
	$query="SELECT * FROM ".$MyOpt["tbl"]."_prevision WHERE annee='".$dte."' ORDER by avion";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabPrev[$sql->data["mois"]][$sql->data["avion"]]=$sql->data["heures"];
		$tabRess[$sql->data["avion"]]=1;
	}

	if ($dte==date("Y"))
	{
		$t=ListeRessources($sql);
		foreach($t as $i=>$rid)
		{
			$tabRess[$rid]=1;
		}
	}

	$query="SELECT uid_avion FROM `".$MyOpt["tbl"]."_calendrier` WHERE uid_avion>0 AND YEAR(dte_deb)='".$dte."' GROUP BY uid_avion ORDER BY uid_avion";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabRess[$sql->data["uid_avion"]]=1;
	}

	
// ---- Liste des ressources
	$txt="";
	foreach($tabRess as $rid=>$i)
	{
	  	$ress = new ress_class($rid,$sql);
		$txt.="<th width='100'>".$ress->aff("immatriculation")."</th>";
	}
	$tmpl_x->assign("aff_lst_ress", $txt);

// ---- Valeurs
	$tabMois[1]="Janvier";
	$tabMois[2]="Février";
	$tabMois[3]="Mars";
	$tabMois[4]="Avril";
	$tabMois[5]="Mai";
	$tabMois[6]="Juin";
	$tabMois[7]="Juillet";
	$tabMois[8]="Aout";
	$tabMois[9]="Septembre";
	$tabMois[10]="Octobre";
	$tabMois[11]="Nomvembre";
	$tabMois[12]="Décembre";


	foreach($tabMois as $m=>$nom)
	{
		$tmpl_x->assign("aff_id",$m);
		$tmpl_x->assign("aff_nom",$nom);
		
		foreach($tabRess as $rid=>$i)
		{
			$tmpl_x->assign("aff_ress",$rid);
			$tmpl_x->assign("aff_val",(isset($tabPrev[$m][$rid])) ? $tabPrev[$m][$rid] : "");
			$tmpl_x->parse("corps.lst_mois.lst_ress");
		}
		$tmpl_x->parse("corps.lst_mois");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>