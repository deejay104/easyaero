<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	if ( (!GetDroit("AccesDisponibilites")) && (!GetMyId($id)) )
	  { apiError(401,"Accès non autorisé (AccesDisponibilites)"); }

// ---- Vérifie les paramètres

	$id=checkVar("id","numeric");
	$mid=checkVar("mid","numeric");
	$jstart=checkVar('jstart','numeric');
	$jend=checkVar('jend','numeric');

	if ($fonc=="delete")
	{
		$t=array(
			"actif"=>"non",
		);	
	}
	else
	{
		$t=array(
			"dte_deb"=>date("Y-m-d H:i:s",$jstart),
			"dte_fin"=>date("Y-m-d H:i:s",$jend),
		);
		
	}
	if ($mid>0)
	{
		$t["uid"]=$mid;
	}

	$t["uid_maj"]=$gl_uid;
	$t["dte_maj"]=now();

	
	$id=$sql->Edit("disponibilite",$MyOpt["tbl"]."_disponibilite",$id,$t);

	$r=array();
	$r["status"]=200;
	$r["id"]=$id;
	$r["eventId"]=$id;
	$r["tz"]=date_default_timezone_get();
	$r["start"]=date("Y-m-d H:i:s",$jstart);
	$r["end"]=date("Y-m-d H:i:s",$jend);


	echo json_encode($r);
?>