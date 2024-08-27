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

				$msg_confirmation.="Notification valideur.<BR>";

				$tabvar=array();
				$tabvar["id"]=$id;

				$lst=ListActiveUsers($sql,"",array("ModifRexStatus"),"non");
				foreach($lst as $i=>$uid)
				{
					$usr = new user_class($uid,$sql,false,true);
					if ($usr->data["mail"]!="")
					{
						$tabMail[]=$usr->data["mail"];
						SendMailFromFile("",$usr->data["mail"],array(),"",$tabvar,"rex_create","");
						$msg_confirmation.=$usr->data["mail"].", ";

					}
				}
		
			}
		}
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Notifier
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

// ---- Affiche les blocs
	if (GetDroit("ModifRexSynthese"))
	{
		$tmpl_x->parse("corps.evaluation");
	}
	if ((GetDroit("ModifRexSynthese")) || ($rex->val("status")!="new"))
	{
		$tmpl_x->parse("corps.conclusion");
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

	addPageMenu("","aviation","Liste",geturl("aviation","rex",""),"mdi-keyboard-backspace",false);

	if ((GetDroit("ModifRex")) || ($gl_uid==$rex->uid_creat))
	{
		addPageMenu("","aviation","Modifier",geturl("aviation","rexdetail","fonc=editer&id=".$id),"",false);
	}
	if (GetDroit("SupprimeRex"))
	{
		addPageMenu("","aviation","Supprimer",geturl("aviation","rexdetail","fonc=supprimer&id=".$id),"",false);
	}	
	addPageMenu("","aviation","Imprimer",geturl("aviation","rexdetail","fonc=imprimer&id=".$id),"",false);
	if ((GetDroit("NotifierRex")) && ($rex->data["status"]=="close"))
	{
		addPageMenu("","aviation","Notifier",geturl("aviation","rexdetail","fonc=notifier&id=".$id),"",false);
	}

// ---- Infos de dernières maj
	$usrmaj = new user_class($rex->uid_maj,$sql);
	$tmpl_x->assign("form_dte_maj", sql2date($rex->dte_maj,"jour"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>