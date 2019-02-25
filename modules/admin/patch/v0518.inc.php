<?

	$q=array();
	$q[]="UPDATE `".$MyOpt["tbl"]."_bapteme` SET status=6 WHERE status=4";
	$q[]="UPDATE `".$MyOpt["tbl"]."_bapteme` SET status=5 WHERE status=3";
	$q[]="UPDATE `".$MyOpt["tbl"]."_bapteme` SET status=4 WHERE status=2";
	$q[]="UPDATE `".$MyOpt["tbl"]."_bapteme` SET status=3 WHERE status=1";

	$q[]="UPDATE `".$MyOpt["tbl"]."_calendrier` SET taxeok='oui' WHERE dte_deb<='".date("Y-m-d",time()-3600*24*31)."'";

	
  	foreach($q as $i=>$query)
	{
		$sql->Update($query);
	}


?>