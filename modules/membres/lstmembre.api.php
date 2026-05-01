<?php

// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	require_once ("class/document.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- List all papers from a folder

	$id=checkVar("id","numeric");
	$crit=checkVar("crit","varchar",50);
	$limit=checkVar("limit","numeric");
	$aff=checkVar("aff","varchar",20);
	$fonc=checkVar("fonc","varchar",20);

	
//	$lstusr=ListActiveUsers($sql,"std","","non");
    $lstusr=ListActiveUsers($sql,"std","",($aff=="virtuel") ? "oui" : "non");

	$result=array();
	$result["status"]=200;
	$result["crit"]=$crit;
	$result["fonc"]=$fonc;
	$result["data"]=array();

	$k=0;
	foreach($lstusr as $i=>$d)
	{
		if (($limit>0) && ($k>=$limit))
		{
			break;
		}

		$usr = new user_class($d,$sql,false);

		if ($crit!="")
		{
			$okcrit=false;
			if (preg_match("/".$crit."/i",$usr->fullname))
			{
				$okcrit=true;
			}
		}
		else
		{
			$okcrit=true;
		}

	
		if ($okcrit)
		{
			$result["data"][$k]["id"]=$usr->id;
			$result["data"][$k]["name"]=$usr->fullname;
			$result["data"][$k]["phone"]=$usr->AffTel();
			$result["data"][$k]["mail"]=$usr->val("mail");
			$result["data"][$k]["virtuel"]=$usr->val("virtuel");

			$lstdoc=ListDocument($sql,$d,"avatar");
			if (count($lstdoc)>0)
			{
				$doc=new document_core($lstdoc[0],$sql);
				if ($fonc=="trombi")
				{
					$result["data"][$k]["avatar"]=$doc->GenerePath(200,240);
				}
				else
				{
					$result["data"][$k]["avatar"]=$doc->GenerePath(64,64);
				}
			}
			else
			{
				$result["data"][$k]["avatar"]="";
			}

			$k++;
		}
	}
	
	echo json_encode($result);	
?>