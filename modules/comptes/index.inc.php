<?
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

<?
	if (!GetDroit("AccesCompte")) { FatalError("Accès non autorisé (AccesCompte)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module",$module."/".$mod);

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Récupère les variables
	$id=checkVar("id","numeric");
	if ($id==0)
	{
		$usr=new user_class($gl_uid,$sql);
		$id=$usr->data["idcpt"];
	}
	
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
	$tabTitre["line"]["aff"]="<line>";
	$tabTitre["line"]["width"]=1;
	$tabTitre["montant"]["aff"]="Montant";
	$tabTitre["montant"]["width"]=100;
	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		$tabTitre["signature"]["aff"]="";
		$tabTitre["signature"]["width"]=20;
	}
	if ($trie=="")
	{
		$tabTitre["solde"]["aff"]="Solde";
		$tabTitre["solde"]["width"]=110;
	}
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
	
	
	// Calcul le nombre ligne totale
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id";
	$res=$sql->QueryRow($query);
	$totligne=$res["nb"];

	// Calcul le solde du compte au début de l'affichage
	$query = "SELECT SUM(lignes.montant) AS solde FROM (SELECT montant FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id ORDER BY $order ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT $ts,$totligne) AS lignes";
	$res=$sql->QueryRow($query);
	$solde=$res["solde"];
	
	// Affiche les lignes
	$query = "SELECT id,mid,uid,date_valeur,date_creat,mouvement,commentaire,montant,hash,precedent,pointe FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id ORDER BY $order ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT $ts,$tl";
	$sql->Query($query);
	$col=50;
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

		$tabValeur[$i]["lid"]["val"]=$sql->data["id"];
		$tabValeur[$i]["date_valeur"]["val"]=CompleteTxt($i,"20","0");
		$tabValeur[$i]["date_valeur"]["aff"]=date("d/m/Y",strtotime($sql->data["date_valeur"]));
		$tabValeur[$i]["mid"]["val"]=$sql->data["mid"];
		$tabValeur[$i]["precedent"]["val"]=$sql->data["precedent"];
		$tabValeur[$i]["date_creat"]["val"]=$sql->data["date_creat"];
		$tabValeur[$i]["mouvement"]["val"]=$sql->data["mouvement"];
		$tabValeur[$i]["commentaire"]["val"]=$sql->data["commentaire"];
		$tabValeur[$i]["line"]["val"]="<line>";
		$tabValeur[$i]["montant"]["val"]=$sql->data["montant"];
		$tabValeur[$i]["montant"]["align"]="right";
		$tabValeur[$i]["montant"]["aff"]=AffMontant($sql->data["montant"])."&nbsp;&nbsp;";

		if ((!isset($trie)) || ($trie==""))
		  {
			$afftotal=round($solde,2);
			$tabValeur[$i]["solde"]["val"]=(($afftotal==0) ? "0" : $afftotal);
			$tabValeur[$i]["solde"]["align"]="right";
			$tabValeur[$i]["solde"]["aff"]=(($afftotal==0) ? "0,00 ".$MyOpt["devise"] : AffMontant($afftotal))."&nbsp;&nbsp;";
			$solde=$solde-$sql->data["montant"];
		  }
		$tabValeur[$i]["releve"]["val"]=$sql->data["pointe"];
		if (GetDroit("AfficheSignatureCompte"))
		{
			$tabValeur[$i]["hash"]["val"]=$sql->data["hash"];
		}
		else
		{
			$tabValeur[$i]["hash"]["val"]="";
		}
	}

	if (GetDroit("AfficheDetailMouvement"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$tabValeur[$i]["date_valeur"]["aff"]="<a title='Créé le ".sql2date($tabValeur[$i]["date_creat"]["val"])."'>".$tabValeur[$i]["date_valeur"]["aff"]."</a>";
			$tabValeur[$i]["mouvement"]["aff"]="<a title='".AfficheDetailMouvement($id,$d["mid"]["val"])."'>".$tabValeur[$i]["mouvement"]["val"]."</a>";
		}
	}

	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$confirm=AfficheSignatureCompte($d["lid"]["val"]);
			$aff="";
			if ($confirm["res"]=="ok")
			{
				$aff="<a title='Signature de la transaction confirmée'><img src='static/images/icn16_signed.png' /></a>";
				if ($fonc!="showhash")
				{
					$tabValeur[$i]["hash"]["val"]="";
				}
			}
			else if ($confirm["res"]=="nok")
			{
				$aff="<a title='Cette transaction ou la précédente sont altérées.\nID courant:".$d["lid"]["val"]." ID précédent:".$d["precedent"]["val"]."'><img src='static/images/icn16_warning.png' /></a>";
				$tabValeur[$i]["hash"]["aff"]="<s>".$tabValeur[$i]["hash"]["val"]."</s><br />".$confirm["hash"];
			}
			else if ($confirm["res"]=="mvt")
			{
				$aff="<a title=\"Le mouvement n'a pas un total nul. Une des transaction a pu être altérée.\nMouvement ID:".$d["mid"]["val"]." Total:".$confirm["total"]."\"><img src='static/images/icn16_warning.png' /></a>";
			}
			
			$tabValeur[$i]["signature"]["val"]=$confirm;
			$tabValeur[$i]["signature"]["aff"]=$aff;
		}
	}
	
	if ($order=="") { $order="date"; }
	$tmpl_x->assign("aff_tableau",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

	


	
?>
