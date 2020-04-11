<?
	$q=array();
	$query="SELECT id FROM ".$MyOpt["tbl"]."_formation ORDER BY id";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q="INSERT INTO `".$MyOpt["tbl"]."_formation` SET description='Formation PPL'";
		$idf=$sql->Insert($q);
	}
	else
	{
		$idf=$res["id"];
	}


	$query="SELECT uid_pilote FROM  ".$MyOpt["tbl"]."_synthese GROUP BY uid_pilote";
	$sql->Query($query);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$i]=$sql->data["uid_pilote"];
	}

	foreach($lst as $i=>$uid)
	{

		$query="SELECT id FROM ".$MyOpt["tbl"]."_livret WHERE iduser='".$uid."' ORDER BY id";
		$res=$sql->QueryRow($query);
		if ($res["id"]==0)
		{		
			$q="INSERT INTO `".$MyOpt["tbl"]."_livret` SET idformation='".$idf."', iduser='".$uid."'";
			$idl=$sql->Insert($q);
		}
		else
		{
			$idl=$res["id"];
		}
		$query="UPDATE `".$MyOpt["tbl"]."_synthese` SET idlivret='".$idl."' WHERE idlivret=0 AND uid_pilote='".$uid."'";
		$sql->Update($query);
	}

	$query="UPDATE `".$MyOpt["tbl"]."_exercice_conf` SET idformation='".$idf."' WHERE idformation=0";
	$sql->Update($query);

	$query="UPDATE `".$MyOpt["tbl"]."_reference` SET idformation='".$idf."' WHERE idformation=0";
	$sql->Update($query);
?>