<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

// ----
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/echeance.inc.php");

// ---- Affiche les données avions

    $year=checkVar("annee","numeric");

    $year_current=$year;
    $year_prev=$year-1;
    $dte_ref=($year==date("Y")) ? date("Y-m-d") : date("Y-m-d",strtotime($year_current."-12-31"));
    $dte_ref_prev=($year==date("Y")) ? date($year_prev."-m-d") : date("Y-m-d",strtotime($year_prev."-12-31"));

    $result=array(
        "object"=>"reservations",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

// ---- Taux de réservation
	require_once ($appfolder."/class/ressources.inc.php");
	$lstusr=ListeRessources($sql);
    $nbAvions=count($lstusr);

    $creneaux = array(
        "08"=>"08-10",
        "09"=>"08-10",
        "10"=>"10-12",
        "11"=>"10-12",
        "12"=>"12-13",
        "13"=>"12-13",
        "14"=>"14-16",
        "15"=>"14-16",
        "16"=>"16-18",
        "17"=>"16-18",
        "18"=>"18-20",
        "19"=>"18-20",
        "20"=>"20-21",
        "21"=>"20-21",
    );       
 
    $plage=21-8;

    $data=array();

//    $minutes = array_fill(1, 7, array_fill(0, count($creneaux), 0));
    for($i=1; $i<=7; $i++)
	{ 
        foreach($creneaux as $ii=>$d)
        {
            $minutes[$i][$d]=0;
        }
    }
    $result["secondes"]=$minutes;
 
    // Nombre de lundi, mardi,... dans la période
    $nbJours = array_fill(1, 7, 0);
    for ($t = strtotime($year_current."-01-01"); $t < strtotime($dte_ref); $t += 86400) {
        $nbJours[(int) gmdate('N', $t)]++;
    }    

    // Récupère les heures de vol
    $query="SELECT dte_deb AS deb, dte_fin AS fin, temps FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='".$year_current."-01-01' AND dte_deb<='".$dte_ref."' AND actif='oui'";
	$sql->Query($query);
    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
 
        $day=date("N",strtotime($sql->data["deb"]));
        $deb=date("H",strtotime($sql->data["deb"]));

        if (isset($creneaux[$deb]))
        {
            $minutes[$day][$creneaux[$deb]]=($minutes[$day][$creneaux[$deb]] ?? 0)+$sql->data["temps"];
        }
    }
 
    // Compile le résultat
    $id=0;
    for($j=1; $j<=7; $j++)
	{ 
        foreach($creneaux as $ii=>$d)
        {
            $ouvertes = $nbJours[$j] * 2 * $nbAvions;   // heures-avion
            $reservees = $minutes[$j][$d] / 60;
            $data[] = [
                'id'               => (string) ++$id,
                'jour'             => (string) $j,
                'creneau'          => (string) $d,
                // plafonné à 100 en cas de doubles réservations
                'taux'             => $ouvertes > 0 ? (string) round(min(100, 100 * $reservees / $ouvertes), 1) : null,
                'heures_reservees' => (string) round($reservees, 1),
                'heures_ouvertes'  => (string) $ouvertes,
            ];
        }
    }

    $result["data"]=$data;

// ---- Result
    echo json_encode($result);

?>