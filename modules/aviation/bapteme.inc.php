<?
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


<?
	if (!is_numeric($id))
	  { $id=0; }

	if (!GetDroit("AccesBapteme"))
	  { FatalError("Accès non autorisé (AccesBapteme)"); }

	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("bapteme.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

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
	else if (($fonc=="Enregistrer") && (($btm->data["status"]==0) || ($btm->data["status"]==1) || ($btm->data["status"]==2)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$btm->Valid("id_pilote",$form_data["id_pilote"],false);
		$btm->Valid("id_avion",$form_data["id_avion"],false);
		$btm->Valid("dte",date2sql($form_data["dte_j"])." ".$form_data["dte_h"],false);

		if ( ($form_data["id_pilote"]>0) && ($form_data["id_avion"]>0) && ($form_data["dte_j"]!='0000-00-00') && ($form_data["dte_h"]!='00:00') )
		  { $btm->Valid("status","2"); }

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
		$btm->data["status"]=2;
		$btm->Save();

		$msg_confirmation.=($msg_resa!="") ? $msg_resa : "Réservation confirmée.<BR>";
	}

// ---- Attribuer le bapteme
	if (($fonc=="affecte") && ($id>0))
	  {
		$btm->data["id_pilote"]=$gl_uid;
		$btm->data["status"]=1;
		$btm->Save();
	  }

// ---- Vol effectué
	if (($fonc=="effectue") && ($id>0))
	{
		$btm->data["status"]=3;
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

	// Menu
	if (GetDroit("CreeBapteme"))
	  {
	  	$tmpl_x->parse("infos.ajout");
	  }
	if (GetDroit("ModifBapteme"))
	  {
	  	$tmpl_x->parse("infos.modification");	  	
	  }
	if (GetDroit("SupprimeBapteme"))
	  {
	  	$tmpl_x->parse("infos.suppression");
	  }

	
	$ress = new ress_class($btm->data["id_avion"],$sql);

	if ($btm->data["id_pilote"]==0)
	  {
	  	$tmpl_x->parse("infos.affecter");
	  }

	if (($btm->data["status"]==1) || ($btm->data["id_resa"]==0))
	{
		$tmpl_x->parse("infos.planifier");
	}

	if (($ress->CheckDispo(strtotime($btm->data["dte"]),strtotime($btm->data["dte"])+45*60)) && ($btm->data["id_pilote"]>0) && ($btm->data["id_avion"]>0) && ($btm->data["dte"]!='0000-00-00 00:00'))
	{
	  	$tmpl_x->parse("infos.reserver");
	}

	if ($btm->data["status"]==2)
	{
	  	$tmpl_x->parse("infos.effectue");
	}


	if (($fonc=="planifier") && (($btm->data["status"]==0) || ($btm->data["status"]==1) || ($btm->data["status"]==2)))
	{
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->data["id_pilote"],"form_data[id_pilote]",$type="",$sexe="",$order="std",$virtuel="non"));
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
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->data["id_pilote"],"form_data[id_pilote]",$type="",$sexe="",$order="std",$virtuel="non"));
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
