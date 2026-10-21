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
        "object"=>"echeances_avions",
        "limit"=>array(),
        "draw"=> 1,
        "crit"=>array(),
        "status"=>200,
        "data"=>array(),
    );

    $avions=array();

	$lstusr=ListeRessources($sql);

    $iii=0;
	foreach($lstusr as $i=>$id)
    {
		$ress = new ress_class($id,$sql,false);
		$d=$ress->EstimeMaintenance();		

        /*
        | Champ | Type | Description |
        |---|---|---|
        | `id` | chaîne | Identifiant |
        | `date` | date | Date prévue ou estimée |
        | `immatriculation` | chaîne | Avion concerné |
        | `libelle` | chaîne | `Visite 50 h`, `Examen de navigabilité`, `Remise en ligne`… |
        | `niveau` | chaîne | `urgent`, `a_planifier` ou `ok`. Facultatif : sinon la page le déduit de la date (≤ 7 j urgent, ≤ 30 j à planifier) |
        | `estimee` | `"1"` / `"0"` | `"1"` si la date est estimée d'après l'utilisation (visites à l'heure) |
        */
        $pot=$ress->Potentiel();
        $niveau=null;
        $libelle="Maintenance (estimée)";
        $estime=1;
		if (floor($pot/60)<0)
		{
			$niveau="urgent";
            $libelle="Maintenance";
            $estime=0;
            $d=now();
		}
		else if (floor($pot/60)<$ress->val("alertpotentiel"))
		{
			$niveau="a_planifier";
            $libelle="Maintenance";
            $estime=1;
            $d=($d!="NA") ? $d : date("Y-m-d",time()+24*3600*8);
		}

        $avion=array(
            "id"=>++$iii,
            "immatriculation"=>$ress->val("immatriculation"),
            "type"=>$ress->val("nom"),
            "actif"=>$ress->val("actif"),
            "statut"=>($ress->val("actif")=="oui") ? "disponible" : "indisponible",
            "niveau"=>$niveau,
            "date"=>$d,
            "estimee"=>$estime,
            "libelle"=>$libelle,
        );

        // Maintenance prévue
        $query ="SELECT uid_ressource,type,dte_deb,status FROM ".$MyOpt["tbl"]."_maintenance WHERE actif='oui' AND dte_deb>='".now()."' AND uid_ressource='".$id."'";
    	$sql->Query($query);

        if ($sql->rows>0)
        {
            for($ii=0; $ii<$sql->rows; $ii++)
            { 
                $sql->GetRow($ii);
                $avion["id"]=++$iii;
                $avion["statut"]="maintenance";
                $avion["libelle"]="Visite ".$sql->data["type"]."h";
                $avion["date"]=$sql->data["dte_deb"];
                $avion["estimee"]=0;
                $avion["niveau"]=($sql->data["status"]=="confirme'") ? "ok" : "a_planifier";
                $result["data"][]=$avion;
            }            
        }
        else
        {
            $result["data"][]=$avion;
        }

        // Echéance
	    $lstdte=VerifEcheance($sql,$id,"ressources",true);
		$nb=0;
		if (count($lstdte)>0)
		{
			foreach($lstdte as $ii=>$d)
			{
				if ($d["id"]>0)
				{
					$dte=new echeance_class($d["id"],$sql);
                    $avion=array(
                        "id"=>++$iii,
                        "immatriculation"=>$ress->val("immatriculation"),
                        "type"=>$ress->val("nom"),
                        "actif"=>$ress->val("actif"),
                        "statut"=>($ress->val("actif")=="oui") ? "disponible" : "indisponible",
                        "niveau"=>null,
                        "date"=>$dte->dte_echeance,
                        "estimee"=>0,
                        "libelle"=>$dte->description,
                    );
                    $result["data"][]=$avion;

                }
				else
				{
					if ($d["resa"]=="obligatoire")
					{
						$dte=new echeance_class(0,$sql);

                        $avion=array(
                            "id"=>++$iii,
                            "immatriculation"=>$ress->val("immatriculation"),
                            "type"=>$ress->val("nom"),
                            "actif"=>$ress->val("actif"),
                            "statut"=>($ress->val("actif")=="oui") ? "disponible" : "indisponible",
                            "niveau"=>null,
                            "date"=>now(),
                            "estimee"=>0,
                            "libelle"=>$d["description"],
                        );
                        $result["data"][]=$avion;

                    }
				}
			}
		}

    }





// ---- Result



        echo json_encode($result);

?>