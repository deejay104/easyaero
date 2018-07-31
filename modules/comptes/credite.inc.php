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
	if (!GetDroit("AccesCredite")) { FatalError("Acc�s non autoris� (AccesCredite)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("credite.htm"));
	$tmpl_x->assign("path_module",$module."/".$mod);

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

	$form_tiers=checkVar("form_tiers","numeric");
	$form_montant=checkVar("form_montant","numeric");
	$form_commentaire=checkVar("form_commentaire","varchar");
	$form_type=checkVar("form_type","varchar");

	$tabType=array();
	$tabType["virement"]="Virement";
	$tabType["cheque"]="Ch�que";
	$tabType["espece"]="Esp�ce";
	$tabType["vacances"]="Ch�que vacances";
	
// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre
	$usr=new user_class($gl_uid,$sql);
	$val=abs($form_montant);

	if (($fonc=="Enregistrer") && ($val>0) && ($MyOpt["PosteCredite"][$form_type]>0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$dte=date("Y-m-d");
	
		$ventil=array();
		if ($form_commentaire=="")
		{
			$form_commentaire="Cr�dit compte pilote ".$usr->val("fullname");
		}
	
		$mvt = new compte_class(0,$sql);
		$mvt->Generate($usr->data["idcpt"],$MyOpt["PosteCredite"][$form_type],$form_commentaire,$dte,$val,$ventil);
		$mvt->Save();
		// $nbmvt=$mvt->Debite();
		
		if ($mvt->erreur!="")
		{
			$ret.=$mvt->erreur;
			$ok=1;
		}
		
		if ($ret!="")
		{
			affInformation("Erreur : ".$ret,"error");
		}
		else
		{
			affInformation("Votre compte a �t� autoris� pour un cr�dit de ".AffMontant($val).". Cette somme sera cr�dit� d�finitivement sur votre compte apr�s validation par le tr�sorier.","ok");
		}

		
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Affiche les types
	foreach($tabType as $id=>$aff)
	{
		if ($MyOpt["PosteCredite"][$id]>0)
		{
			$tmpl_x->assign("form_type", $id);
			$tmpl_x->assign("form_typename", $aff);
			$tmpl_x->parse("corps.lst_type");
		}
	}
	
// ---- Affiche les valeurs
	$s=$usr->CalcSolde();
	$tmpl_x->assign("solde_reel",AffMontant($s));
	$s=$s+$usr->CalcSoldeTemp();
	$tmpl_x->assign("solde_temp",AffMontant($s));

	$tmpl_x->assign("form_montant", "0.00");
	$tmpl_x->assign("form_commentaire", "Cr�dit compte ".$usr->val("fullname"));
	$tmpl_x->assign("FormulaireBackgroundNormal", $MyOpt["styleColor"]["FormulaireBackgroundNormal"]);

// ---- Liste des mouvements
	$tabMvt=array();
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabMvt[$sql->data["id"]]=$sql->data["description"];
	}

// ---- Affiche la liste des mouvements en attente
	$tabBrouillon=listCompteAttente($gl_uid);

	if (count($tabBrouillon)>0)
	{
		foreach($tabBrouillon as $id=>$d)
		{
			$mvt = new compte_class($id,$sql);
			$tmpl_x->assign("lst_date", sql2date($mvt->date_valeur));
			$tmpl_x->assign("lst_poste", $tabMvt[$mvt->poste]);
			$tmpl_x->assign("lst_montant", AffMontant($mvt->montant));
			$tmpl_x->assign("lst_commentaire", $mvt->commentaire);
			$tmpl_x->parse("corps.brouillon.lst_mvt");
		}
		$tmpl_x->parse("corps.brouillon");
	}
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>