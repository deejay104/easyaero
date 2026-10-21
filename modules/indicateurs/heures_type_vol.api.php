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
        "object"=>"heures_type_vol",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

// ---- Type de vols (bapteme)
    $query ="SELECT SUM(cal.temps) AS nb FROM ".$MyOpt["tbl"]."_bapteme AS btm LEFT JOIN ".$MyOpt["tbl"]."_calendrier AS cal ON cal.id=btm.id_resa WHERE cal.actif='oui' AND cal.dte_deb>='".$year_current."-01-01' AND cal.dte_deb<'".$dte_ref."'";
	$res=$sql->QueryRow($query);
    $btm=$res["nb"] ?? 0;

    $result["data"][]=array(
        "id"=>1,
        "code"=>"BTM",
        "libelle"=>"Baptème",
        "heures"=>round($btm/60,1),
    );


// ---- Type de vols (solo)
    $query ="SELECT SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE actif='oui' AND uid_instructeur=0 AND dte_deb>='".$year_current."-01-01' AND dte_deb<'".$dte_ref."'";
	$res=$sql->QueryRow($query);
    $nb=$res["nb"] ?? 0;

    $result["data"][]=array(
        "id"=>2,
        "code"=>"SOLO",
        "libelle"=>"Solo",
        "heures"=>round(($nb-$btm)/60,1),
    );

// ---- Type de vols (double)
    $query ="SELECT SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE actif='oui' AND uid_instructeur>0 AND dte_deb>='".$year_current."-01-01' AND dte_deb<'".$dte_ref."'";
	$res=$sql->QueryRow($query);
    $nb=$res["nb"] ?? 0;

    $result["data"][]=array(
        "id"=>3,
        "code"=>"DC",
        "libelle"=>"Instruction",
        "heures"=>round($nb/60,1),
    );


// ---- Result
    echo json_encode($result);

?>