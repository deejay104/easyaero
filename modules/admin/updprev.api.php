<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	$ret=array();
	if (!isset($_GET["mois"]))
	{
		$ret["result"]="NOK";
		$ret["msg"]="mois not provided.";
		echo json_encode($ret);
	  	exit;
	}
	$mois=$_GET["mois"];
	$dte=checkVar("dte","numeric");

	if (!isset($_GET["dte"]))
	{
		$ret["result"]="NOK";
		$ret["msg"]="dte not provided.";
		echo json_encode($ret);
	  	exit;
	}
	$ress=checkVar("dte","varchar",4);

	if (!isset($_GET["ress"]))
	{
		$ret["result"]="NOK";
		$ret["msg"]="ress not provided.";
		echo json_encode($ret);
	  	exit;
	}
	$ress=checkVar("ress","numeric");

	$var=checkVar("var","numeric");

	if ((!is_numeric($mois)) || ($mois<1) || ($mois>12))
	{
		$ret["result"]="NOK";
		$ret["msg"]="mois is not a valid month";
		echo json_encode($ret);
	  	exit;
	}
	if ((!is_numeric($dte)) || ($dte==0))
	{
		$ret["result"]="NOK";
		$ret["msg"]="dte is not a year";
		echo json_encode($ret);
	  	exit;
	}
	if ($ress==0)
	{
		$ret["result"]="NOK";
		$ret["msg"]="Invalid ressource";
		echo json_encode($ret);
	  	exit;
	}

	
// ---- Update la valeur
	$sql->show=false;

	$q="SELECT id FROM ".$MyOpt["tbl"]."_prevision WHERE annee='".$dte."' AND mois='".$mois."' AND avion='".$ress."'";
	$res=$sql->QueryRow($q);

	if ($res["id"]>0)
	{
		$r=$sql->Edit("prevision",$MyOpt["tbl"]."_prevision",$res["id"],array("heures"=>$var));
	}
	else
	{
		$r=$sql->Edit("prevision",$MyOpt["tbl"]."_prevision",$res["id"],array("annee"=>$dte,"mois"=>$mois,"avion"=>$ress,"heures"=>$var));
	}

	if ($r=="NOK")
	{
		$ret["result"]="NOK";
	}
	else
	{
		$ret["result"]="OK";
	}
	
	echo json_encode($ret);

?>