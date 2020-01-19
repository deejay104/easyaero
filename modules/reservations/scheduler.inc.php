<?php
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

<?php
	if (!GetDroit("AccesReservations")) { FatalError("Accès non autorisé (AccesReservations)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

  
// ---- Définition des constantes
	$ress=checkVar("ress","numeric");
	$jour=checkVar("jour","date");

	
	$h=30;
	$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
	$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";
	$larcol="100";

// ---- Liste des ressources
	$t=ListeRessources($sql,array("oui"));
	foreach($t as $rid)
	{
		$r=new ress_class($rid, $sql);

		$tmpl_x->assign("id_ress", $rid);
		$tmpl_x->assign("nom_ress", $r->val("immatriculation"));
		$tmpl_x->assign("pot_ress", $r->AffPotentiel());
		$tmpl_x->parse("corps.lst_ress");
	}

// ---- Liste des instructeurs
	$lst=ListActiveUsers($sql,"prenom,nom",array("TypeInstructeur"));
	foreach($lst as $i=>$tmpuid)
	{ 
		$usr=new user_core($tmpuid,$sql);

		$tmpl_x->assign("id_inst", "I".$tmpuid);
		$tmpl_x->assign("nom_inst", $usr->val("fullname"));
		$tmpl_x->parse("corps.lst_inst");
	}

// ---- Affichage pour la journée
	
	if ($theme=="phone")
	{
		if ($jour=="0000-00-00")
		  { $myuser->Valid("aff_jour",date("Y-m-d")); }
		else if ($jour!="")
		  { $myuser->Valid("aff_jour",$jour); }
	
		if (($myuser->data["aff_jour"]=="") || ($myuser->data["aff_jour"]=="0000-00-00"))
		  { $myuser->Valid("aff_jour",date("Y-m-d")); }
	
		$jour=$myuser->data["aff_jour"];
		$tmpl_x->assign("TexteTitre","");
	}
	else
	{
		if ($jour=="0000-00-00")
		  { $myuser->Valid("aff_jour",date("Y-m-d")); }
		  // { $myuser->Valid("aff_jour",date("Y-m-d")); }
		else if ($jour!="")
		  { $myuser->Valid("aff_jour",$jour); }
	
		if (($myuser->data["aff_jour"]=="") || ($myuser->data["aff_jour"]=="0000-00-00"))
		  { $myuser->Valid("aff_jour",date("Y-m-d")); }
	
		$jour=date2sql($myuser->data["aff_jour"]);
		$tmpl_x->parse("corps.aff_tooltips");
	}

	$tmpl_x->assign("maintconf",$MyOpt["tabcolresa"]["maintconf"]);
	$tmpl_x->assign("maintplan",$MyOpt["tabcolresa"]["maintplan"]);
	$tmpl_x->assign("meeting",$MyOpt["tabcolresa"]["meeting"]);

	$tmpl_x->assign("form_ress",$ress);
	$tmpl_x->assign("form_jour",$jour);

	$tmpl_x->assign("form_debjour",$debjour);
	$tmpl_x->assign("form_finjour",$finjour);
	$tmpl_x->assign("terrain_nom",$MyOpt["terrain"]["nom"]);	

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>
