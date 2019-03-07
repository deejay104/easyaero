<?
	$q=array();
	
	$query="SELECT id FROM ".$MyOpt["tbl"]."_cron WHERE module='suivi', script='decouvert'";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification de découvert', module='suivi', script='decouvert', schedule='10080', actif='non'";
	}

	$query="SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE nom='club'";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET nom='club', prenom='', initiales='', password='', notification='non', droits='', actif='oui', virtuel='oui', uid_maj=1, dte_maj=NOW()";
	}
	$query="SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE nom='banque'";
	$res=$sql->QueryRow($query);
	if ($res["id"]==0)
	{		
		$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET nom='banque', prenom='', initiales='', password='', notification='non', droits='', actif='oui', virtuel='oui', uid_maj=1, dte_maj=NOW()";
	}
	
  	foreach($q as $i=>$query)
	{
		$sql->Update($query);
	}

?>