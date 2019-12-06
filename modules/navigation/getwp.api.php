<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Variable
	$id=checkVar("id","numeric");
	$nom=checkVar("nom","varchar",20);
	$type=checkVar("type","varchar",20);
	$taxe=checkVar("taxe","varchar",20);
  
// ---- 
	$result=array();

	$query ="SELECT * FROM ".$MyOpt["tbl"]."_navpoints ";

	if ($id>0)
	{
		$query.="WHERE id=".$id;
	}
	else
	{
		$query.="WHERE (nom LIKE '%".$_REQUEST["term"]."%' OR description LIKE '%".$_REQUEST["term"]."%') ";
		$query.=(($type!="") ? " AND icone='".addslashes($type)."'" : "")." ";
		$query.=(($taxe!="") ? " AND taxe>0" : "")." ";
		$query.="LIMIT 0,10";
	}

	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);

		$r=array();
		$r["value"]=strtoupper($sql->data["nom"]);
		$r["label"]=$sql->data["nom"]." : ".$sql->data["description"];
		$r["id"]=strtoupper($sql->data["id"]);
		$r["nom"]=strtoupper($sql->data["nom"]);
		$r["description"]=$sql->data["description"];
		$r["taxe"]=$sql->data["taxe"];
		$r["lat"]=$sql->data["lat"];
		$r["lon"]=$sql->data["lon"];

		array_push($result,$r);
	}
	
	echo json_encode($result);
?>