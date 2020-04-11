<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Variable
	$id=checkVar("id","numeric");
	$fonc=checkVar("fonc","varchar");

// ---- 
	$result=array();

	if (($fonc=="get") && (is_numeric($id)))
	{
		$livret=new livret_class($id,$sql);
		$formation=new formation_class($livret->val("idformation"),$sql);

		$result["id"]=$id;
		$result["idformation"]=$livret->val("idformation");
		$result["showformation"]=$formation->val("description");
		$result["iduser"]=$livret->val("iduser");
		$result["dte_deb"]=$livret->val("dte_deb");
		$result["dte_fin"]=$livret->val("dte_fin");
		$result["tpsdc"]=$livret->val("tpsdc");
		$result["tpssolo"]=$livret->val("tpssolo");

	}
	else if ($fonc=="post")
	{
		if (GetDroit("ModifLivret"))
		{
			$id=checkVar("id","numeric");
			$livret=new livret_class($id,$sql);
			if ($id==0)
			{
				$livret->Valid("idformation",checkVar("idformation","numeric"));
				$livret->Valid("iduser",checkVar("iduser","numeric"));
			}
			$livret->Valid("dte_deb",checkVar("dte_deb","date"));
			$livret->Valid("dte_fin",checkVar("dte_fin","date"));
			$livret->Valid("tpsdc",checkVar("tpsdc","numeric"));
			$livret->Valid("tpssolo",checkVar("tpssolo","numeric"));
			$livret->Save();

			$result["id"]=$livret->id;
			if ($livret->id>0)
			{
				$result["result"]="OK";
			}
			else
			{
				$result["result"]="Error";
			}				
		}
	}
		
	echo json_encode($result);
?>