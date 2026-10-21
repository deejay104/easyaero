<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    if (!GetDroit("AccesIndicateurs"))
    { header("HTTP/1.0 401 Unauthorized"); exit; }


// ---- Affiche les données club

    $id=checkVar("id","numeric");

    $result=array(
        "id"=>$id,
        "object"=>"club",
        "empty"=>false,
        "loaded"=> true,
        "status"=>200,
    );

/*
| Champ | Type | Description |
|---|---|---|
| `annee` | entier | Année |
| `date_reference` | date | Date d'arrêt des chiffres : aujourd'hui pour l'année en cours, 31/12 sinon |
| `heures_vol` | décimal | Heures de vol du 1er janvier à `date_reference` |
| `heures_vol_n1` | décimal | Heures de l'année précédente **sur la même période** (1er janvier → même jour). Pour une année passée : l'année précédente complète. `null` si inconnu |
| `objectif_heures` | décimal | Objectif annuel d'heures, `null` si aucun |
| `nb_vols` | entier | Nombre de vols sur la période |
| `nb_vols_n1` | entier | Même chose pour l'année précédente, même période |
| `membres_pilotes` | entier | Pilotes brevetés actifs (hors instructeurs) |
| `membres_eleves` | entier | Élèves pilotes actifs |
| `membres_instructeurs` | entier | Instructeurs actifs |
| `membres_autres` | entier | Membres non pilotes |
| `jours_maintenance` | entier | Somme, sur tous les avions, des jours d'immobilisation pour maintenance depuis le 1er janvier |
| `jours_periode` | entier | Nombre de jours du 1er janvier à `date_reference` |
| `nb_avions` | entier | Nombre d'avions de la flotte sur la période |


{
    "id": "1",
    "object": "avion",
    "empty": false,
    "loaded": true,
    "data": {
			"field1": "xxx",
			"field3": "xxx"
    },
    "status": 200
}
*/

    $result["data"]=array();
 
    $year_current=$id;
    $year_prev=$id-1;
    $dte_ref=($id==date("Y")) ? date("Y-m-d") : date("Y-m-d",strtotime($year_current."-12-31"));
    $dte_ref_prev=($id==date("Y")) ? date($year_prev."-m-d") : date("Y-m-d",strtotime($year_prev."-12-31"));

    $result["data"]["annee"]=$id;
    $result["data"]["date_reference"]=$dte_ref;
    $result["data"]["date_prev"]=$dte_ref_prev;

    // Heures de vols
    $query ="SELECT SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_current-01-01' AND dte_deb<='".$dte_ref."' AND actif='oui'";
	$res=$sql->QueryRow($query);
    $result["data"]["heures_vol"]=round($res["nb"]/60,0);

    $query ="SELECT SUM(temps) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_prev-01-01' AND dte_deb<='".$dte_ref_prev."' AND actif='oui'";
	$res=$sql->QueryRow($query);
    $result["data"]["heures_vol_n1"]=round($res["nb"]/60,0);

    // Objectif d'heures
    $query ="SELECT SUM(heures) AS nb FROM ".$MyOpt["tbl"]."_prevision WHERE annee='".$year_current."'";
	$res=$sql->QueryRow($query);
    $result["data"]["objectif_heures"]=$res["nb"];

    // Nb de vols
    $query ="SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_current-01-01' AND dte_deb<='".$dte_ref."' AND actif='oui'";
	$res=$sql->QueryRow($query);
    $result["data"]["nb_vols"]=$res["nb"];

    $query ="SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_deb>='$year_prev-01-01' AND dte_deb<='".$dte_ref_prev."' AND actif='oui'";
	$res=$sql->QueryRow($query);
    $result["data"]["nb_vols_n1"]=$res["nb"];

    // Nb membres
    $query ="SELECT grp.description,COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_utilisateurs AS usr LEFT JOIN ".$MyOpt["tbl"]."_groupe AS grp ON grp.groupe=usr.groupe WHERE usr.actif='oui' AND usr.virtuel='non' AND grp.description IS NOT NULL GROUP BY usr.groupe";
	$sql->Query($query);

    $result["data"]["membres"]=array();
    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
        $result["data"]["membres"][$sql->data["description"]]=$sql->data["nb"];
    }
/*
{
"data":
    "membres": {
        "Elève":3,
        "Instructeur":4,
        "Membre":1,
        "Pilote":4    
    }
}
*/
    // Disponibilités avions
    $query ="SELECT SUM(dte_fin-dte_deb+1) AS nb FROM `".$MyOpt["tbl"]."_maintenance` WHERE actif='oui' AND status='cloture' AND dte_deb>='".$year_current."-01-01' AND dte_deb<='".$dte_ref."'";

    $res=$sql->QueryRow($query);
    $result["data"]["jours_maintenance"]=$res["nb"] ?? 0;
    $result["data"]["jours_periode"]=floor((strtotime($dte_ref)-strtotime($year_current."-01-01"))/(24*3600));
    
    // Nombre d'avions    
    $query ="SELECT COUNT(*) AS nb FROM `".$MyOpt["tbl"]."_ressources` WHERE actif='oui'";

    $res=$sql->QueryRow($query);
    $result["data"]["nb_avions"]=$res["nb"] ?? 0;


    echo json_encode($result);
?>

