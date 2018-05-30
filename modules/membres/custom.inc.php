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

	$tmpl_custom = new XTemplate (MyRep("custom.htm"));
	$tmpl_custom->assign("path_module",$corefolder."/".$module."/".$mod);

// ---- Charge l'utilisateur
	require_once ($appfolder."/class/user.inc.php");
	$usrcus = new user_class($id,$sql,true);
	$usrcus->LoadRoles();
  	if (GetModule("aviation"))
	{
		$usrcus->LoadLache();
	}

// ---- Sauvegarde
	// Sauvegarde les infos spécifiques
	if (($fonc=="Enregistrer") && ((GetMyId($id)) || (GetDroit("ModifUserSauve"))))
	{
		// Sauvegarde les données
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$usrcus->Valid($k,$v);
		  	}
		}
		$usrcus->Save();		
	}
	// Sauvegarde le lache
	if (($fonc=="Enregistrer") && ($id>0) && (GetDroit("ModifUserLache")))
	{
		if (!is_array($form_lache))
		{
			$form_lache=array();
		}
		
		$msg_erreur.=$usrcus->SaveLache($form_lache);
		$usrcus->LoadLache();
	}
	
// ---- Données Utilisateurs

	foreach($usrcus->data as $k=>$v)
	  { $tmpl_custom->assign("form_$k", $usrcus->aff($k,$typeaff)); }

	$tmpl_custom->parse("left.type");

	if (((GetDroit("ModifUserDecouvert")) || (GetMyId($id))) && (GetModule("compta")))
	  { $tmpl_custom->parse("left.decouvert"); }

	if ((((GetDroit("ModifUserTarif")) || (GetMyId($id))) && (GetModule("compta"))) && (GetModule("aviation")))
	  { $tmpl_custom->parse("left.tarif"); }
  
  	if (GetModule("aviation"))
	{
	  	$tmpl_custom->parse("left.mod_aviation_lache");
	}

	if (($usrcus->CheckDroit("TypeInstructeur")) && (GetDroit("ModifUserDisponibilite")))
	{
		$tmpl_custom->assign("form_id", $id);
	  	$tmpl_custom->parse("left.disponibilite");
	}
	
// ---- Informations

	$tmpl_custom->assign("unitPoids", $MyOpt["unitPoids"]);


  	if (GetModule("compta"))
	{
		$tmpl_custom->assign("aff_solde", $usrcus->AffSolde());
	  	$tmpl_custom->parse("right.compta");
	}  	
	if (GetModule("aviation"))
	{
		$tmpl_custom->assign("aff_voltotal", $usrcus->AffNbHeures("0000-00-00"));
		$tmpl_custom->assign("aff_volannee", $usrcus->AffNbHeures(date("Y-01-01")));
		$tmpl_custom->assign("aff_vol12mois", $usrcus->AffNbHeures12mois());
	  	$tmpl_custom->parse("right.aviation");
	}

// ---- Affiche la page
	$tmpl_custom->parse("left");
	$left=$tmpl_custom->text("left");

	$tmpl_custom->parse("right");
	$right=$tmpl_custom->text("right");

?>