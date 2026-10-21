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
        "object"=>"echeances_pilotes",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );
    
    /*
    ## `echeances_pilotes`

    Compteurs uniquement, sans noms : la page d'indicateurs n'a pas à exposer de données nominatives.

    | Champ | Type | Description |
    |---|---|---|
    | `id` | chaîne | Identifiant |
    | `code` | chaîne | `medical`, `sep`, `debiteurs`… |
    | `libelle` | chaîne | Texte affiché, ex. « Certificats médicaux expirant sous 60 jours » |
    | `nb` | entier | Nombre de pilotes concernés |
    | `niveau` | chaîne | `urgent`, `a_planifier` ou `ok` (badge affiché) |
    */

// ---- Liste des échéances
	$data=array();

    $query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype WHERE context='utilisateurs' AND actif='oui' ORDER BY description";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabecheance[$sql->data["id"]]=$sql->data["description"];
        $data[$sql->data["description"]]=array("expire"=>0,"planifie"=>0);
	}

	$lstusr=ListActiveUsers($sql,"std","");
	
	foreach($lstusr as $i=>$id)
	  {
		foreach($tabecheance as $ii=>$t)
		{
			$dte = new echeance_core(0,$sql,$id);
			$dte->loadtype($ii);

            if (($dte->dte_echeance!="0000-00-00") && ($dte->dte_echeance!=""))
            {
                if (strtotime($dte->dte_echeance)<time())
                {
                    $data[$t]["expire"]++;
                }
                else if (strtotime($dte->dte_echeance)<time()+24*3600*30)
                {
                    $data[$t]["planifie"]++;
                }
            }
		}
	}

// ---- Result
    $id=0;
    foreach($data as $d=>$dd)
    {
        $result["data"][]=array(
            "id"=>++$id,
            "code"=>$d,
            "libelle"=>$d,
            "nb"=>$dd["expire"]>0 ? $dd["expire"] : $dd["planifie"],
            "niveau"=>($dd["expire"]>0) ? "urgent" : (($dd["planifie"]>0) ? "a_planifier" : "ok"),
        );
    }


        echo json_encode($result);

?>