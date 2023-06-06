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
				if ($btm->isFields($k))
				{
					$msg_erreur=$btm->Valid($k,$form_data[$k]);
					if ($msg_erreur!="")
					{
						affInformation($msg_erreur,"error");
					}
				}
			}
		}

		if ( ($form_data["id_pilote"]>0) && ($form_data["id_avion"]>0) && ($form_data["dte"]["date"]!='0000-00-00') && ($form_data["dte"]["time"]!='00:00') && ($btm->val("status")<4) )
		  { $btm->Valid("status","4"); }

		$btm->Save();

		if ($id==0)
		{
			$id=$btm->id;
		}
		affInformation("Vos données ont été enregistrées","ok");

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
		affInformation("Vos données ont été enregistrées","ok");

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

		$resa->description=$btm->val("type")." ".$btm->val("nom")."\nTéléphone: ".$btm->val("telephone")."\nNuméro: ".$btm->val("num");
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

		if (count($msg_resa)==0)
		{
			affInformation("Réservation confirmée.","ok");
		}
		else
		{
			foreach($msg_resa as $m)
			{
				affInformation($m["txt"],$m["status"]);
			}
		}
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

// ---- Annuler réservation
	if (($fonc=="annulevol") && ($id>0))
	{
		require_once ($appfolder."/class/reservation.inc.php");
		$resa=new resa_class($btm->data["id_resa"],$sql);
		$resa->Delete();

		$btm->data["id_resa"]=0;
		$btm->data["status"]=2;
		$btm->Save();
		affInformation("Vol annulé.","ok");
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
	$tmpl_x->assign("dte_creat", $btm->aff("dte_creat",$typeaff));
	$tmpl_x->assign("uid_avion", $btm->data["id_avion"]);
	$tmpl_x->assign("id_resa", $btm->data["id_resa"]);
	$tmpl_x->assign("deb", strtotime($btm->data["dte"]));
	$tmpl_x->assign("fin", strtotime($btm->data["dte"])+45*60);

// ---- Menu
	$ress = new ress_class($btm->data["id_avion"],$sql);

	addPageMenu("",$mod,"Liste",geturl("aviation","baptemes",""),"mdi-keyboard-backspace",false);

	if (($btm->data["id_pilote"]==0) && ($btm->data["status"]>0))
	{
		addPageMenu("",$mod,"Prendre",geturl("aviation","bapteme","fonc=affecte&id=".$id),"",false);
		$tmpl_x->parse("corps.info_prendre");
	}
	if (($btm->data["status"]==2) || ($btm->data["status"]==3) || (($btm->data["status"]==4) && ($btm->data["id_resa"]==0)))
	{
		addPageMenu("",$mod,"Planifier",geturl("aviation","bapteme","fonc=planifier&id=".$id),"",false);
		$tmpl_x->parse("corps.info_planifier");
	}
	if (($ress->CheckDispo(strtotime($btm->data["dte"]),strtotime($btm->data["dte"])+45*60)) && ($btm->data["id_pilote"]>0) && ($btm->data["id_avion"]>0) && ($btm->data["dte"]!='0000-00-00 00:00'))
	{
		addPageMenu("",$mod,"Réserver",geturl("aviation","bapteme","fonc=reserver&id=".$id),"",false);
		$tmpl_x->parse("corps.info_reserver");
	}
	if ( ($btm->data["status"]==2) || ($btm->data["status"]==3) || ($btm->data["status"]==4) )
	{
		addPageMenu("",$mod,"Effectué",geturl("aviation","bapteme","fonc=effectue&id=".$id),"",false);
		$tmpl_x->parse("corps.info_effectuer");

	}
	if (( ($btm->data["status"]==2) || ($btm->data["status"]==3) || ($btm->data["status"]==4) ) && ($btm->data["id_resa"]>0))
	{
		addPageMenu("",$mod,"Annuler vol",geturl("aviation","bapteme","fonc=annulevol&id=".$id),"",false);
	}
	if (GetDroit("ModifBapteme"))
	{
		addPageMenu("",$mod,"Modifier",geturl("aviation","bapteme","fonc=modifier&id=".$id),"",($fonc=="modifier") ? true : false);
	}
	if (GetDroit("SupprimeBapteme"))
	{
		addPageMenu("",$mod,"Supprimer",geturl("aviation","bapteme","fonc=delete&id=".$id),"",false,"Voulez-vous supprimer ce baptème ?");
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
		$tmpl_x->parse("corps.lst_dispo_script");
	}


// ---- Messages
	// if ($msg_erreur!="")
	// {
		// affInformation($msg_erreur,"error");
	// }		

	// if ($msg_confirmation!="")
	// {
		// affInformation($msg_confirmation,"ok");
	// }

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
