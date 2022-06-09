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
	if (!GetDroit("AccesBaptemes")) { FatalError("Accès non autorisé (AccesBaptemes)"); }

	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ----
	if (GetDroit("CreeBapteme"))
	{
		addPageMenu("",$mod,"Ajouter",geturl("aviation","bapteme","fonc=add&id=0"),"icn32_ajouter.png",false);
	}
// &nbsp;
// <!-- BEGIN: ajout -->
// <p><A href="index.php?mod=aviation&rub=bapteme&fonc=add&id="><IMG src="{path_module}/img/icn32_ajouter.png" alt="">&nbsp;Ajouter</A></p>
// <!-- END: ajout -->

// ----
	$form_status=checkVar("form_status","numeric");
	  
// ---- Liste des status
	if (!isset($_REQUEST["form_status"]))
	  { $form_status=-2; }

  	$btm = new bapteme_class(0,$sql);
	$tabStatus=$btm->ListeStatus();
	foreach($tabStatus as $id=>$txt)
	  {
		$tmpl_x->assign("form_statusid", $id);
		$tmpl_x->assign("form_status", $txt);
		if ($id==$form_status)
		  {
		  	$tmpl_x->assign("form_selected", "selected");
		  }
		else
		  {
		  	$tmpl_x->assign("form_selected", "");
		  }
		$tmpl_x->parse("corps.lst_status");
	  }	

	if ($form_status==-2)
	  {
	  	$tmpl_x->assign("form_selected_open", "selected");
	  }
	if ($form_status==-1)
	  {
	  	$tmpl_x->assign("form_selected_all", "selected");
	  }


// ----
	$tabTitre=array();
	$tabTitre["num"]["aff"]="Numéro";
	$tabTitre["num"]["width"]=120;

	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;

	$tabTitre["type"]["aff"]="Type";
	$tabTitre["type"]["width"]=100;
	$tabTitre["type"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["bonkdo"]["aff"]="Bon KDO";
	$tabTitre["bonkdo"]["width"]=90;
	$tabTitre["bonkdo"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["nb"]["aff"]="Nb";
	$tabTitre["nb"]["width"]=30;
	$tabTitre["nb"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["dte_creat"]["aff"]="Date création";
	$tabTitre["dte_creat"]["width"]=120;
	$tabTitre["dte_creat"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=80;

	$tabTitre["paye"]["aff"]="Payé";
	$tabTitre["paye"]["width"]=100;
	$tabTitre["paye"]["mobile"]="no";
	$tabTitre["paye"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["pilote"]["aff"]="Pilote";
	$tabTitre["pilote"]["width"]=180;
	$tabTitre["pilote"]["mobile"]=($theme=="phone") ? "no" : "";
		
	$tabTitre["resa"]["aff"]="Réservation";
	$tabTitre["resa"]["width"]=100;
	$tabTitre["resa"]["mobile"]=($theme=="phone") ? "no" : "";

	$tabTitre["date"]["aff"]="Date prévue";
	$tabTitre["date"]["width"]=170;
	$tabTitre["date"]["mobile"]=($theme=="phone") ? "no" : "";


	if ((!isset($order)) || ($order=="")) { $order="status"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableauRemote($tabTitre,geturlapi($mod,"baptemes","","status=".$form_status),$order,$trie,true,25,"tbl_baptemes"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
