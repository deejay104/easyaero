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

	if (($fonc=="get") && ($id>0))
	{
		$exo=new exercice_conf_class($id,$sql);

		$result["id"]=$id;
		$result["description"]=utf8_encode($exo->val("description"));
		$result["module"]=utf8_encode($exo->val("module"));
		$result["refffa"]=utf8_encode($exo->val("refffa"));
		$result["refenac"]=utf8_encode($exo->val("refenac"));
		$result["competence"]=utf8_encode($exo->val("competence"));
		
		$lstp=ListProgression($sql,$id);
		$i=1;
		foreach($lstp as $ii=>$dd)
		{
			$prog=new exercice_prog_class($dd["id"],$sql);
			$result["progression"][$i]["id"]=$dd["id"];
			$result["progression"][$i]["ref"]=$prog->val("refffa");
			$result["progression"][$i]["val"]=$prog->val("progression");
			$i=$i+1;
		}
		
		for($ii=$i;$ii<=4;$ii++)
		{
			$result["progression"][$i]["id"]=0;
			$result["progression"][$i]["ref"]="";
			$result["progression"][$i]["val"]="A";
			$i=$i+1;
		}
	}
	else if ($fonc=="post")
	{
		if (GetDroit("ModifExercice"))
		{
			$id=checkVar("id","numeric");
			$exo=new exercice_conf_class($id,$sql);
			$desc=checkVar("description","varchar",200);
			$exo->Valid("description",utf8_decode($desc));
			
			$exo->Save();
			
			$result["result"]="OK";
		}
	}
		
	
	echo json_encode($result);
?>