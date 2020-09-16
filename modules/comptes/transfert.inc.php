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
	if (!GetDroit("AccesTransfert")) { FatalError("Accès non autorisé (AccesTransfert)"); }

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Initialise les variables

	$form_tiers=checkVar("form_tiers","numeric");
	$form_montant=checkVar("form_montant","numeric");
	$form_commentaire=checkVar("form_commentaire","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre
	$usr=new user_class($gl_uid,$sql);
	$max=$usr->CalcSolde();
	$tmpl_x->assign("montant_max",AffMontant($max));
	$val=abs($form_montant);
	$ret="";

	if ($max<0)
	{
		affInformation($tabLang["lang_transferlow"],"error");
	}
	else if ($val>$max)
	{
		affInformation($tabLang["lang_transferhigh"],"error");
	}
	
	else if (($fonc==$tabLang["lang_save"]) && ($form_tiers>0) && ($val>0) && ($MyOpt["id_PosteTransfert"]>0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$res=new user_class($form_tiers,$sql);
		
		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("aff_mouvement_detail", $mvt->AfficheEntete());
		$tmpl_x->parse("corps.msg_enregistre.lst_enregistre");

		$dte=date("Y-m-d");
	
		$ventil=array();
		$ventil["ventilation"]="debiteur";
		$ventil["data"][0]["poste"]=$MyOpt["id_PosteTransfert"];
		$ventil["data"][0]["tiers"]=$gl_uid;
		$ventil["data"][0]["montant"]=$val;
	
		$mvt = new compte_class(0,$sql);
		$mvt->Generate($res->data["idcpt"],$MyOpt["id_PosteTransfert"],$form_commentaire,$dte,$val,$ventil);
		$mvt->Save();
		$nbmvt=$mvt->Debite();

		// A voir si cette partie est nécessaire ?
		$tmpl_x->assign("aff_mouvement_detail", $mvt->Affiche());
		$tmpl_x->parse("corps.msg_enregistre.lst_enregistre");
		
		if ($mvt->erreur!="")
		{
			$ret.=$mvt->erreur;
			$ok=1;
		}
		
		if ($ret!="")
		{
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,"error");
		}
		else
		{
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : ""),"ok");
		}

		$tmpl_x->parse("corps.msg_enregistre");
		
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
	else if ($MyOpt["id_PosteTransfert"]==0)
	{
		affInformation("L'id du poste pour le transfère n'est pas renseigné. Contactez votre administrateur.","error");
	}

	
// ---- Affiche la liste des membres
	$lst=ListActiveUsers($sql,"std","","");

	foreach($lst as $i=>$tmpuid)
	{
		$resusr=new user_class($tmpuid,$sql);

		$tmpl_x->assign("id_compte", $resusr->id);
		$tmpl_x->assign("chk_compte", ($resusr->id==$gl_uid) ? "selected" : "") ;
		$tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
		$tmpl_x->parse("corps.lst_compte");
	}

	$tmpl_x->assign("form_montant", "0.00");
	$tmpl_x->assign("form_commentaire", $tabLang["lang_postcomment"]." ".$myuser->val("fullname"));
	$tmpl_x->assign("FormulaireBackgroundNormal", $MyOpt["styleColor"]["FormulaireBackgroundNormal"]);
	
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>