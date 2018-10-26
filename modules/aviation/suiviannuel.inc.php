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

	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	
// ---- Charge le template
	// $tmpl_x = new XTemplate (MyRep("suivivols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Vérifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");
	$dte=checkVar("dte","varchar",4);
	$type=checkVar("type","varchar",2);

	if (!preg_match("/[0-9]{4}/",$dte))
	{
		$dte=date("Y");
	}

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Date début et fin

	if ($type=="12")
	{
		$tmpl_x->assign("chk_12mois", "checked");
		$dte=date("Y");
		$ddeb=($dte-1)."-".date("m-d");
		$dfin=$dte."-".date("m-d");
	}
	else
	{
		$tmpl_x->assign("chk_annuel", "checked");

		$ddeb=$dte."-01-01";
		$dfin=($dte+1)."-01-01";
	}
	
// ---- Liste des années

	$query = "SELECT MIN(dte_deb) AS dtedeb FROM ".$MyOpt["tbl"]."_calendrier";
	$res=$sql->QueryRow($query);

	$dte1=date("Y",strtotime($res["dtedeb"]));
	if ($dte1<1970)
	  { $dte1=1970; }

	for($i=$dte1; $i<=date("Y"); $i++)
	{ 
			$tmpl_x->assign("dte_annee", $i);
			$tmpl_x->assign("chk_annee", ($i==$dte) ? "selected" : "") ;	
			$tmpl_x->parse("corps.lst_annee");
	}

	
// ---- Entete du tableau
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=210;
	$tabTitre["type"]["aff"]="Groupe";
	$tabTitre["type"]["width"]=140;
	$tabTitre["total"]["aff"]="Total";
	$tabTitre["total"]["width"]=100;
	$tabTitre["cdb"]["aff"]="CdB";
	$tabTitre["cdb"]["width"]=100;
	$tabTitre["dc"]["aff"]="DC";
	$tabTitre["dc"]["width"]=100;
	$tabTitre["inst"]["aff"]="Inst";
	$tabTitre["inst"]["width"]=100;


	// $lstres=ListeRessources($sql,array("oui"));
	// foreach($lstres as $i=>$id)
	// { 
		// $tavion[$i]=new ress_class($id, $sql);
		// $txt=substr($tavion[$i]->val("immatriculation"),strlen($tavion[$i]->val("immatriculation"))-2,2);

		// if ($theme!="phone")
		// {
			// $tabTitre["av".$i]["aff"]=$txt;
			// $tabTitre["av".$i]["width"]=30;
		// }
	// }


// ---- Liste des membres
	if (GetDroit("AccesSuiviAnnuel"))
	{
		$lstusr=ListActiveUsers($sql,"std","");
	}
	else
	{
		$lstusr=array();
		$lstusr[0]=$gl_uid;
	}

	$tabValeur=array();
	$tabTotal=array();
	$tabTotal["total"]=0;
	$tabTotal["cdb"]=0;
	$tabTotal["dc"]=0;
	$tabTotal["inst"]=0;
	
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
		$tabValeur[$i]["type"]["val"]=$usr->val("groupe");
		$tabValeur[$i]["type"]["aff"]=$usr->aff("groupe");


		$t=$usr->NbHeures($ddeb,$dfin,"");
		$tabValeur[$i]["total"]["val"]=$t;
		$tabValeur[$i]["total"]["aff"]=AffTemps($t);
		$tabTotal["total"]=$tabTotal["total"]+$t;

		$t=$usr->NbHeures($ddeb,$dfin,"cdb");
		$tabValeur[$i]["cdb"]["val"]=$t;
		$tabValeur[$i]["cdb"]["aff"]=AffTemps($t);
		$tabTotal["cdb"]=$tabTotal["cdb"]+$t;
		
		$t=$usr->NbHeures($ddeb,$dfin,"dc");
		$tabValeur[$i]["dc"]["val"]=$t;
		$tabValeur[$i]["dc"]["aff"]=AffTemps($t);
		$tabTotal["dc"]=$tabTotal["dc"]+$t;

		$t=$usr->NbHeures($ddeb,$dfin,"inst");
		$tabValeur[$i]["inst"]["val"]=$t;
		$tabValeur[$i]["inst"]["aff"]=AffTemps($t);
		$tabTotal["inst"]=$tabTotal["inst"]+$t;
	}

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tabTitre["type"]["bottom"]="Total ->";
	$tabTitre["total"]["bottom"]=AffTemps($tabTotal["total"]);
	$tabTitre["cdb"]["bottom"]=AffTemps($tabTotal["cdb"]);
	$tabTitre["dc"]["bottom"]=AffTemps($tabTotal["dc"]);
	$tabTitre["inst"]["bottom"]=AffTemps($tabTotal["inst"]);


	$tmpl_x->assign("tab_liste",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
