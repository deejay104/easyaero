<?php
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

<?php
	if (!GetDroit("AccesSuiviHorametre")) { FatalError("Accès non authorisé (AccesSuiviHorametre)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");



// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");
	$ts=checkVar("ts","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Titre
	$tmpl_x->assign("url",geturl("ressources","horametre","q=1"));

	$tabTitre=array();
	if ($theme!="phone")
	{
		$tabTitre["dte_deb"]["aff"]="Date";
		$tabTitre["dte_deb"]["width"]=100;
	}
	$tabTitre["nom"]["aff"]="Equipage";
	$tabTitre["nom"]["width"]=350;

	if ($theme!="phone")
	{
		$tabTitre["dest"]["aff"]="Lieu";
		$tabTitre["dest"]["width"]=100;
		$tabTitre["dest"]["mobile"]="no";
	}
	$tabTitre["heure"]["aff"]="Temps de vol";
	$tabTitre["heure"]["width"]=140;

	$tabTitre["horafin"]["aff"]="Hora Fin";
	$tabTitre["horafin"]["width"]=110;

	$tabTitre["horadeb"]["aff"]="Hora Début";
	$tabTitre["horadeb"]["width"]=110;

	$tabTitre["edite"]["aff"]="Comptabilisé";
	$tabTitre["edite"]["width"]=110;
	
// ---- Affiche la liste des avions
	$tabValeur=array();
	$lstress=ListeRessources($sql);

	foreach($lstress as $i=>$rid)
	{
		$resr=new ress_class($rid,$sql);

		// Initilialise l'id de ressouce s'il est vide
		if ($id==0)
		{
			$id=$rid;
		}

		// Rempli la liste dans le template
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", $resr->val("immatriculation"));
		if ($rid==$id)
		{
			$tmpl_x->assign("chk_avion", "selected");
		}
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	}

	
// ---- Affiche le tableau
	if ($order=="") { $order="dte_deb"; }
	if ($trie=="") { $trie="d"; }
	if (!is_numeric($ts))
	  { $ts = 0; }
	$tl=50;
	$lstresa=ListCarnetVols($sql,$id,$order,$trie,$ts,$tl);
	
	$totligne=ListCarnetNbLignes($sql,$id);
	
	$tabresa=array();
	$horadeb_prec=0;
	foreach($lstresa as $i=>$rid)
	{
		$resa = new resa_class($rid,$sql,false);
		if ((isset($lstresa[$i+1])) && ($lstresa[$i+1]>0))
		{
			$resa_next = new resa_class($lstresa[$i+1],$sql,false);
		}
		else
		{
			continue;
		}
			
		$ress = new ress_class($resa->uid_ressource,$sql);
		$usrpil = new user_class($resa->uid_pilote,$sql);
		if ($resa->uid_instructeur>0)
		{ $usrinst = new user_class($resa->uid_instructeur,$sql); }

		$t1=sql2date($resa->dte_deb,"jour");
		$t2=sql2date($resa->dte_fin,"jour");

		if ($t1!=$t2)
		  { $dte=$t1." - ".$t2; }
		else if ((sql2time($resa->dte_deb)!="00:00:00") && ($theme!="phone"))
		  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
		else if  ($theme!="phone")
		  { $dte=$t1." (N/A)"; }
		else
		  { $dte=$t1; }

		$tabValeur[$i]["dte_deb"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$i]["dte_deb"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".$t1."</a>";

		$tabValeur[$i]["nom"]["val"]=$usrpil->fullname;
		$tabValeur[$i]["nom"]["aff"]=$usrpil->Aff("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->Aff("fullname") : "");

		$tabValeur[$i]["tarif"]["val"]=$resa->tarif;
		$tabValeur[$i]["tarif"]["aff"]=$resa->tarif;
		$tabValeur[$i]["tarif"]["align"]="center";

		$tabValeur[$i]["dest"]["val"]=$resa->destination;
		$tabValeur[$i]["dest"]["aff"]=$resa->destination;
	
		$tabValeur[$i]["heure"]["val"]=$resa->tpsreel;
		$tabValeur[$i]["heure"]["aff"]=$resa->AffTempsReel();
		$tabValeur[$i]["heure"]["align"]="center";

		if ($horadeb_prec==0)
		{
			$horadeb_prec=$resa->horafin;
		}

		$tabValeur[$i]["horadeb"]["val"]=$resa->horadeb;
		// $tabValeur[$i]["horadeb"]["aff"]=$resa->horadeb;
		$tabValeur[$i]["horadeb"]["aff"]=($resa_next->horafin!=$resa->horadeb) ? "<div style='color: #ff0000; background-color: #FFBBAA;'>".$resa->horadeb."</div>" : $resa->horadeb;

		$tabValeur[$i]["horafin"]["val"]=$resa->horafin;
		$tabValeur[$i]["horafin"]["aff"]=($resa->horafin!=$horadeb_prec) ? "<div style='color: #ff0000; background-color: #FFBBAA;'>".$resa->horafin."</div>" : $resa->horafin;

		$c=array("oui"=>"Non","non"=>"Oui");

		$tabValeur[$i]["edite"]["val"]=$resa->edite;
		$tabValeur[$i]["edite"]["aff"]=(($resa->edite=="oui") && ($resa->tpsreel>0)) ? "<div style='color: #ff0000; background-color: #FFBBAA;'>".$c[$resa->edite]."</div>" : $c[$resa->edite];
		$tabValeur[$i]["edite"]["align"]="center";

		$horadeb_prec=$resa->horadeb;

	}
	
// ---- Affiche le tableau
	$tmpl_x->assign("tab_liste",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne,false,false));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>