<?php
/*
    SoceIt v3.0
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
	if (!GetDroit("AccesCompte")) { FatalError("Accès non autorisé (AccesCompte)"); }

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Récupère les variables
	$id=checkVar("id","numeric");
	if ($id==0)
	{
		$usr=new user_class($gl_uid,$sql);
		$id=$usr->data["idcpt"];
	}

	$order=checkVar("order","varchar",10,"date");
	$trie=checkVar("trie","varchar",1,"i");
	
// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des comptes

	// if ((GetDroit("ListeComptes")) && ($liste==""))
	if (GetDroit("AccesSuiviListeComptes"))
	{
			$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");
		
			foreach($lst as $i=>$tmpuid)
			{
			  	$resusr=new user_class($tmpuid,$sql);
	
				$tmpl_x->assign("id_compte", $resusr->id);
				$tmpl_x->assign("chk_compte", ($resusr->id==$id) ? "selected" : "") ;
				$tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
				$tmpl_x->parse("corps.compte.lst_compte");
			}
			$tmpl_x->parse("corps.compte");

	}
	else
	{
		if (GetModule("creche"))
		{
		  	$ok=0;
		  	$myuser->LoadEnfants();
			$tmpl_x->assign("id_compte", $myuser->uid);
			$tmpl_x->assign("chk_compte", ($myuser->uid==$id) ? "selected" : "") ;
			$tmpl_x->assign("nom_compte", $myuser->fullname);
			$tmpl_x->parse("corps.compte.lst_compte");
			if ($myuser->id==$id)
			  { $ok=1; }

	  	  	foreach($myuser->data["enfant"] as $enfant)
	  	  	  {
	  	  		if ($enfant["id"]>0)
	  	  		{
					if ($enfant["id"]==$id)
					  { $ok=1; }
					$tmpl_x->assign("id_compte", $enfant["id"]);
					$tmpl_x->assign("chk_compte", ($enfant["id"]==$id) ? "selected" : "") ;
					$tmpl_x->assign("nom_compte", $enfant["usr"]->fullname);
					$tmpl_x->parse("corps.compte.lst_compte");
				}
			}
			$tmpl_x->parse("corps.compte");
			
			if ($ok==0)
			  { $id=$gl_uid; }
		}
		else
		{
	  		$id=$gl_uid;
	  	}
	}
	$cptusr=new user_class($id,$sql);


// ---- Affiche le compte demandé
	if ((!isset($order)) || ($order==""))
	{ $order="date_valeur"; }

	if (!isset($trie))
	{ $trie=""; }


	// Nom de l'utilisateur
	$tmpl_x->assign("nom_compte", $cptusr->Aff("prenom")." ".$cptusr->Aff("nom"));

	// Définition des variables
	if ((!isset($ts)) || (!is_numeric($ts)))
	  { $ts = 0; }

	// Entete du tableau d'affichage
	$tabTitre=array();
	$tabTitre["date_valeur"]["aff"]="Date";
	$tabTitre["date_valeur"]["width"]=110;
	if ($theme!="phone")
	{
		$tabTitre["mouvement"]["aff"]="Mouvement";
		$tabTitre["mouvement"]["width"]=350;
		$tabTitre["mouvement"]["mobile"]="no";
		$tabTitre["commentaire"]["aff"]="Commentaire";
		$tabTitre["commentaire"]["width"]=400;
	}
	else
	{
		$tabTitre["commentaire"]["aff"]="Commentaire";
		$tabTitre["commentaire"]["width"]=250;
	}
	$tabTitre["montant"]["aff"]="Montant";
	$tabTitre["montant"]["width"]=100;
	$tabTitre["montant"]["calign"]="right";
	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		$tabTitre["signature"]["aff"]="";
		$tabTitre["signature"]["width"]=20;
	}

	$tabTitre["solde"]["aff"]="Solde";
	$tabTitre["solde"]["width"]=110;
	$tabTitre["solde"]["calign"]="right";

	if ($theme!="phone")
	{
		$tabTitre["releve"]["aff"]="&nbsp;";
		$tabTitre["releve"]["width"]=40;
		$tabTitre["hash"]["aff"]="&nbsp;";
		$tabTitre["hash"]["width"]=5;
		$tabTitre["hash"]["mobile"]="no";
	}
	

	$tabValeur=array();
	$tl=50;

	// Affiche le solde du compte
	$tmpl_x->assign("solde_compte", $cptusr->AffSolde());

	$trie="none";
	$order="";
	$tmpl_x->assign("aff_tableau",AfficheTableauRemote($tabTitre,geturlapi($mod,"compte",$fonc,"id=".$id."&theme=".$theme),$order,$trie,true));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

	


	
?>
