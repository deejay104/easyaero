<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }


// ---- Affiche les données club


    $result=array(
        "object"=>"parcours_eleves",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

// ---- Données de formation

	require_once ($appfolder."/class/synthese.inc.php");

    $data=array(
        "MNA"=>array("id"=>1,"ordre"=>1,"nb"=>0,"libelle"=>"Maniabilité"),
        "NAV"=>array("id"=>2,"ordre"=>2,"nb"=>0,"libelle"=>"Navigation"),
        "TST"=>array("id"=>3,"ordre"=>3,"nb"=>0,"libelle"=>"Test"),
        "END"=>array("id"=>4,"ordre"=>4,"nb"=>0,"libelle"=>"Terminé"),
    );

    $lst=ListeObjets($sql,"livret",array("id"),array("actif"=>"oui","status"=>"active"),array("dte_deb"));
	foreach($lst as $fid=>$d)
	{
		$livret = new livret_class($fid,$sql);
        $data[$livret->val("phase")]["nb"]=$data[$livret->val("phase")]["nb"]+1;
    }


// ---- Result
    foreach($data as $id=>$d)
    {
        $result["data"][]=$d;           
    }

    echo json_encode($result);
?>