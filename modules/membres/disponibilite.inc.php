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
	$id=checkVar("id","numeric");

	if ( (!GetDroit("AccesDisponibilites")) && (!GetMyId($id)) )
	  { FatalError("Accès non autorisé (AccesDisponibilites)"); }

  	require_once ($appfolder."/class/user.inc.php");

  	if ($id>0)
	{ 
		$usr = new user_class($id,$sql,((GetMyId($id)) ? true : false));

		if ($usr->data["disponibilite"]=="dispo")
		{
			$tmpl_x->assign("backcolor",$MyOpt["styleColor"]["msgboxBackgroundOk"]);
			$tmpl_x->assign("eventcolor",$MyOpt["styleColor"]["msgboxBackgroundError"]);
		}
		else
		{
			$tmpl_x->assign("backcolor",$MyOpt["styleColor"]["msgboxBackgroundError"]);
			$tmpl_x->assign("eventcolor",$MyOpt["styleColor"]["msgboxBackgroundOk"]);
		}
	}

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche les menus
  	$tmpl_x->assign("id",$id);


// ---- Variable du calendrier
		
	$h=30;
	$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
	$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";
	$larcol="100";
	$jour=date("Y-m-d");

	$tmpl_x->assign("mid",$id);

	$tmpl_x->assign("defaultView","agendaFourWeeks");
	$tmpl_x->assign("headerListe","agendaHeightWeeks,agendaFourWeeks,agendaTwoWeeks,agendaWeek,agendaDay");
	$tmpl_x->assign("TexteTitre","Calendrier pour");
	// $tmpl_x->parse("corps.aff_tooltips");
	$tmpl_x->assign("form_jour",$jour);

	$tmpl_x->assign("form_debjour",$debjour);
	$tmpl_x->assign("form_finjour",$finjour);


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>