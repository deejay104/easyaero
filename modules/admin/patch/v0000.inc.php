<?
	$q=array();
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification de decouvert', module='suivi', script='decouvert', schedule='10080', actif='non'";

  	foreach($q as $i=>$query)
	{
		$sql->Update(utf8_decode($query));
	}

?>