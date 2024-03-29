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
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ("class/document.inc.php");
	require_once ("class/echeance.inc.php");


// ---- Initialisation des variables
  	$id=checkVar("id","numeric");
	$form_data=checkVar("form_data","array");

	$msg_erreur="";
	$msg_confirmation="";
	
// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Modifie les infos
	if (($fonc=="modifier") && GetDroit("ModifRessource"))
	  {
		$typeaff="form";
	  }
	else
	  {
		$typeaff="html";
	  }

// ---- Charge la ressource

	if ($id>0)
	{
	  	$ress = new ress_class($id,$sql);
	}
	else if (GetDroit("CreeRessource"))
	{
	  	$ress = new ress_class(0,$sql);
		$typeaff="form";
	}
	else
	{
		FatalError("Paramètre d'id non valide");
	}

// ---- Supprimer la ressource
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeRessource")))
	{
			$ress->Delete();
			$affrub="index";
			// include("modules/ressources/index.inc.php");
			return;
	}

// ---- Active la ressource
	if (($fonc=="active") && ($id>0) && (GetDroit("DesactiveRessource")))
	{
		$ress->Active();
	}
	
// ---- Désactive la ressource
	if (($fonc=="desactive") && ($id>0) && (GetDroit("DesactiveRessource")))
	{
		$ress->Desactive();
	}
	
// ---- Affiche le sous-menu
	addPageMenu("","ressources","Liste",geturl("ressources","index",""),"mdi-keyboard-backspace");

	if (GetDroit("CreeRessource"))
	{
		// $tmpl_x->parse("corps.ajout");
		// addSubMenu("","Ajouter",geturl("ressources","detail","id=0"),"icn32_ajouter.png",false);
		addPageMenu("","ressources","Ajouter",geturl("ressources","detail","id=0"),"");
	}	

	if (($id==$gl_uid) || (GetDroit("ModifRessource")))
	{
		// $tmpl_x->parse("corps.modification");
		// addSubMenu("","Modifier",geturl("ressources","detail","id=".$id."&fonc=modifier"),"",false);
		addPageMenu("","ressources","Modifier",geturl("ressources","detail","id=".$id."&fonc=modifier"),"");
	}

	if ((GetDroit("DesactiveRessource")) && ($ress->data["actif"]=="oui"))
	{
		// addSubMenu("","Désactiver",geturl("ressources","detail","id=".$id."&fonc=desactive"),"icn32_desactive.png",false,"Voulez-vous désactiver cet avion ?");
		addPageMenu("","ressources","Désactiver",geturl("ressources","detail","id=".$id."&fonc=desactive"),"");
	}
	if ((GetDroit("DesactiveRessource")) && ($ress->data["actif"]=="off"))
	{
		// addSubMenu("","Activer",geturl("ressources","detail","id=".$id."&fonc=active"),"icn32_desactive.png",false,"Voulez-vous activer cet avion ?");
		addPageMenu("","ressources","Activer",geturl("ressources","detail","id=".$id."&fonc=active"),"");
	}
	if ((GetDroit("SupprimeRessource")) && ($ress->data["actif"]=="off"))
	{
		// $tmpl_x->parse("corps.suppression");
		// addSubMenu("","Supprimer",geturl("ressources","detail","id=".$id."&fonc=delete"),"icn32_supprime.png",false,"Voulez-vous supprimer cet avion ?");
		addPageMenu("","ressources","Supprimer",geturl("ressources","detail","id=".$id."&fonc=delete"),"Voulez-vous supprimer cet avion ?");
	}	
	if ((GetDroit("RestoreRessource")) && ($ress->data["actif"]=="non"))
	{
		// $tmpl_x->parse("corps.suppression");
		// addSubMenu("","Activer",geturl("ressources","detail","id=".$id."&fonc=active"),"icn32_supprime.png",false,"Voulez-vous re activer cet avion ?");
		// addPageMenu("","ressources","Activer",geturl("ressources","detail","id=".$id."&fonc=active"),"Voulez-vous re activer cet avion ?");
	}


// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && (isset($_SESSION['tab_checkpost'][$checktime])))
	{
			$typeaff="html";
	}

	if (($fonc=="Enregistrer") && (GetDroit("ModifRessourceSauve")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		// Sauvegarde les données
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  			$msg_erreur.=$ress->Valid($k,$v);
		  	}
		}

		$ress->Save();
		if ($id==0)
		{
			$id=$ress->id;
		}


		// Sauvegarde un document
		if ((isset($_FILES["form_adddocument"])) && (is_array($_FILES["form_adddocument"]["name"])))
		{
			foreach($_FILES["form_adddocument"]["name"] as $i=>$n)
			{
				if ($n!="")
				{
					$doc = new document_core(0,$sql,"ress");
					$doc->droit="ALL";
					$doc->Save($id,$_FILES["form_adddocument"]["name"][$i],$_FILES["form_adddocument"]["tmp_name"][$i]);
				}
			}
		}

		// Sauvegarde des échéances
		$form_echeance=checkVar("form_echeance","array");
		$form_echeance_type=checkVar("form_echeance_type","array");
		foreach($form_echeance as $i=>$d)
		{
			$dte = new echeance_core($i,$sql);
			if ((!is_numeric($i)) || ($i==0))
			{
				$dte->typeid=$form_echeance_type[$i];
				$dte->uid=$id;
			}
			if (($d!='') && ($d!='0000-00-00'))
			{
				$dte->dte_echeance=$d;
				$dte->Save();
			}
			else
			{!!
				$dte->Delete();
			}
		}
		
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

	
// ---- Affiche les infos
	$ress = new ress_class($id,$sql);
	$usrmaj = new user_class($ress->uid_maj,$sql);

	$tmpl_x->assign("id", $id);
	$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." ".$ress->LastUpdate());
	
	foreach($ress->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $ress->aff($k,$typeaff)); }

	$tmpl_x->assign("bk_couleur",$ress->data["couleur"]);
	
	if ($typeaff=="form")
	  {
		$tmpl_x->parse("corps.submit");
		if ((is_numeric($id)) && ($id>0))
		  { $tmpl_x->assign("titre", "Modification : ".$ress->data["nom"]); }
	  }
	else if ($typeaff=="html")
	  {	$tmpl_x->assign("titre", "Détail de ".$ress->data["immatriculation"]); }

	$tmpl_x->parse("corps.caracteristique");

 	if (GetDroit("ModifRessourceParametres"))
	  { $tmpl_x->parse("corps.parametres"); }

	$tmpl_x->assign("aff_tpsvol",$ress->AffTempsVol());
	$tmpl_x->assign("aff_tpspot",$ress->AffPotentiel());
	
	$dteid=$ress->ProchaineMaintenance();
	$dtes="";
	if ($dteid==0)
	{
		$dtem=sql2date($ress->EstimeMaintenance(),"jour");
		if ($dtem!="NA")
		{
			$dtes="(Estimation)";
		}
	}
	else
	{
		$maint=new maint_class($dteid,$sql);
		$dtem=$maint->Aff("dte_deb");
		$dtes="(".$maint->Aff("status").")";

		// $dtes="(Planifié)";
	}
	$tmpl_x->assign("aff_dtemaint",$dtem." ".$dtes);

  
// ---- Affiche les documents

	if ($typeaff=="form")
	{
		$doc = new document_core(0,$sql,"ress");
		$doc->editmode="form";
		$tmpl_x->assign("form_document",$doc->Affiche());
		$tmpl_x->parse("corps.lst_document");
	}

  	if ($id>0)
	{ 
		$lstdoc=ListDocument($sql,$id,"ress");
		if (is_array($lstdoc))
		{
			foreach($lstdoc as $i=>$did)
			{
				$doc = new document_core($did,$sql);
				$doc->editmode=($typeaff=="form") ? "edit" : "std";
				$tmpl_x->assign("form_document",$doc->Affiche());
				$tmpl_x->parse("corps.lst_document");
			}
		}
	}

// ---- Affiche les échéances
	$lstdte=ListEcheance($sql,$id,"ressources");

	if ((is_numeric($id)) && ($id>0))
	{ 
		if ($typeaff=="form")
		{
			$dte = new echeance_core(0,$sql,$id);
			$dte->editmode="form";
			$dte->context="ressources";
			$tmpl_x->assign("form_echeance",$dte->Affiche());
			$tmpl_x->parse("corps.lst_echeance");
		}
			
		if (is_array($lstdte))
		{
			foreach($lstdte as $i=>$did)
			{
				$dte = new echeance_core($did,$sql,$id);
				$dte->editmode=($typeaff=="form") ? "edit" : "html";
				$tmpl_x->assign("form_echeance",$dte->Affiche());
				$tmpl_x->parse("corps.lst_echeance");
			}
		}
	}
	
	// $tmpl_x->parse("corps.aff_echeances");			
	
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
