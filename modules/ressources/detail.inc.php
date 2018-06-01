<?
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

<?
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


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
		
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }


// ---- Supprimer la ressource
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeRessource")))
	{
			$ress->Delete();
			$affrub="index";
			include("modules/ressources/index.inc.php");
			return;
	}

	if (($fonc=="desactive") && ($id>0) && (GetDroit("DesactiveRessource")))
	{
		$ress->Desactive();
	}
	
// ---- Affiche les infos
	// if ((is_numeric($id)) && ($id>0))
	// {
		$ress = new ress_class($id,$sql);
		$usrmaj = new user_class($ress->uid_maj,$sql);

		$tmpl_x->assign("id", $id);
		$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." ".$ress->LastUpdate());
	
	// }
	// else if (GetDroit("CreeRessource"))
	// {
		// $tmpl_x->assign("titre", "Saisie d'une nouvelle ressource");

		// $ress = new ress_class("0",$sql);
		// $usrmaj = new user_class($usr->uid_maj,$sql);

		// $tmpl_x->assign("id", "");
		// $tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." ".$ress->LastUpdate());

		// $typeaff="form";
	// }

	foreach($ress->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $ress->aff($k,$typeaff)); }

	 $tmpl_x->assign("bk_couleur",$ress->data["couleur"]);
	  
	if (($id==$gl_uid) || (GetDroit("ModifRessource")))
	  { $tmpl_x->parse("corps.modification"); }

	if (GetDroit("CreeRessource"))
	  { $tmpl_x->parse("corps.ajout"); }

	if ((GetDroit("DesactiveRessource")) && ($ress->data["actif"]=="oui"))
	  { $tmpl_x->parse("corps.desactive"); }

	if ((GetDroit("SupprimeRessource")) && ($ress->data["actif"]=="off"))
	  { $tmpl_x->parse("corps.suppression"); }

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

// ---- Messages
	if ($msg_erreur!="")
	{
		$tmpl_x->assign("msg_erreur", $msg_erreur);
		$tmpl_x->parse("corps.msg_erreur");
	}

	if ($msg_confirmation!="")
	{
		$tmpl_x->assign("msg_confirmation",  $msg_confirmation);
		$tmpl_x->parse("corps.msg_confirmation");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
