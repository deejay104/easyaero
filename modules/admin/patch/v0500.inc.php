<?

	$q=array();
	$q[]="DELETE FROM `".$MyOpt["tbl"]."_cron` WHERE module='comptabilite' AND scripts='decouvert'";

	$query="SELECT id FROM ".$MyOpt["tbl"]."_cron WHERE module='suivi' AND script='decouvert'";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification de decouvert', module='suivi', script='decouvert', schedule='10080', actif='non'";
	}
	
  	foreach($q as $i=>$query)
	{
		$sql->Update($query);
	}

?>