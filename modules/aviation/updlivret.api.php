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
		$result["iduser"]=$livret->val("iduser");
		$result["idinstructeur"]=$livret->val("idinstructeur");
		$result["status"]=$livret->val("status");
		$result["showformation"]=$formation->val("description");
		$result["dte_deb"]=$livret->val("dte_deb");
		$result["dte_fin"]=$livret->val("dte_fin");
		$result["dte_test_practice"]=$livret->val("dte_test_practice");
		$result["dte_test_theory"]=$livret->val("dte_test_theory");
		$result["phase"]=$livret->val("phase");
		$result["theory"]=$livret->val("theory");
		$result["tpsdc"]=$livret->val("tpsdc");
		$result["tpssolo"]=$livret->val("tpssolo");
		$result["cr"]=$livret->val("cr");
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
			$livret->Valid("idinstructeur",checkVar("idinstructeur","numeric"));
			$livret->Valid("status",checkVar("status","varchar"));
			$livret->Valid("dte_deb",checkVar("dte_deb","date"));
			$livret->Valid("dte_fin",checkVar("dte_fin","date"));
			$livret->Valid("dte_test_practice",checkVar("dte_test_practice","date"));
			$livret->Valid("dte_test_theory",checkVar("dte_test_theory","date"));
			$livret->Valid("phase",checkVar("phase","varchar"));
			$livret->Valid("theory",checkVar("theory","varchar"));
			$livret->Valid("tpsdc",checkVar("tpsdc","numeric"));
			$livret->Valid("tpssolo",checkVar("tpssolo","numeric"));
			$livret->Valid("cr",checkVar("cr","text"));
			$livret->Save();

			$result["id"]=$livret->id;
			if ($livret->id>0)
			{
				$result["status"]=200;
				$result["result"]="OK";
			}
			else
			{
				$result["status"]=500;
				$result["result"]="Error";
			}				
		}
	}	
	else if (($fonc=="cr") && ($id>0))
	{
		if (GetDroit("ModifLivret"))
		{
			$id=checkVar("id","numeric");
			$livret=new livret_class($id,$sql);
			$livret->Valid("cr",checkVar("cr","text"));
			$livret->Save();

			$result["id"]=$livret->id;
			if ($livret->id>0)
			{
				$result["status"]=200;
				$result["result"]="OK";
			}
			else
			{
				$result["status"]=500;
				$result["result"]="Error";
			}				
		}
	}
		
	echo json_encode($result);
?>