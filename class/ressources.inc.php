<?php
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

	protected $fields=array
	(
		"nom" => Array("type" => "varchar", "len"=>20),
		"immatriculation" => Array("type" => "uppercase", "len"=>6 ),
		"marque" => Array("type" => "uppercase", "len"=>20 ),
		"modele" => Array("type" => "uppercase", "len"=>20 ),
		"couleur" => Array("type" => "uppercase", "len"=>6 ),
		"actif" => Array("type" => "enum", "default" => "oui", "index"=>1),
		"poste" => Array("type" => "number", "index" => "1", ),
		"maxpotentiel" => Array("type" => "number", "default" => 50 ),
		"alertpotentiel" => Array("type" => "number", "default" => 45 ),
		"tarif" => Array("type" => "varchar", "len"=>6, "default" => 0 ),
		"tarif_reduit" => Array("type" => "varchar", "len"=>6, "default" => "0", ),
		"tarif_double" => Array("type" => "varchar", "len"=>6, "default" => "0", ),
		"tarif_inst" => Array("type" => "varchar", "len"=>6, "default" => "0", ),
		"tarif_nue" => Array("type" => "varchar", "len"=>6, "default" => "0", ),
		"typehora" => Array("type" => "enum", "default" => "min", ),
		"description" => Array("type" => "text", ),
		"places" => Array("type" => "number", "default" => "0", ),
		"puissance" => Array("type" => "number", "default" => "0", ),
		"charge" => Array("type" => "number", "default" => "0", ),
		"massemax" => Array("type" => "number", "default" => "0", ),
		"vitesse" => Array("type" => "number", "default" => "0", ),
		"autonomie" => Array("type" => "number", "default" => "0", ),
		"tolerance" => Array("type" => "varchar", ),
		"centrage" => Array("type" => "text", ),
		"maintenance" => Array("type" => "varchar", "len"=>200 ),
		"notifmaint" => Array("type" => "datetime" ),
	);

	protected $tabList=array(
		"typehora"=>array("dix"=>"Dixième","cen"=>"Centième","min"=>"Minute"),
		"actif"=>array("oui"=>"oui","non"=>"non","off"=>"off")
	);

	function __construct($id=0,$sql)
	{
		global $MyOpt;
		global $gl_uid;
	
		parent::__construct($id,$sql);

		if ($this->data["couleur"]=="")
		{
			$this->data["couleur"]=dechex(rand(0x000000, 0xFFFFFF));
		}

	}

	# Show informations
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$render=$typeaff;
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);

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
				$ret='						<div class="asColorPicker-wrap">
							<input type="text" id="ress_col" name="'.$formname.'['.$key.']" class="color-picker asColorPicker-input" value="'.strtoupper($txt).'" OnKeyUp='."\"document.getElementById('ress_col_show').style.backgroundColor='#'+document.getElementById('ress_col').value;\"".'\>
							<a href="#" class="asColorPicker-clear"></a>
							<div id="ress_col_show" class="asColorPicker-trigger" style="width:32px; background-color: #'.strtoupper($txt).';">
								<span style="background-color: #ffe74c;"></span>
							</div>
						</div>';
				
				
				// <INPUT id='ress_col' name=\"".$formname."[$key]\" value=\"".strtoupper($txt)."\" style=\"display: inline-block;\" OnKeyUp=\"document.getElementById('ress_showcol').style.backgroundColor='#'+document.getElementById('ress_col').value;\"><div id='ress_showcol' style='margin-left:10px; display: inline-block; width:24px; height:24px; border: 1px solid black; background-color:#".strtoupper($txt).";'></div>";
			}
			else if ($key=="poste")
			{
				$query = "SELECT id,description FROM ".$this->tbl."_mouvement WHERE actif='oui' ORDER BY ordre,description";
				$sql->Query($query);
		  	  	$ret ="<SELECT name=\"".$formname."[$key]\" class='form-control'>";
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
				$query = "SELECT id,description FROM ".$this->tbl."_mouvement WHERE id='".$txt."'";
				$res=$sql->QueryRow($query);
				$ret=$res["description"];
			}
			else if ($key=="immatriculation")
			{
				$ret=strtoupper($this->data[$key]);
				if ($ret=="")
				{
					$ret="<i>NA</i>";
				}
				if ($this->data["actif"]!="oui")
				{
					$ret="<s>".$ret."</s>";
				}
				$ret="<a href='".geturl("ressources","detail","id=".$this->id)."'>".$ret."</a>";
			}
		}
		return $ret;
	}

	function Desactive(){
		global $gl_uid;
		$sql=$this->sql;
		$this->actif="off";
		$this->data["actif"]="off";
		$sql->Edit("ressource",$this->tbl."_ressources",$this->id,array("actif"=>'off', "uid_maj"=>$gl_uid, "dte_maj"=>now()));
	}
	function Active(){
		global $gl_uid;
		$sql=$this->sql;
		$this->actif="oui";
		$this->data["actif"]="oui";
		$sql->Edit("ressource",$this->tbl."_ressources",$this->id,array("actif"=>'oui', "uid_maj"=>$gl_uid, "dte_maj"=>now()));
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
			if (strlen($tdeb[3])<2)
			{
				$tdeb[3]=$tdeb[3]."0";
			}
			if (strlen($tfin[3])<2)
			{
				$tfin[3]=$tfin[3]."0";
			}
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
  		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier AS cal WHERE actif='oui' AND uid_avion='".$this->id."' AND dte_deb<'".date("Y-m-d H:i:s",$fin)."' AND dte_fin>'".date("Y-m-d H:i:s",$deb)."'";
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

	function TempsVols($affvol="")
	{ global $MyOpt;
		$sql=$this->sql;

		$query ="SELECT cal.dte_deb,cal.dte_fin,cal.idmaint, maint.potentiel AS tot FROM ".$this->tbl."_calendrier AS cal ";
		$query.="LEFT JOIN ".$this->tbl."_maintenance AS maint ON cal.idmaint=maint.id ";
		$query.="WHERE cal.idmaint>0 AND maint.actif='oui' AND cal.dte_deb<'".now()."' AND cal.uid_avion='".$this->id."' ORDER BY cal.dte_fin DESC LIMIT 0,1";
		$resvol=$sql->QueryRow($query);

		if ((!isset($resvol)) ||(!is_array($resvol)))
		{
			$resvol=array();
			$resvol["dte_deb"]="0000-00-00";
			$resvol["tot"]="0";
		}


		$t=0;
		
		$query="SELECT dte_fin,potentiel AS tot FROM ".$this->tbl."_calendrier WHERE potentiel>0 AND dte_deb>'".$resvol["dte_deb"]."' AND ".(($affvol=="fin") ? "dte_deb" : "dte_fin")."<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$respot=$sql->QueryRow($query);

		if ((!isset($respot)) ||(!is_array($respot)))
		{
			$respot=array();
			$respot["tot"]=0;
		}
		
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
			$resreel=$sql->QueryRow($query);
			$t=$resvol["tot"]+$resreel["tot"];
		}

		// if ($this->tpsreel>0)
		// {
			// $t=$t+$this->tpsreel;
		// }		

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
		
		$tps=$this->TempsVols();
		// $query="SELECT dte_fin,potentiel AS tot FROM ".$this->tbl."_calendrier WHERE potentiel>0 AND dte_fin<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		// $respot=$sql->QueryRow($query);

		// $query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".now()."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->id."'";
		// $resreel=$sql->QueryRow($query);

		// $t=$respot["tot"]+$resreel["tot"];

		// $query="SELECT dte_fin FROM ".$this->tbl."_calendrier WHERE tpsreel<>0 AND dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".now()."' AND uid_avion='".$this->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		// $reslast=$sql->QueryRow($query);
		// if ($reslast["dte_fin"]=="")
		// {
			// $reslast=array();
			// $reslast["dte_fin"]=$respot["dte_fin"];
		// }

		// $dte=$reslast["dte_fin"];
		
		$dte="NA";
		$query="SELECT dte_deb,dte_fin,tpsestime FROM ".$this->tbl."_calendrier WHERE dte_deb>='".now()."' AND actif='oui' AND uid_avion='".$this->id."' ORDER BY dte_deb";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			
			$dte=$sql->data["dte_deb"];
			$tps=$tps+$sql->data["tpsestime"];

			if (floor($tps/60)>=$this->data["maxpotentiel"])
			{
				$i=$sql->rows;
			}
		}
			
		return $dte;
	}
	
	function ProchaineMaintenance()
	{
		$sql=$this->sql;
		$id=0;
		
		$query="SELECT id FROM ".$this->tbl."_maintenance WHERE dte_deb>='".now()."' AND actif='oui' AND uid_ressource='".$this->id."' ORDER BY dte_deb LIMIT 0,1";
		$res=$sql->QueryRow($query);
		
		if ((is_array($res)) && ($res["id"]>0))
		{
			$id=$res["id"];
		}
		else
		{
			$id=0;
		}

		return $id;
	}
}



function ListeRessources($sql,$actif=array())
{
	global $MyOpt;

	if (count($actif)==0)
	{
		$actif[]="oui";
		if (GetDroit("SupprimeRessource"))
		{
			$actif[]="off";
		}
	}
	
	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id FROM ".$MyOpt["tbl"]."_ressources WHERE ($txt) ";
	$sql->Query($query);
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }
	return $res;
}

function AffListeRessources($sql,$form_uid,$name,$actif=array())
{
	global $MyOpt;

 	if (count($actif)==0)
	{
		$actif[]="oui";
		if (GetDroit("SupprimeRessource"))
		{
			$actif[]="off";
		}
	}

	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id,immatriculation FROM ".$MyOpt["tbl"]."_ressources WHERE ($txt ".((GetDroit("SupprimeRessource")) ? "OR actif='off'" : "" ).") ORDER BY immatriculation";
	$sql->Query($query);

	$lstress ="<select id='".$name."' name='".$name."' class='form-control form-control-lg'>";
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
