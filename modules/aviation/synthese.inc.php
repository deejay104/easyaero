<?
/*
	Easy Aero
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
	require_once ($appfolder."/class/synthese.inc.php");
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x->assign("path_module",$module."/".$mod);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Initialise les variables
	$id=checkVar("id","numeric");
	$uid=checkVar("uid","numeric");
	$idvol=checkVar("idvol","numeric");

// ---- Enregistrer
	$msg_erreur="";
	$msg_confirmation="";
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$form_data=checkVar("form_data","array");
		$form_comp=checkVar("form_comp","array");
		$form_compec=checkVar("form_compec","array");
		$form_compold=checkVar("form_compold","array");

		$fiche=new synthese_class($id,$sql);
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
				$msg_erreur=$fiche->Valid($k,$v);
				
				affInformation($msg_erreur,"error");
		  	}
		}

		$n=$fiche->Save();
		if ($id==0)
		{
			$id=$fiche->id;
		}

		$tabok=array();
		if (is_array($form_compold))
		{
			foreach($form_compold as $i=>$s)
			{
				$exo=new exercice_class(0,$sql);
				$exo->Valid("idsynthese",$id);
				$exo->Valid("idexercice",$i);
				$exo->Valid("uid",$fiche->val("uid_pilote"));
				$exo->Valid("progression",$s["progression"]);
				$exo->Valid("progref","A");
				$exo->Save();
				$tabok[$i]="ok";
			}
		}
		if (is_array($form_comp))
		{
			foreach($form_comp as $i=>$s)
			{
				$prog=new exercice_prog_class($i,$sql);
				$exo=new exercice_class(0,$sql);
				
				if (!isset($tabok[$prog->val("idexercice")]))
				{
					$exo->Valid("idsynthese",$id);
					$exo->Valid("idexercice",$prog->val("idexercice"));
					$exo->Valid("uid",$fiche->val("uid_pilote"));
					$exo->Valid("progression",$s["progression"]);
					$exo->Valid("progref",$prog->val("progression"));
					$exo->Save();
					$tabok[$prog->val("idexercice")]="ok";
				}
			}
		}
		if (is_array($form_compec))
		{
			foreach($form_compec as $i=>$s)
			{
				$exo=new exercice_class($i,$sql);
				$exo->Valid("progression",$s["progression"]);
				$exo->Save();
			}
		}

		if ($n>0)
		{
			affInformation("Vos données ont été enregistrées.","ok");
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Charge la fiche de synthèse
	$fiche=new synthese_class($id,$sql);

	$signed=false;
	if ($fiche->val("skey_instructeur")!="")
	{
		$signed=true;
	}
	if ($fiche->val("skey_pilote")!="")
	{
		$signed=true;
	}


	$ok=false;
	if (GetDroit("SupprimeSynthese"))
	{
		$ok=true;
	}

	if ((GetDroit("SignSynthese")) && (!$signed))
	{
		$ok=true;
	}
	if (($fiche->val("uid_pilote")==$gl_uid) && (!$signed))
	{
		$ok=true;
	}

	if ($ok)
	{
		$tmpl_x->parse("corps.supprimer");
	}

// ---- Supprimer
	if ( ($fonc=="supprimer") && ($id>0) )
	{
		if ($ok==true)
		{
			$fiche->Delete();
			$mod="aviation";
			$affrub="syntheses";
		}
	}

// ---- Signe
	if ((($fonc=="Signature Instructeur") || ($fonc=="Sign. Instructeur")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$fiche->Valid("sid_instructeur",$gl_uid);
		$fiche->Valid("sdte_instructeur",now());
		$fiche->Valid("status","signed");

		$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt","sid_instructeur","sdte_instructeur");
		$s=$fiche->sign($t);
		
		$fiche->Valid("skey_instructeur",$s);
		
		$n=$fiche->Save();
		if ($n>0)
		{
			affInformation("Vos données ont été enregistrées.","ok");
		}
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
	
	if ((($fonc=="Signature Elève") || ($fonc=="Sign. Elève")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt","sid_pilote","sdte_pilote");
		$s=$fiche->sign($t);
		
		$fiche->Valid("sid_pilote",$gl_uid);
		$fiche->Valid("sdte_pilote",now());
		$fiche->Valid("skey_pilote",$s);
		$fiche->Valid("status","signed");
		
		$n=$fiche->Save();
		if ($n>0)
		{
			affInformation("Vos données ont été enregistrées.","ok");
		}
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Modifie les infos

	if ($idvol>0)
	{
		$tmpl_x->assign("prev","mod=reservations&rub=reservation&id=".$idvol);
	}
	else if ($uid>0)
	{
		$tmpl_x->assign("prev","mod=aviation&rub=syntheses&uid=".$uid);
	}
	else
	{
		$tmpl_x->assign("prev","mod=aviation&rub=syntheses");
	}
	
// ---- Charge la fiche

	if ((!GetDroit("AccesSynthese")) && ($fiche->val("uid_pilote")!=$id))
	{
		FatalError("Accès non autorisé (AccesSynthese)");
	}

	
	$typeaff="form";
	if (($fiche->val("skey_instructeur")!="") || ($fiche->val("skey_pilote")!=""))
	{
		$typeaff="html";
	}

	$fiche->Render("form",$typeaff);

	if ($fiche->val("idvol")==0)
	{
		if ($idvol==0)
		{
			FatalError("L'id du vol est vide");
		}
		else
		{
			$fiche->Valid("idvol",$idvol);
		}
	}

// ---- Charge les exercices de la fiche de synthèse
	$lst=ListExercices($sql,$id);

	foreach($lst as $i=>$v)
	{
		$c_conf=new exercice_conf_class($v["idexercice"],$sql);
		$c_line=new exercice_class($v["id"],$sql);

		$tmpl_x->assign("aff_exo_description",$c_conf->Aff("description"));
		$tmpl_x->assign("form_progression",$c_line->Aff("progression",$typeaff,"form_compec[".$v["id"]."]"));
		if ($c_conf->val("type")=="panne")
		{
			$tmpl_x->parse("corps.lst_panne");
		}
		else if ($c_conf->val("type")=="ecercice")
		{
			$tmpl_x->parse("corps.lst_exercice");
		}
		else
		{
			$tmpl_x->parse("corps.lst_pedagogique");
		}
	}

// ---- Informations Users
	$resa=new resa_class($fiche->val("idvol"),$sql);
	$pil=new user_class($resa->uid_pilote,$sql);
	$ins=new user_class($resa->uid_instructeur,$sql);

	if ($fiche->val("uid_pilote")==0)
	{
		$fiche->valid("uid_pilote",$resa->uid_pilote);
	}
	if ($fiche->val("uid_instructeur")==0)
	{
		$fiche->valid("uid_instructeur",$resa->uid_instructeur);
	}
	if ($fiche->val("uid_avion")==0)
	{
		$fiche->valid("uid_avion",$resa->uid_ressource);
	}
	if ($fiche->val("dte_vol")=="0000-00-00 00:00:00")
	{
		$fiche->valid("dte_vol",$resa->dte_deb);
	}

// ---- Charge les exercices non acquis
	if ($id==0)
	{
		$lst=ListExercicesNonAcquis($sql,$resa->uid_pilote);

		foreach($lst as $i=>$v)
		{
			$c_conf=new exercice_conf_class($v["id"],$sql);
			$c_line=new exercice_class(0,$sql);

			$tmpl_x->assign("aff_exo_description",$c_conf->Aff("description"));
			$tmpl_x->assign("form_progression",$c_line->Aff("progression",$typeaff,"form_compold[".$v["id"]."]"));

			$tmpl_x->parse("corps.lst_pedagogique");
		}
	}

// ---- Calcul totaux
	$tmpl_x->assign("form_total_att",$fiche->NbAtt());
	$tmpl_x->assign("form_total_rmg",$fiche->NbRmg());

// ---- Affiche les paramètres
	$tmpl_x->assign("form_id",$id);
	$tmpl_x->assign("prev_uid",$uid);
	$tmpl_x->assign("prev_idvol",$idvol);
	$tmpl_x->assign("form_idvol",$fiche->val("idvol"));
	$tmpl_x->assign("form_uid_pilote",$fiche->val("uid_pilote"));
	$tmpl_x->assign("form_uid_instructeur",$fiche->val("uid_instructeur"));
	$tmpl_x->assign("form_uid_avion",$fiche->val("uid_avion"));
	
	$tmpl_x->assign("form_eleve",$pil->aff("fullname"));
	$tmpl_x->assign("form_instructeur",$ins->aff("fullname"));
	$tmpl_x->assign("form_dtevol",$resa->Aff("dte_deb"));
	$tmpl_x->assign("form_duree",AffTemps($resa->tpsreel));

	$tmpl_x->assign("form_cumuldc",$pil->AffNbHeuresSynthese($fiche->val("dte_vol"),"dc"));
	$tmpl_x->assign("form_cumulsolo",$pil->AffNbHeuresSynthese($fiche->val("dte_vol"),"solo"));

	$ress=new ress_class($resa->uid_ressource,$sql);
	$tmpl_x->assign("form_immat",$ress->Aff("immatriculation"));

		
	if ($typeaff=="form")
	{
		$tmpl_x->parse("corps.aff_addcomp");
		$tmpl_x->parse("corps.submit");
	}

	if ((GetDroit("SignSynthese")) && ($fiche->val("skey_instructeur")=="") && ($id>0))
	{
		$tmpl_x->parse("corps.signinst"); 
	}
	else if ($fiche->val("skey_instructeur")!="") 
	{
		$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt","sid_instructeur","sdte_instructeur");
		$usr=new user_class($fiche->data["sid_instructeur"],$sql);

		$tmpl_x->assign("form_signinst_name",$usr->aff("fullname"));
		$tmpl_x->assign("form_signinst_dte",$fiche->aff("sdte_instructeur"));
		$tmpl_x->assign("form_signinst_key",$fiche->sign($t));
		
		$tmpl_x->parse("corps.signedinst"); 
	}
	
	if ((GetDroit("SignSynthese") || ($fiche->val("uid_pilote")==$gl_uid)) && ($fiche->val("skey_pilote")=="") && ($id>0))
	{
		$tmpl_x->parse("corps.signeleve"); 
	}
	else if ($fiche->val("skey_pilote")!="") 
	{
		$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt","sid_pilote","sdte_pilote");
		$usr=new user_class($fiche->data["sid_pilote"],$sql);

		$tmpl_x->assign("form_signeleve_name",$usr->aff("fullname"));
		$tmpl_x->assign("form_signeleve_dte",$fiche->aff("sdte_pilote"));
		$tmpl_x->assign("form_signeleve_key",$fiche->sign($t));
		
		$tmpl_x->parse("corps.signedeleve"); 
	}
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>