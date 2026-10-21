<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }


// ---- Affiche les données club


    $result=array(
        "object"=>"heures_mois",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

// ---- Calcul du nombre d'heures par mois

    $year=checkVar("annee","numeric");


    $year_current=$year;
    $year_prev=$year-1;
    $dte_ref=($year==date("Y")) ? date("Y-m-d") : date("Y-m-d",strtotime($year_current."-12-31"));
    $dte_ref_prev=($year==date("Y")) ? date($year_prev."-m-d") : date("Y-m-d",strtotime($year_prev."-12-31"));


    $tabHeures=array();
	for($i=1; $i<=12; $i++)
	{ 
        $tabHeures[$i]=array("nb"=>0,"nb_n1"=>0);
    }

    $query ="SELECT date_format(dte_deb,'%c') AS dte, SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_current-01-01' AND dte_deb<='".$dte_ref."' AND temps<>0 AND actif='oui' GROUP BY date_format(dte_deb,'%Y%m')";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabHeures[$sql->data["dte"]]["nb"]=round($sql->data["nb"]/60,1);
		$tabHeures[$sql->data["dte"]]["nb_n1"]=round($sql->data["nb"]/60,1);
	}

	$query ="SELECT date_format(dte_deb,'%c') AS dte, SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_prev-01-01' AND dte_deb<='".$dte_ref_prev."' AND temps<>0 AND actif='oui' GROUP BY date_format(dte_deb,'%Y%m')";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabHeures[$sql->data["dte"]]["nb_n1"]=round($sql->data["nb"]/60,1);
	}

    foreach($tabHeures as $dte=>$nb)
    {
        $result["data"][]=array(
            "id"=>$dte,
            "mois"=>$dte,
            "heures"=>$nb["nb"],
            "heures_n1"=>$nb["nb_n1"],
        );
    }

    echo json_encode($result);
?>