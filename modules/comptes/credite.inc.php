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
	if (!GetDroit("AccesCredite")) { FatalError("Accès non autorisé (AccesCredite)"); }

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

	$tabType["virement"]="Virement";
	$tabType["cheque"]="Chèque";
	$tabType["espece"]="Espèce";
	
// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre
	$usr=new user_class($gl_uid,$sql);
	$max=$usr->CalcSolde();
	$tmpl_x->assign("montant_max",AffMontant($max));
	$val=abs($form_montant);

	if (($fonc=="Enregistrer") && ($val>0) && ($MyOpt["id_PosteCredite"]>0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$dte=date("Y-m-d");
	
		$ventil=array();
		if ($form_commentaire=="")
		{
			$form_commentaire="Crédit compte pilote ".$usr->val("fullname");
		}
	
		$mvt = new compte_class(0,$sql);
		$mvt->Generate($usr->data["idcpt"],$MyOpt["id_PosteCredite"],$tabType[$form_type]." : ".$form_commentaire,$dte,$val,$ventil);
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
			affInformation("Votre compte a été autorisé pour un crédit de ".AffMontant($val).". Cette somme sera crédité définitivement sur votre compte après validation par le trésorier.","ok");
		}

		
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
	else if ($MyOpt["id_PosteCredite"]==0)
	{
		affInformation("L'id du poste pour le crédit n'est pas renseigné. Contactez votre administrateur.","error");
	}

	
// ---- Affiche la liste des membres

	$tmpl_x->assign("form_montant", "0.00");
	$tmpl_x->assign("form_commentaire", "Crédit ".$usr->val("fullname"));
	$tmpl_x->assign("FormulaireBackgroundNormal", $MyOpt["styleColor"]["FormulaireBackgroundNormal"]);

	
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>