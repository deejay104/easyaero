<?php
	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Permissions d'accès
	if (!GetDroit("AccesCompte")) { FatalError("Accès non autorisé (AccesCompte)"); }

// ---- Récupère les variables
	$id=checkVar("id","numeric");
	$c=checkVar("columns","array");
	$s=checkVar("order","array");
	$draw=checkVar("draw","numeric");
	$ts=checkVar("start","numeric");
	$tl=checkVar("length","numeric");
	$theme=checkVar("theme","varchar",20);
	$search=checkVar("search","array");

	// error_log(print_r($_REQUEST,true));

	$col=array();
	$qs="";
	foreach($c as $i=>$d)
	{
		$col[$d["name"]]=$d["data"];
	}
	if ($search["value"]!="")
	{
		$qs="AND (";
		$qs.="date_valeur LIKE '%".$search["value"]."%' OR ";
		$qs.="mouvement LIKE '%".$search["value"]."%' OR ";
		$qs.="commentaire LIKE '%".$search["value"]."%' OR ";
		$qs.="montant LIKE '%".$search["value"]."%' OR ";
		$qs.="hash LIKE '%".$search["value"]."%'";
		$qs.=")";

	}
	// error_log(print_r($col,true));
	$order="date_valeur";
	$trie="desc";
	if ((isset($s[0]["column"])))
	{
		$order=$c[$s[0]["column"]]["data"];
	}
	if (isset($s[0]["dir"]))
	{
		$trie=($s[0]["dir"]=="asc") ? "asc" : "desc";
	}


// ---- Liste des mouvements
	$ret=array();

	// Calcul le nombre ligne totale
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid='".$id."' ".$qs;
	$res=$sql->QueryRow($query);
	$totligne=$res["nb"];

	// Calcul le solde du compte au début de l'affichage
	$query = "SELECT SUM(lignes.montant) AS solde FROM (SELECT montant FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid='".$id."' ".$qs." ORDER BY $order ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT $ts,$totligne) AS lignes";
	$res=$sql->QueryRow($query);
	$solde=$res["solde"];

	
	// Affiche les lignes
	$query = "SELECT id,mid,uid,date_valeur,date_creat,mouvement,commentaire,montant,hash,precedent,pointe FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid='".$id."' ".$qs." ORDER BY $order $trie, id DESC LIMIT $ts,$tl";
	$sql->Query($query);

// error_log($query);

	$ret["draw"]=$draw;
	$ret["recordsTotal"]=$totligne;
	$ret["recordsFiltered"]=$totligne;
	$ret["data"]=array();

	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

		$tabValeur[$i]["lid"]["val"]=$sql->data["id"];
		$tabValeur[$i]["date_valeur"]["val"]=CompleteTxt($i,"20","0");
		$tabValeur[$i]["date_valeur"]["aff"]=date("d/m/Y",strtotime($sql->data["date_valeur"]));
		$tabValeur[$i]["mid"]["val"]=$sql->data["mid"];
		$tabValeur[$i]["precedent"]["val"]=$sql->data["precedent"];
		$tabValeur[$i]["date_creat"]["val"]=$sql->data["date_creat"];
		$tabValeur[$i]["mouvement"]["val"]=$sql->data["mouvement"];
		$tabValeur[$i]["commentaire"]["val"]=$sql->data["commentaire"];
		$tabValeur[$i]["commentaire"]["aff"]=$sql->data["commentaire"];
		$tabValeur[$i]["line"]["val"]="<line>";
		$tabValeur[$i]["montant"]["val"]=$sql->data["montant"];
		$tabValeur[$i]["montant"]["align"]="right";
		$tabValeur[$i]["montant"]["aff"]=AffMontant($sql->data["montant"])."&nbsp;&nbsp;";

		if (($order=="date_valeur") && ($trie=="desc"))
		{
			$afftotal=round($solde,2);
			$tabValeur[$i]["solde"]["val"]=(($afftotal==0) ? "0" : $afftotal);
			$tabValeur[$i]["solde"]["aff"]=(($afftotal==0) ? "0,00 ".$MyOpt["devise"] : AffMontant($afftotal))."&nbsp;&nbsp;";
			$solde=$solde-$sql->data["montant"];
		}
		else
		{
			$tabValeur[$i]["solde"]["val"]="NA";
			$tabValeur[$i]["solde"]["aff"]="NA&nbsp;&nbsp;";
		}
		
		$tabValeur[$i]["releve"]["val"]=$sql->data["pointe"];
		$tabValeur[$i]["releve"]["aff"]=$sql->data["pointe"];
		if (GetDroit("AfficheSignatureCompte"))
		{
			$tabValeur[$i]["hash"]["val"]=$sql->data["hash"];
			$tabValeur[$i]["hash"]["aff"]=$sql->data["hash"];
		}
		else
		{
			$tabValeur[$i]["hash"]["val"]="";
			$tabValeur[$i]["hash"]["aff"]="";
		}
	}

	if (GetDroit("AfficheDetailMouvement"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$tabValeur[$i]["date_valeur"]["aff"]="<a title='Créé le ".sql2date($tabValeur[$i]["date_creat"]["val"])."'>".$tabValeur[$i]["date_valeur"]["aff"]."</a>";
			$tabValeur[$i]["mouvement"]["aff"]="<a title='".AfficheDetailMouvement($id,$d["mid"]["val"])."'>".$tabValeur[$i]["mouvement"]["val"]."</a>";
		}
	}

	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$confirm=AfficheSignatureCompte($d["lid"]["val"]);
			$aff="";
			if ($confirm["res"]=="ok")
			{
				$aff="<a title='Signature de la transaction confirmée'><img src='static/images/icn16_signed.png' /></a>";
				if ($fonc!="showhash")
				{
					$tabValeur[$i]["hash"]["val"]="";
					$tabValeur[$i]["hash"]["aff"]="";
				}
			}
			else if ($confirm["res"]=="nok")
			{
				$aff="<a title='Cette transaction ou la précédente sont altérées.\nID courant:".$d["lid"]["val"]." ID précédent:".$d["precedent"]["val"]."'><img src='static/images/icn16_warning.png' /></a>";
				$tabValeur[$i]["hash"]["aff"]="<s>".$tabValeur[$i]["hash"]["val"]."</s><br />".$confirm["hash"];
			}
			else if ($confirm["res"]=="mvt")
			{
				$aff="<a title=\"Le mouvement n'a pas un total nul. Une des transaction a pu être altérée.\nMouvement ID:".$d["mid"]["val"]." Total:".$confirm["total"]."\"><img src='static/images/icn16_warning.png' /></a>";
			}
			
			$tabValeur[$i]["signature"]["val"]=$confirm;
			$tabValeur[$i]["signature"]["aff"]=$aff;
		}
	}

	foreach($tabValeur as $i=>$d)
	{
		$ret["data"][$i]=array();
		$ret["data"][$i]["id"]=$i;
		$ret["data"][$i]["date_valeur"]=$d["date_valeur"]["aff"];
		$ret["data"][$i]["mouvement"]=$d["mouvement"]["aff"];
		$ret["data"][$i]["commentaire"]=$d["commentaire"]["aff"];
		$ret["data"][$i]["montant"]=$d["montant"]["aff"];
		$ret["data"][$i]["solde"]=($search["value"]=="") ? $d["solde"]["aff"] : "NA";
		$ret["data"][$i]["releve"]=$d["releve"]["aff"];
		$ret["data"][$i]["hash"]=$d["hash"]["aff"];
		if (GetDroit("AfficheSignatureCompte"))
		{
					$ret["data"][$i]["signature"]=$d["signature"]["aff"];

		}
	}

	echo json_encode($ret);
	
?>