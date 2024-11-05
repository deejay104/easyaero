<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Load libraries  
  	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Load parameters

	$fonc=checkVar("fonc","varchar");
	$id=checkVar("id","numeric");
	$uid_ress=checkVar("uid_ress","numeric");
	$uid_pilote=checkVar("uid_pilote","numeric");
	$uid_debite=checkVar("uid_debite","numeric");
	$uid_instructeur=checkVar("uid_instructeur","numeric");
	$tarif=checkVar("tarif","varchar");
	$destination=checkVar("destination","varchar");
	$tpsestime=checkVar("tpsestime","numeric");
	$nbpersonne=checkVar("nbpersonne","numeric");
	$invite=checkVar("invite","varchar");
	$dte_deb=checkVar("dte_deb","varchar");
	$hor_deb=checkVar("hor_deb","varchar");
	$dte_fin=checkVar("dte_fin","varchar");
	$hor_fin=checkVar("hor_fin","varchar");
	$description=checkVar("description","text");
	$horadeb=checkVar("horadeb","varchar");
	$horafin=checkVar("horafin","varchar");
	$tpsreel=checkVar("tpsreel","varchar");
	$potentielh=checkVar("potentielh","varchar");
	$potentielm=checkVar("potentielm","varchar");
	$carbavant=checkVar("carbavant","varchar");
	$carbapres=checkVar("carbapres","varchar");
	$prixcarbu=checkVar("prixcarbu","varchar");
	$accept=checkVar("accept","varchar",3);



	$result=array();
	
	$result["status"]=200;
	$result["id"]=$id;
	$result["instructeur"]=0;
	$result["checks"]=array();

	$resa=new resa_class($id,$sql);

	$valid=1;

// ---- Check Echeances

	$lstdte=VerifEcheance($sql,$uid_pilote,"utilisateurs");

	if ( (is_array($lstdte)) && (count($lstdte)>0) )
	{
		foreach($lstdte as $i=>$d)
		{
			$status="";
			$field="";
			$m="";
			if ($d["dte_echeance"]!="")
			{
				$m="L'échéance ".$d["description"]." a été dépassée (".sql2date($d["dte_echeance"]).")<br/>";
			}
			else
			{
				$m="Vous n'avez pas de date d'échéance pour ".$d["description"]."<br/>";
			}
			
			if ($d["resa"]=="instructeur")
			{
				$m.=$tabLang["lang_instructorneeded"];
				$result["instructeur"]=1;
				if ($uid_instructeur==0)
				{
					$status="warning";
					$field="form_uid_instructeur";
				}
				else
				{
					$status="info";
				}
				if ($uid_instructeur==0)
				{
					$valid=0;
				}
			}
			else if ($d["resa"]=="obligatoire")
			{
				$m.="La réservation n'est pas possible car cette échéance est obligatoire";
				$status="error";
				$valid=0;
			}

			if ($status!="")
			{
				$ret=array();
				$ret["title"]="";
				$ret["message"]=$m;
				$ret["status"]=$status;
				$ret["field"]=$field;
				$result["checks"][]=$ret;
			}
		}
	}

// ---- Check availability

	if ($uid_pilote>0)
	{
		$pilote=new user_class($uid_pilote,$sql,false,true);
	}
	else
	{
		$pilote=new user_class($resa->uid_pilote,$sql,false,true);
	}

	if ($uid_debite>0)
	{
		$debite=new user_class($uid_debite,$sql,false,true);
		$isSoldeNeg=$debite->isSoldeNegatif();
		$solde=$debite->CalcSolde();
	}
	else
	{
		$isSoldeNeg=$pilote->isSoldeNegatif();
		$solde=$pilote->CalcSolde();
	}

	if ($isSoldeNeg)
	{
		$ret=array();
		$ret["title"]=$tabLang["lang_nomoney"];
		$ret["message"]="Solde du compte ".$solde." €.<br />Appeller le trésorier pour l'autorisation d'un découvert.<br />";
		$ret["status"]="error";
		$ret["field"]="";
		$result["checks"][]=$ret;
		$valid=0;
	}

	if ($resa->edite=='non')
	{
		$ret=array();
		$ret["title"]=$tabLang["lang_alreadychecked"];
		$ret["message"]=$tabLang["lang_alreadychecked_msg"];
		$ret["status"]="information";
		$ret["field"]="";
		$result["checks"][]=$ret;
		$valid=0;
	}

	// Vérifie si le pilote est laché sur l'avion
	if (!$pilote->CheckLache($uid_ress))
	{
		$result["instructeur"]=1;

		if ($uid_instructeur==0)
		{
			$ret=array();
			$ret["title"]=$tabLang["lang_warning"];
			$msg_err =$tabLang["lang_nocheck"];
			$msg_err.=$tabLang["lang_instructorneeded"];
			$ret["message"]=$msg_err;
			$ret["status"]="warning";
			$ret["field"]="form_uid_instructeur";
			$result["checks"][]=$ret;
			$valid=0;			
		}
	}

	// Vérifie si le pilote est autorisé
	if (!$pilote->CheckDroit("TypePilote"))
	{ 
		$ret=array();
		$ret["title"]=$tabLang["lang_forbiden"];
		$ret["message"]=$tabLang["lang_forbiden_msg"];
		$ret["status"]="error";
		$result["checks"][]=$ret;
		$valid=0;
	}

	$result["edite"]=$resa->edite;
	$result["uid_pilote"]=$resa->uid_pilote;

	// Vérifie si l'utilisateur a le droit de modifier la réservation
	$result["upd_pilote"]="oui";
	$result["upd_instructeur"]="oui";
	if ($MyOpt["AllowUpdateAllCalendar"]=="off")
	{
		if (GetDroit("ModifReservations"))
		{
			$result["upd_pilote"]="oui";
		}
		else if ($resa->uid_pilote==0)
		{
			$result["upd_pilote"]="non";
		}
		else if ($resa->uid_pilote==$gl_uid)
		{
			$result["upd_pilote"]="non";
		}
		else if ($resa->uid_instructeur==$gl_uid)
		{
			$result["upd_pilote"]="non";
			$result["upd_instructeur"]="non";
		}
		else if (($resa->uid_pilote!=$gl_uid) && ($resa->uid_instructeur!=$gl_uid))
		{
			$result["edite"]="non";
			$result["upd_pilote"]="non";
			$valid=0;
		}
		else
		{
			$result["upd_pilote"]="non";
		}
	}		


	// Vérifie si on doit cocher la case d'acception des conditions

	$nbvol=$pilote->NombreVols(floor($MyOpt["maxDernierVol"]/30),"val",$uid_ress);
	$result["nbvol"]=$nbvol;

	if ($resa->edite=='non')
	{
		$result["chkreservation"]=0;
	}
	else if ($result["instructeur"]==1)
	{
		$result["chkreservation"]=0;
	}
	else if (($MyOpt["ChkValidResa"]=="on") && ($uid_instructeur==0) && ($nbvol==0))
	{
		$result["chkreservation"]=1;

		if ($accept!="oui")
		{
			$valid=0;
			$result["checks"][]=array("message"=>$tabLang["lang_acceptcond"],"status"=>"info","field"=>"ValidResa");
		}
	}
	else
	{
		$result["chkreservation"]=0;
	}

	$resa->tpsreel=$tpsreel;
	$resa->horadeb=$horadeb;
	$resa->horafin=$horafin;
	$resa->potentielh=$potentielh;
	$resa->potentielm=$potentielm;
	$resa->carbavant=$carbavant;
	$resa->carbapres=$carbapres;
	$resa->prixcarbu=$prixcarbu;

	if ($fonc=="submit")
	{
		// Mise à jour de la réservation
		if (($id>0) && (($tpsreel!="") || ($horadeb!="") || ($horafin!="")))
		{
			$resa->Save();
			$result["checks"][]=array("title"=>"","message"=>$tabLang["lang_horasaved"],"status"=>"ok");
		}
	}

	$resa->description=$description;
	$resa->uid_pilote=$uid_pilote;
	$resa->uid_debite=$uid_debite;
	$resa->uid_instructeur=$uid_instructeur;
	$resa->uid_ressource=$uid_ress;
	$resa->tarif=$tarif;
	$resa->destination=$destination;
	$resa->nbpersonne=$nbpersonne;
	$resa->invite=$invite;
	$resa->accept=$accept;
	$resa->tpsestime=$tpsestime;
	$resa->dte_deb=$dte_deb." ".$hor_deb;
	$resa->dte_fin=$dte_fin." ".$hor_fin;

	$r=$resa->CheckResa();

	$result["res"]=$r;
	
	if (count($r)>0)
	{
		foreach($r as $m)
		{
			$valid=0;
			$result["checks"][]=array("title"=>"","message"=>$m["txt"],"status"=>$m["status"],"field"=>$m["field"]);
		}
	}

	$result["valid"]=$valid;

	echo json_encode($result);
?>