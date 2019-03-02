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
	if (!GetDroit("AccesBaptemes")) { FatalError("Accès non autorisé (AccesBaptemes)"); }

	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

	
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("baptemes.htm"));
	$tmpl_x->assign("path_module",$module."/".$mod);
	$tmpl_x->assign("corefolder",$corefolder);

// ----
	if (GetDroit("CreeBapteme"))
	  {
	  	$tmpl_x->parse("infos.ajout");
	  }

// ----
	$form_status=checkVar("form_status","numeric");
	  
// ---- Liste des status
	if ((!isset($form_status)) || (!is_numeric($form_status)))
	  { $form_status=-2; }

  	$btm = new bapteme_class(0,$sql);
	$tabStatus=$btm->ListeStatus();
	foreach($tabStatus as $id=>$txt)
	  {
		$tmpl_x->assign("form_statusid", $id);
		$tmpl_x->assign("form_status", $txt);
		if ($id==$form_status)
		  {
		  	$tmpl_x->assign("form_selected", "selected");
		  }
		else
		  {
		  	$tmpl_x->assign("form_selected", "");
		  }
		$tmpl_x->parse("corps.lst_status");
	  }	

	if ($form_status==-2)
	  {
	  	$tmpl_x->assign("form_selected_open", "selected");
	  }
	if ($form_status==-1)
	  {
	  	$tmpl_x->assign("form_selected_all", "selected");
	  }


// ----
	$tabTitre=array();
	$tabTitre["num"]["aff"]="Numéro";
	$tabTitre["num"]["width"]=120;

	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;

	if ($theme!="phone")
		{
			$tabTitre["nb"]["aff"]="Nb";
			$tabTitre["nb"]["width"]=30;
		
			$tabTitre["telephone"]["aff"]="Téléphone";
			$tabTitre["telephone"]["width"]=120;
		}

	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=80;

	if ($theme!="phone")
		{
			$tabTitre["paye"]["aff"]="Payé";
			$tabTitre["paye"]["width"]=100;
		
			$tabTitre["date"]["aff"]="Date prévue";
			$tabTitre["date"]["width"]=170;
		
			$tabTitre["pilote"]["aff"]="Pilote";
			$tabTitre["pilote"]["width"]=180;
		
			$tabTitre["type"]["aff"]="Type";
			$tabTitre["type"]["width"]=100;

			$tabTitre["bonkdo"]["aff"]="Bon KDO";
			$tabTitre["bonkdo"]["width"]=90;
		
			$tabTitre["resa"]["aff"]="Réservation";
			$tabTitre["resa"]["width"]=100;
		}

	$lstusr=ListeBaptemes($sql,array("oui"),$form_status);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$btm = new bapteme_class($id,$sql);
		$tabValeur[$i]["num"]["val"]=$btm->val("num");
		$tabValeur[$i]["num"]["aff"]=$btm->aff("num","html");
		$tabValeur[$i]["nom"]["val"]=$btm->val("nom");
		$tabValeur[$i]["nom"]["aff"]=$btm->aff("nom","html");
		$tabValeur[$i]["nb"]["val"]=$btm->val("nb");
		$tabValeur[$i]["nb"]["aff"]=$btm->aff("nb","html");
		$tabValeur[$i]["telephone"]["val"]=$btm->val("telephone");
		$tabValeur[$i]["telephone"]["aff"]=$btm->aff("telephone","html");
		$tabValeur[$i]["status"]["val"]=$btm->val("status");
		$tabValeur[$i]["status"]["aff"]=$btm->aff("status","html");

		if ($btm->val("dte_paye")!="0000-00-00")
		{
			$tabValeur[$i]["paye"]["val"]=$btm->val("dte_paye");
			$tabValeur[$i]["paye"]["aff"]=$btm->aff("dte_paye","html");
		}
		else
		{
			$tabValeur[$i]["paye"]["val"]=$btm->val("paye");
			$tabValeur[$i]["paye"]["aff"]=$btm->aff("paye","html");
		}

		$tabValeur[$i]["date"]["val"]=strtotime($btm->data["dte"]);
		$tabValeur[$i]["date"]["aff"]=$btm->aff("dte","html");

		$usr = new user_class($btm->data["id_pilote"],$sql,true);

		$tabValeur[$i]["pilote"]["val"]=$btm->val("id_pilote");
		$tabValeur[$i]["pilote"]["aff"]=($btm->data["id_pilote"]>0) ? $usr->Aff("fullname") : "-";

		if ($btm->data["id_avion"]>0)
		{
			$ress = new ress_class($btm->data["id_avion"],$sql);
			$tabValeur[$i]["resa"]["val"]=$ress->val("immatriculation");
			$tabValeur[$i]["resa"]["aff"]=$ress->aff("immatriculation");
		}
		else
		{
		  	$tabValeur[$i]["resa"]["val"]="-";
			$tabValeur[$i]["resa"]["aff"]="-";
		}
		$tabValeur[$i]["type"]["val"]=$btm->val("type");
		$tabValeur[$i]["type"]["aff"]=$btm->aff("type","html");
		$tabValeur[$i]["bonkdo"]["val"]=$btm->val("bonkdo");
		$tabValeur[$i]["bonkdo"]["aff"]=$btm->aff("bonkdo","html");

	  }

	if ((!isset($order)) || ($order=="")) { $order="status"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"form_status=".$form_status));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
