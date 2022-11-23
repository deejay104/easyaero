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
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$uid_ressource=checkVar("uid_ressource","numeric");
	$form_data=checkVar("form_data","array");
	$form_fiche=checkVar("form_fiche","array");

	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");
	$orderv=checkVar("order","varchar");
	$triev=checkVar("trie","varchar");

	$msg_erreur="";
	$msg_ok="";

// ---- Retour
	if ($fonc=="Retour")
	{
		$affrub="liste";
		return;
	}

// ---- Charge le template
	if ($fonc=="imprimer")
	{
		$tmpl_prg = new XTemplate (MyRep("print.htm","default"));
		$tmpl_prg->assign("corefolder", $corefolder);
		$tmpl_prg->assign("username", $myuser->aff("fullname"));
		$tmpl_prg->assign("style_url", GenereStyle("print"));
		$tmpl_prg->assign("version", $version."-".$core_version.(($MyOpt["maintenance"]=="on") ? " - MAINTENANCE ACTIVE" : "")." le ".date("d/m/Y")." à ".date("H:i"));
		$tmpl_prg->assign("site_title", $MyOpt["site_title"]);
	}

// ---- Charge les infos
	$maint=new maint_class($id,$sql);

// ---- Enregistre
	if (count($form_data)>0)
	{
		foreach($form_data as $k=>$v)
		{
			$msg_erreur=$maint->Valid($k,$v);
			affInformation($msg_erreur,"error");
		}
	}

	if (GetDroit("ModifMaintenance") && ($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$ok=$maint->Save();
		if ($ok<>1)
		{
			$msg_erreur="Erreur lors de la sauvegarde des données";
			affInformation($msg_erreur,"error");
		}
		$maint->SetIntervention();
		if ($id==0)
		{
			$id=$maint->id;
		}
		
		$lstFiche=GetActiveFiche($sql,$maint->data["uid_ressource"],$maint->id);
	
		if (count($lstFiche)>0)
		{
			foreach($lstFiche as $i=>$fid)
			{
				$fiche=new fichemaint_class($fid,$sql);

				if ((isset($form_fiche[$fid])) && ($form_fiche[$fid]!=""))
				{
				  	$fiche->data["uid_planif"]=$id;
					if ($maint->data["status"]=='cloture')
					{
					  	$fiche->data["traite"]="oui";
					}
				  	$fiche->Save();
				}
				else if ($fiche->data["uid_planif"]==$id)
				{
				  	$fiche->Affecte(0);
				}
			}
		}


		if ($msg_erreur=="")
		{
			$msg_ok="Enregistrement effectué.";
			affInformation($msg_ok,"ok");
		}
	}
	else if (GetDroit("SupprimeMaintenance") && ($fonc=="Supprimer"))
	{
	  	$msg_erreur=$maint->Delete();
		$mod="ressources";
		$affrub="liste";
		return;
	}


// ---- Ajout d'un atelier
	if (GetDroit("CreeAtelier") && ($fonc=="ajoutatelier") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$atelier=new atelier_class(0,$sql);
		
		$form_atelier=checkVar("form_atelier","array");
		if (count($form_atelier)>0)
		{
			foreach($form_atelier as $k=>$v)
		  	{
		  		$msg_erreur=$atelier->Valid($k,$v);
				affInformation($msg_erreur,"error");
		  	}
			$atelier->Save();
			$msg_ok="Atelier ajouté";
			affInformation($msg_ok,"ok");
		}
		
		$_SESSION['tab_checkpost'][$checktime]=$checktime;		
	}


// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche le sous-menu

	addPageMenu("","ressources","Liste",geturl("ressources","liste",""),"");
	addPageMenu("","ressources","Imprimer",geturl("ressources","detailmaint","id=".$id."&fonc=imprimer"),"");
		
	if (GetDroit("SupprimeMaintenance"))
	{
		addPageMenu("","ressources","Supprimer la maintenance",geturl("ressources","detailmaint","id=".$id."&fonc=Supprimer"),"",false,"Voulez-vous supprimer cette maintenance ?");
	}


// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("id", $maint->id);

	if (($maint->data["status"]!="cloture") && ($maint->actif=="oui") && (GetDroit("ModifMaintenance")))
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
	$tabTitre["tarif"]["aff"]="Tarifs";
	$tabTitre["tarif"]["width"]=140;

	$tabValeur=array();

	$lstFiche=ListLastReservation($sql,0,$maint->data["uid_ressource"],5,$maint->data["dte_deb"]);


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
				$tabValeur[$ii]["chk"]["aff"]=($resa->id==$maint->data["uid_lastresa"]) ? "<i class='mdi mdi-check'></i>" : "";
			}


			$usr = new user_class($resa->uid_pilote,$sql,false);
			$tabValeur[$ii]["pilote"]["val"]=$usr->aff("fullname","val");
			$tabValeur[$ii]["pilote"]["aff"]=$usr->aff("fullname");
			
			$tabValeur[$ii]["dtecreat"]["val"]="a".strtotime($resa->dte_deb);
			$tabValeur[$ii]["dtecreat"]["aff"]=$resa->AffDate();

			$tabValeur[$ii]["temps"]["val"]=$resa->temps;
			$tabValeur[$ii]["temps"]["aff"]=$resa->AffTempsReel();

			$query="SELECT * FROM ".$MyOpt["tbl"]."_tarifs WHERE code='".$resa->tarif."' AND ress_id='".$resa->uid_ressource."'";
			$res=$sql->QueryRow($query);

			if (is_array($res))
			{
				$tabValeur[$ii]["tarif"]["val"]=$resa->tarif;
				$tabValeur[$ii]["tarif"]["aff"]=$res["reservation"];
			}
			else
			{
				$tabValeur[$ii]["tarif"]["val"]="";
				$tabValeur[$ii]["tarif"]["aff"]="";
			}
		
			$ii++;
		}
	}

	if ($order=="") { $order="dtecreat"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_resa",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"id=".$id));

// ---- Affiche la liste des fiches

	$tabTitre=array();
	$tabTitre["chk"]["aff"]="";
	$tabTitre["chk"]["width"]=30;
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=70;
	$tabTitre["ress"]["mobile"]="no";
	$tabTitre["auteur"]["aff"]="Auteur";
	$tabTitre["auteur"]["width"]=200;
	$tabTitre["auteur"]["mobile"]="no";
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

			if ((($maint->data["status"]!="cloture") && (GetDroit("EnregistreMaintenance") && ($maint->actif=="oui"))) || ($maint->id==$fiche->data["uid_planif"]))
			  {

				$tabValeur[$i]["chk"]["val"]=(($fiche->data["uid_planif"]==$maint->id) ? "1" : "0");
				if (($maint->data["status"]!="cloture") && ($maint->actif=="oui") && ($typeaff=="form"))
				  {
					$tabValeur[$i]["chk"]["aff"]="<input type='checkbox' name='form_fiche[".$fid."]' ".(($fiche->data["uid_planif"]==$maint->id) ? "checked" : "").">";
				} else {
					$tabValeur[$i]["chk"]["aff"]=($fiche->data["uid_planif"]==$maint->id) ? "<i class='mdi mdi-check'></i>" : "";
				  }
				
				$ress = new ress_class($fiche->data["uid_avion"],$sql,false);
				$tabValeur[$i]["ress"]["val"]=$ress->val("immatriculation");
				$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
				
				$usr = new user_class($fiche->uid_creat,$sql,false);
				$tabValeur[$i]["auteur"]["val"]=$usr->aff("fullname","val");
				$tabValeur[$i]["auteur"]["aff"]=$usr->aff("fullname");
	
				$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["description"]["val"]=$fiche->val("description");
				$tabValeur[$i]["description"]["aff"]=$fiche->aff("description");

				$tabValeur[$i]["maint"]["val"]=(($fiche->data["uid_planif"]>0) ? "1" : "0");
				$tabValeur[$i]["maint"]["aff"]=((($fiche->data["uid_planif"]>0) && ($fiche->data["uid_planif"]!=$id)) ? "<a href='".geturl("ressources","detailmaint","id=".$fiche->data["uid_planif"])."' title='Cette fiche est déjà affectée à une autre maintenance'><i class='mdi mdi-file'></i></a>" : " ");
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

	$tmpl_x->assign("aff_fiche",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"id=".$id));

	if (($maint->data["status"]!="cloture") && (GetDroit("ModifMaintenance")) && ($maint->actif=="oui"))
	  {
			$tmpl_x->parse("corps.form_submit.aff_bouttons");
	  }


// ---- Bouttons du formulaire
	if ($fonc!="imprimer")
	{
		$tmpl_x->parse("corps.form_submit");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
?>