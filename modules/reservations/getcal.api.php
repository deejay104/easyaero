<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge les dépendances
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/manifestation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Vérifie les paramètres

	// Short-circuit if the client did not give us a date range.
	if (!isset($_GET['start']) || !isset($_GET['end'])) {
		$input_arrays["error"]="Please provide a date range";
		echo json_encode($input_arrays);
		exit;
	}

	$start=checkVar("start","varchar");
	$end=checkVar("end","varchar");
	$ress=checkVar("ress","numeric");

	$ii=0;

	$lstres=array();
	
	if ($ress==0)
	{
		$lstres=ListeRessources($sql);
	}
	else
	{
		$lstres[]=$ress;
	}
	
	// Affichage des réservations	
	foreach ($lstres as $i=>$rid)
	{
		$tresa=GetReservation($sql,$start,$end,$rid);

		$d=floor(date_diff_txt($start,$end)/86400);
		$affnom=($d<=7) ? "fullname" : "initiales";

		if (is_array($tresa))
		{
			foreach($tresa as $r)
			{
				$resa=array();
				$resa["resa"]=new resa_class($r["id"],$sql);
				$resa["pilote"]=new user_core($resa["resa"]->uid_pilote,$sql,false,true);
				$resa["instructeur"]=new user_core($resa["resa"]->uid_instructeur,$sql,false,true);
				$resa["ress"]=new ress_class($resa["resa"]->uid_ressource,$sql);
			
				$col="";
				$resa["instructeur"]->LoadRoles();
				if (($resa["instructeur"]->CheckDroit("TypeInstructeur")) && ($resa["instructeur"]->id==$myuser->id))
				{
					$col=$MyOpt["tabcolresa"]["own"];
				}
				else if ($resa["pilote"]->id==$myuser->id)
				{
					$col=$MyOpt["tabcolresa"]["own"];
				}
				else if ($ress==0)
				{
					  $col=$resa["ress"]->data["couleur"];
				}
				else
				{
					$col=$MyOpt["tabcolresa"]["booked"];
				}
	
				$input_arrays[$ii]["id"]=$resa["resa"]->id;
				$input_arrays[$ii]["resourceId"]=$resa["resa"]->uid_ressource;
				$input_arrays[$ii]["title"]=(($d==1) ? $resa["ress"]->val("immatriculation")." : \n" : "").$resa["pilote"]->val($affnom).(($resa["instructeur"]->id>0) ? " + ".($resa["instructeur"]->val($affnom)) : "").(($resa["resa"]->invite=="oui") ? " <img src='static/modules/reservations/img/icn16_invite.png'>" : "");
				$input_arrays[$ii]["start"]=date("c",strtotime($resa["resa"]->dte_deb));
				$input_arrays[$ii]["end"]=date("c",strtotime($resa["resa"]->dte_fin));
				$input_arrays[$ii]["description"]=$resa["ress"]->val("immatriculation")." de ".sql2time($resa["resa"]->dte_deb,"nosec")." à ".sql2time($resa["resa"]->dte_fin,"nosec")."<br>".$resa["pilote"]->Aff("fullname","val").(($resa["instructeur"]->id>0) ? "<br/>+ ".($resa["instructeur"]->Aff("fullname","val")) : "").(($resa["resa"]->description!="") ? "<br>----<br>".($resa["resa"]->description) : "");
				$input_arrays[$ii]["editable"]=($resa["resa"]->edite=='non') ? false : true;
				if ($col!="") { $input_arrays[$ii]["color"]='#'.$col; }
				$ii=$ii+1;
			}
		}
	}
	
	// Affichage des manifestations
	$tmanip=GetManifestation($sql,$start,$end);

	if (is_array($tmanip))
	{
		foreach($tmanip as $r)
		{
			$m=new manip_class($r,$sql);

			$input_arrays[$ii]["id"]="M".$m->id;
			$input_arrays[$ii]["title"]=$m->data["titre"];
			$input_arrays[$ii]["start"]=date("c",strtotime($m->data["dte_manip"]." 00:00:00"));
			$input_arrays[$ii]["end"]=date("c",strtotime($m->data["dte_manip"]." 23:59:59"));
			$input_arrays[$ii]["color"]='#'.$MyOpt["tabcolresa"]["meeting"];
			$input_arrays[$ii]["rendering"]='background';
			$ii=$ii+1;
		}
	}

	// Affichage des maintenances
	$tmaint=GetAllMaintenance($sql,$ress);

	if (is_array($tmaint))
	{
		foreach($tmaint as $r)
		{
			$m=new maint_class($r,$sql);

			$input_arrays[$ii]["id"]="M".$m->id;
			$input_arrays[$ii]["title"]="Maintenance";
			$input_arrays[$ii]["start"]=date("c",strtotime($m->data["dte_deb"]));
			$input_arrays[$ii]["end"]=date("c",strtotime($m->data["dte_fin"])+86400);
			$input_arrays[$ii]["color"]='#'.(($m->data["status"]>1) ? $MyOpt["tabcolresa"]["maintconf"] : $MyOpt["tabcolresa"]["maintplan"]);
			$input_arrays[$ii]["rendering"]='background';

			$ii=$ii+1;
		}
	}

	// Affichage du jour et de la nuit
	for($i=floor(strtotime($start)/86400)*86400; $i<=floor(strtotime($end)/86400)*86400; $i=$i+86400)
	{
			$tabcs=CalculSoleil($i,-$MyOpt["terrain"]["longitude"],$MyOpt["terrain"]["latitude"]);

			$input_arrays[$ii]["title"]="";
			$input_arrays[$ii]["start"]=date("c",$i);
			$input_arrays[$ii]["end"]=date("c",$i+$tabcs["ls"]-30*60);
			$input_arrays[$ii]["color"]='gray';
			$input_arrays[$ii]["rendering"]='background';
			$ii=$ii+1;
			$input_arrays[$ii]["title"]="";
			$input_arrays[$ii]["start"]=date("c",$i+$tabcs["cs"]+30*60);
			$input_arrays[$ii]["end"]=date("c",$i+86399);
			$input_arrays[$ii]["color"]='gray';
			$input_arrays[$ii]["rendering"]='background';
			$ii=$ii+1;
	}

	// Send JSON to the client.
	echo json_encode($input_arrays);


// ---- Calcul du lever/coucher du soleil

function CalculSoleil($jour,$lo,$la)
{
	// Constantes
	$fh=0;
	$k = 0.0172024;
	$jm = 308.67;
	$jl = 21.55;
	$e = 0.0167;
	$ob = 0.4091;
	$pi = 3.141593;
	$dr = $pi/180;
	$hr = $pi/12;
	// Hauteur du soleil au lever et au coucher
	$ht = -50/60;
	$ht = $ht * $dr;
	// Fuseau horaire et coordonnÃ©es gÃ©ographiques
	$jo=date("j",$jour);
	$mo=date("n",$jour);
//$la=48.905;
//$lo=-7.750;
	$lo=$lo*$dr;
	$la=$la*$dr;
	if ($mo<3) { $mo = $mo + 12; }
	// Heure TU du milieu de la journÃ©e
	$h = 12 + $lo/$hr;
	// Nombre de jours écoulés depuis le 1 Mars O h TU
	$j = floor(30.61 * ($mo + 1)) + $jo + $h / 24 - 123;
	// Anomalie et longitude moyenne
	$m = $k * ($j - $jm);
	$l = $k * ($j - $jl);
	// Longitude vrai
	$s = $l + 2 * $e * sin($m) + 1.25 * $e * $e * sin(2 * $m);
	// Coordonnées rectangulaires du soleil dans le repère équatorial
	$x = cos($s);
	$y = cos($ob) * sin($s);
	$z = sin($ob) * sin($s);
	// equation du temps et déclinaison
	$r = $l;
	$rx = cos($r) * $x + sin($r) * $y;
	$ry = -sin($r) * $x + cos($r) * $y;
	$x = $rx;
	$y = $ry;
	$et = atan($y / $x);
	$dc = atan($z / sqrt(1 - $z * $z)) ;
	// Heure de passage au méridien
	$pm = $h + $fh + $et / $hr;
	$hs = floor($pm);
	$pm = 60 * ($pm - $hs);
	$ms = floor($pm);
	$pm = 60 * ($pm - $ms);
	// Angle horaire au lever et au coucher
	$cs = (SIN($ht) - SIN($la) * SIN($dc)) / COS($la) / COS($dc);
	// if ($cs > 1)
	  // { echo "Le soleil ne se leve pas<BR>"; }
	// if ($cs < -1)
	  // { echo "Le soleil ne se couche pas<BR>"; }
	if ($cs == 0)
	  { $ah = $pi / 2; }
	else
	  { $ah = atan(sqrt(1 - $cs * $cs) / $cs); }
	if ($cs < 0)
	  { $ah = $ah + $pi; }

	// Lever du soleil
	$pm = $h + $fh + ($et - $ah) / $hr;
	if ($pm < 0) { $pm = $pm + 24; }
	$hs = floor($pm);
	$pm = floor(60 * ($pm - $hs));
//echo "$hs:$pm ";
	$res["ls"]=$hs*3600+$pm*60;

	// Coucher du soleil
	$pm = $h + $fh + ($et + $ah) / $hr;
	if ($pm >24) { $pm = $pm - 24; }
	$hs = floor($pm);
	$pm = floor(60 * ($pm - $hs));
//echo " $hs:$pm, ";	
	$res["cs"]=$hs*3600+$pm*60;
	return $res;
}


?>