<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  
	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/user.inc.php");

	$res=array();
	$msg_erreur="";
	$msg_confirmation="";

	$data=json_decode(file_get_contents('php://input'), true);
	if ($data=="")
	{
		$data=array( 
			"data"=>checkVar("data","array")
		);
	}

	$id=0;
	$num=0;
	if (isset($data["data"]["bid"]))
	{
		preg_match("/^#?([0-9]{6})$/",$data["data"]["bid"],$t);
		$num=(isset($t[1])) ? $t[1] : "";
	}
	if (isset($data["data"]["id"]) && ($data["data"]["id"]>0))
	{
		$id=$data["data"]["id"];
	}

	$res["rid"]=$id;
	$res["bid"]=$num;

	$btm = new bapteme_class($id,$sql);

	if ($id>0)
	{
		if (isset($data["data"]["description"]))
		{
			$btm->data["description"].="\r\n\r\n** ".date("d/m/Y H:i")." - ".$myuser->fullname." :\r\n";
			$btm->data["description"].=$data["data"]["description"];
			$res["description"]="** ".date("d/m/Y H:i")." - ".$myuser->fullname." :<br />".nl2br($data["data"]["description"]);
		}
		$name="";
	}
	else if ($num>0)
	{
		$id=$btm->getid($num);
		$btm->load($id);

		if (isset($data["data"]["description"]))
		{
	//		$msg_erreur.=$btm->Valid("description", $btm->data["description"]."\\n".$data["data"]["description"]);
			$btm->data["description"].="\r\n\r\n** ".date("d/m/Y H:i")." - ".$data["data"]["nom"]." (".$data["data"]["mail"].") :\r\n";
			$btm->data["description"].=$data["data"]["description"];
		}
		$name="btm_update";
	}
	else
	{
		$btm->Create();
		if (count($data["data"])>0)
		{
			foreach($data["data"] as $k=>$v)
			{
				$msg_erreur.=$btm->Valid($k,$v);
			}
		}
		$name="btm_create";
	}
	
	$btm->Save();

	if ($name!="")
	{
		$tabvar=array(
			"id"=>$btm->id,
			"num"=>$btm->Aff("num"),
			"type"=>$btm->Aff("type"),
		);
		$tabUser=array();
		$lst=ListActiveUsers($sql,"",array("NotifBapteme"),"non");

		foreach($lst as $i=>$id)
		{
			$usr = new user_class($id,$sql,false,true);
			if ($usr->data["mail"]!="")
			{
				$res["mail"]=SendMailFromFile("noreply@easy-aero.fr",$usr->data["mail"],"","",$tabvar,$name);
			}
		}
	}

	// Send JSON to the client.
	$res["status"]=200;
	$res["id"]=$btm->id;
	$res["num"]=$btm->data["num"];
	$res["error"]=$msg_erreur;
	echo json_encode($res);
  
?>