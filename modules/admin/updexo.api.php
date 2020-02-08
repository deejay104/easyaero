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
		$exo=new exercice_conf_class($id,$sql);

		$result["id"]=$id;
		$result["description"]=$exo->val("description");
		$result["type"]=$exo->val("type");
		$result["module"]=$exo->val("module");
		$result["refffa"]=$exo->val("refffa");
		$result["refenac"]=$exo->val("refenac");
		$result["compcat"]=$exo->val("compcat");
		$result["competence"]=$exo->val("competence");
		
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
			$exo->Valid("description",checkVar("description","varchar",200));
			$exo->Valid("type",checkVar("type","varchar",20));
			$exo->Valid("module",checkVar("module","varchar",40));
			$exo->Valid("refffa",checkVar("refffa","varchar",10));
			$exo->Valid("refenac",checkVar("refenac","numeric"));
			$exo->Valid("compcat",checkVar("compcat","varchar",100));
			$exo->Valid("competence",checkVar("competence","varchar",100));
			
			$exo->Save();

			for ($i=1; $i<=4; $i++)
			{
				$ii=checkVar($i."_prog_id","numeric");
					$prog=new exercice_prog_class($ii,$sql);
					
					$ref=checkVar($i."_prog_ref","varchar",10);
					$p=checkVar($i."_prog_val","varchar",1);
					if ($ref!="")
					{
						$prog->Valid("idexercice",$exo->id);
						$prog->Valid("refffa",$ref);
						$prog->Valid("progression",$p);
						$prog->Save();
error_log("id:".$ii.",ref:".$ref."(".$i."_prog_ref):".$ref.",prog:".$p);
					}
					else if ($ii>0)
					{
						$prog->Delete();
					}
			}

			$result["result"]="OK";
		}
	}
		
	
	echo json_encode($result);
?>