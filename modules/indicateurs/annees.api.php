
<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Liste des années

    $result=array(
        "object"=>"annees",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

	$query="SELECT DATE_FORMAT(dte_deb,'%Y') AS annee FROM ".$MyOpt["tbl"]."_calendrier GROUP BY DATE_FORMAT(dte_deb,'%Y') ORDER BY DATE_FORMAT(dte_deb,'%Y')";
	$sql->Query($query);

    $result["recordsTotal"]=$sql->rows;
    $result["recordsFiltered"]=$sql->rows;

    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

        $result["data"][]=array(
            "id"=>$sql->data["annee"],
            "annee"=>$sql->data["annee"],
            "current"=>($sql->data["annee"]=="Y") ? 1 : 0,
        );

    }

    echo json_encode($result);
    
?>