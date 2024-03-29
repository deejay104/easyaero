<?
// ---------------------------------------------------------------------------------------------
//   Facturation
// ---------------------------------------------------------------------------------------------
//   Variables  : $id - numéro du compte
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0 ($Revision: 432 $)
    Copyright (C) 2012 Matthieu Isorez

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
	if (!GetDroit("AccesFactures")) { FatalError("Accès non autorisé (AccesFactures)"); }

  	require_once ($appfolder."/class/facture.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	// $tmpl_x = new XTemplate (MyRep("index.htm"));
	// $tmpl_x->assign("path_module","$module/$mod");




// index.php?mod=facturation&rub=detail&uid={id_user}&facid=

// ---- Initialise les variables
	// $tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$dte=checkVar("dte","varchar",6);
	$uid=checkVar("uid","numeric");
	$ts=checkVar("ts","numeric");
	$order=checkVar("order","varchar",10,"date");
	$trie=checkVar("trie","varchar",1,"i");

	if ((!is_numeric($dte)) && preg_match("/[0-9]{6}/",$dte))
	  { $dte=""; }

	$tmpl_x->assign("year1",date("Y")-1);
	$tmpl_x->assign("year2",date("Y"));

// ---- Menu
	addPageMenu($corefolder,$mod,"Nouvelle facture",geturl("facturation","detail","uid=".$uid."&facid="),"",false);


// ---- Liste des comptes
	if (!is_numeric($uid))
	  {
	  	$uid=$myuser->idcpt;
	  }

	if (!isset($uid))
	  { $uid=$myuser->idcpt; }

	if (GetDroit("ListeFactures"))
	{		
		// if (($myuser->data["type"]!=$FacturationMembre) && ($FacturationMembre!=""))
		  // {
			// $tmpl_x->assign("id_compte", "0");
			// $tmpl_x->assign("chk_compte", "") ;
			// $tmpl_x->assign("nom_compte", "-");
			// $tmpl_x->parse("corps.aff_compte.lst_compte");
		  // }

		$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["facturation"]);

		$tmpl_x->assign("id_compte", "");
		$tmpl_x->assign("chk_compte", "") ;
		$tmpl_x->assign("nom_compte", "-");
		$tmpl_x->parse("corps.aff_compte.lst_compte");
	
		foreach($lst as $i=>$tmpuid)
		  {
		  	$resusr=new user_class($tmpuid,$sql);
			$tmpl_x->assign("id_compte", $resusr->id);
			$tmpl_x->assign("chk_compte", ($resusr->id==$uid) ? "selected" : "") ;
			$tmpl_x->assign("nom_compte", $resusr->fullname);
			$tmpl_x->parse("corps.aff_compte.lst_compte");
		}
		$tmpl_x->parse("corps.aff_compte");
	}
	else
	{
  		$uid=$gl_uid;
	}

// ---- Affiche les 12 derniers mois

	$y=date("Y");
	$m=date("n");

	for ($i=0; $i<12; $i++)
	  {
		$tmpl_x->assign("id_dte", $y.CompleteTxt($m,2,"0"));
		$tmpl_x->assign("chk_dte", ($dte==$y.CompleteTxt($m,2,"0")) ? "selected" : "");
		$tmpl_x->assign("aff_dte", $tabMois[$m]." ".$y);
		$tmpl_x->parse("corps.lst_dte");
		
		$m=$m-1;
		if ($m<=0)
		  { $m=12; $y=$y-1; }
	  }

// ---- Affiche le compte demandé

	// Nom de l'utilisateur
	$cptusr=new user_class($uid,$sql);
	$tmpl_x->assign("nom_compte", $cptusr->Aff("prenom")." ".$cptusr->Aff("nom"));

	$tmpl_x->assign("id_user",$uid);

	// Définition des variables
	$myColor[50]="F0F0F0";
	$myColor[60]="F7F7F7";
	if (!is_numeric($ts))
	  { $ts = 0; }
	if ($order=="") { $order="num"; }
	if ($trie=="") { $trie="i"; }

	// Entete du tableau d'affichage
	$tabTitre=array();
	$tabTitre["num"]["aff"]="Numéro";
	$tabTitre["num"]["width"]=110;
	$tabTitre["date"]["aff"]="Date";
	$tabTitre["date"]["width"]=110;
	$tabTitre["usr"]["aff"]="Membres";
	$tabTitre["usr"]["width"]=250;
	$tabTitre["rem"]["aff"]="Commentaire";
	$tabTitre["rem"]["width"]=200;
	$tabTitre["total"]["aff"]="Total";
	$tabTitre["total"]["width"]=100;
	$tabTitre["paid"]["aff"]="Restant";
	$tabTitre["paid"]["width"]=100;
	$tabTitre["prel"]["aff"]="Aut. Prel.";
	$tabTitre["prel"]["width"]=70;


	$nbline=NbFactures($sql,$uid,$dte);
	$lst=ListeFactures($sql,$uid,$ts,80,"id","DESC",$dte);

	$tabValeur=array();

	if (is_array($lst))
	  {	
		foreach($lst as $i=>$id)
		  {
		  	$fac=new facture_class($id,$sql);
			$tabValeur[$i]["num"]["val"]=$fac->id;
			$tabValeur[$i]["num"]["aff"]="<a href='index.php?mod=facturation&rub=detail&uid=".$fac->uid."&facid=".$fac->id."'>".$fac->id."</a>";
			$tabValeur[$i]["date"]["val"]=CompleteTxt($i,"20","0");
			$tabValeur[$i]["date"]["aff"]=sql2date($fac->dte);
			$tabValeur[$i]["rem"]["val"]=$fac->comment;
			$tabValeur[$i]["total"]["val"]=AffMontant($fac->total)." &nbsp;&nbsp;";
			$tabValeur[$i]["total"]["align"]="right";
			$r=$fac->restant();

			if (($fac->paid=="Y") && ($r!=0))
			  {
			  	$fac->NonPaye();
			  }

			$tabValeur[$i]["paid"]["val"]=(($fac->paid=="Y") ? "0" : AffMontant($r));
			$tabValeur[$i]["paid"]["aff"]=(($fac->paid=="Y") ? "payée&nbsp;&nbsp;&nbsp;&nbsp;" : "<font color=red>".AffMontant($r)." &nbsp;&nbsp;</font>");
			$tabValeur[$i]["paid"]["align"]="right";
	
			$m=new user_class($fac->uid,$sql);
			$tabValeur[$i]["usr"]["val"]=$m->fullname;
			$tabValeur[$i]["usr"]["aff"]=$m->Aff("fullname");
			$tabValeur[$i]["prel"]["val"]=$m->fullname;
			$tabValeur[$i]["prel"]["aff"]=$m->Aff("aut_prelevement");
		  }
	  }

	if ($order=="") { $order="date"; }
	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,$url="uid=$uid&dte=$dte",0,80,$nbline));

	if (GetDroit("CreeFacture"))
	{
		$tmpl_x->parse("corps.aff_nouvellefacture");
	}


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
