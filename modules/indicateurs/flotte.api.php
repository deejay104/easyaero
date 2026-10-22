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
	require_once ($appfolder."/class/maintenance.inc.php");

// ---- Affiche les données avions

    $year=checkVar("annee","numeric");

    $year_current=$year;
    $year_prev=$year-1;
    $dte_ref=($year==date("Y")) ? date("Y-m-d") : date("Y-m-d",strtotime($year_current."-12-31"));
    $dte_ref_prev=($year==date("Y")) ? date($year_prev."-m-d") : date("Y-m-d",strtotime($year_prev."-12-31"));

    $result=array(
        "object"=>"flotte",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

    $avions=array();

	$lstusr=ListeRessources($sql);

	foreach($lstusr as $i=>$id)
    {
		$usr = new ress_class($id,$sql,false);
		$avions[$id]["id"]=$id;
		$avions[$id]["immatriculation"]=$usr->val("immatriculation");
		$avions[$id]["type"]=$usr->val("nom");
		$avions[$id]["actif"]=$usr->val("actif");
        $avions[$id]["statut"]=($usr->val("actif")=="oui") ? "disponible" : "indisponible";
        $avions[$id]["visite_intervalle"]=$usr->val("maxpotentiel");

        $tv=$usr->TempsPeriode($year_current."-01-01",$dte_ref);
		$avions[$id]["heures"]=(($tv>0) ? round($tv/60,1) : "0");

		$tp=$usr->AffPotentiel();
		$avions[$id]["visite_potentiel_restant"]=(($tp>0) ? $tp : "0");

		$t=$usr->EstimeMaintenance();		
		$avions[$id]["visite_date_estimee"]=$t;
		$avions[$id]["visite_libelle"]="NA";
	}

// ---- Maintenance
    $query ="SELECT uid_ressource,type FROM ".$MyOpt["tbl"]."_maintenance WHERE actif='oui' AND dte_deb<'".now()."' AND dte_fin>'".now()."'";
	$sql->Query($query);

    $maint=new maint_class(0,$sql);
    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
        $avions[$sql->data["uid_ressource"]]["statut"]="maintenance";
        $maint->valid("type",$sql->data["type"]);
		$avions[$sql->data["uid_ressource"]]["visite_libelle"]=strip_tags($maint->aff("type"));
    }

// ---- Result

    foreach($avions as $id=>$d)
    {
        if (($d["actif"]=="oui") || ($d["heures"]>0))
        {
            $result["data"][]=$d;           
        }
    }


    echo json_encode($result);

?>