<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  

// ---- Récupère les paramètres
	// $id=(is_numeric($_REQUEST["id"]) ? $_REQUEST["id"] : 0);
	// $deb=(is_numeric($_REQUEST["deb"]) ? $_REQUEST["deb"] : 0);
	// $fin=(is_numeric($_REQUEST["fin"]) ? $_REQUEST["fin"] : 0);

	$id=checkVar("id","numeric");
	$deb=checkVar("deb","numeric");
	$fin=checkVar("fin","numeric");
	$resa=checkVar("resa","numeric");

//	$deb=strtotime("2013-08-07 17:00");
//	$fin=strtotime("2013-08-07 18:00");

// ---- Charge les informations sur le chargement
	$ret=array();
	$ret["status"]=200;
	$ret["idavion"]=$id;
	$ret["availability"]="";
	$ret["icon"]="mdi-checkbox-blank-outline";
	$ret["message"]="";

	if (($id>0) && ($deb>0) && ($fin>0))
	{
		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier AS cal WHERE uid_avion='$id' AND dte_deb<'".date("Y-m-d H:i:s",$fin)."' AND dte_fin>'".date("Y-m-d H:i:s",$deb)."' AND actif='oui'";
		$sql->Query($query);

		if ($sql->rows>0)
		  {
			$ok="nok";
			$txt="Occupé";
			$ret["availability"]="";
			$ret["icon"]="mdi-checkbox-blank-outline";
			$ret["message"]="";

			$ret["availability"]="nok";
			$ret["icon"]="mdi-close-box-outline";
			$ret["message"]="Occupé";

			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
				if ($sql->data["id"]==$resa)
				{

					$ret["availability"]="ok";
					$ret["icon"]="mdi-checkbox-marked-outline";
					$ret["message"]="Réservé";
				}
			}
		}
		else
		{
			$ret["availability"]="nc";
			$ret["icon"]="mdi-checkbox-blank-outline";
			$ret["message"]="Disponible";
		}
	}
	else
	{
		if (($deb>0) && ($deb>0))
		{
			$ret["status"]=500;
			$ret["availability"]="error";
			$ret["icon"]="mdi-alert";
			$ret["message"]="Les paramètres sont incorrects";
		}
		else
		{
			// $ret["status"]=500;
			$ret["availability"]="error";
			// $ret["icon"]="mdi-alert";
			$ret["message"]="";
		}
	}

	echo json_encode($ret);
	exit;

?>
