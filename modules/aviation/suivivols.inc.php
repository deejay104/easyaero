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
	if (!GetDroit("AccesSuiviVolsMembres")) { FatalError("Acc�s non authoris� (AccesSuiviVolsMembres)"); }

	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	
// ---- Charge le template
	// $tmpl_x = new XTemplate (MyRep("suivivols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- V�rifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");


// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);
	
// ---- Liste des �ch�ances
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype WHERE resa='instructeur' ORDER BY description";
	$sql->Query($query);

	$tabecheance=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabecheance[$sql->data["id"]]=$sql->data["description"];
	}

// ---- Entete du tableau
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Pr�nom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=210;
	$tabTitre["type"]["aff"]="Groupe";
	$tabTitre["type"]["width"]=140;
	$tabTitre["total"]["aff"]="Total";
	$tabTitre["total"]["width"]=100;
	$tabTitre["lastyear"]["aff"]="12 mois";
	$tabTitre["lastyear"]["width"]=100;

	if ($theme!="phone")
	{
		$tabTitre["lastflight"]["aff"]="Dernier vol";
		$tabTitre["lastflight"]["width"]=140;
		$tabTitre["lastflight"]["mobile"]="no";

		// $tabTitre["lic"]["aff"]="Licence";
		// $tabTitre["lic"]["width"]=100;
		// $tabTitre["med"]["aff"]="Visite Med.";
		// $tabTitre["med"]["width"]=100;
		foreach($tabecheance as $i=>$t)
		{
			$tabTitre["ech".$i]["aff"]=$t;
			$tabTitre["ech".$i]["width"]=100;
		}
	}
	$lstres=ListeRessources($sql,array("oui"));
	$tavion=array();
	foreach($lstres as $i=>$id)
	{ 
		$tavion[$i]=new ress_class($id, $sql);
		$txt=substr($tavion[$i]->val("immatriculation"),strlen($tavion[$i]->val("immatriculation"))-2,2);

		if ($theme!="phone")
		{
			$tabTitre["av".$i]["aff"]=$txt;
			$tabTitre["av".$i]["width"]=30;
		}
	}


// ---- Liste des membres
	$lstusr=ListActiveUsers($sql,"std","");
	
	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
		$tabValeur[$i]["type"]["val"]=$usr->val("groupe");
		$tabValeur[$i]["type"]["aff"]=$usr->aff("groupe");
		$tabValeur[$i]["lastyear"]["val"]=$usr->AffNbHeures12mois("val");
		$tabValeur[$i]["lastyear"]["aff"]=$usr->AffNbHeures12mois();
		$tabValeur[$i]["total"]["val"]=$usr->NbHeures("0000-00-00");
		$tabValeur[$i]["total"]["aff"]=AffTemps($usr->NbHeures("0000-00-00"));
		$dte=$usr->DernierVol();
		$tabValeur[$i]["lastflight"]["val"]=strtotime($dte["dte"]);
		$tabValeur[$i]["lastflight"]["aff"]="<a href='index.php?mod=aviation&vols&id=$id'>".$usr->AffDernierVol()."</a>";

		//$lastdc=strtotime($usr->AffDernierVol("DC"));
		//$renewlic=strtotime($usr->data["dte_licence"]);
		
		$lastdc=$usr->DernierVol("DC",60);

		// $daystodc=floor((strtotime($usr->data["dte_licence"])-strtotime($lastdc["dte"]))/86400);
		// $daystolic=floor((time()-strtotime($usr->data["dte_licence"]))/86400);
		
		// if ($usr->data["dte_licence"]=="0000-00-00")
		  // {
				// $tabValeur[$i]["prorogation"]["val"]="0";
				// $tabValeur[$i]["prorogation"]["aff"]="-";
		  // }
		// else if ($daystolic>0)
		  // {
				// $tabValeur[$i]["prorogation"]["val"]="2";
				// $tabValeur[$i]["prorogation"]["aff"]=" ";
		  // }
		// else if ($daystolic>-365)
		  // {
/*
				if ($daystodc>365)
					{
						$tabValeur[$i]["prorogation"]["val"]="3";
						$tabValeur[$i]["prorogation"]["aff"]="<img src='images/valid_non.gif' alt='' border='0' />";
				  }
*/
				// if ( ($daystodc<365) && ($daystodc>0))
				  // {
						// $tabValeur[$i]["prorogation"]["val"]="4";
						// $tabValeur[$i]["prorogation"]["aff"]="<a href='reservations.php?rub=reservation&id=".$lastdc["id"]."'><img src='$module/$mod/img/icn16_ok.png' alt='' /></a>";
				  // }
				// else
				  // {
		
						// $tabValeur[$i]["prorogation"]["val"]="3";
						// $tabValeur[$i]["prorogation"]["aff"]="<img src='$module/$mod/img/icn16_nc.png' />";
					// }
		  // }
		// else
		  // {
				// $tabValeur[$i]["prorogation"]["val"]="2";
				// $tabValeur[$i]["prorogation"]["aff"]=" ";
		  // }

		// $tabValeur[$i]["lic"]["val"]=$usr->data["dte_licence"];
		// $tabValeur[$i]["lic"]["aff"]="<a href='membres.php?rub=detail&id=$id'>".$usr->aff("dte_licence")."</a>";
		// $tabValeur[$i]["med"]["val"]=$usr->data["dte_medicale"];
		// $tabValeur[$i]["med"]["aff"]="<a href='membres.php?rub=detail&id=$id'>".$usr->aff("dte_medicale")."</a>";
		foreach($tabecheance as $ii=>$t)
		{
			$dte = new echeance_core(0,$sql,$id);
			$dte->loadtype($ii);
			$tabValeur[$i]["ech".$ii]["val"]=$dte->Val();
			$tabValeur[$i]["ech".$ii]["aff"]=$dte->Affiche("val");
		}

		foreach($tavion as $ii=>$res)
		{
		  	if ($usr->CheckLache($res->id))
		  	{
					$tabValeur[$i]["av".$ii]["val"]="1";
					$tabValeur[$i]["av".$ii]["aff"]="<a href='membres.php?rub=detail&id=$id'><img src='$module/$mod/img/icn16_ok2.png' alt=''></a>";
			}
			else
		  	{
					$tabValeur[$i]["av".$ii]["val"]="0";
					$tabValeur[$i]["av".$ii]["aff"]="&nbsp;";
			}
		}
	}

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }



	$tmpl_x->assign("tab_liste",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
