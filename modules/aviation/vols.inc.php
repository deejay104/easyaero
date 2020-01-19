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
	if (!GetDroit("AccesVols")) { FatalError("Accès non authorisé (AccesVols)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");


// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("vols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Vérifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");
	$ts=checkVar("ts","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste des membres
	if (GetDroit("ListeVols"))
	{
		if (!isset($id))
		  { $id=$uid; }

		$lstusr=ListActiveUsers($sql,"prenom","");

		foreach($lstusr as $i=>$tid)
		{ 
			$tmpusr=new user_class($tid,$sql);
			$tmpl_x->assign("id_user", $tid);
			$tmpl_x->assign("chk_user", ($tid==$id) ? "selected" : "") ;
			$tmpl_x->assign("nom_user", $tmpusr->fullname);
			$tmpl_x->parse("corps.listeVols.lst_user");
		}
		$tmpl_x->parse("corps.listeVols");
	}
	else
	{
		$id=$gl_uid;
	}

// ---- Titre

	$tabTitre=array();
	$tabTitre["dte_deb"]["aff"]="Date";
	$tabTitre["dte_deb"]["width"]=($theme=="phone") ? 120 : 250 ;
	$tabTitre["immat"]["aff"]="Immat";
	$tabTitre["immat"]["width"]=80;
	$tabTitre["tpsreel"]["aff"]="Bloc";
	$tabTitre["tpsreel"]["width"]=70;
	$tabTitre["temps"]["aff"]="Temps";
	$tabTitre["temps"]["width"]=70;
	$tabTitre["cout"]["aff"]="Cout";
	$tabTitre["cout"]["width"]=100;
	$tabTitre["instructeur"]["aff"]="Instructeur";
	$tabTitre["instructeur"]["width"]=270;


// ---- Chargement des données
	// if ($order=="") { $order="dte_deb"; }
	// if ($trie=="") { $trie="i"; }

	// $tl=40;
	// $lstresa=ListReservationVols($sql,$id,$order,$trie,$ts,$tl);
	// $usr=new user_class($id,$sql);
	// $tmpl_x->assign("username",$usr->Aff("prenom")." ".$usr->Aff("nom"));

	// $totligne=ListReservationNbLignes($sql,$id);
	
	// $tabresa=array();
	// $tabValeur=array();
	// foreach($lstresa as $i=>$rid)
	// {
		// $resa = new resa_class($rid,$sql,false);
		// $ress = new ress_class($resa->uid_ressource,$sql);
		// $usrinst = new user_class($resa->uid_instructeur,$sql);

		// $t1=sql2date($resa->dte_deb,"jour");
		// $t2=sql2date($resa->dte_fin,"jour");

		// if ($t1!=$t2)
		  // { $dte=$t1." - ".$t2; }
		// else if ((sql2time($resa->dte_deb)!="00:00:00") && ($theme!="phone"))
		  // { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
		// else if  ($theme!="phone")
		  // { $dte=$t1." (N/A)"; }
		// else
		  // { $dte=$t1; }

		// $tabValeur[$i]["dte_deb"]["val"]=strtotime($resa->dte_deb);
		// $tabValeur[$i]["dte_deb"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".$dte."</a>";
		// $tabValeur[$i]["immat"]["val"]=$ress->val("nom");
		// $tabValeur[$i]["immat"]["aff"]=$ress->aff("immatriculation");
		// $tabValeur[$i]["tpsreel"]["val"]=$resa->tpsreel;
		// $tabValeur[$i]["tpsreel"]["aff"]=$resa->AffTempsReel();
		// $tabValeur[$i]["temps"]["val"]=$resa->temps;
		// $tabValeur[$i]["temps"]["aff"]=$resa->AffTemps();
		// $tabValeur[$i]["cout"]["val"]=$resa->prix;
		// $tabValeur[$i]["cout"]["aff"]=$resa->AffPrix()."&nbsp;&nbsp;";
		// $tabValeur[$i]["cout"]["align"]="right";
		// if ($id==$resa->uid_instructeur)
		// {
		  	// $usrinst = new user_class($resa->uid_pilote,$sql);
			// $tabValeur[$i]["instructeur"]["val"]="Avec : ".$usrinst->Aff("fullname");
		// }
		// else
		// {
			// $tabValeur[$i]["instructeur"]["val"]=$usrinst->Aff("fullname");
		// }

	// }

// ---- Affiche le tableau
	$tmpl_x->assign("tab_liste",AfficheTableauRemote($tabTitre,$order,geturlapi($mod,"vols","mesvols","id=".$id),false));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
