<?
// ---- Refuse l'acc�s en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ----
	$l=640;
	$h=480;

	$id=checkVar("id","numeric");


// ---- Load trace
	$query="SELECT rte.id,rte.nom,wpt.description,wpt.lon,wpt.lat FROM ".$MyOpt["tbl"]."_navroute AS rte LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wpt ON rte.nom=wpt.nom WHERE rte.idnav='".$id."' ORDER BY ordre";
	$sql->Query($query);

	error_log($query);		

	$tabPoints=array();
	$lastx=0;
	$lasty=0;


	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		
		$tabPoints["data"][$i]["nom"]=$sql->data["nom"];
		$tabPoints["data"][$i]["latitude"]=$sql->data["lat"];
		$tabPoints["data"][$i]["longitude"]=$sql->data["lon"];
	}


// ---- Show image	

	echo json_encode($tabPoints);
?>