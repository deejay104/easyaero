<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	set_time_limit(0);
	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

// ----
	
	if (GetDroit("SYS"))
	{
		$q="DELETE FROM ".$MyOpt["tbl"]."_compte WHERE uid=0";
		$sql->Delete($q);
				
		// $query = "SELECT id FROM ".$MyOpt["tbl"]."_compte WHERE uid=0";
		// $res=$sql->QueryRow($query);
		// if ($res["id"]>0)
		// {
			// echo "Signature déjà effectuée";
			// return;
		// }

		$id=checkVar("id","numeric");
		
		// Récupère la liste des utilisateurs
		$query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs ".(($id>0) ? "WHERE id=".$id." " : "")." ORDER BY id";
		// $query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE id>=47 ORDER BY id";
		$sql->Query($query);
		$tabUser=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$tabUser[$i]=$sql->data["id"];
		}
		
		$sql_upd = new mysql_core($mysqluser, $mysqlpassword, $hostname, $db, $port);

		foreach($tabUser as $ii=>$id)
		{
			echo "Signature des transactions pour utilisateur id:".$id."\n";
			$q="UPDATE ".$MyOpt["tbl"]."_compte SET precedent='0', signature='',hash='',clepublic='' WHERE uid='".$id."'";
			$sql->Update($q);

			// Créé la première entrée
			// $key=openssl_pkey_new(array("private_key_bits"=>1024,"private_key_type"=>OPENSSL_KEYTYPE_RSA));
			// openssl_pkey_export($key,$priv_key);
			// $details=openssl_pkey_get_details($key);
			// $public_key=$details["key"];
			// $hash=md5($public_key);

			$prev_hash="";
			$prev_id=0;

			// $q="INSERT ".$MyOpt["tbl"]."_compte SET mid='0', uid='0', clepublic='".$public_key."',hash='".$hash."', uid_creat='".$gl_uid."',date_creat='".now()."'";
			// $prev_id=$sql->Insert($q);

			$tabCpt=array();
			$query = "SELECT id,uid,mid,montant,date_valeur,uid_creat,date_creat FROM ".$MyOpt["tbl"]."_compte WHERE uid='".$id."' ORDER BY mid";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$tabCpt[$i]=$sql->data;
			}
			
			foreach($tabCpt as $i=>$d)
			{
				$key=openssl_pkey_new(array("private_key_bits"=>1024,"private_key_type"=>OPENSSL_KEYTYPE_RSA));
				openssl_pkey_export($key,$priv_key);
				$details=openssl_pkey_get_details($key);
				$public_key=$details["key"];
				
				echo $d["id"]." ";
				
				$data =md5($prev_hash."-".$public_key);
				$data.="-".$prev_id;
				$data.="-".$d["id"];
				$data.="-".$d["uid"];
				$data.="-".$d["mid"];
				$data.="-".$d["montant"];
				$data.="-".$d["date_valeur"];
				$data.="-".$d["uid_creat"];
				$data.="-".$d["date_creat"];

				$hash=md5($data);

				echo $hash."\n";

				$sign="";
				openssl_sign($data,$sign,$priv_key,OPENSSL_ALGO_SHA256);

				$q="UPDATE ".$MyOpt["tbl"]."_compte SET clepublic='".$public_key."',hash='".$hash."', signature='".base64_encode($sign)."', precedent='".$prev_id."' WHERE id=".$d["id"];
				$sql->Update($q);

				openssl_pkey_free ($key);
				unset($key);
				
				$prev_id=$d["id"];
				$prev_hash=$hash;
			}
		}
	}
?>