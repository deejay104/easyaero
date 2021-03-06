<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }
  
	require_once ($appfolder."/class/synthese.inc.php");

	$id=checkVar("id","numeric");
	$ref=checkVar("ref","varchar");
	$term=checkVar("term","varchar");
	$fonc=checkVar("fonc","varchar",10);
	$ret=array();

	if ($fonc=="theme")
	{
		$query="SELECT * FROM ".$MyOpt["tbl"]."_reference AS ref ";
		$query.="WHERE refffa='".$ref."' AND actif='oui' ";
		$res=$sql->QueryRow($query);
		$ret["refffa"]=$res["refffa"];
		$ret["theme"]=$res["theme"];
	}
	if ($fonc=="delete")
	{
		$exo=new exercice_class($id,$sql);
		$exo->Delete();
		$ret["status"]="deleted";
		error_log("exo $id deleted");
	}
	else
	{
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
		
		if ($term!="")
		{
			$query="SELECT id,description,'A' FROM ".$MyOpt["tbl"]."_exercice_conf AS exo WHERE (module LIKE '%".$term."%' OR refffa LIKE '%".$term."%' OR description LIKE '%".$term."%') AND actif='oui'";

			$query ="SELECT prog.id,prog.refffa, exo.description,exo.module,progression, exo.type FROM ".$MyOpt["tbl"]."_exercice_conf AS exo LEFT JOIN ".$MyOpt["tbl"]."_exercice_prog AS prog ON exo.id=prog.idexercice ";
			$query.="WHERE (exo.description LIKE '%".$term."%' OR prog.refffa LIKE '%".$term."%') AND exo.actif='oui' ".(($type!="") ? "AND type='".$type."'" : "");
			// $query.="GROUP BY exo.id";
		}
		else
		{
			$query="SELECT prog.id,prog.refffa,exo.description,exo.module,prog.progression, exo.type FROM ".$MyOpt["tbl"]."_exercice_conf AS exo ";
			$query.="LEFT JOIN ".$MyOpt["tbl"]."_exercice_prog AS prog ON exo.id=prog.idexercice ";
			$query.="WHERE prog.refffa='".$ref."' AND exo.actif='oui' ".(($type!="") ? "AND type='".$type."'" : "");
			$query.="ORDER BY prog.progression DESC, prog.id";
		}
		$sql->Query($query);

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
			array_push($ret,$r);
		}
	}
	// Send JSON to the client.
	echo json_encode($ret);

?>