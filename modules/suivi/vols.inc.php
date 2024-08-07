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
*/?>

<?php
	if (!GetDroit("AccesSuiviVols")) { FatalError("Accès non autorisé (AccesSuiviVols)"); }

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les variables
	$idavion=checkVar("idavion","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Charge les tarifs
  	$tabTarif=array();
	$query="SELECT tarifs.*,mvt.debiteur FROM ".$MyOpt["tbl"]."_tarifs AS tarifs LEFT JOIN ".$MyOpt["tbl"]."_mouvement AS mvt ON tarifs.poste=mvt.id";		
	$sql->Query($query);		
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["poste"]=$sql->data["poste"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["tier"]=$sql->data["debiteur"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["pilote"]=$sql->data["pilote"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["instructeur"]=$sql->data["instructeur"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["reduction"]=$sql->data["reduction"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["defaut_pil"]=$sql->data["defaut_pil"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["defaut_ins"]=$sql->data["defaut_ins"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["nom"]=$sql->data["nom"];
	}

// ---- Valide les vols à enregistrer
	if ((($fonc=="Enregistrer") || ($fonc=="Débiter")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$form_tarif=checkVar("form_tarif","array");
		$form_horadeb=checkVar("form_horadeb","array");
		$form_horafin=checkVar("form_horafin","array");
		$form_tempsresa=checkVar("form_tempsresa","array");
		$form_blocresa=checkVar("form_blocresa","array");
		$form_date=checkVar("form_date","array");
		$form_pilote=checkVar("form_pilote","array");
		$form_instructeur=checkVar("form_instructeur","array");
		$form_temps=checkVar("form_temps","array");
		$form_bloc=checkVar("form_bloc","array");


		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("enr_mouvement",$mvt->AfficheEntete());
		$tmpl_x->parse("corps.enregistre.lst_enregistre");
		$msg_result="";

		if (is_array($form_tempsresa))
		{
			foreach ($form_tempsresa as $k=>$tps)
			{
				if (!is_numeric($tps))
				{
					$tps=0;
				}
				if ($tps<>0)
				{
					$res=new resa_class($k,$sql);

					// Récupérer tarifs pilote depuis la base
					$p=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["pilote"]*$tps/60,2);

					// Calcul du tarif instructeur
					// Si tarif instructeur mis dans la fiche prendre celui là à la place du tarif sélectionné
					$pi=0;
					if ($res->uid_instructeur>0)
					{
						$pi=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["instructeur"]*$tps/60,2);

					  	$resinst=new user_class($res->uid_instructeur,$sql,false,true);
						if ($resinst->data["tarif"]>0)
					  	{
							$pi=round($resinst->data["tarif"]*$tps/60,2);
						}
					}


					// S'il y a une réduction de temps on l'a soustrait au temps pilote
					$pc=0;
					if ($tabTarif[$res->uid_ressource][$form_tarif[$k]]["reduction"]>0)
					{
						// $pc=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$res->uid_ressource][$form_tarif[$k]]["reduction"])/60,2);
						$pc=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["pilote"]*($tabTarif[$res->uid_ressource][$form_tarif[$k]]["reduction"])/60,2);
					}

					$res->horadeb=$form_horadeb[$k];
					$res->horafin=$form_horafin[$k];
					$res->temps=$tps;
					$res->tpsreel=$form_blocresa[$k];
					$res->tarif=$form_tarif[$k];
					$msg_result=$res->Save();

					if ($fonc=="Débiter")
					{
						DebiteVol($k,$tps,$res->uid_ressource,($res->uid_debite>0) ? $res->uid_debite : $res->uid_pilote,$res->uid_instructeur,$form_tarif[$k],$p,$pi,$pc,date('Y-m-d',strtotime($res->dte_deb)));

					}
				}
				else if ($fonc=="Débiter")
				{
					if (($form_horadeb[$k]=="0") && ($form_horafin[$k]=="0"))
					{
						$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET temps='0', prix=0, tpsreel=0, edite='non' WHERE id=".$k;
						$sql->Update($query);
					}
				}
			}
		}

		if (is_array($form_temps))
		{
			foreach ($form_temps as $k=>$tps)
			{
				if ($tps<>0)
				{
					$dte=date2sql($form_date[$k]);
					if (($dte=="nok") || ($form_date[$k]==""))
					{
					  	$msg_result="DATE INVALIDE !!!";
					}
					else
					{
						$uid_p=$form_pilote[$k];
						$uid_i=$form_instructeur[$k];
						$uid_a=$idavion;
						$tarif=$form_tarif[$k];
						$horadeb=$form_horadeb[$k];
						$horafin=$form_horafin[$k];
						$tpsreel=$form_bloc[$k];
						
						// Récupérer tarifs pilote depuis la base
						$p=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*$tps/60,2);

						// Calcul du tarif instructeur
						$pi=0;
						if ($uid_i>0)
						{
							$pi=round($tabTarif[$uid_a][$form_tarif[$k]]["instructeur"]*$tps/60,2);
	
						  	$resinst=new user_class($uid_i,$sql,false,true);
							if ($resinst->data["tarif"]>0)
						  	{
								$pi=round($resinst->data["tarif"]*$tps/60,2);
							}
						}

						// S'il y a une réduction de temps on l'a soustrait au temps pilote
						$pc=0;
						if ($tabTarif[$uid_a][$form_tarif[$k]]["reduction"]>0)
						{
							// $pc=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$uid_a][$form_tarif[$k]]["reduction"])/60,2);
							$pc=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$uid_a][$form_tarif[$k]]["reduction"])/60,2);
						}

					  	if (!isset($_SESSION['tab_checkpost'][$checktime]))
						{
							$res=new resa_class(0,$sql);
							$res->uid_pilote=$uid_p;
							$res->uid_debite=0;
							$res->uid_instructeur=$uid_i;
							$res->uid_ressource=$uid_a;
							$res->tarif=$tarif;
							$res->dte_deb=date('d/m/Y 00:00:00',strtotime($form_date[$k]));
							$res->dte_fin=date('d/m/Y 00:00:00',strtotime($form_date[$k]));
							$res->reel='non';
							$res->accept='oui';
							$res->temps=$tps;
							$res->tpsreel=$tpsreel;
							$res->tpsestime="60";
							$res->horadeb=$horadeb;
							$res->horafin=$horafin;
							$msg_result=$res->Save();
						}

						if ($fonc=="Débiter")
						{
							DebiteVol(0,$tps,$uid_a,$uid_p,$uid_i,$tarif,$p,$pi,$pc,$dte);
						}
					}
				}
			}
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ((is_array($msg_result)) && (count($msg_result)>0))
		{
			foreach($msg_result as $i=>$t)
			{
				affInformation($t["txt"],$t["status"]);
			}
		}
		else
		{
			affInformation("Vols enregistrés","ok");
		}
	  
		if ($fonc=="Débiter")
		{
			$tmpl_x->assign("form_page", "vols");
			$tmpl_x->parse("corps.enregistre");
		}
	}

// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$form_calid=checkVar("form_calid","array");
		$form_mid=checkVar("form_mid","array");

		if (is_array($form_mid))
		{
			$ret="";
			$nbmvt=0;
			$ok=0;

			foreach ($form_mid as $id=>$d)
			{			
				$mvt = new compte_class($id,$sql);
				$nbmvt=$nbmvt+$mvt->Debite();
				
				if ($mvt->erreur!="")
				{
					$ret.=$mvt->erreur;
					$ok=1;
				}
				
				if ($form_calid[$id]>0)
				{
				  	$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET prix='".$mvt->montant."', edite='non' WHERE id=".$form_calid[$id];
				  	// echo "$query<BR>";
				  	$sql->Update($query);
  				}
			}

			// $tmpl_x->assign("msg_confirmation", $nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret);
			// $tmpl_x->assign("msg_confirmation_class", ($ret!="") ? "msgerror" : "msgok");			
			// $tmpl_x->parse("corps.msg_enregistre");
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,($ret!="") ? "error" : "ok");

		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		$tmpl_x->assign("form_page", "vols");
	}

// ---- Annule les enregistrements
	else if ($fonc=="Annuler")
	{
		$form_mid=checkVar("form_mid","array");

		if (is_array($form_mid))
		{
			foreach ($form_mid as $id=>$d)
			{			
				$mvt = new compte_class($id,$sql);
				$mvt->Annule();
			}
		}
	}

// ---- Affiche la page demandée
	if ($fonc!="Débiter")
	{
		if ($MyOpt["updateBloc"]=="on")
		{
			$tmpl_x->parse("corps.updateBloc");
		}

		// Liste les ressources
		$lst=ListeRessources($sql,array("oui","off"));
		$tab_avions=array();
		foreach($lst as $i=>$id)
		{
			$ress=new ress_class($id,$sql);

			if ($idavion==0)
			{
				$idavion=$id;
			}

			$tab_avions[$id]=$ress->data;
			$tmpl_x->assign("id_avion", $id);
			if ($id==$idavion)
			{
				$tmpl_x->assign("sel_avion", "selected");
				$tmpl_x->assign("nom_avion", $ress->val("immatriculation")." *");
			}
			else
			{
			  	$tmpl_x->assign("sel_avion", "");
				$tmpl_x->assign("nom_avion", $ress->val("immatriculation"));
			}
			$tmpl_x->parse("corps.aff_vols.lst_avion");
		}
		
		if (isset($tab_avions[$idavion]))
		{
			$tmpl_x->assign("nom_avion_edt",$tab_avions[$idavion]["immatriculation"]);
		}

		// Récupère la plus vieille date de saisie des vols
		$horadeb_prec=0;

		// $query = "SELECT dte_deb,horafin FROM ".$MyOpt["tbl"]."_calendrier WHERE prix>0 AND uid_avion='$idavion' ORDER BY dte_deb DESC LIMIT 0,1";
		// $res=$sql->QueryRow($query);
		// $dte=$res["dte_deb"];
		// $horadeb_prec=$res["horafin"];

		// Liste des vols réservés
		$query = "SELECT id ";
		$query.= "FROM ".$MyOpt["tbl"]."_calendrier ";
		// $query.= "WHERE dte_deb>='$dte' AND dte_deb<'".now()."' AND actif='oui' AND prix=0 AND uid_avion='$idavion' ORDER BY dte_deb,horadeb";
		$query.= "WHERE dte_deb<'".now()."' AND actif='oui' AND edite='oui' AND uid_avion='".$idavion."' ORDER BY dte_deb,horadeb";
		$sql->Query($query);
		$tvols=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$tvols[$i]=$sql->data["id"];
		}


		foreach ($tvols as $i=>$id)
		{
			$resa["resa"]=new resa_class($id,$sql);
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
			$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);

			$tmpl_x->assign("date_vols", sql2date(preg_replace("/^([0-9]*-[0-9]*-[0-9]*)[^$]*$/","\\1",$resa["resa"]->dte_deb)));

			if ($resa["resa"]->uid_debite>0)
			{
				$resa["debite"]=new user_class($resa["resa"]->uid_debite,$sql);

				$p = $resa["debite"]->Aff("fullname");
				$p.=" (".$resa["pilote"]->Aff("fullname").")";
			}
			else
			{
				$p=$resa["pilote"]->Aff("fullname");
			}

			$tmpl_x->assign("pilote_vols", $p);
			$tmpl_x->assign("instructeur_vols", $resa["instructeur"]->Aff("fullname"));

			$tmpl_x->assign("id_ligne", $id);

			if (isset($tabTarif[$idavion]))
			{
				foreach ($tabTarif[$idavion] as $c=>$t)
				{ 
					$tmpl_x->assign("tarif_code", $c);
					$tmpl_x->assign("tarif_nom", $t["nom"]);

					if ($c==$resa["resa"]->tarif)
					  {
						$tmpl_x->assign("tarif_selected", "selected");
					  }
					else if ( ($t["defaut_ins"]=="oui") && ($resa["resa"]->uid_instructeur>0) && ($resa["resa"]->tarif=="") )
					  {
						$tmpl_x->assign("tarif_selected", "selected");
					  }
					else if ( ($t["defaut_pil"]=="oui") && ($resa["resa"]->tarif=="") )
					  {
						$tmpl_x->assign("tarif_selected", "selected");
					  }
					else
					  {
						$tmpl_x->assign("tarif_selected", "");
					  }
					$tmpl_x->parse("corps.aff_vols.lst_vols.lst_tarifs");	
				}
			}

	
			if ($resa["resa"]->temps>0)
			{
				$tps=$resa["resa"]->temps;
			}
			else if (($resa["resa"]->horadeb>0) && ($resa["resa"]->horafin>0))
			{
				$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
				$tps=$resr->CalcHorametre($resa["resa"]->horadeb,$resa["resa"]->horafin);
			}
			else
			{
				$tps=0;
			}

			if ($resa["resa"]->tpsreel==0)
			{
				$tbl=$tps;
			}
			else
			{
				$tbl=$resa["resa"]->tpsreel;
			}

			if ($horadeb_prec==0)
			{ $horadeb_prec=$resa["resa"]->horadeb; }
			
			$tmpl_x->assign("idresa", $id);
			$tmpl_x->assign("horadeb",  " <INPUT type=\"text\" id=\"form_horadeb_".$id."\" class='form-control' name=\"form_horadeb[".$id."]\" value=\"".$resa["resa"]->horadeb."\" style='width:80px; ".(($resa["resa"]->horadeb!=$horadeb_prec) ? "color: #ff0000; background-color: #FFBBAA;" : "")."' OnChange=\"calcHorametre(".$id.");\">");
			$horadeb_prec=$resa["resa"]->horafin;

			$alerttps="";
			if (is_numeric($tps))
			{
				$alerttps=((abs($tps-$tbl)>1.2*$tbl) ? "color: #ff0000; background-color: #FFBBAA;" : "");
			}
			$alerttbl="";
			if (is_numeric($tbl))
			{
				$alerttbl=((abs($tps-$tbl)>1.2*$tps) ? "color: #ff0000; background-color: #FFBBAA;" : "");
			}

			$tmpl_x->assign("horafin", "<INPUT type=\"text\" id=\"form_horafin_".$id."\" class='form-control' name=\"form_horafin[".$id."]\" value=\"".$resa["resa"]->horafin."\" style='width:80px;' OnChange=\"calcHorametre(".$id.");\">");
			$tmpl_x->assign("temps_vols", " <INPUT type=\"text\" id=\"form_tempsresa_".$id."\" class='form-control' name=\"form_tempsresa[".$id."]\" value=\"".$tps."\" style='width:50px;".$alerttps."'>");
			$tmpl_x->assign("bloc_vols", " <INPUT type=\"text\" id=\"form_blocresa_".$id."\" class='form-control' name=\"form_blocresa[".$id."]\" value=\"".$tbl."\" style='width:50px;".$alerttbl."' >");

			$tmpl_x->assign("destination_vols", $resa["resa"]->destination);
			
			$tmpl_x->assign("distance_vols", "0");
			if ($resa["resa"]->destination!="LOCAL")
			{
				$query="SELECT description, lon, lat FROM ".$MyOpt["tbl"]."_navpoints AS wpt WHERE nom='".$resa["resa"]->destination."'";
				$res=$sql->QueryRow($query);

				if ((isset($res["description"])) && ($res["description"]!=""))
				{
					$dist=round(getDistance($MyOpt["terrain"]["latitude"], $MyOpt["terrain"]["longitude"], $res["lat"], $res["lon"], "N"),0)." N";
					$tmpl_x->assign("distance_vols", $dist);
				}
			}

			$tmpl_x->parse("corps.aff_vols.lst_vols");
		}



		// Liste des pilotes
		// $lstPilotes=ListActiveUsers($sql,"prenom,nom",array("TypePilote"),"","",array("prenom","nom","idcpt"));
		$lstPilotes=ListUsers($sql,array("prenom","nom","idcpt"),array("virtuel"=>"non"),array("TypePilote"),array("prenom","nom"));

		// Liste des instructeurs
		// $lstInstructeurs=ListActiveUsers($sql,"prenom,nom",array("TypeInstructeur"),"");
		$lstInstructeurs=ListUsers($sql,array("prenom","nom","idcpt"),array("virtuel"=>"non"),array("TypeInstructeur"),array("prenom","nom"));


		// Liste vierge
		for($ii=0; $ii<5; $ii++)
		{ 
		
			foreach($lstPilotes as $i=>$v)
			{
			  	// $resusr=new user_class($id,$sql);
				// $tmpl_x->assign("id_pilote", $resusr->idcpt);
				// $tmpl_x->assign("nom_pilote",  $resusr->Aff("fullname","val"));
				$tmpl_x->assign("id_pilote", $v["idcpt"]);
				$tmpl_x->assign("nom_pilote",   AffFullname($v["prenom"],$v["nom"]));
				$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_pilote");
			}

			foreach($lstInstructeurs as $i=>$v)
			{ 
				$resusr=new user_class($id,$sql);
				$tmpl_x->assign("id_instructeur", $v["idcpt"]);
				$tmpl_x->assign("nom_instructeur", AffFullname($v["prenom"],$v["nom"]));
				$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_instructeur");
			}

			if ((isset($tabTarif[$idavion])) && (is_array($tabTarif[$idavion])))
			{
				foreach ($tabTarif[$idavion] as $c=>$t)
				{ 
					$tmpl_x->assign("tarif_code", $c);
					$tmpl_x->assign("tarif_nom", $t["nom"]);
					$tmpl_x->assign("tarif_selected", "");
					$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_tarifs");	
				}
			}
			$tmpl_x->assign("id_new", "N$ii");
			$tmpl_x->parse("corps.aff_vols.lst2_vols");
		}


		$tmpl_x->assign("form_page", "vols");
	  	$tmpl_x->parse("corps.aff_vols");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


// ---- FONCTIONS


function DebiteVol($idvol,$temps,$idavion,$uid_pilote,$uid_instructeur,$tarif,$p,$pi,$pc,$dte)
{
	global $MyOpt, $uid,$tmpl_x,$sql,$tabTarif;

	$ress = new ress_class($idavion,$sql);
	$pilote = new user_class($uid_pilote,$sql);
	$poste=$ress->data["poste"];

	$ventil=array();
	if (($poste!=$tabTarif[$idavion][$tarif]["poste"]) && ($tabTarif[$idavion][$tarif]["poste"]>0))
	{
		if ($tabTarif[$idavion][$tarif]["tier"]=="0")
		{
			$tier=$pilote->data["idcpt"];
		}
		else if ($tabTarif[$idavion][$tarif]["tier"]=="C")
		{
			$tier=$MyOpt["uid_club"];
		}
		else if ($tabTarif[$idavion][$tarif]["tier"]=="B")
		{
			$tier=$MyOpt["uid_banque"];
		}

		$ventil["ventilation"]="debiteur";
		$ventil["data"][0]["poste"]=$tabTarif[$idavion][$tarif]["poste"];
		$ventil["data"][0]["tiers"]=$tier;
		$ventil["data"][0]["montant"]=$p;
	}

	if ($pc<>0)
	{
		$ventil=array();
		$ventil["ventilation"]="debiteur";

		if ($pc>=$p)
		{
			$pilote = new user_class($MyOpt["uid_club"],$sql);
			$ventil["data"][0]["poste"]=$tabTarif[$idavion][$tarif]["poste"];
			$ventil["data"][0]["tiers"]=$MyOpt["uid_club"];
			$ventil["data"][0]["montant"]=$p;
		}
		else
		{
			$ventil["data"][0]["poste"]=$ress->data["poste"];
			$ventil["data"][0]["tiers"]=$tier;
			$ventil["data"][0]["montant"]=$p-$pc;
			$ventil["data"][1]["poste"]=$tabTarif[$idavion][$tarif]["poste"];
			$ventil["data"][1]["tiers"]=$MyOpt["uid_club"];
			$ventil["data"][1]["montant"]=$pc;
		}
		
	}
	
	$mvt = new compte_class(0,$sql);
	$mvt->Generate($pilote->data["idcpt"],$poste,"Vol de $temps min (".$ress->val("immatriculation")."/$tarif)",$dte,$p,$ventil,($MyOpt["facturevol"]=="on") ? "" : "NOFAC");
	$mvt->Save();
	$tmpl_x->assign("enr_mouvement",$mvt->Affiche());

	$tmpl_x->assign("form_mvtid",$mvt->id);
	$tmpl_x->assign("form_calid",$idvol);
	$tmpl_x->parse("corps.enregistre.lst_enregistre");

	if ($pi<>0)
	{
		$inst = new user_class($uid_instructeur,$sql);
	
		$mvt = new compte_class(0,$sql);
		$mvt->Generate($inst->data["idcpt"],$poste,"Remb. vol d'instruction de $temps min (".$ress->val("immatriculation")."/$tarif)",$dte,-$pi,array());
		$mvt->Save();
		$tmpl_x->assign("enr_mouvement",$mvt->Affiche());

		$tmpl_x->assign("form_mvtid",$mvt->id);
		$tmpl_x->assign("form_calid","0");
		$tmpl_x->parse("corps.enregistre.lst_enregistre");
	}
}

?>
