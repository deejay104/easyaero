<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- 
	$result=array();

	$query ="SELECT nom,description,taxe FROM ".$MyOpt["tbl"]."_navpoints ";
	$query.="WHERE (nom LIKE '%".$_REQUEST["term"]."%' OR description LIKE '%".$_REQUEST["term"]."%') ";
	$query.=(($_REQUEST["type"]!="") ? " AND icone='".addslashes($_REQUEST["type"])."'" : "")." ";
	$query.=(($_REQUEST["taxe"]!="") ? " AND taxe>0" : "")." ";
	$query.="LIMIT 0,10";

	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
//		array_push($result,array(strtoupper($sql->data["nom"]),strtoupper($sql->data["nom"]." - ".$sql->data["description"])));
		$r=array();
		$r["value"]=strtoupper($sql->data["nom"]);
		$r["label"]=$sql->data["nom"]." : ".$sql->data["description"];
		$r["taxe"]=$sql->data["taxe"];
		
		array_push($result,$r);
	}
	
	echo json_encode($result);
?>