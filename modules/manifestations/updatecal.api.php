<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	$id=checkVar("id","numeric");
	$jstart=checkVar('jstart','numeric');

	if ($id==0) {
		$input_arrays["error"]="Please provide an event id.";
		echo json_encode($input_arrays);
		exit;
	}


// ---- Update
	$query="UPDATE ".$MyOpt["tbl"]."_manips SET dte_manip='".date("Y-m-d H:i:s",$jstart)."' WHERE id='".$id."'";
	$sql->Update($query);

// ---- Return
	$r=array();
	$r["status"]=200;
	$r["tz"]=date_default_timezone_get();
	$r["start"]=date("Y-m-d H:i:s",$jstart);

	echo json_encode($r);

?>