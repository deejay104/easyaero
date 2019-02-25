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
*/?>

<?
	if (!GetDroit("AccesSuiviTaxeAT")) { FatalError("Accès non autorisé (AccesSuiviTaxeAT)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/navigation.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("taxeat.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre
	if ($fonc=="Enregistrer")
	{
		$dest=checkVar("dest","array");
		
		foreach($dest as $id=>$d)
		{
			$objresa=new resa_class($id,$sql);
			$objresa->destination=$d;
			$objresa->Save();
		}
	}

// ---- Débiter nouvelle entrée
	if (($fonc=="debite") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$dest=checkVar("ddest","varchar");
		$taxe=checkVar("dtaxe","varchar");
		$date=checkVar("ddate","date");

		$id=checkVar("id","numeric");
		if ($id>0)
		{
			$objresa=new resa_class($id,$sql);
			$dest=$objresa->val("destination");
			$date=$objresa->val("dte_deb");
		}
		
		echo "Taxe AT ".$dest." du ".$date;
	}

// ---- Liste des vols avec une destination avec taxe
	$query = "SELECT cal.id,wp.id AS idnav ";
	$query.= "FROM ".$MyOpt["tbl"]."_calendrier AS cal ";
	$query.= "LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wp ON cal.destination=wp.nom ";
	$query.= "WHERE wp.taxe>0 AND cal.taxeok='non'";

	$sql->Query($query);
	$tabVols=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabVols[$i]["resa"]=$sql->data["id"];
		$tabVols[$i]["nav"]=$sql->data["idnav"];
	}
		$tmpl_x->assign("today", date("Y-m-d"));


	foreach ($tabVols as $i=>$id)
	{
		$objresa=new resa_class($id["resa"],$sql);
		$objpilote=new user_class($objresa->val("uid_pilote"),$sql);
		$objavion=new ress_class($objresa->val("uid_avion"),$sql);
		$objwp=new navpoint_class($id["nav"],$sql);

		$tmpl_x->assign("id", $id["resa"]);
		$tmpl_x->assign("date_vols", $objresa->Aff("dte_deb"));
		$tmpl_x->assign("status_vols", $objresa->Aff("taxe"));
		$tmpl_x->assign("pilote_vols", $objpilote->Aff("fullname"));
		$tmpl_x->assign("avion_vols", $objavion->Aff("immatriculation"));
		$tmpl_x->assign("destination_vols", $objresa->val("destination"));
		$tmpl_x->assign("taxe_vols", $objwp->val("taxe"));

		$tmpl_x->parse("corps.lst_vols");
		$tmpl_x->parse("corps.lst_vols_search");

	}
	
	
// ---- Affecte les variables d'affichage

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
