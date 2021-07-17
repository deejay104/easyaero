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


// ---- Menu
	addPageMenu("",$mod,"Calendrier",geturl("reservations","",""),"icn32_titre.png",true);
	addPageMenu("",$mod,"Journée",geturl("reservations","scheduler",""),"icn32_scheduler.png",false);

  
// ---- Définition des constantes
	if ((!isset($ress)) || (!is_numeric($ress)))
	{
	  	$ress=0;
	}

	$ress=checkVar("ress","numeric");
	$start=checkVar("start","varchar");

	
	$h=30;
	$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
	$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";
	$larcol="100";

	$t=ListeRessources($sql);
	$tress=array();

	$ii=1;
	foreach($t as $rid)
	{
		$tress[$ii]=new ress_class($rid, $sql);

		$tmpl_x->assign("uid_ress", $rid);
		$tmpl_x->assign("nom_ress", $tress[$ii]->val("immatriculation"));
		if ($ress==$rid)
		  { $tmpl_x->assign("chk_ress", "selected"); }
		else
		  { $tmpl_x->assign("chk_ress", ""); }

		$tmpl_x->assign("couleur_col", $tress[$ii]->val("couleur"));
		$tmpl_x->assign("couleur_nom", $tress[$ii]->aff("immatriculation"));
		$tmpl_x->parse("infos.lst_ress");
		$tmpl_x->parse("corps.lst_couleur");
		$ii=$ii+1;
	}



	// Liste des ressources
	$tmpl_x->assign("ress", $ress);


// ---- Affichage pour la journée
	if ($start!="")
	{
		if ($start=="today")
		{
			$start=date("Y-m-d");
		}
		$caltime=strtotime($start);
		$_SESSION["caltime"]=$caltime;
	}
	else if (isset($_SESSION["caltime"]))
	{
		$caltime=$_SESSION["caltime"];
	}
	else
	{
		$caltime=time();
		$_SESSION["caltime"]=$caltime;
	}
	
	if ($theme=="phone")
	{
		$jour=date("Y-m-d",$caltime);
		$tmpl_x->assign("defaultView","agendaDay");
		$tmpl_x->assign("headerListe","agendaWeek,agendaDay");
		$tmpl_x->assign("TexteTitre","");
	}
	else
	{
		$jour=date("Y-m-d",$caltime-24*3600*2);

		$tmpl_x->assign("defaultView","agendaTwoWeeks");
		$tmpl_x->assign("headerListe","agendaMonth,agendaFourWeeks,agendaTwoWeeks,agendaWeek,agendaDay");
		$tmpl_x->assign("TexteTitre","Calendrier pour");
		$tmpl_x->parse("corps.aff_tooltips");
	}

	$tmpl_x->assign("maintconf",$MyOpt["tabcolresa"]["maintconf"]);
	$tmpl_x->assign("maintplan",$MyOpt["tabcolresa"]["maintplan"]);
	$tmpl_x->assign("meeting",$MyOpt["tabcolresa"]["meeting"]);

	$tmpl_x->assign("form_ress",$ress);
	$tmpl_x->assign("form_jour",$jour);
	$tmpl_x->assign("timezone",$MyOpt["timezone"]);

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
