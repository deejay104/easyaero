<?php

    $t=array("idvol","uid_pilote","uid_instructeur","uid_avion","status","type","dte_vol","module","nb_att","nb_rmg","tps_theo","sid_instructeur","sdte_instructeur","sip_instructeur","nonce_instructeur");

	$query="SELECT * FROM ".$MyOpt["tbl"]."_synthese WHERE skey_instructeur<>'' ORDER BY id";
	$res=$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

        $nonce=bin2hex(random_bytes(16));
        $sql->data["nonce_instructeur"]=$nonce;
        $sql->data["sip_instructeur"]="null";

        $payload="id=".$sql->data["id"];
		foreach($t as $k)
		{
			$payload.="|".$k."=".((isset($sql->data[$k])) ? $sql->data[$k] : 'null');
		}

		$hmachash=hash_hmac('sha256', $payload, $hmacKey);
		$hash=hash('sha256', $hmachash);

        $query="UPDATE `".$MyOpt["tbl"]."_synthese` SET nonce_instructeur='".$nonce."', sip_instructeur='null', skey_instructeur='".$hash."' WHERE id='".$sql->data["id"]."'";
		$sql->Update($query);
    }

    $t=array("idvol","uid_pilote","uid_instructeur","uid_avion","status","type","dte_vol","module","nb_att","nb_rmg","tps_theo","sid_pilote","sdte_pilote","sip_pilote","nonce_pilote");

    $query="SELECT * FROM ".$MyOpt["tbl"]."_synthese WHERE skey_pilote<>'' ORDER BY id";
	$res=$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

        $nonce=bin2hex(random_bytes(16));
        $sql->data["nonce_pilote"]=$nonce;
        $sql->data["sip_pilote"]="null";

        $payload="id=".$sql->data["id"];
		foreach($t as $k)
		{
			$payload.="|".$k."=".((isset($sql->data[$k])) ? $sql->data[$k] : 'null');
		}

		$hmachash=hash_hmac('sha256', $payload, $hmacKey);
		$hash=hash('sha256', $hmachash);

        $query="UPDATE `".$MyOpt["tbl"]."_synthese` SET nonce_pilote='".$nonce."', sip_pilote='null', skey_pilote='".$hash."' WHERE id='".$sql->data["id"]."'";
		$sql->Update($query);
    }

?>