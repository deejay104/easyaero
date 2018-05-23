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
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detailmaint.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	if ($fonc=="imprimer")
	{
		$tmpl_prg = new XTemplate (MyRep("print.htm"));
	}

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Vérification des données
	if (!is_numeric($uid_ressource))
	  { $uid_ressource=0; }

// ---- Charge les infos
	if (!is_numeric($id))
	  { $id=0; }

	$maint=new maint_class($id,$sql);

// ---- Enregistre
	$msg_erreur="";

	if (GetDroit("ModifMaintenance") && ($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$maint->Valid($k,$v);
		  	}
			$msg_confirmation.="Vos données ont été enregistrées.<BR>";
		}

		$msg_erreur=$maint->Save();
		if ($id==0)
		{
			$id=$maint->id;
		}
		
		$lstFiche=GetActiveFiche($sql,$maint->uid_ressource,$maint->id);
	
		if (count($lstFiche)>0)
		{
			foreach($lstFiche as $i=>$fid)
			{
				$fiche=new fichemaint_class($fid,$sql);

				if ($form_fiche[$fid]!="")
				{
				  	$fiche->uid_planif=$id;
					if ($maint->status=='cloture')
					{
					  	$fiche->traite="oui";
					}
				  	$fiche->Save();
				}
				else if ($fiche->uid_planif==$id)
				{
				  	$fiche->Affecte(0);
				}
			}
		}


		if ($msg_erreur=="")
		  { $msg_ok="Enregistrement effectué."; }
	}
	else if (GetDroit("SupprimeMaintenance") && ($fonc=="Supprimer"))
	{
	  	$msg_erreur=$maint->Delete();
		$mod="ressources";
		$affrub="liste";
	}
	else if ($fonc=="Retour")
	{
		$mod="ressources";
		$affrub="liste";
	}
// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_ok!="")
	{
		affInformation($msg_ok,"ok");
	}
	
// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("id", $maint->id);

	if (($maint->data["status"]!="cloture") && ($maint->data["actif"]=="oui") && (GetDroit("ModifMaintenance")))
	  { $typeaff="form"; }
	else
	  { $typeaff="html"; }


// ---- Infos de dernières maj
	$usrmaj = new user_class($maint->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($maint->dte_maj));

// ---- Affiche les informations

	foreach($maint->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $maint->aff($k,$typeaff)); }


// ---- Affiche la liste des derniers vols

	$tabTitre=array();
	$tabTitre["chk"]["aff"]="";
	$tabTitre["chk"]["width"]=30;
	$tabTitre["dtecreat"]["aff"]="Date";
	$tabTitre["dtecreat"]["width"]=220;
	$tabTitre["pilote"]["aff"]="Pilote";
	$tabTitre["pilote"]["width"]=150;
	$tabTitre["temps"]["aff"]="Temps";
	$tabTitre["temps"]["width"]=80;

	$tabValeur=array();

	$lstFiche=ListLastReservation($sql,0,$maint->data["uid_ressource"],5,$maint->data["dte_deb"]);

	$chk="";

	if (count($lstFiche)>0)
	{
		if ($maint->data["uid_lastresa"]==0)
		{
			$maint->data["uid_lastresa"]=$lstFiche[0];
		}

		$ii=0;

		foreach($lstFiche as $i=>$fid)
		{
			$resa = new resa_class($fid,$sql);

			$tabValeur[$ii]["chk"]["val"]="";
			if ($typeaff=="form")
			{
				$tabValeur[$ii]["chk"]["aff"]="<input type='radio' name='form_data[uid_lastresa]' ".(($resa->id==$maint->data["uid_lastresa"]) ? "checked='checked'" : "")." value='".$resa->id."'>";
			}
			else
			{
				$tabValeur[$ii]["chk"]["aff"]=($resa->id==$maint->data["uid_lastresa"]) ? "<img src='".$corefolder."/static/images/icn16_ok.png'>" : "";
			}
			if ($resa->id==$maint->data["uid_lastresa"])
			  { $chk="ok"; }

			$usr = new user_class($resa->uid_pilote,$sql,false);
			$tabValeur[$ii]["pilote"]["val"]=$usr->aff("fullname","val");
			$tabValeur[$ii]["pilote"]["aff"]=$usr->aff("fullname");
			
			$tabValeur[$ii]["dtecreat"]["val"]="aa".$ii;
			$tabValeur[$ii]["dtecreat"]["aff"]=$resa->AffDate();

			$tabValeur[$ii]["temps"]["val"]=$resa->temps;
			$tabValeur[$ii]["temps"]["aff"]=$resa->AffTempsReel();
			
			$ii++;
		}
	}

	if ($orderv=="") { $orderv="dtecreat"; }
	if ($triev=="") { $triev="d"; }

	$tmpl_x->assign("aff_resa",AfficheTableau($tabValeur,$tabTitre,$orderv,$triev));

// ---- Affiche la liste des fiches

	$tabTitre=array();
	$tabTitre["chk"]["aff"]="";
	$tabTitre["chk"]["width"]=30;
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=70;
	$tabTitre["auteur"]["aff"]="Auteur";
	$tabTitre["auteur"]["width"]=200;
	$tabTitre["dtecreat"]["aff"]="Date";
	$tabTitre["dtecreat"]["width"]=100;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=380;
	$tabTitre["maint"]["aff"]="";
	$tabTitre["maint"]["width"]=20;

	$tabValeur=array();

	$lstFiche=GetActiveFiche($sql,$maint->data["uid_ressource"],$maint->id);

	if (count($lstFiche)>0)
	  {
		foreach($lstFiche as $i=>$fid)
		  {
			$fiche = new fichemaint_class($fid,$sql);

			if ((($maint->data["status"]!="cloture") && (GetDroit("EnregistreMaintenance") && ($maint->data["actif"]=="oui"))) || ($maint->id==$fiche->uid_planif))
			  {

				$tabValeur[$i]["chk"]["val"]=(($fiche->uid_planif==$maint->id) ? "1" : "0");
				if (($maint->data["status"]!="cloture") && ($maint->data["actif"]=="oui") && ($typeaff=="form"))
				  {
					$tabValeur[$i]["chk"]["aff"]="<input type='checkbox' name='form_fiche[".$fid."]' ".(($fiche->uid_planif==$maint->id) ? "checked" : "").">";
				} else {
					$tabValeur[$i]["chk"]["aff"]=($fiche->uid_planif==$maint->id) ? "<img src='".$corefolder."/static/images/icn16_ok.png'>" : "";
				  }
				
				$ress = new ress_class($fiche->uid_avion,$sql,false);
				$tabValeur[$i]["ress"]["val"]=$ress->aff("immatriculation","val");
				$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
				
				$usr = new user_class($fiche->uid_creat,$sql,false);
				$tabValeur[$i]["auteur"]["val"]=$usr->aff("fullname","val");
				$tabValeur[$i]["auteur"]["aff"]=$usr->aff("fullname");
	
				$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["description"]["val"]=$fiche->description;
				$tabValeur[$i]["description"]["aff"]=htmlentities($fiche->description);

				$tabValeur[$i]["maint"]["val"]=(($fiche->uid_planif>0) ? "1" : "0");
				$tabValeur[$i]["maint"]["aff"]=((($fiche->uid_planif>0) && ($fiche->uid_planif!=$id)) ? "<a href='maintenance.php?rub=detailmaint&id=$fiche->uid_planif' title='Cette fiche est déjà affectée à une autre maintenance'><img src='images/12_feuilles.gif' border='0'></a>" : " ");
			  }	
		  }
	  }
	else
	  {
			$tabValeur[$i]["chk"]["val"]="";
			$tabValeur[$i]["chk"]["aff"]="";
			$tabValeur[$i]["ress"]["val"]="";
			$tabValeur[$i]["ress"]["aff"]="";
			$tabValeur[$i]["auteur"]["val"]="";
			$tabValeur[$i]["auteur"]["aff"]="";
			$tabValeur[$i]["dtecreat"]["val"]="";
			$tabValeur[$i]["dtecreat"]["aff"]="";
			$tabValeur[$i]["description"]["val"]="-Aucune fiche en cours-";
			$tabValeur[$i]["description"]["aff"]="-Aucune fiche en cours-";
			$tabValeur[$i]["dteresolv"]["val"]="";
			$tabValeur[$i]["dteresolv"]["aff"]="";
			$tabValeur[$i]["maint"]["val"]="";
			$tabValeur[$i]["maint"]["aff"]="";
	  }


	if ($order=="") { $order="dtecreat"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_fiche",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (($maint->data["status"]!="cloture") && (GetDroit("ModifMaintenance")) && ($maint->data["actif"]=="oui"))
	  {
			$tmpl_x->parse("corps.form_submit.aff_bouttons");
	  }
	if (GetDroit("SupprimeMaintenance"))
	  {
			$tmpl_x->parse("corps.supprimemaint");
	  }

// ---- Bouttons du formulaire
	if ($fonc!="imprimer")
	{
		$tmpl_x->parse("corps.form_submit");
	}

// ---- Affecte les variables d'affichage
	if (($fonc!="Retour") && ($fonc!="Supprimer"))
	  {
			$tmpl_x->parse("icone");
			$icone=$tmpl_x->text("icone");
			$tmpl_x->parse("infos");
			$infos=$tmpl_x->text("infos");
			$tmpl_x->parse("corps");
			$corps=$tmpl_x->text("corps");
	  }
	else
	  {
	  	$order="dte_deb";
	  	$trie="i";
	  }
?>