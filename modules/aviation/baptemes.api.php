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
	$form_type=checkVar("type","varchar");

	// error_log(print_r($_REQUEST,true));

	$ret=array();

	if (isset($search["value"]))
	{
		$crit=$search["value"];
	}

	$order=array();
	$order["name"]=(isset($s[0]["column"])) ? $c[$s[0]["column"]]["data"] ?? "" : "";
	$order["dir"]=$s[0]["dir"] ?? "";

	$tot=TotalBaptemes($sql,array("oui"),$form_status,$form_type,$crit);
	$ret["recordsTotal"]=$tot;
	$ret["recordsFiltered"]=$tot;

	$lstusr=ListeBaptemes($sql,array("oui"),$form_status,$form_type,$crit,$order,$ts,$tl);
	

	$ret["draw"]=$draw;
	$ret["data"]=array();


	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$btm = new bapteme_class($id,$sql);

		$ret["data"][$i]=array();
		$ret["data"][$i]["id"]=$i;
		$ret["data"][$i]["num"]=$btm->val("num","html");
		$ret["data"][$i]["nom"]=$btm->val("nom","html");
		$ret["data"][$i]["nb"]=$btm->val("nb","html");
		$ret["data"][$i]["dte_creat"]=$btm->val("dte_creat","html");
		$ret["data"][$i]["status"]=$btm->val("status");
		$ret["data"][$i]["paye"]=($btm->val("dte_paye")!="0000-00-00") ? $btm->val("dte_paye") : $btm->val("paye");
		$ret["data"][$i]["date"]=strtotime($btm->data["dte"]);
		$ret["data"][$i]["type"]=$btm->val("type");
		$ret["data"][$i]["bonkdo"]=$btm->val("bonkdo");


		$ret["data"][$i]["_link"]["num"]=$btm->aff("num","html");
		$ret["data"][$i]["_link"]["nom"]=$btm->aff("nom","html");
		$ret["data"][$i]["_link"]["nb"]=$btm->aff("nb","html");
		$ret["data"][$i]["_link"]["dte_creat"]=$btm->aff("dte_creat","html");
		$ret["data"][$i]["_link"]["status"]=$btm->aff("status");
		$ret["data"][$i]["_link"]["paye"]=($btm->val("dte_paye")!="0000-00-00") ? $btm->aff("dte_paye") : $btm->aff("paye");
		$ret["data"][$i]["_link"]["date"]=($btm->val("dte")!='0000-00-00 00:00:00') ? $btm->aff("dte","html") : "-";
		$ret["data"][$i]["_link"]["type"]=$btm->aff("type");
		$ret["data"][$i]["_link"]["bonkdo"]=$btm->aff("bonkdo");


		if ($btm->val("id_pilote")>0)
		{
			$usr = new user_class($btm->val("id_pilote"),$sql,true);
			$ret["data"][$i]["pilote"]=$usr->val("fullname");
			$ret["data"][$i]["_link"]["pilote"]=$usr->Aff("fullname");
		}
		else
		{
		  	$ret["data"][$i]["pilote"]="-";
		}
		if ($btm->val("id_avion")>0)
		{
			$ress = new ress_class($btm->val("id_avion"),$sql);
			$ret["data"][$i]["resa"]=$ress->val("immatriculation");
			$ret["data"][$i]["_link"]["resa"]=$ress->aff("immatriculation");
		}
		else
		{
		  	$ret["data"][$i]["resa"]="-";
		}


	  }


	echo json_encode($ret);
	
?>