<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	$id=checkVar("id","numeric");
	$ress=checkVar("ress","numeric");
	$jstart=checkVar('jstart','numeric');
	$jend=checkVar('jend','numeric');


	if ($id==0) {
		apiError(500,"Please provide an event id.");
	}

	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

	$resa=new resa_class($id,$sql);
	$resa->dte_deb=date("Y-m-d H:i:s",$jstart);
	$resa->dte_fin=date("Y-m-d H:i:s",$jend);
	if ($ress>0)
	{
		$resa->uid_ressource=$ress;
	}

	$r=array();
	$r["status"]=200;
	$r["tz"]=date_default_timezone_get();
	$r["start"]=date("Y-m-d H:i:s",$jstart);
	$r["end"]=date("Y-m-d H:i:s",$jend);

	if ($MyOpt["AllowUpdateAllCalendar"]=="off")
	{
		if (($resa->uid_pilote!=$gl_uid) && ($resa->uid_instructeur!=$gl_uid) && (!GetDroit("ModifReservations")))
		{
			$r["status"]=401;
			$r["value"]="Modification non autorisée";
		}
	}

	if ($r["status"]==200)
	{
		$ret=$resa->Save();
		if (count($ret)==0)
		{
			$r["status"]=200;
			$r["result"]="OK";
			$r["value"]="";
		}
		else
		{
			$r["status"]=500;
			$r["result"]="NOK";
			$r["value"]=$ret;
		}	
	}

	echo json_encode($r);
?>