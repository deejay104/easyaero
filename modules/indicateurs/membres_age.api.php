<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }


// ---- Affiche les données club


    $result=array(
        "object"=>"annees",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

// ---- Données de formation

	require_once ($appfolder."/class/synthese.inc.php");

    $data=array(
        "1"=>array("id"=>1,"ordre"=>1,"nb"=>0,"tranche"=>"< 25 ans"),
        "2"=>array("id"=>2,"ordre"=>2,"nb"=>0,"tranche"=>"25-39"),
        "3"=>array("id"=>3,"ordre"=>3,"nb"=>0,"tranche"=>"40-54"),
        "4"=>array("id"=>4,"ordre"=>4,"nb"=>0,"tranche"=>"55-69"),
        "5"=>array("id"=>5,"ordre"=>5,"nb"=>0,"tranche"=>"70 et +"),
    );

    $query="SELECT * FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui'";
	$sql->Query($query);
    $now=time();
    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
 
		if ($sql->data["dte_naissance"]!="0000-00-00")
        {
            $age=floor(($now-strtotime($sql->data["dte_naissance"]))/(365.25*24*3600));
        }
		else
        {
			$age=0;
        }

        if ($age<25)
        {
            $data[1]["nb"]=$data[1]["nb"]+1;
        }
        else if (($age>=25) && ($age<39))
        {
            $data[2]["nb"]=$data[2]["nb"]+1;
        }
        else if (($age>=40) && ($age<54))
        {
            $data[3]["nb"]=$data[2]["nb"]+1;
        }
        else if (($age>=54) && ($age<69))
        {
            $data[4]["nb"]=$data[2]["nb"]+1;
        }
        else if ($age>=70)
        {
            $data[5]["nb"]=$data[2]["nb"]+1;
        }
    }


// ---- Result
    foreach($data as $id=>$d)
    {
        $result["data"][]=$d;           
    }

    echo json_encode($result);
?>