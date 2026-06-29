<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres

	$start=checkVar("start","varchar");
	$end=checkVar("end","varchar");

	$mid=checkVar('mid','numeric');

	if ($mid==0)
	{
		apiError(500,"Please provide a user id");
	}

// ---- Charge les données utilisateurs
  	require_once ($appfolder."/class/user.inc.php");

	$usr=new user_class($mid,$sql,false,true);

	if ($usr->data["disponibilite"]=="dispo")
	{
		$backcolor="rgb(0,255,0,0.2)";
		$eventcolor="#".$MyOpt["styleColor"]["msgboxBackgroundError"];
	}
	else
	{
		$backcolor="rgb(255,0,0,0.2)";
		$eventcolor="#".$MyOpt["styleColor"]["msgboxBackgroundOk"];
	}


// ---- Charge les disponibilités

	$input_arrays=array();


	$q="SELECT * FROM ".$MyOpt["tbl"]."_disponibilite WHERE actif='oui' AND dte_fin>='".date("Y-m-d",strtotime($start))." 00:00:00' AND dte_deb<='".date("Y-m-d",strtotime($end))." 00:00:00' AND uid='".$mid."'";
	$sql->Query($q);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);

		$input_arrays[$i]["id"]=$sql->data["id"];
		$input_arrays[$i]["eventId"]=$sql->data["id"];
		$input_arrays[$i]["title"]="";
		$input_arrays[$i]["start"]=date("c",strtotime($sql->data["dte_deb"]));
		$input_arrays[$i]["end"]=date("c",strtotime($sql->data["dte_fin"]));
		$input_arrays[$i]["color"]="$eventcolor";

	}
	
	$i=$sql->rows;
	$input_arrays[$i]=array(
        "daysOfWeek" => array(0,1, 2, 3, 4, 5, 6),
        "startTime" => '00:00',
        "endTime" => '24:00',
        "display" => 'background',
        "color" => $backcolor
	);




// ---- Send JSON to the client.
	echo json_encode($input_arrays);
	
?>
  