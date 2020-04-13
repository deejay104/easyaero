<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	$id=checkVar("id","numeric");
	$fonc=checkVar("fonc","varchar",20);

	// error_log(print_r($_REQUEST,true));
	$ret=array();

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

	$c=checkVar("columns","array");
	$s=checkVar("order","array");


	$draw=checkVar("draw","numeric");
	$ts=checkVar("start","numeric");
	$tl=checkVar("length","numeric");
	$theme=checkVar("theme","varchar",20);

	$taborder=array(
		"dte_deb"=>"dte_deb",
		"immat"=>"uid_avion",
		"tpsreel"=>"tpsreel",
		"temps"=>"temps",
		"cout"=>"prix",
		"instructeur"=>"uid_instructeur",
	);

	$order="";
	if (isset($taborder[$c[$s[0]["column"]]["data"]]))
	{
		$order=$taborder[$c[$s[0]["column"]]["data"]];
	}
	$trie=($s[0]["dir"]=="asc") ? "asc" : "desc";

// ---- Mes Vols
	if ($fonc=="mesvols")
	{

		$lstresa=ListReservationVols($sql,$id,$order,$trie,$ts,$tl);
		$usr=new user_class($id,$sql);

		$totligne=ListReservationNbLignes($sql,$id);
		
		$ret["draw"]=$draw;
		$ret["recordsTotal"]=$totligne;
		$ret["recordsFiltered"]=$totligne;
		$ret["data"]=array();

		$ii=0;
		foreach($lstresa as $i=>$rid)
		{
			$resa = new resa_class($rid,$sql,false);
			$ress = new ress_class($resa->uid_ressource,$sql);
			$usrinst = new user_class($resa->uid_instructeur,$sql);

			$t1=sql2date($resa->dte_deb,"jour");
			$t2=sql2date($resa->dte_fin,"jour");

			if ($theme!="phone")
			{
				if ($t1!=$t2)
				  { $dte=$t1." - ".$t2; }
				else if (sql2time($resa->dte_deb)!="00:00:00")
				  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
				else
				  { $dte=$t1." (N/A)"; }
			}
			else
			{
				if ($t1!=$t2)
				  { $dte=$t1; }
				else if (sql2time($resa->dte_deb)!="00:00:00")
				  { $dte=$t1; }
				else
				  { $dte=$t1; }
			}

			if ($id==$resa->uid_instructeur)
			{
				$usrinst = new user_class($resa->uid_pilote,$sql);
				$ins="Avec : ".$usrinst->Aff("fullname");
			}
			else
			{
				$ins=$usrinst->Aff("fullname");
			}


			$ret["data"][$ii]=array();
			$ret["data"][$ii]["id"]=$ii;
			$ret["data"][$ii]["dte_deb"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($dte,ENT_QUOTES,"utf-8")."</a>";
			$ret["data"][$ii]["immat"]=$ress->aff("immatriculation");
			$ret["data"][$ii]["tpsreel"]=$resa->AffTempsReel();
			$ret["data"][$ii]["temps"]=$resa->AffTemps();
			$ret["data"][$ii]["cout"]=$resa->AffPrix();

			if ($theme!="phone")
			{
				$ret["data"][$ii]["instructeur"]=$ins;
			}
			$ii=$ii+1;
		}
	}
// ---- Carnet de vols
	else if ($fonc=="carnet")
	{
		
		$lstresa=ListCarnetVols($sql,$id,$order,$trie,$ts,$tl);
		
		$totligne=ListCarnetNbLignes($sql,$id);

		$ret["draw"]=$draw;
		$ret["recordsTotal"]=$totligne;
		$ret["recordsFiltered"]=$totligne;
		$ret["data"]=array();

		$ii=0;
		foreach($lstresa as $i=>$rid)
		{
			$resa = new resa_class($rid,$sql,false);
			$ress = new ress_class($resa->uid_ressource,$sql);
			$usrpil = new user_class($resa->uid_pilote,$sql);
			if ($resa->uid_instructeur>0)
			{ $usrinst = new user_class($resa->uid_instructeur,$sql); }

			$t1=sql2date($resa->dte_deb,"jour");
			$t2=sql2date($resa->dte_fin,"jour");

			if ($t1!=$t2)
			  { $dte=$t1." - ".$t2; }
			else if ((sql2time($resa->dte_deb)!="00:00:00"))
			  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
			else
			  { $dte=$t1; }

			if ($theme!="phone")
			{
				$ret["data"][$ii]=array();
				$ret["data"][$ii]["id"]=$ii;
				$ret["data"][$ii]["dte_deb"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($t1,ENT_QUOTES,"utf-8")."</a>";
				$ret["data"][$ii]["nom"]=$usrpil->Aff("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->Aff("fullname") : "");
				$ret["data"][$ii]["tarif"]=$resa->tarif;
				$ret["data"][$ii]["dest"]=$resa->destination;
				$ret["data"][$ii]["heure_deb"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".htmlentities(sql2time($resa->dte_deb,"nosec"),ENT_QUOTES,"utf-8")."</a>";
				$ret["data"][$ii]["heure_fin"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".htmlentities(sql2time($resa->dte_fin,"nosec"),ENT_QUOTES,"utf-8")."</a>";
				$ret["data"][$ii]["heure"]=$resa->AffTempsReel();
				$ret["data"][$ii]["carbavant"]=($resa->carbavant>0) ? $resa->carbavant."L" : " ";
				$ret["data"][$ii]["carbapres"]=($resa->carbapres>0) ? $resa->carbapres."L" : " ";
				$ret["data"][$ii]["potentiel"]=$resa->AffPotentiel("fin");
				$ret["data"][$ii]["total"]=$resa->AffTempsVols("fin");
			}
			else
			{
				$ret["data"][$ii]=array();
				$ret["data"][$ii]["id"]=$ii;
				$ret["data"][$ii]["dte_deb"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($t1,ENT_QUOTES,"utf-8")."</a>";
				$ret["data"][$ii]["nom"]=$usrpil->Aff("fullname");
				$ret["data"][$ii]["heure"]=$resa->AffTempsReel();
				$ret["data"][$ii]["carbavant"]=($resa->carbavant>0) ? $resa->carbavant."L" : (($resa->carbapres>0) ? $resa->carbapres."L" : " ");
				$ret["data"][$ii]["potentiel"]=$resa->AffPotentiel("fin");
			}
			$ii=$ii+1;

		}
	
	}
	
	
	// error_log(print_r($ret,true));
	// error_log(json_encode($ret));


	echo json_encode($ret);
?>