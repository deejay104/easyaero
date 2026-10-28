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
	$o=checkVar("order","array");
	$search=checkVar("search","array");


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
		"heure_deb"=>"dte_deb",
		"heure_fin"=>"dte_fin",
		"tarif"=>"tarif",
		"dest"=>"destination",
		"heure"=>"tpsreel",
		"nom"=>"uid_pilote",	
	);

	if (!isset($o[0]))
	{
		$o=array(0=>array("column"=>0,"dir"=>"desc"));
	}

	$order=$taborder[$c[$o[0]["column"]]["data"]] ?? "";
	$trie=($o[0]["dir"]=="asc") ? "asc" : "desc";

// ---- Mes Vols
	if ($fonc=="mesvols")
	{

		$lstresa=ListReservationVols($sql,$id,$order,$trie,$ts,$tl);
		$usr=new user_class($id,$sql);

		$totligne=ListReservationNbLignes($sql,$id);
		
		$ret["draw"]=$draw;
		$ret["recordsTotal"]=$totligne;
		$ret["recordsFiltered"]=0;
		$ret["order"]=$order;
		$ret["trie"]=$trie;
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


			$tab=array();
			$tab["id"]=$ii;
			$tab["dte_deb"]=htmlentities($dte,ENT_QUOTES,"utf-8");
			$tab["immat"]=$ress->val("immatriculation");
			$tab["tpsreel"]=$resa->AffTempsReel();
			$tab["temps"]=$resa->AffTemps();
			$tab["cout"]=$resa->AffPrix();
			$tab["instructeur"]=$resa->uid_instructeur;


			if (CheckSearch($tab,$search["value"]))
			{
				$ret["data"][$ii]=$tab;

				$ret["data"][$ii]["_link"]["dte_deb"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($dte,ENT_QUOTES,"utf-8")."</a>";
				$ret["data"][$ii]["_link"]["immat"]=$ress->aff("immatriculation");
				$ret["data"][$ii]["_link"]["instructeur"]=$ins;
				$ii++;
			}
		}

		$ret["recordsFiltered"]=$ii;

	}
// ---- Carnet de vols
	else if ($fonc=="carnet")
	{
		
		$lstresa=ListCarnetVols($sql,$id,$order,$trie,$ts,$tl);
		
		$totligne=ListCarnetNbLignes($sql,$id);

		$ret["draw"]=$draw;
		$ret["recordsTotal"]=$totligne;
		$ret["recordsFiltered"]=0;
		$ret["order"]=$order;
		$ret["trie"]=$trie;
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

			$tab=array();
			$tab["id"]=$ii;
			$tab["dte_deb"]=htmlentities($t1,ENT_QUOTES,"utf-8");
			$tab["nom"]=$usrpil->val("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->val("fullname") : "");
			$tab["tarif"]=$resa->tarif;
			$tab["dest"]=$resa->destination;
			$tab["heure_deb"]=htmlentities(sql2time($resa->dte_deb,"nosec"),ENT_QUOTES,"utf-8");
			$tab["heure_fin"]=htmlentities(sql2time($resa->dte_fin,"nosec"),ENT_QUOTES,"utf-8");
			$tab["heure"]=$resa->AffTempsReel();
			$tab["carbavant"]=($resa->carbavant>0) ? $resa->carbavant."L" : "-";
			$tab["carbapres"]=($resa->carbapres>0) ? $resa->carbapres."L" : "-";
			$tab["potentiel"]=$resa->AffPotentiel("fin");
			$tab["total"]=$resa->AffTempsVols("fin");


			if (CheckSearch($tab,$search["value"]))
			{
				$ret["data"][$ii]=$tab;

				$ret["data"][$ii]["_link"]["dte_deb"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".htmlentities($t1,ENT_QUOTES,"utf-8")."</a>";

				$ret["data"][$ii]["_link"]["nom"]=$usrpil->Aff("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->Aff("fullname") : "");
				$ret["data"][$ii]["_link"]["tarif"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".$resa->tarif."</a>";
				$ret["data"][$ii]["_link"]["dest"]="<a href='".geturl("reservations","reservation","id=".$rid)."'>".$resa->destination."</a>";

				$ii++;
			}

		}
		$ret["recordsFiltered"]=$ii;

	}
	
	
	// error_log(print_r($ret,true));
	// error_log(json_encode($ret));


	echo json_encode($ret);
?>