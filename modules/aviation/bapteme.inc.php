<?php
/*
    Easy-Aero v3.0
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
	if (!GetDroit("AccesBapteme"))
	  { FatalError("Accès non autorisé (AccesBapteme)"); }

	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Initialisation des variables

	$id=checkVar("id","numeric");
	$form_data=checkVar("form_data","array");

	$msg_erreur="";
	$msg_confirmation="";

	if ($id>0)
	  { $btm = new bapteme_class($id,$sql); }
	else
	  { $btm = new bapteme_class(0,$sql); }


// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && (($id=="") || ($id==0)) && ((GetDroit("CreeBapteme"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$btm->Create();
			$id=$btm->id;
	  }
	else if (($fonc=="Enregistrer") && ($id=="") && (isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$mod="aviation";
			$affrub="baptemes";
	  }

	if (($fonc=="Enregistrer") && ((GetMyId($btm->uid_creat)) || (GetDroit("ModifBapteme"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$btm->Valid($k,$v);
		  	}
		}

		if ( ($form_data["id_pilote"]>0) && ($form_data["id_avion"]>0) && ($form_data["dte"]["date"]!='0000-00-00') && ($form_data["dte"]["time"]!='00:00') && ($btm->val("status")<4) )
		  { $btm->Valid("status","4"); }

		$btm->Save();

		if ($id==0)
		{
			$id=$btm->id;
		}
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
	else if (($fonc=="Enregistrer") && (($btm->data["status"]==2) || ($btm->data["status"]==3) || ($btm->data["status"]==4)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$btm->Valid("id_pilote",$form_data["id_pilote"],false);
		$btm->Valid("id_avion",$form_data["id_avion"],false);
		$btm->Valid("dte",$form_data["dte"],false);

		if ( ($form_data["id_pilote"]>0) && ($form_data["id_avion"]>0) && ($form_data["dte"]!='0000-00-00 00:00') )
		  { $btm->Valid("status","4"); }

		$btm->Save();
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

	if (($fonc=="Annuler") && ($id==0))
	{
			$mod="aviation";
			$affrub="baptemes";
	}

// ---- Réserve l'avion
	if (($fonc=="reserver") && ($btm->data["id_resa"]==0))
	{
		require_once ($appfolder."/class/reservation.inc.php");
		$resa=new resa_class(0,$sql);

		$resa->description="Bapteme ".$btm->data["nom"]."\nTéléphone: ".$btm->data["telephone"];
		$resa->uid_pilote=$btm->data["id_pilote"];
		$resa->uid_debite=($MyOpt["uid_bapteme"]>0) ? $MyOpt["uid_bapteme"] : $btm->data["id_pilote"];
		$resa->uid_instructeur=0;
		$resa->uid_ressource=$btm->data["id_avion"];
		$resa->destination="LOCAL";
		$resa->nbpersonne=$btm->data["nb"];
		$resa->tpsestime="20";
		$resa->dte_deb=sql2date($btm->data["dte"]);
		$resa->dte_fin=date("d/m/Y H:i:s",strtotime($btm->data["dte"])+60*45);
		$resa->tpsreel="";
		$resa->horadeb="";
		$resa->horafin="";

		$msg_resa=$resa->Save(true);

		$btm->data["id_resa"]=$resa->id;
		$btm->data["status"]=4;
		$btm->Save();

		$msg_confirmation.=($msg_resa!="") ? $msg_resa : "Réservation confirmée.<BR>";
	}

// ---- Attribuer le bapteme
	if (($fonc=="affecte") && ($id>0))
	  {
		$btm->data["id_pilote"]=$gl_uid;
		$btm->data["status"]=3;
		$btm->Save();
	  }

// ---- Vol effectué
	if (($fonc=="effectue") && ($id>0))
	{
		$btm->data["status"]=5;
		$btm->Save();
	}

// ---- Supprimer
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeBapteme")))
	{
		$btm->Delete();
		$mod="aviation";
		$affrub="baptemes";
	}


// ---- Modifie les infos
	if (($fonc=="modifier") && (GetDroit("ModifBapteme")))
	{
		$typeaff="form";
	  	$tmpl_x->parse("corps.submit");
	}
	else if (($fonc=="add") && (GetDroit("CreeBapteme")))
	{
		$typeaff="form";
	  	$tmpl_x->parse("corps.submit");
	}
	else
	{
		$typeaff="html";
	}
	
	
// ---- Affiche les infos

	$tmpl_x->assign("id", $id);

	$btm->Render("form",$typeaff);

	$tmpl_x->assign("form_num", $btm->aff("num",$typeaff));
	$tmpl_x->assign("uid_avion", $btm->data["id_avion"]);
	$tmpl_x->assign("id_resa", $btm->data["id_resa"]);
	$tmpl_x->assign("deb", strtotime($btm->data["dte"]));
	$tmpl_x->assign("fin", strtotime($btm->data["dte"])+45*60);

// ---- Menu
	$ress = new ress_class($btm->data["id_avion"],$sql);

// <p><A href="index.php?mod=aviation&rub=baptemes"><IMG src="{path_module}/img/icn32_retour.png" alt="">&nbsp;Liste</A></p>
	addPageMenu("",$mod,"Liste",geturl("aviation","baptemes",""),"icn32_retour.png",false);

// <!-- BEGIN: affecter -->
// <p><A href="index.php?mod=aviation&rub=bapteme&id={id}&fonc=affecte"><IMG src="{path_module}/img/icn32_affecte.png" alt="">&nbsp;Prendre</A></p>
// <!-- END: affecter -->
	if (($btm->data["id_pilote"]==0) && ($btm->data["status"]>0))
	{
		addPageMenu("",$mod,"Prendre",geturl("aviation","bapteme","fonc=affecte&id=".$id),"icn32_retour.png",false);
		$tmpl_x->parse("corps.info_prendre");
	}

// <!-- BEGIN: planifier -->
// <p><A href="index.php?mod=aviation&rub=bapteme&id={id}&fonc=planifier"><IMG src="{path_module}/img/icn32_planifie.png" alt="">&nbsp;Planifier</A></p>
// <!-- END: planifier -->
	if (($btm->data["status"]==2) || ($btm->data["status"]==3) || (($btm->data["status"]==4) && ($btm->data["id_resa"]==0)))
	{
		addPageMenu("",$mod,"Planifier",geturl("aviation","bapteme","fonc=planifier&id=".$id),"icn32_planifie.png",false);
		$tmpl_x->parse("corps.info_planifier");
	}

// <!-- BEGIN: reserver -->
// <p><a href="index.php?mod=aviation&rub=bapteme&id={id}&fonc=reserver"><IMG src="{path_module}/img/icn32_reservation.png" alt="">&nbsp;Réserver</A></p>
// <!-- END: reserver -->
	if (($ress->CheckDispo(strtotime($btm->data["dte"]),strtotime($btm->data["dte"])+45*60)) && ($btm->data["id_pilote"]>0) && ($btm->data["id_avion"]>0) && ($btm->data["dte"]!='0000-00-00 00:00'))
	{
		addPageMenu("",$mod,"Réserver",geturl("aviation","bapteme","fonc=reserver&id=".$id),"icn32_reservation.png",false);
		$tmpl_x->parse("corps.info_reserver");
	}


// <!-- BEGIN: effectue -->
// <p><a href="index.php?mod=aviation&rub=bapteme&id={id}&fonc=effectue"><IMG src="{path_module}/img/icn32_effectue.png" alt="">&nbsp;Effectué</A></p>
// <!-- END: effectue -->
	if ( ($btm->data["status"]==2) || ($btm->data["status"]==3) || ($btm->data["status"]==4) )
	{
		addPageMenu("",$mod,"Effectué",geturl("aviation","bapteme","fonc=effectue&id=".$id),"icn32_effectue.png",false);
		$tmpl_x->parse("corps.info_effectuer");
	}
	
// <!-- BEGIN: ajout -->
// <p><A href="index.php?mod=aviation&rub=bapteme&fonc=add&id="><IMG src="{path_module}/img/icn32_ajouter.png" alt="">&nbsp;Ajouter</A></p>
// <!-- END: ajout -->
	if (GetDroit("CreeBapteme"))
	{
		addPageMenu("",$mod,"Ajouter",geturl("aviation","bapteme","fonc=add&id=0"),"icn32_ajouter.png",false);
	}

// <!-- BEGIN: modification -->
// <p><A href="index.php?mod=aviation&rub=bapteme&id={id}&fonc=modifier"><IMG src="{path_module}/img/icn32_modifier.png" alt="">&nbsp;Modifier</A></p>
// <!-- END: modification -->
	if (GetDroit("ModifBapteme"))
	{
		addPageMenu("",$mod,"Modifier",geturl("aviation","bapteme","fonc=modifier&id=".$id),"icn32_modifier.png",($fonc=="modifier") ? true : false);
	}

// <!-- BEGIN: suppression -->
// <p><A href="#" OnClick="ConfirmeClick('index.php?mod=aviation&rub=bapteme&id={id}&fonc=delete','Voulez-vous supprimer ce baptème ?');"><IMG src="{path_module}/img/icn32_supprimer.png" alt="">&nbsp;Supprimer</A></p>
// <!-- END: suppression -->


	if (GetDroit("SupprimeBapteme"))
	{
		addPageMenu("",$mod,"Supprimer",geturl("aviation","bapteme","fonc=delete&id=".$id),"icn32_supprimer.png",false,"Voulez-vous supprimer ce baptème ?");
	}






	if (($fonc=="planifier") && (($btm->data["status"]==2) || ($btm->data["status"]==3) || ($btm->data["status"]==4)))
	{
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->data["id_pilote"],"form_data[id_pilote]","","","std","non",array("AutoriseBapteme")));
		$tmpl_x->assign("form_id_avion",AffListeRessources($sql,$btm->data["id_avion"],"form_data[id_avion]",array("oui")));
		$tmpl_x->assign("form_dte", $btm->aff("dte","form"));
	  	$tmpl_x->parse("corps.submit");
	}

	else if ($typeaff=="html")
	{
		$usr = new user_core($btm->data["id_pilote"],$sql,true);
		$tmpl_x->assign("form_id_pilote", $usr->Aff("fullname"));
		$tmpl_x->assign("form_id_avion", strtoupper($ress->val("immatriculation")));
	}
	else
	{
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->data["id_pilote"],"form_data[id_pilote]","","","std","non",array("AutoriseBapteme")));
		$tmpl_x->assign("form_id_avion",AffListeRessources($sql,$btm->data["id_avion"],"form_data[id_avion]",array("oui")));
	}

// ---- Liste des dispos
	$lst=ListeRessources($sql,array("oui"));
	foreach($lst as $i=>$id)
	  {
		$ress = new ress_class($id,$sql);
		$tmpl_x->assign("lst_uid_avion", $id);
		$tmpl_x->assign("dispo_immat", $ress->val("immatriculation"));
		$tmpl_x->parse("corps.lst_dispo");
		$tmpl_x->parse("corps.lst_dispo_reload");
	  }


// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_confirmation!="")
	{
		affInformation($msg_confirmation,"ok");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
