<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  

// ---- Récupère les paramètres
	// $id=(is_numeric($_REQUEST["id"]) ? $_REQUEST["id"] : 0);
	// $deb=(is_numeric($_REQUEST["deb"]) ? $_REQUEST["deb"] : 0);
	// $fin=(is_numeric($_REQUEST["fin"]) ? $_REQUEST["fin"] : 0);

	$id=checkVar("id","numeric");
	$deb=checkVar("deb","numeric");
	$fin=checkVar("fin","numeric");
	$resa=checkVar("resa","numeric");

//	$deb=strtotime("2013-08-07 17:00");
//	$fin=strtotime("2013-08-07 18:00");

// ---- Charge les informations sur le chargement
	$ret=array();

	if (($id>0) && ($deb>0) && ($fin>0))
	  {
		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier AS cal WHERE uid_avion='$id' AND dte_deb<'".date("Y-m-d H:i:s",$fin)."' AND dte_fin>'".date("Y-m-d H:i:s",$deb)."' AND actif='oui'";
		$sql->Query($query);

		if ($sql->rows>0)
		  {
			$ok="nok";
			$txt="Occupé";
			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
				if ($sql->data["id"]==$resa)
				  {
				  	$ok="ok";
					$txt="Réservé";
				  }
			  }
		  }
		else
		  {
		  	$ok="nc";
			$txt="Disponible";
		  }
	}
	else
	{
		if (($deb>0) && ($deb>0))
		{
			// erreur(utf8_decode("Les paramètres sont incorrects."));
			$ok="error";
			$txt="Les paramètres sont incorrects.";
		}
		else
		{
			$ok="error";
			$txt="";
		}
		exit;
	}

	$ret["status"]=200;
	$ret["idavion"]=$id;
	$ret["availability"]=$ok;
	$ret["message"]=$txt;

// print_r($ret);
	echo json_encode($ret);
	exit;

// ---- Variables d'affichage
	$l = 100;
	$h = 20;

// ---- Affiche le graph
	$img = imagecreate($l, $h);
	$white = imagecolorallocate ($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);
	$grisclair = imagecolorallocate($img, 240, 240, 240);
	$gris = imagecolorallocate($img, 170, 170, 170);
	$textcolor = imagecolorallocate($img, 0, 0, 0);
	imagefill($img,0,0,$white); 

	$logo = imagecreatefrompng($appfolder."/static/modules/".$mod."/img/icn16_".$ok.".png");
	list($width, $height) = getimagesize($appfolder."/static/modules/".$mod."/img/icn16_".$ok.".png");
	imagecopy($img,$logo,2,2,0,0,$width,$height);

	imagestring($img, 2, 20, 2, $txt, $textcolor);

	// Affiche l'image
	imagepng($img);


// ---- Fonctions

function erreur($txt)
{
	global $appfolder,$mod;
	
	$error = imagecreate(320, 16);
	$logo = imagecreatefrompng($appfolder."/static/modules/".$mod."/img/icn16_erreur.png");
	list($width, $height) = getimagesize($appfolder."/static/modules/".$mod."/img/icn16_erreur.png");
	$white = imagecolorallocate ($error, 255, 255, 255);
	$textcolor = imagecolorallocate($error, 255, 0, 0);

	imagefill($error,0,0,$white); 
	imagecopy($error,$logo,0,0,0,0,$width,$height);

	imagestring($error, 2, 30, 0, $txt, $textcolor);
	imagepng($error);
}

function CalcCoor($x,$y)
{
	global $aminx,$amaxx,$aminy,$amaxy,$l,$h;
  	$t=array();
  	$t[0]=round(($x-$aminx)*($l-40)/($amaxx-$aminx)+20,0);
	$t[1]=round($h-20-($y-$aminy)*($h-80)/($amaxy-$aminy),0);
	return $t;
}

function parsePlace($mvalues)
{
	for ($i=0; $i < count($mvalues); $i++)
	$t[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
	return $t;
}


?>
