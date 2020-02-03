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

// ---- Initialisation des variables

  	$ok=1;
	$msg_err="";

//echo "Mettre le test de résa ici";

// --- Charge la réservation
	$id=checkVar("id","numeric");
	$prev=checkVar("prev","varchar");
	$fonch=checkVar("fonch","varchar");

	$form_data=checkVar("form_data","array");

	$form_accept=checkVar("form_accept","varchar",3);
	$form_uid_ress=checkVar("form_uid_ress","numeric");
	$form_uid_pilote=checkVar("form_uid_pilote","numeric");
	$form_uid_debite=checkVar("form_uid_debite","numeric");
	$form_uid_instructeur=checkVar("form_uid_instructeur","numeric");
	$form_tarif=checkVar("form_tarif","varchar");
	$form_destination=checkVar("form_destination","varchar");
	$form_tpsestime=checkVar("form_tpsestime","numeric");
	$form_nbpersonne=checkVar("form_nbpersonne","numeric");
	$form_invite=checkVar("form_invite","varchar");
	$form_dte_deb=checkVar("form_dte_deb","varchar");
	$form_hor_deb=checkVar("form_hor_deb","varchar");
	$form_dte_fin=checkVar("form_dte_fin","varchar");
	$form_hor_fin=checkVar("form_hor_fin","varchar");
	$form_description=checkVar("form_description","text");
	$form_horadeb=checkVar("form_horadeb","varchar");
	$form_horafin=checkVar("form_horafin","varchar");
	$form_tpsreel=checkVar("form_tpsreel","varchar");
	$form_potentielh=checkVar("form_potentielh","varchar");
	$form_potentielm=checkVar("form_potentielm","varchar");
	$form_carbavant=checkVar("form_carbavant","varchar");
	$form_carbapres=checkVar("form_carbapres","varchar");
	$form_prixcarbu=checkVar("form_prixcarbu","varchar");
	
	$resa["resa"]=new resa_class($id,$sql);

// ---- Vérifie les infos
	if (($fonc=="Enregistrer") || ($fonc=="Actualiser") || ($fonch=="centrage") || ($fonch=="synthese"))
	  {
		$ok=1;

		if ($form_uid_pilote>0)
		{
			$resa["pilote"]=new user_class($form_uid_pilote,$sql,false,true);
		}
		else
		{
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql,false,true);
		}

		if ($form_uid_instructeur>0)
		{
			$resa["instructeur"]=new user_class($form_uid_instructeur,$sql,false,true);
		}
		else
		{
			$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql,false,true);
		}

		
		if ($resa["pilote"]->isSoldeNegatif())
		{
			$s=$resa["pilote"]->CalcSolde();
		  	$msg_err.="<u>Le compte du pilote est NEGATIF ($s €)</u>.<br />";
		  	$msg_err.="Appeller le trésorier pour l'autorisation d'un découvert.<br />";
			$ok=4;
		}

		if ($resa["resa"]->edite=='non')
		{
		  	$msg_err.="<u>Réservation déjà saisie en compta</u>.<br />";
		  	$msg_err.="Il n'est plus possible de modifier cette réservation car elle a déjà été saisie en compta.<br />";
			$ok=3;
		}

		// Vérifie si le pilote est laché sur l'avion
		if ((!$resa["pilote"]->CheckLache($form_uid_ress)) && ($form_uid_instructeur==0))
		{
		  	$msg_err.="<u>Réservation impossible</u>.<br />";
		  	$msg_err.="Le pilote sélectionné n'est pas laché sur cet avion.<br />";
		  	$msg_err.="Il n'est pas possible de réserver sans instructeur.<br />";
			$ok=3;
		}

		// Vérifie si le pilote est autorisé
		if (!$resa["pilote"]->CheckDroit("TypePilote"))
		{ 
		  	$msg_err.="<u>Réservation impossible</u>.<br />";
			$msg_err.="Le pilote sélectionné n'a pas le droit d'effectuer de réservation d'avion<BR>";
			$ok=3;
		}

		if ($resa["resa"]->edite!='non')
		{
		  	if ($ok==1)
			{
				$resa["resa"]->description=$form_description;
				$resa["resa"]->uid_pilote=$form_uid_pilote;
				$resa["resa"]->uid_debite=$form_uid_debite;
				$resa["resa"]->uid_instructeur=$form_uid_instructeur;
				$resa["resa"]->uid_ressource=$form_uid_ress;
				$resa["resa"]->tarif=$form_tarif;
				$resa["resa"]->destination=$form_destination;
				$resa["resa"]->nbpersonne=$form_nbpersonne;
				$resa["resa"]->invite=$form_invite;
				$resa["resa"]->accept=$form_accept;
				$resa["resa"]->tpsestime=$form_tpsestime;
				$resa["resa"]->dte_deb="$form_dte_deb $form_hor_deb";
				$resa["resa"]->dte_fin="$form_dte_fin $form_hor_fin";
				$resa["resa"]->tpsreel=$form_tpsreel;
				$resa["resa"]->horadeb=$form_horadeb;
				$resa["resa"]->horafin=$form_horafin;
				$resa["resa"]->potentielh=$form_potentielh;
				$resa["resa"]->potentielm=$form_potentielm;

				$resa["resa"]->carbavant=$form_carbavant;
				$resa["resa"]->carbapres=$form_carbapres;
				$resa["resa"]->prixcarbu=$form_prixcarbu;

				if (isset($form_data["taxe"]))
				{
					$resa["resa"]->Valid("taxe",$form_data["taxe"]);
				}
			}


			// On peut mettre à jour les données de vols même si on est en négatif.
			if (($id>0) && ($ok==4) && (($form_tpsreel!="") || ($form_horadeb!="") || ($form_horafin!="")))
			{
				$ok=1;

				$resa["resa"]->dte_deb=sql2date($resa["resa"]->dte_deb);
				$resa["resa"]->dte_fin=sql2date($resa["resa"]->dte_fin);

				$resa["resa"]->tpsreel=$form_tpsreel;
				$resa["resa"]->horadeb=$form_horadeb;
				$resa["resa"]->horafin=$form_horafin;
			}
		}
	}

// ---- Supprime la réservation
	else if (($fonc=="delete") && ($id>0))
	{
		$ok=0;
		$resa["resa"]=new resa_class($id,$sql);

		if ($resa["resa"]->uid_pilote!=$gl_uid)
		{
			$resp=new user_class($resa["resa"]->uid_pilote,$sql);
			$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
		
			$tabvar=array();
			$tabvar["pilote"]=$resp->Aff("fullname","val");
			$tabvar["editeur"]=$myuser->Aff("fullname","val");
			$tabvar["avion"]=strtoupper($resr->val("immatriculation"));
			$tabvar["dte_deb"]=sql2date($resa["resa"]->dte_deb);
			$tabvar["dte_fin"]=sql2date($resa["resa"]->dte_fin);
			$tabvar["url"]=$MyOpt["host"]."/index.php?mod=reservations&rub=reservation&id=".$resa["resa"]->id;

			SendMailFromFile("",$resp->mail,array(),"",$tabvar,"resa_supp","");

			affInformation($resp->Aff("fullname","val")." a été notifié".(($resp->data["sexe"]=="F") ? "e" : "")." que sa réservation a été supprimée.","ok");
		}

		$ress=$resa["resa"]->uid_ressource;
		$resa["resa"]->Delete();
		$affrub="index";
	}
	else
	{
	  	$ok=3;
	}



// ---- Enregistre
	$affrub="index";
	$jour=(isset($resa["resa"]->dte_deb)) ? $resa["resa"]->dte_deb : now();
	$msg_err2="";
	if (($ok==1) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$msg_err2.=$resa["resa"]->Save();
		if ($id==0)
		{
			$id=$resa["resa"]->id;
		}

		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);

		if (($msg_err2=="") && ($msg_err==""))
		{
			if (($id==0) && ($resa["resa"]->invite=='oui'))
			{
				$tabvar=array(
					"dte_deb"=>sql2date($resa["resa"]->dte_deb,"jour"),
					"dte_deb_heure"=>sql2date($resa["resa"]->dte_deb,"heure"),
					"dte_fin_heure"=>sql2date($resa["resa"]->dte_fin,"heure"),
					"pilote"=>$resa["pilote"]->Aff("fullname","val"),
				);

				SendMailFromFile("","",array(),"",$tabvar,"invite","","actualite");
			}

			$resr=new ress_class($resa["resa"]->uid_ressource,$sql);

			if ($resa["resa"]->uid_pilote!=$gl_uid)
			{
				// $resp=new user_class($resa["resa"]->uid_pilote,$sql);
				
				$tabvar=array();
				$tabvar["pilote"]=$resa["pilote"]->Aff("fullname","val");
				$tabvar["editeur"]=$myuser->Aff("fullname","val");
				$tabvar["avion"]=strtoupper($resr->val("immatriculation"));
				$tabvar["dte_deb"]=sql2date($resa["resa"]->dte_deb);
				$tabvar["dte_fin"]=sql2date($resa["resa"]->dte_fin);
				$tabvar["url"]=$MyOpt["host"]."/index.php?mod=reservations&rub=reservation&id=".$resa["resa"]->id;

				SendMailFromFile("",$resa["pilote"]->mail,array(),"",$tabvar,"resa_modif","");

				affInformation($resa["pilote"]->Aff("fullname","val")." a été notifié".(($resa["pilote"]->data["sexe"]=="F") ? "e" : "")." que sa réservation a été mise à jour.","ok");
			}
			
			// Email l'instructeur
			if ($resa["resa"]->uid_instructeur>0)
			{
				$resi=new user_class($resa["resa"]->uid_instructeur,$sql);
				
				$tabvar=array();
				$tabvar["pilote"]=$resa["pilote"]->Aff("fullname","val");
				$tabvar["avion"]=strtoupper($resr->val("immatriculation"));
				$tabvar["dte_deb"]=sql2date($resa["resa"]->dte_deb);
				$tabvar["dte_fin"]=sql2date($resa["resa"]->dte_fin);
				$tabvar["url"]=$MyOpt["host"]."/index.php?mod=reservations&rub=reservation&id=".$resa["resa"]->id;

				$ics = new ICS(array());
				$ics->set(array(
					'summary' => "Réservation ".strtoupper($resr->val("immatriculation"))." avec ".$resa["pilote"]->val("fullname"),
					'description' => preg_replace("/\s\s+/","\\n",$resa["resa"]->description),
					'dtstart' => gmdate("Y-m-d H:i",strtotime($resa["resa"]->dte_deb)),
					'dtend' => gmdate("Y-m-d H:i",strtotime($resa["resa"]->dte_fin)),
					'url' => $MyOpt["host"]."/index.php?mod=reservations&rub=reservation&id=".$resa["resa"]->id,
				));

				$f=array();
				$f[0]["type"]="text";
				$f[0]["nom"]="reservation.ics";
				$f[0]["data"]=$ics->to_string();

				SendMailFromFile($resa["pilote"]->mail,$resi->mail,array(),"",$tabvar,"resa_inst",$f);
			}
			
			// Valide la page
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
			$affrub=($prev=="scheduler") ? "scheduler" : "index";
			$ress=$form_uid_ress;
			$ok=0;
		}
		else
		  {
		  	$msg_err.=$msg_err2;
		  	$ok=3;
		  }
	  }

	if ($fonc=="Annuler")  	
	{
		$ok=0;
		$affrub=($prev=="scheduler") ? "scheduler" : "index";
		$ress=$form_uid_ress;
	}
	else if ($fonch=="centrage")  	
	{
		$ok=0;
		$idvol=$id;
		$mod="aviation";
		$affrub="centrage";
	}
	else if ($fonch=="synthese")  	
	{
		$ok=0;
		$mod="aviation";
		$_REQUEST["id"]=0;
		$_REQUEST["idvol"]=$id;
		$affrub="synthese";
	}
	else if ($fonc=="Actualiser")  	
	{
		$ok=2;
	  	$affrub="reservation";
	}
	else if ($ok>1)
	{
	  	$affrub="reservation";
	}

?>
