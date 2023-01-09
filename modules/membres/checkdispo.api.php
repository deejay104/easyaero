<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  
// ---- Header de la page


// ---- Variables d'affichage
	$l = 100;
	$h = 16;

// ---- Récupère les paramètres
	$mid=checkVar("mid","numeric");
	$deb=checkVar("deb","numeric");
	$fin=checkVar("fin","numeric");
	
//	$deb=strtotime("2013-08-07 17:00");
//	$fin=strtotime("2013-08-07 18:00");

// ---- Charge les informations sur le chargement
	require_once ($appfolder."/class/user.inc.php");

	$result=array("status"=>200);

	if (($mid>0) && ($deb>0) && ($fin>0))
	{
		$dte_deb=date("Y-m-d H:i:s",$deb);
		$dte_fin=date("Y-m-d H:i:s",$fin);
		$usr_inst=new user_class($mid,$sql,false,true);
		if ($usr_inst->CheckDisponibilite($dte_deb,$dte_fin))
		{	
			$result["dispo"]="ok";
			$result["text"]="Disponible";
			$result["icon"]="mdi-alarm-check";
			$result["dte_deb"]=$dte_deb;
			$result["dte_fin"]=$dte_fin;
		}
		else
		{
			$result["dispo"]="nok";
			$result["text"]="Occupé";
			$result["icon"]="mdi-close-box-outline";
		}
	}
	else
	{
		if ($mid==0)
		{
			$result["dispo"]="";
			$result["text"]="no id";
			$result["icon"]="";
		}
		else
		{
			$result["dispo"]="";
			$result["text"]="bad parameters";
			$result["icon"]="mdi-alert";
		}
	}


	echo json_encode($result);


?>
