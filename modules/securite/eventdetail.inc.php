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
	require_once ($appfolder."/class/evenement.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$form_data=checkVar("form_data","array");

	$msg_erreur="";
	$msg_confirmation="";

// ---- Sauvegarde
	if ($fonc=="Enregistrer")
	{
		$event=new event_class($id,$sql);
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$event->Valid($k,$v);
		  	}
			$msg_confirmation.="Vos données ont été enregistrées.<BR>";
		}
        $event->Save();
		header('Location: /securite/eventdetail?id='.$id, true, 303);
    	exit;
	}
// ---- Annuler
	if ($fonc=="Annuler")
	{
		header('Location: /securite/eventdetail?id='.$id, true, 303);
		exit;
	}

// ---- Notifier
/*
    if ($fonc=="notifier")
	{
		$event=new rex_class($id,$sql);
		$tabvar=array(
			"url"=>$event->url(),
		);

		SendMailFromFile("","",array(),$event->val("titre"),$tabvar,"rex","","actualite");
		$msg_confirmation.="Les membres vont être notifiés de ce REX";
	}
*/

// ---- Suppression
	if ($fonc=="supprimer")
	{
		$event=new event_class($id,$sql);
		$event->Delete();
		header('Location: /securite/events', true, 303);
    	exit;
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
	$event=new event_class($id,$sql);
	$tmpl_x->assign("id",$id);

	foreach($event->data as $k=>$v)
	{
		$tmpl_x->assign("form_".$k,$event->Aff($k,$typeaff));
	}
	

	// Dernière mise à jour
	$maj=new user_class($event->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $maj->aff("fullname")." le ".sql2date($event->dte_maj));

// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_confirmation!="")
	{
		affInformation($msg_confirmation,"ok");
	}

	addPageMenu("","securite","Liste",geturl("securite","events",""),"mdi-keyboard-backspace",false);

	if (GetDroit("ModifEvent"))
	{
		addPageMenu("","securite","Modifier",geturl("securite","eventdetail","fonc=editer&id=".$id),"",($fonc=="editer") ? true : false);
	}
	if (GetDroit("SupprimeEvent"))
	{
		addPageMenu("","securite","Supprimer",geturl("securite","eventdetail","fonc=supprimer&id=".$id),"",false,"","","outline-danger");
	}	
	addPageMenu("","securite","Imprimer",geturl("securite","eventdetail","fonc=imprimer&id=".$id),"",false);


// ---- Infos de dernières maj
	$usrmaj = new user_class($event->uid_maj,$sql);
	$tmpl_x->assign("form_dte_maj", sql2date($event->dte_maj,"jour"));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>