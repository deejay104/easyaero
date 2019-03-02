<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	$id=checkVar("id","numeric");
	$fonc=checkVar("fonc","varchar");

// ---- Récupère les infos
	$ret=array();
	$ret["type"]=$fonc;

	if (($fonc=="get") && ($id>0))
	{
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_navpoints WHERE id='".$id."'";
		$res=$sql->QueryRow($query);

		if ($res["id"]>0)
		{
			$ret["id"]=$id;
			$ret["nom"]=utf8_encode($res["nom"]);
			$ret["description"]=utf8_encode($res["description"]);
			$ret["taxe"]=utf8_encode($res["taxe"]);
			$ret["lon"]=utf8_encode($res["lon"]);
			$ret["lat"]=utf8_encode($res["lat"]);
		}
	}
	
	else if (($fonc=="post") && ($id>0))
	{
		$nom=checkVar("nom","varchar",20);
		$desc=checkVar("description","varchar",200);
		$taxe=checkVar("taxe","varchar",20);
		$lon=checkVar("lon","varchar",10);
		$lat=checkVar("lat","varchar",10);

		$td=array();
		$td["nom"]=addslashes(utf8_decode(strtoupper($nom)));
		$td["description"]=addslashes(utf8_decode($desc));
		$td["taxe"]=addslashes(utf8_decode($taxe));
		$td["lon"]=addslashes(utf8_decode($lon));
		$td["lat"]=addslashes(utf8_decode($lat));
		$td["uid_maj"]=$gl_uid;
		$td["dte_maj"]=now();
		$sql->Edit("navpoints",$MyOpt["tbl"]."_navpoints",$id,$td);

		$ret["result"]="OK";
	}
	
// ---- Renvoie le résultat
	echo json_encode($ret);
?><?

?>