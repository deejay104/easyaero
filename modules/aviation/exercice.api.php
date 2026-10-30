<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }
  
	if (!GetDroit("ModifSynthese"))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ----
	require_once ($appfolder."/class/synthese.inc.php");

	$id=checkVar("id","numeric");
	$idformation=checkVar("idformation","numeric");
	$ref=checkVar("ref","varchar");
	$term=checkVar("term","varchar");
	$fonc=checkVar("fonc","varchar",10);
	$ret=array();

	if ($fonc=="delete")
	{
		$exo=new exercice_class($id,$sql);
		$exo->Delete();
		$ret["status"]="deleted";
		error_log("exo $id deleted");
	}
	else
	{
		if ($idformation==0)
	  	  { header("HTTP/1.0 422 missing parameters"); exit; }
		if (($ref=="") && ($term==""))
	  	  { header("HTTP/1.0 422 missing parameters"); exit; }



		$type="";
		if ($fonc=="peda")
		{
			$type="peda";
		}
		else if ($fonc=="exercice")
		{
			$type="exercice";
		}
		else if ($fonc=="panne")
		{
			$type="panne";
		}
		
		$query ="SELECT prog.id,prog.refffa, exo.description,exo.module,progression, exo.type FROM ".$MyOpt["tbl"]."_exercice_conf AS exo ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_exercice_prog AS prog ON exo.id=prog.idexercice ";

		if ($term!="")
		{
			$query.="WHERE ".(($idformation>0) ? "exo.idformation='".$idformation."' AND" : "")." (exo.description LIKE '%".$term."%' OR prog.refffa LIKE '%".$term."%') AND exo.actif='oui' ".(($type!="") ? "AND type='".$type."'" : "");
		}
		else
		{
			$query.="WHERE ".(($idformation>0) ? "exo.idformation='".$idformation."' AND" : "")." prog.refffa='".$ref."' AND exo.actif='oui' ".(($type!="") ? "AND type='".$type."'" : "");
		}
		$query.="ORDER BY prog.progression DESC, prog.id";

		$sql->Query($query);

		$ret["data"]=array();

		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$r=array();
			$r["value"]=$sql->data["id"];
			$r["label"]=$sql->data["description"]." (".$sql->data["type"].(($sql->data["refffa"]!="") ? "/" : "").$sql->data["progression"].")";
			$r["id"]=$sql->data["id"];
			$r["description"]=htmlentities($sql->data["description"],ENT_HTML5,"UTF-8");
			$r["type"]=htmlentities($sql->data["type"],ENT_HTML5,"UTF-8");
			$r["module"]=htmlentities($sql->data["module"],ENT_HTML5,"UTF-8");
			$r["progression"]=$sql->data["progression"];

			$ret["data"][]=$r;
		}

		if ($ref!="")
		{
			$query="SELECT * FROM ".$MyOpt["tbl"]."_reference AS ref ";
			$query.="WHERE refffa='".$ref."' AND idformation='".$idformation."' AND actif='oui' ";

			$res=$sql->QueryRow($query);
			$ret["refffa"]=$res["refffa"];
			$ret["theme"]=$res["theme"];

		}

	}

	// Send JSON to the client.
	echo json_encode($ret);

?>