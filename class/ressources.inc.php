<?
/*
    SoceIt v2.0
    Copyright (C) 2009 Matthieu Isorez

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

    ($Author: miniroot $)
    ($Date: 2016-04-22 22:08:32 +0200 (ven., 22 avr. 2016) $)
    ($Revision: 460 $)
*/

class ress_class extends objet_core
{
	# Constructor
	protected $table="ressources";
	protected $mod="ressources";
	protected $rub="detail";

	protected $type=array(
		"immatriculation"=>"uppercase",
		"nom"=>"varchar",
		"couleur"=>"uppercase",
		"modele"=>"uppercase",
		"marque"=>"uppercase",
		"typehora"=>"enum",
		"description"=>"text",
		"centrage"=>"text",
	);

	protected $tabList=array(
		"typehora"=>array("dix"=>"Dixième","cen"=>"Centième","min"=>"Minute"),
	);

	function __construct($id=0,$sql)
	{
		global $MyOpt;
		global $gl_uid;
	
		$this->data["nom"]="";
		$this->data["immatriculation"]="";
		$this->data["actif"]="oui";
		$this->data["poste"]=0;
		$this->data["maxpotentiel"]=50;
		$this->data["alertpotentiel"]=45;
		$this->data["marque"]="";
		$this->data["modele"]="";
		$this->data["couleur"]=dechex(rand(0x000000, 0xFFFFFF));
		$this->data["description"]="";

		$this->data["places"]="0";
		$this->data["puissance"]="0";
		$this->data["massemax"]="0";
		$this->data["vitesse"]="0";
		$this->data["tolerance"]="";
		$this->data["centrage"]="";

		$this->data["tarif"]="0";
		$this->data["tarif_reduit"]="0";
		$this->data["tarif_double"]="0";
		$this->data["tarif_nue"]="0";

		$this->data["typehora"]="dix";


		parent::__construct($id,$sql);
	}

	# Show informations
	function aff($key,$typeaff="html",$formname="form_data",&$render="")
	{
		$render=$typeaff;
		$ret=parent::aff($key,$typeaff,$formname,$render);

		$sql=$this->sql;
		$txt="";
		if (isset($this->data[$key]))
		{
			$txt=$this->data[$key];
		}

		if ($typeaff=="form")
		{
			if ($key=="couleur")
		  	{
				$ret="<INPUT id='ress_col' name=\"".$formname."[$key]\" value=\"".strtoupper($txt)."\" style=\"display: inline-block;\" OnKeyUp=\"document.getElementById('ress_showcol').style.backgroundColor='#'+document.getElementById('ress_col').value;\"><div id='ress_showcol' style='margin-left:10px; display: inline-block; width:24px; height:24px; border: 1px solid black; background-color:#".strtoupper($txt).";'></div>";
			}
			else if ($key=="poste")
			{
				$query = "SELECT id,description FROM ".$this->tbl."_mouvement WHERE actif='oui' ORDER BY ordre,description";
				$sql->Query($query);
		  	  	$ret ="<SELECT name=\"form_ress[$key]\">";
				for($i=0; $i<$sql->rows; $i++)
				{ 
					$sql->GetRow($i);
					$ret.="<OPTION value=\"".$sql->data["id"]."\" ".(($txt==$sql->data["id"])?"selected":"").">".$sql->data["description"]."</OPTION>";
				}
		  	  	$ret.="</SELECT>";
			}
		}
		else
		{
			if ($key=="couleur")
			{
				$ret="<div style='display: inline-block; width:80px;'>".strtoupper($txt)."</div><div style='display: inline-block; width:24px; height:24px; border: 1px solid black; background-color:#".strtoupper($txt).";'></div>";
			}
			else if ($key=="poste")
			{
				$query = "SELECT id,description FROM ".$this->tbl."_mouvement WHERE id='".$ret."'";
				$res=$sql->QueryRow($query);
				$ret=$res["description"];
			}
		}
		return $ret;
	}
	
	function CalcHorametre($deb,$fin)
	{
		preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$deb,$tdeb);
		preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$fin,$tfin);

		if (!isset($tdeb[3]))
		{
			$tdeb[3]=0;
		}
		if (!isset($tfin[3]))
		{
			$tfin[3]=0;
		}

		if ($this->data["typehora"]=="min")
		{
			$t=round(($tfin[1]-$tdeb[1])*60+($tfin[3]-$tdeb[3]));
		}
		else if ($this->data["typehora"]=="dix")
		{
			$t=round(($tfin[1]-$tdeb[1])*60+($tfin[3]-$tdeb[3])*6);
		}
		else if ($this->data["typehora"]=="cen")
		{
			$t=round((($tfin[1]-$tdeb[1])*100+($tfin[3]-$tdeb[3]))*60/100);
		}
		else
		{
			$t=($fin-$deb)*60;
		}

		return $t;
	}

	function CalcTempsVol($type="all"){
			
	  }

	function CheckDispo($deb,$fin)
	  { global $MyOpt;
		$sql=$this->sql;
  		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier AS cal WHERE uid_avion='$this->id' AND dte_deb<'".date("Y-m-d H:i:s",$fin)."' AND dte_fin>'".date("Y-m-d H:i:s",$deb)."'";
		$sql->Query($query);

		if ($sql->rows>0)
		  {
		  	return false;
		  }
		else
		  {
		  	return true;
		  }
	}

	function TempsVols()
	{ global $MyOpt;
		$sql=$this->sql;

		$query="SELECT dte_deb,dte_fin,idmaint FROM ".$this->tbl."_calendrier WHERE idmaint>0 AND dte_deb<'".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$resvol=$sql->QueryRow($query);

		$query="SELECT potentiel AS tot FROM ".$this->tbl."_maintenance WHERE id='".$resvol["idmaint"]."'";
		$resmaint=$sql->QueryRow($query);

		$query="SELECT dte_fin,potentiel AS tot FROM ".$this->tbl."_calendrier WHERE potentiel>0 AND dte_deb>'".$resvol["dte_deb"]."' AND ".(($affvol=="fin") ? "dte_deb" : "dte_fin")."<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$respot=$sql->QueryRow($query);

		if ($respot["tot"]>0)
		{
			$query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".now()."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->id."'";
			$resreel=$sql->QueryRow($query);

			$t=$respot["tot"]+$resreel["tot"];
		}
		else
		{
			$query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>'".$resvol["dte_deb"]."' AND dte_deb<'".now()."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->id."'";
			$resreel=$sql->QueryRow($query);
			$t=$resmaint["tot"]+$resreel["tot"];
		}

		if ($this->tpsreel>0)
		{
			$t=$t+$this->tpsreel;
		}		

		return $t;
	}
	
	function Potentiel()
	{ global $MyOpt;
		$sql=$this->sql;

		return $this->data["maxpotentiel"]*60-$this->TempsVols();
	}

	function AffPotentiel()
	{
		$t=$this->Potentiel();
		if (floor($t/60)<0)
		{
			$ret="<font color=red>".AffTemps($t)."</font>";
		}
		else if (floor($t/60)<$this->data["alertpotentiel"])
		{
			$ret="<font color=orange>".AffTemps($t)."</font>";
		}
		else
		{
			$ret=AffTemps($t);
		}
		return $ret;
	}

	function AffTempsVol()
	{
		$t=$this->TempsVols();
		if (floor($t/60)>$this->data["maxpotentiel"])
		{
			$ret="<font color=red>".AffTemps($t)."</font>";
		}
		else if (floor($t/60)>$this->data["maxpotentiel"]-$this->data["alertpotentiel"])
		{
			$ret="<font color=orange>".AffTemps($t)."</font>";
		}
		else
		{
			$ret=AffTemps($t);
		}
		return $ret;
	}

	function EstimeMaintenance()
	{ global $MyOpt;
		$sql=$this->sql;

		$query="SELECT dte_fin,potentiel AS tot FROM ".$this->tbl."_calendrier WHERE potentiel>0 AND dte_fin<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$respot=$sql->QueryRow($query);

		$query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".now()."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->id."'";
		$resreel=$sql->QueryRow($query);

		$t=$respot["tot"]+$resreel["tot"];

		$query="SELECT dte_fin FROM ".$this->tbl."_calendrier WHERE tpsreel<>0 AND dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$reslast=$sql->QueryRow($query);
		if ($reslast["dte_fin"]=="")
		{
			$reslast=array();
			$reslast["dte_fin"]=$respot["dte_fin"];
		}

		$dte=$reslast["dte_fin"];
		
		$query="SELECT dte_fin,tpsestime FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$reslast["dte_fin"]."' AND tpsreel=0 AND actif='oui' AND uid_avion='".$this->id."' ORDER BY dte_deb";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);

			if (floor($t/60)>=$this->data["maxpotentiel"])
			{
				$i=$sql->rows;
			}
			else
			{
				$dte=$sql->data["dte_fin"];
				$t=$t+$sql->data["tpsestime"];
			}
		}
			
		return $dte;
	}
}



function ListeRessources($sql,$actif=array("oui"))
{
	global $MyOpt;

	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id FROM ".$MyOpt["tbl"]."_ressources WHERE ($txt ".((GetDroit("SupprimeRessource")) ? "OR actif='off'" : "" ).") ";
	$sql->Query($query);
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }
	return $res;
}

function AffListeRessources($sql,$form_uid,$name,$actif=array("oui"))
 { global $MyOpt;

	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id,immatriculation FROM ".$MyOpt["tbl"]."_ressources WHERE ($txt ".((GetDroit("SupprimeRessource")) ? "OR actif='off'" : "" ).") ORDER BY immatriculation";
	$sql->Query($query);

	$lstress ="<select id=\"$name\" name=\"$name\">";
	$lstress.="<option value=\"0\">Aucun</option>";

	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$lstress.="<option value=\"".$sql->data["id"]."\" ".(($form_uid==$sql->data["id"]) ? "selected" : "").">".strtoupper($sql->data["immatriculation"])."</option>";
	  }
	$lstress.="</select>";

	return $lstress;
  }
?>
