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

	$order=$c[$s[0]["column"]]["name"];
	$trie=($s[0]["dir"]=="asc") ? "i" : "d";

	$draw=checkVar("draw","numeric");
	$ts=checkVar("start","numeric");
	$tl=checkVar("length","numeric");
	$theme=checkVar("theme","varchar",20);

// ---- Mes Vols
	if ($fonc=="mesvols")
	{

		$lstresa=ListReservationVols($sql,$id,$order,$trie,$ts,$tl);
		$usr=new user_class($id,$sql);


		$totligne=ListReservationNbLignes($sql,$id);
		// $totligne=count($lstresa);
		
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

			if ($t1!=$t2)
			  { $dte=$t1." - ".$t2; }
			else if (sql2time($resa->dte_deb)!="00:00:00")
			  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
			else
			  { $dte=$t1." (N/A)"; }

			if ($id==$resa->uid_instructeur)
			{
				$usrinst = new user_class($resa->uid_pilote,$sql);
				$ins="Avec : ".$usrinst->Aff("fullname");
			}
			else
			{
				$ins=$usrinst->Aff("fullname");
			}


			$ret["data"][$ii]=array(
				0=>"<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($dte,ENT_QUOTES,"utf-8")."</a>",
				1=>$ress->aff("immatriculation"),
				2=>$resa->AffTempsReel(),
				3=>$resa->AffTemps(),
				4=>$resa->AffPrix(),
			);
			if ($theme!="phone")
			{
				$ret["data"][$ii][5]=$ins;
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
				$ret["data"][$ii]=array(
					0=>"<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($t1,ENT_QUOTES,"utf-8")."</a>",
					1=>$usrpil->Aff("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->Aff("fullname") : ""),
					2=>$resa->tarif,
					3=>$resa->destination,
					4=>"<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".htmlentities(sql2time($resa->dte_deb,"nosec"),ENT_QUOTES,"utf-8")."</a>",
					5=>"<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".htmlentities(sql2time($resa->dte_fin,"nosec"),ENT_QUOTES,"utf-8")."</a>",
					6=>$resa->AffTempsReel(),
					7=>($resa->carbavant>0) ? $resa->carbavant."L" : " ",
					8=>($resa->carbapres>0) ? $resa->carbapres."L" : " ",
					9=>$resa->AffPotentiel("fin"),
					10=>$resa->AffTempsVols("fin"),
				);
			}
			else
			{
				$ret["data"][$ii]=array(
					0=>"<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($t1,ENT_QUOTES,"utf-8")."</a>",
					1=>$usrpil->Aff("fullname"),
					2=>$resa->AffTempsReel(),
					3=>($resa->carbavant>0) ? $resa->carbavant."L" : (($resa->carbapres>0) ? $resa->carbapres."L" : " "),
					4=>$resa->AffPotentiel("fin"),
				);
			}
			$ii=$ii+1;

		}
	
	}
	
	
	// error_log(print_r($ret,true));
	// error_log(json_encode($ret));


	echo json_encode($ret);
?>