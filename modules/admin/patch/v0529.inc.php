<?php

	$q=array();
	$query="SELECT id FROM ".$MyOpt["tbl"]."_cron WHERE module='ressources' AND script='maintenance'";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification de maintenance', module='ressources', script='maintenance', schedule='1440', actif='non'";
	}
	
  	foreach($q as $i=>$query)
	{
		$sql->Update($query);
	}

?>