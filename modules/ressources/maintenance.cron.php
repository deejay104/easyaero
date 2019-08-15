<?php
	if ($gl_mode!="batch")
	  { FatalError("Acces refusé","Ne peut etre executé qu'en arriere plan"); }

  	require_once ($appfolder."/class/user.inc.php");
  	require_once ($appfolder."/class/ressources.inc.php");

// ---- Variables
	$mailfrom=array();
	$mailfrom["name"]=$MyOpt["site_title"];
	$mailfrom["mail"]=$MyOpt["from_email"];
	if ($mailfrom=="")
	{
		return;
	}


	
// ---- Liste des membres à notifier
	$tabUser=array();
	$lst=ListActiveUsers($sql,"",array("NotifMaintenance"),"non");
	foreach($lst as $i=>$id)
	{
		$usr = new user_class($id,$sql,false,true);
		if ($usr->data["mail"]!="")
		{
			$tabUser[]=$usr->data["mail"];
		}
	}


// ---- Teste le potentiel restant de chaque avion
	$lstress=ListeRessources($sql,array("oui"));
	$gl_res="OK";

	foreach($lstress as $i=>$id)
	{
		$ress = new ress_class($id,$sql,false);
		$tpspot=$ress->Potentiel();
myPrint($ress->Aff("immatriculation")." ".$ress->data["actif"]);	
		if (time()-strtotime($ress->val("notifmaint"))>7*24*3600)
		{
			if (floor($tpspot/60)<$ress->data["alertpotentiel"])
			{
				$dtemaint=$ress->EstimeMaintenance();
				
				myPrint($ress->Aff("immatriculation")." - Potentiel: ".AffTemps($tpspot));
				$tabvar=array();
				$tabvar["immatriculation"]="<a href='".$ress->url()."'>".$ress->val("immatriculation")."</a>";
				$tabvar["potentiel"]=AffTemps($tpspot);
				$tabvar["dte_maint"]=$dtemaint;
				
				foreach($tabUser as $ii=>$usr)
				{
					myPrint("Notification:".$usr);
					SendMailFromFile($mailfrom,$usr,array(),"",$tabvar,"maintenance");
				}
				
				$ress->Valid("notifmaint",now());
				$ress->Save();
			}
		}
	}
	
	myPrint($gl_res);

?>