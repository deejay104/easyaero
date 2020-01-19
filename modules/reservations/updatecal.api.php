<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	// Short-circuit if the client did not give us a date range.
	if (!isset($_GET['jstart']) || !isset($_GET['jend'])) {
		die("Please provide a date range.");
	}

	$jstart=$_GET['jstart'];
	$jend=$_GET['jend'];

	$fh=date("O",floor($jstart)/1000+4*3600)/100;
	$jstart=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
	$fh=date("O",floor($jend)/1000+4*3600)/100;
	$jend=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
	
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		die("Please provide an event id.");
	}

	$id=$_GET['id'];
	$ress=checkVar("ress","numeric");

	// $query="SELECT edite FROM ".$MyOpt["tbl"]."_calendrier WHERE id='".$id."'";
	// $res=$sql->QueryRow($query);

	// if ($res["edite"]=='oui')
	// {	
		// $query="UPDATE ".$MyOpt["tbl"]."_calendrier SET dte_deb='".$jstart."',dte_fin='".$jend."'".(($ress>0) ? ",uid_avion='".$ress."'" : "")." WHERE id='".$id."'";
		// error_log($query);
		// $sql->Update($query);
	// }
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

	$resa=new resa_class($id,$sql);
	$resa->dte_deb=$jstart;
	$resa->dte_fin=$jend;
	if ($ress>0)
	{
		$resa->uid_ressource=$ress;
	}
	$ret=$resa->Save();

	$r=array();
	$r["result"]="OK";
	if ($ret!="")
	{
		$r["result"]="NOK";
		$r["value"]=$ret;
	}
	echo json_encode($r);
?>