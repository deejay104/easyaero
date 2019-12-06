<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }
  
	require_once ($appfolder."/class/synthese.inc.php");

	$ref=checkVar("ref","varchar");
	$term=utf8_decode(checkVar("term","varchar"));

	if ($term!="")
	{
		$query="SELECT id,description,'A' FROM ".$MyOpt["tbl"]."_exercice_conf AS exo WHERE (module LIKE '%".$term."%' OR refffa LIKE '%".$term."%' OR description LIKE '%".$term."%') AND actif='oui'";

		$query ="SELECT prog.id,exo.description,exo.module,progression FROM ".$MyOpt["tbl"]."_exercice_conf AS exo LEFT JOIN ".$MyOpt["tbl"]."_exercice_prog AS prog ON exo.id=prog.idexercice ";
		$query.="WHERE (exo.description LIKE '%".$term."%' OR prog.refffa LIKE '%".$term."%') AND exo.actif='oui' ";
		// $query.="GROUP BY exo.id";
	}
	else
	{
		$query="SELECT prog.id,exo.description,exo.module,prog.progression FROM ".$MyOpt["tbl"]."_exercice_conf AS exo ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_exercice_prog AS prog ON exo.id=prog.idexercice ";
		$query.="WHERE prog.refffa='".$ref."' AND exo.actif='oui' ";
		$query.="ORDER BY prog.progression DESC, prog.id";
	}
	$sql->Query($query);
	$res=array();

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$r=array();
		$r["value"]=$sql->data["id"];
		$r["label"]=utf8_encode($sql->data["description"]." (".$sql->data["progression"].")");
		$r["id"]=$sql->data["id"];
		$r["description"]=utf8_encode(htmlentities($sql->data["description"],ENT_HTML5,"ISO-8859-1"));
		$r["module"]=utf8_encode(htmlentities($sql->data["module"],ENT_HTML5,"ISO-8859-1"));
		$r["progression"]=$sql->data["progression"];
		array_push($res,$r);
	}

	// Send JSON to the client.
	echo json_encode($res);

?>