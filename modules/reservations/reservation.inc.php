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
	require_once ("class/echeance.inc.php");

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/synthese.inc.php");

// ---- Charge le template
	// $tmpl_x = new XTemplate (MyRep("reservation.htm"));

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$prev=checkVar("prev","varchar");
	$ress=checkVar("ress","numeric");
	$heure=checkVar("heure","numeric");
	$jstart=checkVar("jstart","numeric");
	$jend=checkVar("jend","numeric");
	$jour=checkVar("jour","date");

	$res=array();
	if (!isset($msg_err))
	{
		$msg_err="";
	}
	if ((!isset($ok)) || (!is_numeric($ok)))
	{
		$ok=checkVar("ok","numeric");
	}
	
// ---- Charge les données de la réservation
	if (($id>0) && ($ok!=3))
	{
		// Charge une nouvelle réservation
		$resa["resa"]=new resa_class($id,$sql);
		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	else if ($ok!=3)
	{
		$id="";
		if ($heure=="") { $heure="8"; }
		if ($jour=="0000-00-00") { $jour=date("Y-m-d"); }

		$dte_deb=$jour." ".$heure.":00:00";
		$dte_fin=$jour." ".($heure+1).":00:00";
		if ((isset($jstart)) && ($jstart>0))
		{
			$fh=date("O",floor($jstart)/1000+4*3600)/100;
			$dte_deb=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
		}
			
		if ((isset($jend)) && ($jend>0))
		  {
				$fh=date("O",floor($jend)/1000+4*3600)/100;
				$dte_fin=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
		  }

		$resa["resa"]=new resa_class(0,$sql);

		$resa["resa"]->dte_deb=$dte_deb;
		$resa["resa"]->dte_fin=$dte_fin;
		$resa["resa"]->uid_pilote=$gl_uid;
		// $resa["resa"]->uid_instructeur=$res["uid_instructeur"];
		$resa["resa"]->uid_instructeur=0;
		$resa["resa"]->uid_ressource=$ress;
		// $resa["resa"]->type=$res_user["type"];
		$resa["resa"]->uid_maj=$gl_uid;
		$resa["resa"]->dte_maj=date("Y-m-d H:i:s");

		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	else if ($ok==3)
	{
	  	// Il y a eu une erreur on recharge les valeurs postées
		$ress=$form_uid_ress;

		$resa["resa"]=new resa_class(($id>0) ? $id : 0,$sql);

	  	$resa["resa"]->dte_deb=date2sql($form_dte_deb)." $form_hor_deb";
	  	$resa["resa"]->dte_fin=date2sql($form_dte_fin)." $form_hor_fin";
	  	$resa["resa"]->uid_pilote=$form_uid_pilote;
	  	$resa["resa"]->uid_instructeur=$form_uid_instructeur;
	  	$resa["resa"]->uid_ressource=$form_uid_ress;
	  	$resa["resa"]->tpsestime=$form_tpsestime;
	  	$resa["resa"]->tpsreel=$form_tpsreel;
	  	$resa["resa"]->horadeb=$form_horadeb;
	  	$resa["resa"]->horafin=$form_horafin;
	  	$resa["resa"]->potentielh=$form_potentielh;
	  	$resa["resa"]->potentielm=$form_potentielm;
	  	$resa["resa"]->carbavant=$form_carbavant;
	  	$resa["resa"]->carbapres=$form_carbapres;
	  	$resa["resa"]->prixcarbu=$form_prixcarbu;
	  	$resa["resa"]->destination=$form_destination;
	  	$resa["resa"]->nbpersonne=$form_nbpersonne;
	  	$resa["resa"]->invite=$form_invite;
	  	$resa["resa"]->description=$form_description;
		$resa["resa"]->uid_maj=$gl_uid;
	  	$resa["resa"]->dte_maj=date("Y-m-d H:i:s");

		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	
	$lstress=ListeRessources($sql);
	if ($resa["resa"]->uid_ressource==0)
	{
		foreach($lstress as $i=>$rid)
		{
			if ($resa["resa"]->uid_ressource==0)
			{
				$resa["resa"]->uid_ressource=$rid;
			}
		}
	}

// ---- Charge le template


	if ($resa["resa"]->edite=='non')
	{
		$tmpl_x = new XTemplate (MyRep("reservation-visu.htm"));
	  	$tmpl_hora = new XTemplate (MyRep("horametre-visu.htm"));
	}
	else
	{
	  	$tmpl_hora = new XTemplate (MyRep("horametre.htm"));
	}

// ---- Initialise les variables
	$ok_aff=0;
	$ok_save=0;
	$ok_inst=0;
	
	$resusr=new user_class($resa["resa"]->uid_pilote,$sql,true);
	$resa["resa"]->pilote_data=$resusr->data;

// ---- Vérifie les échéances
	$lstdte=VerifEcheance($sql,$resa["resa"]->uid_pilote,"utilisateurs");

	if ( (is_array($lstdte)) && (count($lstdte)>0) )
	{
		foreach($lstdte as $i=>$d)
		{
			if ($d["dte_echeance"]!="")
			{
				$m ="<b><font color='red'>L'échéance ".$d["description"]." a été dépassée (".sql2date($d["dte_echeance"]).").</font></b><br />";
			}
			else
			{
				$m ="<b><font color='red'>Vous n'avez pas de date d'échéance pour ".$d["description"].".</font></b><br />";
			}
			
			if ($d["resa"]=="instructeur")
			{
				$m.="La présence d'un instructeur est obligatoire.<br />";
				affInformation($m,"warning");

				$ok_inst=1;
			}
			else if ($d["resa"]=="obligatoire")
			{
				$m.="La réservation n'est pas possible.<br />";
				affInformation($m,"error");
				$save=1;
			}

		}
	}

// ---- Vérifie si le compte est provisionné

	// $s=$resa["pilote"]->CalcSolde();
	// if ($s<-$resa["pilote"]->data["decouvert"])
	if ($resa["pilote"]->isSoldeNegatif())
	{
		$s=$resa["pilote"]->CalcSolde();
		$m ="<b><font color='red'>Le compte du pilote est NEGATIF ($s €).</font></b><br />";
		$m.="Appeller le trésorier pour l'autorisation d'un découvert.<br />";
		affInformation($m,"error");

		if ($id==0)
		{
			$ok_save=1;
		}
	}


// ---- Vérifie si l'utilisateur est laché sur l'avion
	if (!$resa["pilote"]->CheckLache($resa["resa"]->uid_ressource))
	{
		$m="<b><font color='red'>Vous n'êtes pas laché sur cet avion.</font></b><br />La présence d'un instructeur est obligatoire.";
		affInformation($m,"warning");

		$ok_inst=1;
	}
	
// ---- Initialisation des variables
	$tmpl_x->assign("id", $id);
	$tmpl_x->assign("prev", $prev);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche les messages d'erreurs
	if ($msg_err!="")
	{ 
		affInformation($msg_err,"error");
	}
	

// ---- Affiche les infos de la réservation

	// Dernière mise à jour
	$maj=new user_class($resa["resa"]->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $maj->aff("fullname")." le ".sql2date($resa["resa"]->dte_maj));


	// Historique des modifications
	$lstmaj=$resa["resa"]->Historique();
	$txtmaj="";
	foreach($lstmaj as $i=>$k)
	{
	    $maj=new user_core($k["uid"],$sql);
	  	$txtmaj.=sql2date($k["dte"])." - ";

		if ($k["type"]=="ADD")
			$txtmaj.="Création par";
		else if ($k["type"]=="MOD")
			$txtmaj.="Modification par";
		else if ($k["type"]=="DEL")
			$txtmaj.="Suppression par";

	  	$txtmaj.=" ".$maj->aff("fullname","val")."<br>";
	}
	$tmpl_x->assign("info_historique",$txtmaj);


// **************************************

	$resa["resa"]->Render("form","form");

// **************************************
	
	$tmpl_x->assign("form_uid_ress", $resa["resa"]->uid_ressource);

	// Récupère la liste des ressources
	$lstress=ListeRessources($sql,array("oui"));

	foreach($lstress as $i=>$rid)
	{
		$resr=new ress_class($rid,$sql);

		// Initilialise l'id de ressouce s'il est vide

		// Rempli la liste dans le template
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", strtoupper($resr->val("immatriculation")));
		if ($resa["resa"]->uid_ressource==$resr->id)
		{
			$tmpl_x->assign("chk_avion", "selected");
			$tmpl_x->assign("uid_avionrmq", $resr->id);
			$tmpl_x->assign("aff_nom_avion", strtoupper($resr->val("immatriculation")));
		}
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.aff_reservation.lst_avion");
	}
	

	// Liste des pilotes	
	$lst=ListActiveUsers($sql,"prenom,nom",array("TypePilote"));
	
	$txt="-";
	foreach($lst as $i=>$tmpuid)
	{
		$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("uid_pilote", $resusr->id);
		$tmpl_x->assign("nom_pilote", $resusr->val("fullname"));
		if ($resa["resa"]->uid_pilote==$resusr->id)
		{
			$tmpl_x->assign("chk_pilote", "selected");
		}
		else
		{
			$tmpl_x->assign("chk_pilote", "");
		}
		$tmpl_x->parse("corps.aff_reservation.lst_pilote");
	}
	
	$resusr=new user_class($resa["resa"]->uid_pilote,$sql);
	$tmpl_x->assign("aff_nom_pilote", $resusr->aff("fullname"));

	
	// Liste des pilotes débité	
	$lst=ListActiveUsers($sql,"prenom,nom","","");

	$txt="-";
	foreach($lst as $i=>$tmpuid)
	  {
	  	$resusr=new user_class($tmpuid,$sql);
			$tmpl_x->assign("uid_debite", $resusr->id);
			$tmpl_x->assign("nom_debite", $resusr->val("fullname"));
			if ($resa["resa"]->uid_debite==$resusr->id)
			{
			  	$tmpl_x->assign("chk_debite", "selected");
			}
			else
			{
				$tmpl_x->assign("chk_debite", "");
			}
			$tmpl_x->parse("corps.aff_reservation.lst_debite");
	}

	$resusr=new user_class($resa["resa"]->uid_debite,$sql);
	$tmpl_x->assign("aff_nom_debite", $resusr->aff("fullname"));

	
	if ($ok_inst==0)
	{
		$tmpl_x->assign("uid_instructeur", "0");
		$tmpl_x->assign("nom_instructeur", "Aucun");
		$tmpl_x->assign("chk_instructeur", "");
		$tmpl_x->parse("corps.aff_reservation.aff_instructeur.lst_instructeur");
	}


	// Liste des instructeurs
	$lst=ListActiveUsers($sql,"prenom,nom",array("TypeInstructeur"));
	$tmpl_x->assign("aff_nom_instructeur", "-");

	$txt="-";
	foreach($lst as $i=>$tmpuid)
	{ 
		$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("uid_instructeur", $resusr->id);
		$tmpl_x->assign("nom_instructeur", $resusr->Aff("fullname","val"));
		if ($resa["resa"]->uid_instructeur==$resusr->id)
		{
			$tmpl_x->assign("chk_instructeur", "selected");
			$txt=$resusr->Aff("fullname");
		}
		else
		{
			$tmpl_x->assign("chk_instructeur", "");
		}
		
		if (($ok_inst==1) && ($resa["resa"]->uid_instructeur==0))
		{
			$resa["resa"]->uid_instructeur=$tmpuid;
		}

		$tmpl_x->parse("corps.aff_reservation.aff_instructeur.lst_instructeur");
	}

	$resusr=new user_class($resa["resa"]->uid_instructeur,$sql);
	$tmpl_x->assign("aff_nom_instructeur", $resusr->aff("fullname"));

	$tmpl_x->assign("form_uid_instructeur", $resa["resa"]->uid_instructeur);

	if ($ok==2)
	{
		$tmpl_x->assign("deb", strtotime(date2sql($form_dte_deb)));
		$tmpl_x->assign("fin", strtotime(date2sql($form_dte_fin)));
	}
	else
	{
		$tmpl_x->assign("deb", strtotime($resa["resa"]->dte_deb));
		$tmpl_x->assign("fin", strtotime($resa["resa"]->dte_fin));
	}

	$tmpl_x->parse("corps.aff_reservation.aff_instructeur");


	// Tarif

	$query="SELECT * FROM ".$MyOpt["tbl"]."_tarifs WHERE reservation<>'' AND ress_id='".$resa["resa"]->uid_ressource."'";		
	$sql->Query($query);		
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

		$tmpl_x->assign("tarif", $sql->data["code"]);
		$tmpl_x->assign("nom_tarif", $sql->data["reservation"]);
		if ($sql->data["code"]==$resa["resa"]->tarif)
		{
			$tmpl_x->assign("chk_tarif", "selected");
		}
		else if ( ($sql->data["defaut_ins"]=="oui") && ($resa["resa"]->uid_instructeur>0) && ($resa["resa"]->tarif=="") )
		{
			$tmpl_x->assign("chk_tarif", "selected");
		}
		else if ( ($sql->data["defaut_pil"]=="oui") && ($resa["resa"]->tarif=="") )
		{
			$tmpl_x->assign("chk_tarif", "selected");
		}
		else
		{
			$tmpl_x->assign("chk_tarif", "");
		}

		$tmpl_x->parse("corps.aff_reservation.aff_tarif.lst_tarif");

	}
	$tmpl_x->parse("corps.aff_reservation.aff_tarif");

	
	// POB
	if (($resa["resa"]->uid_instructeur>0) && ($resa["resa"]->nbpersonne<2))
	{
		$resa["resa"]->nbpersonne=2;
	}
	
	$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
	for ($i=1;$i<=$resr->data["places"];$i++)
	{
		$tmpl_x->assign("pob", $i);
		$tmpl_x->assign("chk_pob", ($resa["resa"]->nbpersonne==$i) ? "checked='checked'" : "");
		$tmpl_x->parse("corps.aff_reservation.lst_pob");
	}
	
	// Horaires
	if ($ok==2)
	  {
		$tmpl_x->assign("form_dte_deb", $form_dte_deb);
		$tmpl_x->assign("form_dte_debsql", date2sql($form_dte_deb));
		$tmpl_x->assign("form_hor_deb", $form_hor_deb);
		$tmpl_x->assign("form_dte_fin", $form_dte_fin);
		$tmpl_x->assign("form_dte_finsql", date2sql($form_dte_fin));
		$tmpl_x->assign("form_hor_fin", $form_hor_fin);
		
	  }
	else
	  {
		$tmpl_x->assign("form_dte_deb", sql2date($resa["resa"]->dte_deb,"jour"));
		$tmpl_x->assign("form_dte_debsql", date2sql($resa["resa"]->dte_deb));
		$tmpl_x->assign("form_hor_deb", sql2date($resa["resa"]->dte_deb,"heure"));

		$tmpl_x->assign("form_dte_fin", sql2date($resa["resa"]->dte_fin,"jour"));
		$tmpl_x->assign("form_dte_finsql", date2sql($resa["resa"]->dte_fin));
		$tmpl_x->assign("form_hor_fin", sql2date($resa["resa"]->dte_fin,"heure"));
	  }

	// Sauve le jour de la réservation pour afficher cette date lors du retour sur le calendrier
	$_SESSION["caltime"]=strtotime($resa["resa"]->dte_deb);

	$tmpl_x->assign("form_destination", $resa["resa"]->destination);
	$tmpl_x->assign("form_nbpassager", $resa["resa"]->nbpersonne);
	$tmpl_x->assign("chk_invite_".$resa["resa"]->invite, "checked='checked'");

	$tmpl_x->assign("form_tpsestime", $resa["resa"]->tpsestime);

	$tmpl_hora->assign("form_tpsreel", $resa["resa"]->tpsreel);
	$tmpl_hora->assign("form_horadeb", $resa["resa"]->horadeb);
	$tmpl_hora->assign("form_horafin", $resa["resa"]->horafin);

	// Affiche l'horamètre
	$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
	$t=$resr->CalcHorametre($resa["resa"]->horadeb,$resa["resa"]->horafin);
	
	$tmpl_hora->assign("tps_hora", (($t>0) ? AffTemps($t) : "0h 00"));

	if ($MyOpt["updateBloc"]=="on")
	{
		$tmpl_hora->parse("aff_horametre.updateBloc");
	}
	
	$tmpl_hora->parse("aff_horametre");
	$tmpl_x->assign("aff_horametre", $tmpl_hora->text("aff_horametre"));

	// Description de la réservation
	$tmpl_x->assign("form_description", $resa["resa"]->description);

	// Potentiel restant
	$tmpl_x->assign("potentiel", $resa["resa"]->AffPotentiel("prev"));
	
	$tmpl_x->assign("form_potentiel", $resa["resa"]->AffPotentiel("estime"));

	$tmpl_x->assign("form_tpsvol", $resa["resa"]->AffTempsVols("estime"));

	$tmpl_x->assign("form_potentielh", $resa["resa"]->potentielh);
	$tmpl_x->assign("form_potentielm", $resa["resa"]->potentielm);

	$tmpl_x->assign("form_carbavant", $resa["resa"]->carbavant);
	$tmpl_x->assign("form_carbapres", $resa["resa"]->carbapres);
	$tmpl_x->assign("form_prixcarbu", ($resa["resa"]->prixcarbu>0) ? $resa["resa"]->prixcarbu : "0");

	// Texte d'acceptation
	if ($MyOpt["ChkValidResa"]=="on")
	{
		if ($resa["pilote"]->NombreVols(floor($MyOpt["maxDernierVol"]/30),"val",$resa["resa"]->uid_ressource)>0)
		{
			$tmpl_x->parse("corps.aff_reservation.aff_chkreservation_ok");
		}
		else
		{
			$tmpl_x->assign("TxtValidResa", $MyOpt["TxtValidResa"]);
			$tmpl_x->parse("corps.aff_reservation.aff_chkreservation");
		}
	}

	if ( ($resa["resa"]->edite!='non') && ($ok_save==0) )
	{
		$tmpl_x->parse("corps.aff_reservation.aff_enregistrer");
	}

// ---- Menu


	// Affiche le boutton supprimer
	if ($resa["resa"]->edite!="non")
	{
		addPageMenu("",$mod,"Supprimer",geturl("reservations","reservation_sauve","fonc=delete&id=".$id),"icn32_supprimer.png",false,"Souhaitez-vous supprimer cette réservation ?");
	}
	
	// Liste les fiches de synthèse du vol
	if ($id>0)
	{
		$t=ListSyntheseVol($sql,$id);

		if (count($t)>0)
		{
			foreach($t as $i=>$d)
			{
				$fiche=new synthese_class($i,$sql);
				$tmpl_x->assign("sid", $i);
				$tmpl_x->assign("synt_refffa", $fiche->val("refffa"));
				$tmpl_x->assign("synt_module", $fiche->Aff("module"));
				$tmpl_x->assign("synt_progression", $fiche->Aff("progression"));
				$tmpl_x->assign("synt_status", $fiche->Aff("status"));
				$tmpl_x->parse("corps.aff_reservation.aff_syntheses.lst_synthese");		
			}
			$tmpl_x->parse("corps.aff_reservation.aff_syntheses");
		}
		// Ajoute une synthèse de vol
		if ((GetDroit("CreeSynthese")) && (count($t)==0))
		{
			$tmpl_x->parse("infos.synthese"); 
		}
	}

// ---- Affichage		
	if ($ok_aff==0)
	{ 
		$tmpl_x->parse("corps.aff_reservation"); 
    }
	


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
