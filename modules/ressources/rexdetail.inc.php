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
	require_once ($appfolder."/class/rex.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$form_data=checkVar("form_data","array");

	$msg_erreur="";
	$msg_confirmation="";

// ---- Sauvegarde
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$rex=new rex_class($id,$sql);
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$rex->Valid($k,$v);
		  	}
			$msg_confirmation.="Vos données ont été enregistrées.<BR>";
		}

		if ((GetDroit("ModifRex")) || ($gl_uid==$rex->uid_creat)) 
		{
			$rex->Save();
			if ($id==0)
			{
				$id=$rex->id;
			}
		}
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Notifer
	if ($fonc=="notifier")
	{
		$rex=new rex_class($id,$sql);
		$tabvar=array(
			"url"=>$rex->url(),
		);

		SendMailFromFile("","",array(),$rex->val("titre"),$tabvar,"rex","","actualite");
		$msg_confirmation.="Les membres vont être notifiés de ce REX";
	}

// ---- Suppression
	if ($fonc=="supprimer")
	{
		$rex=new rex_class($id,$sql);
		$rex->Delete();
		$affrub="rex";
		return;
	}
	
// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);
	

// ---- Type d'affichage

	$typeaff="aff";
	if (($id==0) || ($fonc=="editer"))
	{
		$typeaff="form";
		$tmpl_x->parse("corps.form_submit");
	}
	
// ---- Affiche les informations
	$rex=new rex_class($id,$sql);
	$tmpl_x->assign("id",$id);

	foreach($rex->data as $k=>$v)
	{
		$tmpl_x->assign("form_".$k,$rex->Aff($k,$typeaff));
	}
	
	$ress=new ress_class($rex->data["uid_avion"],$sql);
	$tmpl_x->assign("form_avion",$ress->Aff("immatriculation"));
	
// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_confirmation!="")
	{
		affInformation($msg_confirmation,"ok");
	}

// ---- Menu
// <div class="pagetitle pagemenu">
	// <A href="index.php?mod=ressources&rub=rex"><IMG src="{path_module}/img/icn32_retour.png" border=0 alt="">Liste</A>
// <!-- BEGIN: editer -->
	// <A href="index.php?mod=ressources&rub=rexdetail&fonc=editer&id={id}"><IMG src="{path_module}/img/icn32_modifier.png" border=0 alt="">Modifier</A>
// <!-- END: editer -->
// <!-- BEGIN: supprimer -->
	// <A href="index.php?mod=ressources&rub=rexdetail&fonc=supprimer&id={id}"><IMG src="{path_module}/img/icn32_supprimer.png" border=0 alt="">Supprimer</A>
// <!-- END: supprimer -->
	// <a href="index.php?mod=ressources&rub=rexdetail&id={id}&fonc=imprimer"><img src="{path_module}/img/icn32_printer.png" alt="">Imprimer</a>
// <!-- BEGIN: notifier -->
	// <a href="index.php?mod=ressources&rub=rexdetail&id={id}&fonc=notifier"><img src="{path_module}/img/icn32_notifier.png" alt="">Notifier</a>
// <!-- END: notifier -->
// </div>

	addSubMenu("","Liste",geturl("ressources","rex",""),"icn32_retour.png",false,"");
	if ((GetDroit("ModifRex")) || ($gl_uid==$rex->uid_creat))
	{
		addSubMenu("","Modifier",geturl("ressources","rexdetail","fonc=editer&id=".$id),"icn32_modifier.png",false,"");
	}
	if (GetDroit("SupprimeRex"))
	{
		addSubMenu("","Supprimer",geturl("ressources","rexdetail","fonc=supprimer&id=".$id),"icn32_supprimer.png",false,"Voulez-vous supprimer ce REX ?");
	}	
	addSubMenu("","Imprimer",geturl("ressources","rexdetail","fonc=imprimer&id=".$id),"icn32_printer.png",false,"");
	if ((GetDroit("NotifierRex")) && ($rex->data["status"]=="close"))
	{
		addSubMenu("","Notifier",geturl("ressources","rexdetail","fonc=notifier&id=".$id),"icn32_notifier.png",false,"");
	}
	affSubMenu();

// ---- Infos de dernières maj
	$usrmaj = new user_class($rex->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($rex->dte_maj));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>