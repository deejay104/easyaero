<?php
/*
    Easy Aero v2.4
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

    ($Author: miniroot $)
    ($Date: 2016-04-22 22:08:32 +0200 (ven., 22 avr. 2016) $)
    ($Revision: 460 $)
*/

$tabValeurConstat=array();
$tabValeurConstat["new"]="Nouveau";
$tabValeurConstat["inprg"]="En cours";
$tabValeurConstat["close"]="Cloturé";
$tabValeurConstat["cancel"]="Annulé";


// Class Utilisateur
class event_class extends objet_core
{
	protected $table="evenement";
	protected $mod="securite";
	protected $rub="eventdetail";

	protected $droit=array("status"=>"ModifRexStatus","planaction"=>"ModifRexSynthese","synthese"=>"ModifRexSynthese");
	// protected $type=array("titre"=>"varchar","status"=>"enum","description"=>"text","commentaire"=>"text","synthese"=>"text","planaction"=>"text","dte_rex"=>"date");
	protected $fields=array(
		"title" => Array("type" => "varchar", "len"=>100 ),
		"status" => Array("type" => "enum","default"=>"new","index"=>1, "show"=>"tag" ),
		"description" => Array("type" => "text" ),
		"owner" => Array("type" => "number", "index"=>1 ),
		"probability" => Array("type" => "enum","default"=>"0"),
		"gravity" => Array("type" => "enum","default"=>"0"),
		"risk" => Array("type" => "number"),
		"dte_event" => Array("type" => "date", "default" => "now" ),
		"dte_due" => Array("type" => "date" ),
		"dte_close" => Array("type" => "date" ),
		"action" => Array("type" => "text" ),
	);

	protected $tabList=array(
		"status"=>array('new'=>'Nouveau','inprg'=>'En cours','close'=>'Cloturé','cancel'=>'Annulé'),
		"probability"=>array(0=>'Non défini',1=>'Faible',2=>'Moyenne',3=>'Elevée'),
		"gravity"=>array(0=>'Non défini',1=>'Faible',2=>'Moyenne',3=>'Elevée'),
	);

	protected $color=array(
		"status"=>array("new"=>"red","inprg"=>"orange","close"=>"green","cancel"=>"gray-light"),
		"risk"=>array("0"=>"gray-light","1"=>"green","2"=>"green","3"=>"orange","4"=>"orange","6"=>"red","9"=>"red"),
	);

	function save()
	{
		$this->data["risk"]=$this->data["gravity"]*$this->data["probability"];
		return parent::save();
	}

	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);
		$sql=$this->sql;

		if ($key=="risk")
		{
			// Variables
			$tabProb=array();
			$tabGrav=array();
			$tabBg=array();

			$tabProb[1]="Faible";
			$tabProb[2]="Moyenne";
			$tabProb[3]="Elevé";

			$tabGrav[1]="Faible";
			$tabGrav[2]="Moyenne";
			$tabGrav[3]="Elevée";

			$tabBg["0"]="#888888";
			$tabBg["1"]="#00ff00"; $tabBg["2"]="#00ff00"; $tabBg["3"]="#ffc529";
			$tabBg["2"]="#00ff00"; $tabBg["4"]="#ffc529"; $tabBg["6"]="#ff0000";
			$tabBg["3"]="#ffc529"; $tabBg["6"]="#ff0000"; $tabBg["9"]="#ff0000";

			if ($render=="form")
			{
				$render="html";
			}
		}

		if ($render=="list")
		{
			$txt=$this->val($key);
			if ($key=="risk")
			{
				$risk=$this->data["probability"]*$this->data["gravity"];
				$ret="<div class='tagsinput'><span class='tag ".((isset($this->color[$key][$risk])) ? "bg-".$this->color[$key][$risk] : "")."'>".$risk."</span></div>";
			}
		}
		else
		{
			if ($key=="risk")
			{
				$ret ="<style>.risqueREX th { width:140px; } td { width:140px; text-align: center; border: 1px solid #000000; font-weight: bold; }</style>";

				$ret.="<table class='risqueREX' style='margin-top:35px;'>";
				$ret.="<tr><th></th>";
				foreach($tabGrav as $i=>$d)
				{
					$ret.="<th>".$d."</th>";
				}
				$ret.="</tr>";

				$risk=$this->data["probability"].$this->data["gravity"];

				foreach($tabProb as $l=>$ln)
				{
					$ret.="<tr><th>".$ln."</th>";
					foreach($tabGrav as $c=>$cn)
					{
						$ret.="<td style='background-color:".$tabBg[$l*$c].";'>".(($risk==$l.$c) ? "<i class='mdi mdi-checkbox-blank-circle'></i>" : "")."</td>";
					}
					$ret.="</tr>";
				}

				$ret.="</table>";
			}
		}


		return $ret;
	}
}


function ListEvents($sql,$fields=array())
{ global $MyOpt;

	$f=implode(",",$fields);
	$lst=array();
 
	$query="SELECT id".((count($fields)>0) ? ",".$f : "")." FROM ".$MyOpt["tbl"]."_evenement WHERE actif='oui'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$lst[$i]["id"]=$sql->data["id"];
		if (count($fields)>0)
		{
			foreach ($fields as $f)
			{
				$lst[$i][$f]=$sql->data[$f];
			}
		}
	  }
	return $lst;
}

  
?>