<?php
/*
    SoceIt v3.0
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
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Droit d'accès
	if (!GetDroit("AccesFichesMaintenance")) { FatalError("Accès non authorisé (AccesFichesMaintenance)"); }

// ---- Vérification des données
	$uid_avion=checkVar("uid_avion","numeric");
	$form_avion=checkVar("form_avion","numeric");
	$form_description=checkVar("form_description","text");
  
	$form_description=preg_replace("/<BR[^>]*>/i","\n",$form_description);
	$form_description=preg_replace("/<[^>]*>/i","",$form_description);
	
// ---- Enregistre
	$msg_ok="";
	$msg_erreur="";
	$affrub="";
	if (($fonc=="Enregistrer") && ($form_avion>0) && ($form_description!="") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$fiche=new fichemaint_class(0,$sql);

		$msg_erreur="";
  		$msg_erreur.=$fiche->Valid("uid_avion",$form_avion);
  		$msg_erreur.=$fiche->Valid("description",$form_description);
		$fiche->Save();

		if ($fiche->id>0)
		{
		  	$_SESSION['tab_checkpost'][$checktime]=$checktime;
		  	$form_description="";
		  	$msg_ok="Fiche créée.<BR>";
			$affrub="fiche";
		}
		else
		{
		  	$msg_erreur.="Erreur lors de l'enregistrement !<BR>";
		}

	  }
	else if (($fonc=="Enregistrer") && ($form_avion>0) && ($form_description==""))
	  {
		$msg_erreur="Il faut saisir une description pour l'incident !<BR>";
	  }
	else if ($fonc=="Retour")
	  {
	  	$form_description="";
	  	$affrub="fiche";
		return;
	  }
	else
	  {
	  	$form_description="";
	  }

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_ok!="")
	{
		affInformation($msg_ok,"ok");
	}
	
// ---- Liste les avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$rid)
	  {
		$resr=new ress_class($rid,$sql);
		
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", $resr->val("immatriculation"));
		if ($uid_avion==$resr->id)
		  { $tmpl_x->assign("chk_avion", "selected"); }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	  }

// ---- Affiche la description
	$tmpl_x->assign("form_description", htmlentities($form_description));

// ---- Affiche les liens
	if (GetDroit("PlanifieMaintenance"))
	  {
		$tmpl_x->parse("corps.aff_planif");
	  }

// ---- Affecte les variables d'affichage
	if ($affrub=="")
	  {
		$tmpl_x->parse("icone");
		$icone=$tmpl_x->text("icone");
		$tmpl_x->parse("infos");
		$infos=$tmpl_x->text("infos");
		$tmpl_x->parse("corps");
		$corps=$tmpl_x->text("corps");
	  }
?>