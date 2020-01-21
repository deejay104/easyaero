<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  
	require_once ($appfolder."/class/bapteme.inc.php");

	$res=array();
	$msg_erreur="";
	$msg_confirmation="";

	$btm = new bapteme_class(0,$sql);
	$btm->Create();

	$data=json_decode(file_get_contents('php://input'), true);

	if (count($data["data"])>0)
	{
		foreach($data["data"] as $k=>$v)
		{
			$msg_erreur.=$btm->Valid($k,$v);
		}
	}
	
	$btm->Save();

	// Send JSON to the client.
	$res["id"]=$btm->id;
	$res["num"]=$btm->data["num"];
	echo json_encode($res);
  
?>