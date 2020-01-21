<?php
/*
    Easy-Aero
    Copyright (C) 2018 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  
// ---- Header de la page
	// Date du passé
	header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// toujours modifié
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	
	// HTTP/1.0
	header("Pragma: no-cache");

	// Image PNG
	header('Content-type: image/png');

// ---- Variables d'affichage
	$l = 600;
	$h = 400;


// ---- Récupère les paramètres
	$id=checkVar("id","numeric");
	$rid=checkVar("rid","numeric");

	$tabplace=array();
	
// ---- Charge les informations de l'avion
	if ($id>0)
	{
		$query="SELECT cal.dte_deb, cal.dte_fin,avion.immatriculation,avion.tolerance,avion.centrage FROM ".$MyOpt["tbl"]."_calendrier AS cal LEFT JOIN ".$MyOpt["tbl"]."_ressources AS avion ON cal.uid_avion=avion.id WHERE cal.id='$id'";
		$resvol=$sql->QueryRow($query);
		
		// Récupère la liste des passagers
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_masses WHERE uid_vol='$id'";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tabplace[$sql->data["uid_place"]]["idpilote"]=$sql->data["uid_pilote"];
			$tabplace[$sql->data["uid_place"]]["poids"]=$sql->data["poids"];
			$tabplace[$sql->data["uid_place"]]["idenr"]=$sql->data["id"];
		  }
	}
	else if ($rid>0)
	{
		$query="SELECT avion.immatriculation,avion.tolerance,avion.centrage FROM ".$MyOpt["tbl"]."_ressources AS avion WHERE id='$rid'";
		$resvol=$sql->QueryRow($query);
	}
	else
	{
		erreur("Les paramètres sont incorrects.");
		exit;
	}
	
// ---- Charge le plan de centrage de l'avion
	$data=$resvol["centrage"];
	$parser = xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parse_into_struct($parser,$data,$values,$tags);
	xml_parser_free($parser);

	// boucle à travers les structures
	foreach ($tags as $key=>$val)
	{
		if ($key == "place")
		{
			$ranges = $val;
			// each contiguous pair of array entries are the
			// lower and upper range for each molecule definition
			for ($i=0; $i < count($ranges); $i+=2)
			{
				$offset = $ranges[$i] + 1;
				$len = $ranges[$i + 1] - $offset;
				$t = parsePlace(array_slice($values, $offset, $len));
				$tabplace[$t["id"]]["id"]=$t["id"];
				$tabplace[$t["id"]]["name"]=$t["name"];
				$tabplace[$t["id"]]["bras"]=$t["bras"];
				$tabplace[$t["id"]]["coef"]=$t["coef"];
				$tabplace[$t["id"]]["type"]=$t["type"];

				if ($tabplace[$t["id"]]["poids"]==0)
				{
					$tabplace[$t["id"]]["poids"]=$t["poids"];
				}

				// print_r($t);
			}
		}
		else
		{
			continue;
		}
	}


//print_r($tabplace);


// ---- Affiche le graph
	$img = imagecreate($l, $h);
	$white = imagecolorallocate ($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);
	$grisclair = imagecolorallocate($img, 240, 240, 240);
	$gris = imagecolorallocate($img, 170, 170, 170);
	$textcolor = imagecolorallocate($img, 0, 0, 0);

	imagefill($img,0,0,$white); 

	// Titre
	imagestring($img, 5, 40, 15, "Devis de masse et centrage", $textcolor);
	imagestring($img, 2, 40, 30, "Vol du ".sql2date($resvol["dte_deb"],"nosec")." au ".sql2date($resvol["dte_fin"],"nosec")." sur le ".strtoupper($resvol["immatriculation"]), $textcolor);

	// Axes
	imageline($img,20,50,20,$h-10,$black);
	imageline($img,19,51,21,51,$black);
	imageline($img,10,$h-20,$l-10,$h-20,$black);
	imageline($img,$l-11,$h-21,$l-11,$h-19,$black);
	imagestring($img, 2, $l-20, $h-20, "m", $textcolor);
	imagestring($img, 2, 5, 50, $MyOpt["unitPoids"], $textcolor);
	imagefilledrectangle($img,21,60,$l-20,$h-21,$grisclair);
	imagerectangle($img,0,0,$l-1,$h-1,$black);

	// Trace l'enveloppe de tolérance
	$env = preg_split("/,/",$resvol["tolerance"]);

	$minx=$env[0];
	$maxx=0;
	$miny=$env[1];
	$maxy=0;
	for ($i=0; $i < count($env); $i=$i+2)
	  {
		if ($env[$i]<$minx) { $minx=$env[$i]; }
		if ($env[$i]>$maxx) { $maxx=$env[$i]; }
		if ($env[$i+1]>$maxy) { $maxy=$env[$i+1]; }
		
	  }

	$aminx=round($minx,1)-0.1;	
	$amaxx=round($maxx,1)+0.1;	
	$aminy=$miny;
	$amaxy=$maxy+50;

	$affenv=array();
	for ($i=0; $i < count($env); $i=$i+2)
	  {
		$t=CalcCoor($env[$i],$env[$i+1]);
		$affenv[$i]=$t[0];
		$affenv[$i+1]=$t[1];
	  }

	$t=CalcCoor($maxx,$maxy);
	imageline($img,17,$t[1],$t[0],$t[1],$gris);
	imagestring($img, 2, 25, $t[1]-15, "$maxy ".$MyOpt["unitPoids"], $textcolor);
	imagestring($img, 2, 25, $h-35, "$miny ".$MyOpt["unitPoids"], $textcolor);

	imageline($img,$t[0],$t[1],$t[0],$h-17,$gris);
	imagestring($img, 2, $t[0]-8, $h-17, "$maxx", $textcolor);


	$t=CalcCoor($minx,$maxy);
	imageline($img,$t[0],$h-23,$t[0],$h-17,$gris);
	imagestring($img, 2, $t[0]-8, $h-17, "$minx", $textcolor);

	imagefilledpolygon($img, $affenv, count($env)/2, $white);
	imagepolygon($img, $affenv, count($env)/2, $black);

	// Affiche le devis
	$xx=0;
	$yy=0;
	foreach ($tabplace as $k=>$v)
	  {
		if (!isset($v["coef"])) { $v["coef"]=1; }
		if (!isset($v["poids"])) { $v["poids"]=0; }
		if (!isset($v["bras"])) { $v["bras"]=0; }
		$coef=($v["coef"]>0) ? $v["coef"] : 1;
		$xx=$xx+$v["poids"]*$coef*$v["bras"];
		$yy=$yy+round($v["poids"]*$coef,0);
	  }
	$xx=round($xx/$yy,3);

	$t=CalcCoor($xx,$yy);

	imageline($img,17,$t[1],$t[0],$t[1],$gris);
	imageline($img,$t[0],$t[1],$t[0],$h-17,$gris);

	imageline($img,$t[0]-4,$t[1],$t[0]+4,$t[1],$black);
	imageline($img,$t[0],$t[1]-4,$t[0],$t[1]+4,$black);

	imagestring($img, 2, $t[0]-8, $h-17, "$xx", $textcolor);
	imagestring($img, 2, $t[0]+4, $t[1]-15, "$yy ".$MyOpt["unitPoids"], $textcolor);


	// Affiche l'image
	imagepng($img);


// ---- Fonctions

function erreur($txt)
  {
	$error = imagecreate(320, 240);
	$logo = imagecreatefrompng("../../images/icn32_erreur.png");
	list($width, $height) = getimagesize("../../images/icn32_erreur.png");
	$white = imagecolorallocate ($error, 255, 255, 255);
	$textcolor = imagecolorallocate($error, 255, 0, 0);

	imagefill($error,0,0,$white); 
	imagecopy($error,$logo,5,15,0,0,$width,$height);

	imagestring($error, 5, 50, 15, "ERREUR", $textcolor);
	imagestring($error, 2, 50, 30, $txt, $textcolor);
	imagepng($error);
  }

function CalcCoor($x,$y)
  { global $aminx,$amaxx,$aminy,$amaxy,$l,$h;
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
