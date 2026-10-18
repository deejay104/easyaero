<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	{ header("HTTP/1.0 401 Unauthorized"); exit; }

	if (!GetDroit("AccesConfigExercices"))
	{ header("HTTP/1.0 401 Unauthorized"); exit; }

	require_once ($appfolder."/class/synthese.inc.php");

// ---- Variable
	$id=checkVar("id","numeric",0,-1);
	$fid=checkVar("fid","numeric");

// ---- 
	$result=array();

    if (($_SERVER['REQUEST_METHOD']=="GET") && ($id>=0))
    {
		$exo=new exercice_conf_class($id,$sql);
        $result=$exo->export();

		$result["id"]=$id;
		$result["idformation"]=$exo->val("idformation");
		$result["description"]=$exo->val("description");
		$result["type"]=$exo->val("type");
		$result["focus"]=$exo->val("focus");
		$result["module"]=$exo->val("module");
		$result["refffa"]=$exo->val("refffa");
		$result["refenac"]=$exo->val("refenac");
		$result["compcat"]=$exo->val("compcat");
		$result["competence"]=$exo->val("competence");
		
		$lstp=ListeProgression($sql,$id);
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
	else if ($_SERVER['REQUEST_METHOD']=="POST")
	{
		if (GetDroit("ModifExercice"))
		{
			$id=checkVar("id","numeric");
			$exo=new exercice_conf_class($id,$sql);
			$exo->Valid("idformation",checkVar("idformation","numeric"));
			$exo->Valid("description",checkVar("description","varchar",200));
			$exo->Valid("type",checkVar("type","varchar",20));
			$exo->Valid("focus",checkVar("focus","varchar",3));
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
// error_log("id:".$ii.",ref:".$ref."(".$i."_prog_ref):".$ref.",prog:".$p);
					}
					else if ($ii>0)
					{
						$prog->Delete();
					}
			}

			$result["result"]="OK";
			$result["status"]=200;
		}
	}
	else if ($_SERVER['REQUEST_METHOD']=="GET")
	{		
        $c=checkVar("columns","array");
        $s=checkVar("order","array");
        $draw=checkVar("draw","numeric");
        $ts=checkVar("start","numeric");
        $tl=checkVar("length","numeric");
        $search=checkVar("search","array");

		
		$col=array();
		$qs="";
		foreach($c as $i=>$d)
		{
			$col[$d["name"]]=$d["data"];
		}

		// error_log(print_r($col,true));
		$order="refffa";
		$trie="desc";
		if ((isset($s[0]["column"])))
		{
			$order=$c[$s[0]["column"]]["data"];
		}
		if (isset($s[0]["dir"]))
		{
			$trie=($s[0]["dir"]=="asc") ? "asc" : "desc";
		}
	
		$search=array("actif"=>"oui","idformation"=>$fid);
		$lst=ListeExercicesConf($sql,$search,array($order." ".$trie,"id"));
		$totligne=count($lst);
		$tl=$totligne;

        $result["draw"]=$draw;
        $result["recordsTotal"]=count($lst);
        $result["recordsFiltered"]=count($lst);
        $result["data"]=array();

		$c=0;
		foreach($lst as $i=>$d)
		{
			$exo=new exercice_conf_class($d["id"],$sql);

			$result["data"][$c]["id"]=$d["id"];
			$result["data"][$c]["description"]=$exo->val("description");
			$result["data"][$c]["description_click"]="<a onclick='loadExercice(".$d["id"].");'>".$exo->val("description")."</a>";
			$result["data"][$c]["type"]=$exo->aff("type");
			$result["data"][$c]["refffa"]=$exo->val("refffa");
			$result["data"][$c]["refenac"]=$exo->val("refenac");
			$result["data"][$c]["compcat"]=$exo->val("compcat");
			$result["data"][$c]["competence"]=$exo->val("competence");
			$result["data"][$c]["focus"]=$exo->aff("focus");
			$result["data"][$c]["action"]="";

			$result["data"][$c]["progression"]="";

			$lstp=ListeProgression($sql,$d["id"]);
			$tabValeur[$i]["progression"]["val"]="";
			$s="";
			foreach($lstp as $ii=>$dd)
			{
				$prog=new exercice_prog_class($dd["id"],$sql);
	
				$result["data"][$c]["progression"].=$s.$prog->val("refffa").": ".$prog->val("progression");
				$s=" / ";
			}
			$c++;
		}
		$result["status"]=200;

	}
	else if ($_SERVER['REQUEST_METHOD']=="DELETE")
	{
		$result["status"]=200;
		if (GetDroit("SupprimeExercice"))
		{
			$id=checkVar("id","numeric");
			if ($id>0)
			{
				$exo=new exercice_conf_class($id,$sql);
				$exo->Delete();
			}
		}
	}

	echo json_encode($result);
?>