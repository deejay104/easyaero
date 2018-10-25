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
	if (!GetDroit("AccesSuiviMouvements")) { FatalError("Accès non autorisé (AccesSuiviMouvements)"); }
	
// ---- Charge le template
	$tmpl_x = LoadTemplate("index");
	$tmpl_x->assign("path_module","$module/$mod");

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Vérifie les variables
	$form_poste=checkVar("form_poste","numeric");
	$form_tiers=checkVar("form_tiers","numeric");
	$form_commentaire=checkVar("form_commentaire","varchar");
	$form_date=checkVar("form_date","date");
	
// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Initialise les variables

	$tmpl_x->assign("FormulaireBackgroundNormal", $MyOpt["styleColor"]["FormulaireBackgroundNormal"]);
	
// ---- Enregistre le mouvement
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$form_montant=checkVar("form_montant","varchar");
		$form_ventilation=checkVar("form_ventilation","numeric");
		$form_poste_ventil=checkVar("form_poste_ventil","array");
		$form_tiers_ventil=checkVar("form_tiers_ventil","array");
		$form_montant_ventil=checkVar("form_montant_ventil","array");
		$form_temp_select=checkVar("form_temp_select","array");
	
		$msg_result="";

		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("enr_mouvement",$mvt->AfficheEntete());
		$tmpl_x->parse("corps.enregistre.lst_visualisation");

		foreach($form_temp_select as $id=>$d)
		{
			$mvt = new compte_class($id,$sql);
			$tmpl_x->assign("enr_mouvement",$mvt->Affiche());
			$tmpl_x->parse("corps.enregistre.lst_visualisation");
		}


		$dte=date2sql($form_date);
		if ($dte=="nok")
		{
		  	$msg_result="DATE INVALIDE !!!";
		  	$dte="";
		}

		$ventil=array();
		$ventil["ventilation"]=$form_ventilation;
		
		foreach($form_montant_ventil as $i=>$p)
		{
			if ($p<>0)
			{
				$ventil["data"][$i]["poste"]=$form_poste_ventil[$i];
				$ventil["data"][$i]["tiers"]=$form_tiers_ventil[$i];
				$ventil["data"][$i]["montant"]=$form_montant_ventil[$i];
			}
		}

		if ($form_montant<>0)
		{
			$mvt = new compte_class(0,$sql);
			$mvt->Generate($form_tiers,$form_poste,trim($form_commentaire),date2sql($form_date),$form_montant,$ventil,($form_facture=="") ? "NOFAC" : "");
			$mvt->Save();
			$tmpl_x->assign("enr_mouvement",$mvt->Affiche());
			$tmpl_x->parse("corps.enregistre.lst_visualisation");
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($msg_result!="")
		{
			affInformation($msg_result,"error");
			$fonc="";
		}
		else
		{
		  	$tmpl_x->assign("form_date", $form_date);
		  	$tmpl_x->assign("form_poste", $form_poste);
		  	$tmpl_x->assign("form_commentaire", $form_commentaire);
		  	$tmpl_x->assign("form_montant", $form_montant);
		  	$tmpl_x->assign("form_tiers", $form_tiers);

		  	$tmpl_x->assign("msg_resultat", "");
			$tmpl_x->parse("corps.enregistre");
		}
	}


// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$form_mid=checkVar("form_mid","array");
		$form_montant=checkVar("form_montant","varchar");

		$ret="";
		$nbmvt="";
		$ok=0;
		foreach ($form_mid as $id=>$d)
		{			
			$mvt = new compte_class($id,$sql);
			$nbmvt=$nbmvt+$mvt->Debite();
			
			if ($mvt->erreur!="")
			{
				$ret.=$mvt->erreur;
				$ok=1;
			}
		}

		$form_tiers=0;
		affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,($ret!="") ? "error" : "ok");
		$tmpl_x->assign("form_page", "vols");
	  }

// ---- Annule les enregistrements
	else if ($fonc=="Annuler")
	{
		$form_mid=checkVar("form_mid","array");
		$form_montant=checkVar("form_montant","varchar");

		if (is_array($form_mid))
		{
			foreach ($form_mid as $id=>$d)
			{			
				$mvt = new compte_class($id,$sql);
				$mvt->Annule();
			}
		}
	}
	else if ($fonc=="deltemp")
	{
		$id=checkVar("id","numeric");
		if ($id>0)
		{
			$mvt = new compte_class($id,$sql);
			$mvt->Annule();
		}
	}

	
// ---- Liste des mouvements
	$tabMvt=array();
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabMvt[$sql->data["id"]]=$sql->data;
	}

// ---- Affiche la saisi d'un mouvement
	if ($fonc!="Enregistrer")
	{
		// Edite un brouillon
		if ($fonc=="edit")
		{
			$id=checkVar("id","numeric");
			if ($id>0)
			{
				$mvt = new compte_class($id,$sql);
				
				$form_tiers=$mvt->tiers;
				$form_id=$mvt->id;
				$form_poste=$mvt->poste;
				$form_date=sql2date($mvt->date_valeur);
				$form_montant=$mvt->montant;
				$form_commentaire=$mvt->commentaire;
				
				$mvt->Annule();
			}
		}

		// Affiche la saisie des mouvements en attente
		$tabBrouillon=listCompteAttente(0);

		
		foreach($tabBrouillon as $id=>$d)
		{
			$mvt = new compte_class($id,$sql);
			$usr=new user_class($mvt->tiers,$sql);
			
			$tmpl_x->assign("form_id", $id);
			$tmpl_x->assign("form_date", sql2date($mvt->date_valeur));
			$tmpl_x->assign("form_poste", $tabMvt[$mvt->poste]["description"]);
			$tmpl_x->assign("form_tiers", $usr->aff("fullname"));
			$tmpl_x->assign("form_montant", AffMontant($mvt->montant));
			$tmpl_x->assign("form_commentaire", $mvt->commentaire);

			$tmpl_x->parse("corps.aff_mouvement.lst_aff_brouillon");
		}


		// Affiche la saisie d'un mouvement vide
		if (!isset($form_tiers))
		{
			$form_tiers=0;
		}
		if (!isset($form_id))
		{
			$form_id=0;
		}
		if (!isset($form_poste))
		{
			$form_poste=0;
		}			
		if (!isset($form_date))
		{
			$form_date=date("Y-m-d");
		}		
		if (!isset($form_montant))
		{
			$form_montant=0;
		}		
		if (!isset($form_commentaire))
		{
			$form_commentaire="";
		}		

		$montant=0;
		foreach($tabMvt as $id=>$d)
		{
			$tmpl_x->assign("id_mouvement", $d["id"]);
			$tmpl_x->assign("nom_mouvement", $d["description"].((($d["debiteur"]=="0") || ($d["crediteur"]=="0")) ? "" : " (sans tiers)"));
			$tmpl_x->assign("chk_mouvement", (($form_id==$d["id"]) || ($form_poste==$d["id"])) ? "selected" : "");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_mouvement");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation.lst_mouvement");
			if (($form_id==$d["id"]) || ($form_poste==$d["id"]))
			{
				$montant=$d["montant"];
			}
		}

		// Liste des tiers
		$lst=ListActiveUsers($sql,"std","","");

		foreach($lst as $i=>$tmpuid)
		{
			$resusr=new user_class($tmpuid,$sql);
		
			$tmpl_x->assign("id_tiers", $resusr->data["idcpt"]);
			$tmpl_x->assign("nom_tiers", $resusr->fullname);
			$tmpl_x->assign("chk_tiers", ($form_tiers==$tmpuid) ? "selected" : "");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_tiers");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation.lst_tiers");
		}

		// if ((isset($_REQUEST["form_dte"])) && ($_REQUEST["form_dte"]!=''))
		  // { $dte=$_REQUEST["form_dte"]; }
		$dte=$form_date;
		if ($dte=="0000-00-00")
		{
			$dte=date("d/m/Y");
		}

		$tmpl_x->assign("date_mouvement", $dte);
		$tmpl_x->assign("form_montant", (($form_montant<>0) ? $form_montant : $montant));
		$tmpl_x->assign("form_commentaire", $form_commentaire);

		$tmpl_x->AUTORESET=0;
		for ($iii=1; $iii<=$MyOpt["ventilationNbLigne"]; $iii++)
		{
			$tmpl_x->assign("ventilid",$iii);
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation");
		}
		$tmpl_x->AUTORESET=1;
	
		$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement");

		$tmpl_x->assign("form_page", "mvt");
	  	$tmpl_x->parse("corps.aff_mouvement");
	}



// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
