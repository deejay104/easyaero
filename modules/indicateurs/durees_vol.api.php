<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }


// ---- Affiche les données club

    $annee=checkVar("annee","numeric");

    $result=array(
        "id"=>$annee,
        "object"=>"durees_vol",
        "empty"=>false,
        "loaded"=> true,
        "status"=>200,
    );

    $result["data"]=array();
 
    $year_current=$annee;
    $year_prev=$annee-1;
    $dte_ref=($annee==date("Y")) ? date("Y-m-d") : date("Y-m-d",strtotime($year_current."-12-31"));
    $dte_ref_prev=($annee==date("Y")) ? date($year_prev."-m-d") : date("Y-m-d",strtotime($year_prev."-12-31"));

    $result["data"]["annee"]=$annee;
    $result["data"]["date_reference"]=$dte_ref;
    $result["data"]["date_prev"]=$dte_ref_prev;

// ---- 
    // Heures de vols
    $query ="SELECT ";
    $query.="CASE ";
    $query.="WHEN m <= 30 THEN 1 ";
    $query.="WHEN m <= 60 THEN 2 ";
    $query.="WHEN m <= 90 THEN 3 ";
    $query.="ELSE 4 ";
    $query.="END AS tranche, ";
    $query.="SUM(CASE WHEN periode = 'N'  THEN 1 ELSE 0 END) AS nb, ";
    $query.="SUM(CASE WHEN periode = 'N'  THEN m ELSE 0 END) AS minutes, ";
    $query.="SUM(CASE WHEN periode = 'N1' THEN 1 ELSE 0 END) AS nb_n1 ";
    $query.="FROM ( ";
    $query.="SELECT ";
    $query.="temps AS m,  ";
    $query.="CASE WHEN dte_deb >= '".$year_current."-01-01' THEN 'N' ELSE 'N1' END AS periode ";
    $query.="FROM ".$MyOpt["tbl"]."_calendrier    ";
    $query.="WHERE (   (dte_deb >= '".$year_current."-01-01' AND dte_deb < '".$dte_ref."')   "; 
    $query.="     OR (dte_deb >= '".$year_prev."-01-01' AND dte_deb < '".$dte_ref_prev."'))    "; 
    $query.="AND actif='oui'         ";                                     
    $query.=") v ";
    $query.="WHERE m > 0 ";
    $query.="GROUP BY tranche ";
    $query.="ORDER BY tranche";
	$sql->Query($query);

    $tabTranche=array(
        1=>"0-30 min",
        2=>"30-60 min",
        3=>"60-90 min",
        4=>">90 min",
    );

    $result["data"]=array();
    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
        $result["data"][]=array(
            "ordre"=>$sql->data["tranche"],
            "tranche"=>$tabTranche[$sql->data["tranche"]],
            "nb"=>$sql->data["nb"],
            "minutes"=>$sql->data["minutes"],
            "nb_n1"=>$sql->data["nb_n1"],
        );

    }
/*

| Champ | Type | Description |
|---|---|---|
| `id` | chaîne | Identifiant |
| `ordre` | entier | Ordre d'affichage (1 = vols les plus courts) |
| `tranche` | chaîne | Libellé : `0–30 min`, `30–60 min`, `60–90 min`, `Plus de 90 min` |
| `nb` | entier | Nombre de vols de la tranche sur la période |
| `minutes` | entier | Total des minutes de vol de la tranche ; sert à la durée moyenne et à la part des heures. Facultatif |
| `nb_n1` | entier | Nombre de vols de la tranche sur la même période de l'année précédente ; `null` si inconnu (pas de comparaison) |

*/


    echo json_encode($result);
?>

