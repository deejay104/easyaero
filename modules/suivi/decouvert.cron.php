<?
// ---------------------------------------------------------------------------------------------
//   Batch de notification 
// ---------------------------------------------------------------------------------------------
?>
<?
	if ($gl_mode!="batch")
	  { FatalError("Acces refusé","Ne peut etre executé qu'en arriere plan"); }

  	require_once ($appfolder."/class/user.inc.php");

// ---- Mail du trésorier

	$mailtre=array();
	$mailtre["name"]=$MyOpt["site_title"];
	$mailtre["mail"]=$MyOpt["from_email"];
	if ($mailtre=="")
	{
		return;
	}

	$tabTre=array();
	$lst=ListActiveUsers($sql,"",array("NotifDecouvert"),"non");
	foreach($lst as $i=>$id)
	{
		$usr = new user_class($id,$sql,false,true);
		if ($usr->data["mail"]!="")
		{
			$tabTre[]=$usr->data["mail"];
		}
	}

	myPrint("Notification Découvert : ".implode(",",$tabTre));

// ---- Liste les comptes actifs
	$lstusr=ListActiveUsers($sql,"std",array(),"non");
	$gl_res="OK";

	foreach($lstusr as $i=>$id)
	{
		$usr = new user_class($id,$sql,false,true);
		$ret=true;
		$solde=$usr->CalcSolde();
		if (($usr->isSoldeNegatif()) && ($usr->data["mail"]!="") && ($usr->data["virtuel"]=="non"))
		{
			myPrint($usr->fullname." - Solde: ".$solde);
			$tabvar=array();
			$tabvar["solde"]=AffMontant($solde);
			
			SendMailFromFile($mailtre,$usr->data["mail"],$tabTre,"[".$MyOpt["site_title"]."] Compte à découvert",$tabvar,"decouvert");
		}
		if (!$ret)
		{
			$gl_res="ERREUR";
		}
	}

	myPrint($gl_res);
?>