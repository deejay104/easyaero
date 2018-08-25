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
	$tmpl_custom->assign("corefolder",$corefolder);

// ---- Charge l'utilisateur
	require_once ($appfolder."/class/user.inc.php");
	$myuser = new user_class($gl_uid,$sql,true);

	$tmpl_custom->assign("solde", $myuser->AffSolde());
	$nbmois=floor($MyOpt["maxDernierVol"]/30);

	$tmpl_custom->assign("nb_vols", $myuser->NombreVols($nbmois));
	$tmpl_custom->assign("nb_mois", $nbmois);


  	if (GetModule("compta"))
	{
	  	$tmpl_custom->parse("custom.mod_compta_detail");
	}  	
	if (GetModule("aviation"))
	{
	  	$tmpl_custom->parse("custom.mod_aviation_detail");

		$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
		$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";

		$tmpl_custom->assign("form_jour",date("Y-m-d"));
		$tmpl_custom->assign("defaultView","agendaDay");
		$tmpl_custom->assign("form_debjour",$debjour);
		$tmpl_custom->assign("form_finjour",$finjour);

	  	$tmpl_custom->parse("custom.aff_reservation");
	}

// ---- Affiche la page
	$tmpl_custom->parse("custom");
	$custom=$tmpl_custom->text("custom");

?>