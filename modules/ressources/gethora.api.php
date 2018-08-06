<?php
// ---- Refuse l'acc�s en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge les d�pendances
	require_once ($appfolder."/class/ressources.inc.php");

// ---- V�rifie les param�tres

	$id=checkVar("id","numeric");
	$deb=checkVar("deb","varchar");
	$fin=checkVar("fin","varchar");

// ---- Calcul de l'horametre
	$ret=array();
	$ret["tps"]=0;
	$ret["aff"]="0h 00";
	if ($id>0)
	{
		$resr=new ress_class($id,$sql);
		
		$ret["tps"]=$resr->CalcHorametre($deb,$fin);
		$ret["aff"]=AffTemps($ret["tps"]);
	}

	// Send JSON to the client.
	echo json_encode($ret);

?>