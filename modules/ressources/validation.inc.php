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
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("validation.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Droit d'accès
	if (!GetDroit("AccesFichesValidation")) { FatalError("Accès non authorisé (AccesFichesValidation)"); }

// ---- Vérification des données
	$uid_avion=checkVar("uid_avion","numeric");
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Enregistre
	$msg_erreur="";

	if ((GetDroit("ValideFichesMaintenance")) && ($fonc=="Accepter") && (is_array($form_valid)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		foreach($form_valid as $fid=>$k)
		{
			$fiche=new fichemaint_class($fid,$sql);
			$fiche->data["uid_valid"]=$gl_uid;
			$fiche->data["traite"]="non";
			$fiche->Save();
		}	
	}
	else if ((GetDroit("RefuserFicheMaintenance")) && ($fonc=="Refuser") && (is_array($form_valid)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		foreach($form_valid as $fid=>$k)
		{
			$fiche=new fichemaint_class($fid,$sql);
			$fiche->data["uid_valid"]=$gl_uid;
			$fiche->data["traite"]="ref";
			$fiche->Save();	
		}
	}

 
	if (($fonc!="") && ($msg_erreur==""))
	{
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}


// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("msg_erreur", $msg_erreur);

// ---- Affiche la liste des opérations à valider
		$tabTitre=array();
		$tabTitre["valid"]["aff"]=" ";
		$tabTitre["valid"]["width"]=30;
		$tabTitre["ress"]["aff"]="Avion";
		$tabTitre["ress"]["width"]=100;
		$tabTitre["auteur"]["aff"]="Auteur";
		$tabTitre["auteur"]["width"]=150;
		$tabTitre["dtecreat"]["aff"]="Date";
		$tabTitre["dtecreat"]["width"]=120;
		$tabTitre["description"]["aff"]="Description";
		$tabTitre["description"]["width"]=400;
	
		$lstFiche=GetValideFiche($sql,$uid_avion);
		if (count($lstFiche)>0)
		{
			foreach($lstFiche as $i=>$id)
			{
				$fiche = new fichemaint_class($id,$sql,false);

				$tabValeur[$i]["valid"]["val"]="";
				$tabValeur[$i]["valid"]["aff"]="<input type='checkbox' name='form_valid[".$id."]'>";

				$ress = new ress_class($fiche->data["uid_avion"],$sql,false);
				$tabValeur[$i]["ress"]["val"]=$ress->val("immatriculation");
				$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
				
				$usr = new user_class($fiche->uid_creat,$sql,false);
				$tabValeur[$i]["auteur"]["val"]=$usr->Aff("fullname","val");
				$tabValeur[$i]["auteur"]["aff"]=$usr->Aff("fullname");
				$tabValeur[$i]["dtecreat"]["val"]=strtotime($fiche->aff("dte_creat"));
				$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["description"]["val"]=$fiche->data["description"];
				$tabValeur[$i]["description"]["aff"]=$fiche->aff("description","val");
		
			}
		}
		else
		{
			$i=0;
			$tabValeur[$i]["ress"]["val"]="";
			$tabValeur[$i]["ress"]["aff"]="";
			$tabValeur[$i]["auteur"]["val"]="";
			$tabValeur[$i]["auteur"]["aff"]="";
			$tabValeur[$i]["dtecreat"]["val"]="";
			$tabValeur[$i]["dtecreat"]["aff"]="";
			$tabValeur[$i]["description"]["val"]="-Aucune fiche en cours-";
			$tabValeur[$i]["description"]["aff"]="-Aucune fiche en cours-";
		}
		if ($order=="") { $order="ress"; }
		if ($trie=="") { $trie="d"; }
	
		$tmpl_x->assign("aff_tabavalider",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("ValideFichesMaintenance"))
	{
		$tmpl_x->parse("corps.aff_valide");
	}

// ---- Liste des avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$id)
	{
		$ress=new ress_class($id,$sql);

		$tmpl_x->assign("uid_avion", $ress->id);
		$tmpl_x->assign("nom_avion", $ress->val("immatriculation"));
		if ($uid_avion==$ress->id)
		  { $tmpl_x->assign("chk_avion", "selected"); }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	}


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
