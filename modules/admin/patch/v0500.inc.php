<?

	$q=array();
	$q[]="UPDATE `".$MyOpt["tbl"]."_cron` SET module='suivi' WHERE scripts='decouvert'";

  	foreach($q as $i=>$query)
	{
		$sql->Update(utf8_decode($query));
	}

?>