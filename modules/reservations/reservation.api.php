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
	$result["checks"]=array();

	$resa["resa"]=new resa_class($id,$sql);

// --- Check availaibility
	if ($fonc=="check")
	{
		$valid=1;

		if ($uid_pilote>0)
		{
			$resa["pilote"]=new user_class($uid_pilote,$sql,false,true);
		}
		else
		{
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql,false,true);
		}

		if ($uid_instructeur>0)
		{
			$resa["instructeur"]=new user_class($uid_instructeur,$sql,false,true);
		}
		else
		{
			$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql,false,true);
		}


		if ($resa["pilote"]->isSoldeNegatif())
		{
			$s=$resa["pilote"]->CalcSolde();
			$ret=array();
		  	$ret["title"]="Le compte du pilote est NEGATIF";
		  	$ret["message"]="Solde du compte $s €.<br />Appeller le trésorier pour l'autorisation d'un découvert.<br />";
			$ret["status"]="error";
			$result["checks"][]=$ret;
		}

		if ($resa["resa"]->edite=='non')
		{
			$ret=array();
		  	$ret["title"]="Réservation déjà saisie en compta";
			$ret["message"]="Il n'est plus possible de modifier cette réservation car elle a déjà été saisie en compta";
			$ret["status"]="information";
			$result["checks"][]=$ret;
		}

		// Vérifie si le pilote est laché sur l'avion
		if ((!$resa["pilote"]->CheckLache($uid_ress)) && ($uid_instructeur==0))
		{
			$ret=array();
		  	$ret["title"]="Réservation impossible";
		  	$msg_err.="Le pilote sélectionné n'est pas laché sur cet avion.<br />";
		  	$msg_err.="Il n'est pas possible de réserver sans instructeur.<br />";
			$ret["message"]=$msg_err;
			$ret["status"]="error";
			$result["checks"][]=$ret;
		}

		// Vérifie si le pilote est autorisé
		if (!$resa["pilote"]->CheckDroit("TypePilote"))
		{ 
			$ret=array();
			$ret["title"]="Réservation impossible";
			$ret["message"]="Le pilote sélectionné n'a pas le droit d'effectuer de réservation d'avion";
			$ret["status"]="error";
			$result["checks"][]=$ret;
		}


		// Mise à jour de la réservation
		$resa["resa"]->description=$description;
		$resa["resa"]->uid_pilote=$uid_pilote;
		$resa["resa"]->uid_debite=$uid_debite;
		$resa["resa"]->uid_instructeur=$uid_instructeur;
		$resa["resa"]->uid_ressource=$uid_ress;
		$resa["resa"]->tarif=$tarif;
		$resa["resa"]->destination=$destination;
		$resa["resa"]->nbpersonne=$nbpersonne;
		$resa["resa"]->invite=$invite;
		$resa["resa"]->accept=$accept;
		$resa["resa"]->tpsestime=$tpsestime;
		$resa["resa"]->dte_deb="$dte_deb $hor_deb";
		$resa["resa"]->dte_fin="$dte_fin $hor_fin";
		$resa["resa"]->tpsreel=$tpsreel;
		$resa["resa"]->horadeb=$horadeb;
		$resa["resa"]->horafin=$horafin;
		$resa["resa"]->potentielh=$potentielh;
		$resa["resa"]->potentielm=$potentielm;

		$resa["resa"]->carbavant=$carbavant;
		$resa["resa"]->carbapres=$carbapres;
		$resa["resa"]->prixcarbu=$prixcarbu;

		// Vérifie si on doit cocher la case d'acception des conditions
		if ($resa["resa"]->edite=='non')
		{
			$result["chkreservation"]=0;
		}
		else if (($MyOpt["ChkValidResa"]=="on") && ($resa["resa"]->uid_instructeur==0) && ($resa["pilote"]->NombreVols(floor($MyOpt["maxDernierVol"]/30),"val",$resa["resa"]->uid_ressource)>0))
		{
			$result["chkreservation"]=1;

			if ($resa["resa"]->accept!="oui")
			{
				$valid=0;
				$result["checks"][]=array("message"=>"Vous devez accepter les conditions de vol","status"=>"warning");
			}
		}
		else
		{
			$result["chkreservation"]=0;
		}


		$r=$resa["resa"]->CheckResa();
		
		if (count($r)>0)
		{
			foreach($r as $m)
			{
				$valid=0;
				$result["checks"][]=array("title"=>$m["title"],"message"=>$m["txt"],"status"=>$m["status"]);
			}
		}

		$result["valid"]=$valid;
		$result["edite"]=$resa["resa"]->edite;
	}

	echo json_encode($result);
?>