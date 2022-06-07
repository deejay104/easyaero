<?php
	require_once ($appfolder."/class/bapteme.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Permissions d'accès
	if (!GetDroit("AccesBaptemes")) { FatalError("Accès non autorisé (AccesBaptemes)"); }

// ---- Récupère les variables
	$id=checkVar("id","numeric");
	$c=checkVar("columns","array");
	$s=checkVar("order","array");
	$draw=checkVar("draw","numeric");
	$ts=checkVar("start","numeric");
	$tl=checkVar("length","numeric");
	$theme=checkVar("theme","varchar",20);
	$search=checkVar("search","array");

	$form_status=checkVar("status","numeric");

	// error_log(print_r($_REQUEST,true));

	$ret=array();

	if (isset($search["value"]))
	{
		$crit=$search["value"];
	}

	$order=array();
	$order["name"]=$c[$s[0]["column"]]["data"];
	$order["dir"]=$s[0]["dir"];

	$tot=TotalBaptemes($sql,array("oui"),$form_status,$crit);
	$ret["recordsTotal"]=$tot;
	$ret["recordsFiltered"]=$tot;

	$lstusr=ListeBaptemes($sql,array("oui"),$form_status,$crit,$order,$ts,$tl);

	$ret["draw"]=$draw;
	$ret["data"]=array();


	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$btm = new bapteme_class($id,$sql);
		$tabValeur[$i]["num"]["val"]=$btm->val("num");
		$tabValeur[$i]["num"]["aff"]=$btm->aff("num","html");
		$tabValeur[$i]["nom"]["val"]=$btm->val("nom");
		$tabValeur[$i]["nom"]["aff"]=$btm->aff("nom","html");
		$tabValeur[$i]["nb"]["val"]=$btm->val("nb");
		$tabValeur[$i]["nb"]["aff"]=$btm->aff("nb","html");
		$tabValeur[$i]["dte_creat"]["val"]=$btm->dte_creat;
		$tabValeur[$i]["dte_creat"]["aff"]=$btm->aff("dte_creat","html");
		$tabValeur[$i]["status"]["val"]=$btm->val("status");
		$tabValeur[$i]["status"]["aff"]=$btm->aff("status","html");

		$tabValeur[$i]["line1"]["val"]="<line>";

			if ($btm->val("dte_paye")!="0000-00-00")
		{
			$tabValeur[$i]["paye"]["val"]=$btm->val("dte_paye");
			$tabValeur[$i]["paye"]["aff"]=$btm->aff("dte_paye","html");
		}
		else
		{
			$tabValeur[$i]["paye"]["val"]=$btm->val("paye");
			$tabValeur[$i]["paye"]["aff"]=$btm->aff("paye","html");
		}

		$tabValeur[$i]["date"]["val"]=strtotime($btm->data["dte"]);
		$tabValeur[$i]["date"]["aff"]=$btm->aff("dte","html");

		$usr = new user_class($btm->data["id_pilote"],$sql,true);

		$tabValeur[$i]["pilote"]["val"]=$btm->val("id_pilote");
		$tabValeur[$i]["pilote"]["aff"]=($btm->data["id_pilote"]>0) ? $usr->Aff("fullname") : "-";

		if ($btm->data["id_avion"]>0)
		{
			$ress = new ress_class($btm->data["id_avion"],$sql);
			$tabValeur[$i]["resa"]["val"]=$ress->val("immatriculation");
			$tabValeur[$i]["resa"]["aff"]=$ress->aff("immatriculation");
		}
		else
		{
		  	$tabValeur[$i]["resa"]["val"]="-";
			$tabValeur[$i]["resa"]["aff"]="-";
		}
		$tabValeur[$i]["type"]["val"]=$btm->val("type");
		$tabValeur[$i]["type"]["aff"]=$btm->aff("type","html");
		$tabValeur[$i]["bonkdo"]["val"]=$btm->val("bonkdo");
		$tabValeur[$i]["bonkdo"]["aff"]=$btm->aff("bonkdo","html");

	  }

	foreach($tabValeur as $i=>$d)
	{
		$ret["data"][$i]=array();
		$ret["data"][$i]["id"]=$i;
		$ret["data"][$i]["num"]=$d["num"]["aff"];
		$ret["data"][$i]["nom"]=$d["nom"]["aff"];
		$ret["data"][$i]["nb"]=$d["nb"]["aff"];
		$ret["data"][$i]["dte_creat"]=$d["dte_creat"]["aff"];
		$ret["data"][$i]["status"]=$d["status"]["aff"];
		$ret["data"][$i]["line1"]="<line>";
		$ret["data"][$i]["paye"]=$d["paye"]["aff"];
		$ret["data"][$i]["date"]=$d["date"]["aff"];
		$ret["data"][$i]["pilote"]=$d["pilote"]["aff"];
		$ret["data"][$i]["resa"]=$d["resa"]["aff"];
		$ret["data"][$i]["type"]=$d["type"]["aff"];
		$ret["data"][$i]["bonkdo"]=$d["bonkdo"]["aff"];

	}

	echo json_encode($ret);
	
?>