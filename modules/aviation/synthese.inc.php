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
	$form_data=checkVar("form_data","array");
	
// ---- Enregistrer
	$msg_erreur="";
	$msg_confirmation="";
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
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
		if ($n>0)
		{
			affInformation("Vos données ont été enregistrées.","ok");
		}
		if ($id==0)
		{
			$id=$fiche->id;
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
// ---- Supprimer
	if (($fonc=="supprimer") && ($id>0) && (GetDroit("SupprimeSynthese")))
	{
		$fiche=new synthese_class($id,$sql);
		$fiche->Delete();
		$mod="aviation";
		$affrub="syntheses";
	}

// ---- Signe
	if (($fonc=="Signature Instructeur") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$fiche=new synthese_class($id,$sql);

		$fiche->Valid("sid_instructeur",$gl_uid);
		$fiche->Valid("sdte_instructeur",now());

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
	
	if (($fonc=="Signature Elève") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$fiche=new synthese_class($id,$sql);

		$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt","sid_pilote","sdte_pilote");
		$s=$fiche->sign($t);
		
		$fiche->Valid("sid_pilote",$gl_uid);
		$fiche->Valid("sdte_pilote",now());
		$fiche->Valid("skey_pilote",$s);
		
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
		$tmpl_x->assign("prev","mod=aviation&rub=syntheses&id=".$uid);
	}
	else
	{
		$tmpl_x->assign("prev","mod=aviation&rub=syntheses");
	}
	
// ---- Charge la fiche

	$fiche = new synthese_class($id,$sql);

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
	
	$resa=new resa_class($fiche->val("idvol"),$sql);
	$pil=new user_class($resa->uid_pilote,$sql);
	$ins=new user_class($resa->uid_instructeur,$sql);

// ---- Set les paramètres
	$tmpl_x->assign("form_id",$id);
	$tmpl_x->assign("prev_uid",$uid);
	$tmpl_x->assign("prev_idvol",$idvol);
	$tmpl_x->assign("form_idvol",$fiche->val("idvol"));
	$tmpl_x->assign("form_uid_pilote",$resa->uid_pilote);
	$tmpl_x->assign("form_uid_instructeur",$resa->uid_instructeur);
	$tmpl_x->assign("form_uid_avion",$resa->uid_ressource);
	
	$tmpl_x->assign("form_eleve",$pil->aff("fullname"));
	$tmpl_x->assign("form_instructeur",$ins->aff("fullname"));
	$tmpl_x->assign("form_dtevol",sql2date($resa->dte_deb,"jour"));

	
	if (GetDroit("SupprimeSynthese"))
	{
		$tmpl_x->parse("corps.supprimer");
	}

	
	if ($fiche->val("lecon")!="")
	{
		$tmpl_x->assign("form_img_lecon","ok");
		$tmpl_x->assign("form_img_autre","nc");
	}
	else
	{
		$tmpl_x->assign("form_img_autre","ok");
		$tmpl_x->assign("form_img_lecon","nc");
	}
		
	
	if ($typeaff=="form")
	{
		$tmpl_x->parse("corps.submit");
	}

	$t=array("idvol","uid_pilote","uid_instructeur","uid_avion","lecon","remtech","remnotech","menace","erreur","remnotech","travail","nbatt");

	if ((GetDroit("signinst")) && ($fiche->val("skey_instructeur")=="") && ($id>0))
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
	
	if (($fiche->val("skey_pilote")=="") && ($id>0))
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