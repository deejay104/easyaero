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
	if (!GetDroit("AccesSuiviTableauBord")) { FatalError("Accès non autorisé (AccesSuiviTableauBord)"); }

	require_once ($appfolder."/class/user.inc.php");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$dte=checkVar("dte","varchar",4);
	$annee=checkVar("annee","varchar",4);
	$show=checkVar("show","date");
	$poste=checkVar("poste","varchar");

	if (preg_match("/[0-9]{4}/",$dte))
	  { $annee=$dte; }
	if (($annee=="") && (!preg_match("/[0-9]{4}/",$annee)))
	  { $annee=date("Y"); }
	$dte=$annee;

	if ($poste=="--")
	  { $poste=""; }
	
	if ($id==0)
	  { $id=$MyOpt["uid_tableaubord"]; }


// ---- Liste des comptes

	$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");

	foreach($lst as $i=>$tmpuid)
	  {
	  	$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("id_compte", $resusr->id);
		$tmpl_x->assign("chk_compte", ($resusr->id==$id) ? "selected" : "") ;
		$tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
		$tmpl_x->parse("corps.lst_compte");
	  }


// ---- Liste des années

	$query = "SELECT MIN(date_valeur) AS dtedeb FROM ".$MyOpt["tbl"]."_compte";
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
		
  	$tmpl_x->assign("annee", $annee);
	$tmpl_x->assign("cur_annee", $dte);
	$tmpl_x->assign("old_annee", $dte-1);

// ---- Fonctions


	function somme($tab,$tmp,$val,$consval=false)
	{
	  	if ((isset($tmp[0])) && ($tmp[0]!=""))
	  	{
			if (!isset($tab[$tmp[0]]["_total"]))
			{
			  $tab[$tmp[0]]["_total"]=0;
			}
			if (isset($val["montant"]))
			{
				$tab[$tmp[0]]["_total"]=$tab[$tmp[0]]["_total"]+$val["montant"];
			}
	
			$t=array();
			foreach($tmp as $k=>$v)
			  {
			  	if ($k>0) { $t[$k-1]=$v; }
			  }
				
			$tab[$tmp[0]]=somme($tab[$tmp[0]],$t,$val,$consval);
		}
		else if ($consval==true)
		{
			$tab["_enr"][$val["id"]]=$val;
		}
		return $tab;
	}

	function AfficheSousPoste($tab,$oldtab,$dep,$pwd)
	  { global $tmpl_x,$id, $annee;
		foreach($tab as $poste=>$mtab)
		  {
			if ($poste!="_total")
			  {
				$tmpl_x->assign("nom_sousposte", Duplique("&nbsp;",$dep)."<A href=\"index.php?mod=suivi&rub=tableaubord&poste=$pwd/$poste&id=$id&dte=$annee\">".$poste."</A>");
				$tmpl_x->assign("date_sousposte", (isset($mtab["_enr"]["date_valeur"])) ? sql2date($mtab["_enr"]["date_valeur"]) : "");
				$tmpl_x->assign("tot_sousposte", AffMontant($mtab["_total"]));
				$tmpl_x->assign("old_tot_sousposte", (isset($oldtab[$poste]["_total"])) ? AffMontant($oldtab[$poste]["_total"]) : AffMontant(0));
	
				// Affiche le résultat
				$tmpl_x->parse("corps.lst_poste.lst_sousposte.lst_old_sousposte");
				$tmpl_x->parse("corps.lst_poste.lst_sousposte");
	
				AfficheSousPoste($mtab,isset($oldtab[$poste]) ? $oldtab[$poste] : array(),$dep+4,$pwd."/".$poste);
			  }
		  }
	  }

	function AffichePoste($txt)
	  { global $id, $annee;
	
		$tmp=explode("/",$txt);
	
		$t="";
		foreach ($tmp as $k=>$v)
		  {
		  	$tmp[$k]="<A href=\"index.php?mod=suivi&rub=tableaubord&poste=$t$v&id=$id&dte=$annee\">$v</A>";
		  	if ($v!="--") { $t.="$v/"; }
		  }
	
		return implode("/",$tmp);
	  }
		
	function AfficheListePoste($tab,$dep,$pwd)
	  { global $tmpl_x, $id, $annee;
	
		if (is_array($tab))
		  {
			foreach($tab as $p=>$v)
			  {
				if ($p=="_enr")
				  {
					foreach($v as $enr)
					  {	
						$tmpl_x->assign("nom_sousposte", Duplique("&nbsp;",$dep)."<A href=\"index.php?mod=suivi&rub=tableaubord&poste=". $enr["mouvement"]."&id=$id&dte=$annee\">".$enr["commentaire"]."</A>");
						$tmpl_x->assign("date_sousposte", sql2date($enr["date_valeur"]));
						$tmpl_x->assign("tot_sousposte", AffMontant($enr["montant"]));
				
						// Affiche le résultat
						$tmpl_x->parse("corps.lst_poste.lst_sousposte");
					  }
				  }
				else if ($p=="_total")
				  {
				  }
				else
				  {
					$tmpl_x->assign("nom_sousposte", Duplique("&nbsp;",$dep)."<A href=\"index.php?mod=suivi&rub=tableaubord&poste=$pwd/$p&id=$id\"><U>$p</U></A>");
					$tmpl_x->assign("tot_sousposte", AffMontant($v["_total"]));
			
					// Affiche le résultat
					$tmpl_x->parse("corps.lst_poste.lst_sousposte.lst_ligne");
					$tmpl_x->parse("corps.lst_poste.lst_sousposte");
	
					AfficheListePoste($v,$dep+4,"$poste/$lposte/$k");
				  }
			
			  }
		  }	
	  }

// ---- Calcul les totaux

	$tabposte=array();
	$taboldposte=array();

	$mois="";
	if ((isset($show)) && (preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/",$show)))
	{
		$tmpl_x->assign("form_mois", $show);
	  	$mois=preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/","$2-$1",$show);
	}

	if ($poste=="")
	  {
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE uid=$id AND date_valeur>='".($annee-1)."-01-01' AND date_valeur<='".(($mois=="") ? "$annee-01-01" : ($annee-1)."-".$mois )."' ORDER BY mouvement";
		$sql->Query($query);
		$t=array();
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tmp=explode("/",$sql->data["mouvement"]);
		  	$taboldposte=somme($taboldposte,$tmp,$sql->data);
		  	$tabposte=somme($tabposte,$tmp,$t);
		  }

		$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE uid=$id AND date_valeur>='$annee-01-01' AND date_valeur<='".(($mois=="") ? ($annee+1)."-01-01" : $annee."-".$mois )."' ORDER BY mouvement";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tmp=explode("/",$sql->data["mouvement"]);
		  	$tabposte=somme($tabposte,$tmp,$sql->data);
		  }

	//	asort($tabposte);

		$total=0;
		$oldtotal=0;
		foreach($tabposte as $poste=>$tab)
		{
			if (!isset($taboldposte[$poste]["_total"]))
			{
				$taboldposte[$poste]["_total"]=0;
			}
			
			$tmpl_x->assign("nom_poste", "<A href=\"index.php?mod=suivi&rub=tableaubord&poste=$poste&id=$id&dte=$annee\">$poste</A>");
			$tmpl_x->assign("total_poste", AffMontant($tab["_total"]));
			$tmpl_x->assign("old_total_poste", AffMontant($taboldposte[$poste]["_total"]));
			AfficheSousPoste($tab,isset($taboldposte[$poste]) ? $taboldposte[$poste] : array(),4,$poste);
	
			$total=$total+$tab["_total"];
			$oldtotal=$oldtotal+$taboldposte[$poste]["_total"];
	
			// Affiche le résultat
			$tmpl_x->parse("corps.lst_poste.lst_old_poste");
			$tmpl_x->parse("corps.lst_poste");
		  }
	
		$tmpl_x->parse("corps.old_poste");
		$tmpl_x->assign("total", AffMontant($total));
		$tmpl_x->assign("old_total", AffMontant($oldtotal));
		$tmpl_x->parse("corps.aff_old_vide");
		$tmpl_x->parse("corps.aff_old_total");
	  }
	else
	  {
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE uid=$id AND date_valeur>='$annee-01-01' AND date_valeur<'".($annee+1)."-01-01' AND mouvement LIKE '$poste%' ORDER BY mouvement,date_valeur";
		$total=0;

		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tmp=explode("/",$sql->data["mouvement"]);
		  	$tabposte=somme($tabposte,$tmp,$sql->data,true);
		  }

//		asort($tabposte);

		$tmp=explode("/",$poste);
		foreach($tmp as $v)
		{
		  	$oldp=$v;
			if (!isset($tabposte[$v]))
			{
				$tabposte[$v]=array();
				$tabposte[$v]["_total"]=0;
			}
		  	$tabposte=$tabposte[$v];
		  	$tot=$tabposte["_total"];
		  	unset($tabposte["_total"]);
		}

		$last=0;
		if (!isset($tabposte["_total"]))
		  {
			$t=array();
			$t[$oldp]=$tabposte;
			$tabposte=$t;
			$last=1;
		  }

		foreach($tabposte as $lposte=>$tab)
		  {
			$tmpl_x->assign("nom_poste", AffichePoste(($last>0) ? "--/$poste" : ".../$poste/$lposte"));
			$tmpl_x->assign("total_poste", ($last>0) ?  AffMontant($tot) : AffMontant($tab["_total"]));

			AfficheListePoste($tab,4,"$poste");

			$tmpl_x->parse("corps.lst_poste");
		  }

		$tmpl_x->assign("total", AffMontant($tot));
	  }

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
