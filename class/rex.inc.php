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

$tabValeurRex=array();
$tabValeurRex["new"]="Nouveau";
$tabValeurRex["inprg"]="En cours";
$tabValeurRex["close"]="Cloturé";
$tabValeurRex["cancel"]="Annulé";


// Class Utilisateur
class rex_class extends objet_core
{
	protected $table="rex";
	protected $mod="aviation";
	protected $rub="rexdetail";

	protected $droit=array("status"=>"ModifRexStatus","planaction"=>"ModifRexSynthese","synthese"=>"ModifRexSynthese");
	// protected $type=array("titre"=>"varchar","status"=>"enum","description"=>"text","commentaire"=>"text","synthese"=>"text","planaction"=>"text","dte_rex"=>"date");
	protected $fields=array(
		"titre" => Array("type" => "varchar", "len"=>40 ),
		"status" => Array("type" => "enum","default"=>"new","index"=>1 ),
		"description" => Array("type" => "text" ),
		"commentaire" => Array("type" => "text" ),
		"synthese" => Array("type" => "text" ),
		"planaction" => Array("type" => "text" ),
		"categorie" => Array("type" => "varchar", "len"=>30 ),
		"nature" => Array("type" => "varchar", "len"=>30 ),
		"mto" => Array("type" => "varchar", "len"=>30 ),
		"environnement" => Array("type" => "varchar","len"=>30 ),
		"phase" => Array("type" => "varchar","len"=>30 ),
		"typevol" => Array("type" => "varchar","len"=>30 ),
		"typeevt" => Array("type" => "varchar","len"=>30),
		"uid_avion" => Array("type" => "number", "Index"=>1 ),
		"risque" => Array("type" => "varchar","len"=>2 ),
		"dte_rex" => Array("type" => "date", "default" => "0000-00-00" ),
	);

	protected $tabList=array(
		"status"=>array('new'=>'Nouveau','inprg'=>'En cours','close'=>'Cloturé','cancel'=>'Annulé'),
	);

	# Constructor
	function __construct($id=0,$sql="")
	{
		global $gl_uid;
		
		$this->data["titre"] = "";
		$this->data["status"] = "new";
		$this->data["description"] = "";
		$this->data["commentaire"] = "";
		$this->data["synthese"] = "";
		$this->data["planaction"] = "";
		$this->data["categorie"] = "";
		$this->data["nature"] = "";
		$this->data["mto"] = "";
		$this->data["environnement"] = "";
		$this->data["phase"] = "";
		$this->data["typevol"] = "";
		$this->data["typeevt"] = "";
		$this->data["uid_avion"] = 0;
		$this->data["risque"] = "";
		$this->data["dte_rex"] = date("Y-m-d");
		$this->data["actif"] = "oui";

		parent::__construct($id,$sql);
	}	

	
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);
		$sql=$this->sql;

		// Variables
		$tabCol=array();
		$tabLig=array();
		$tabBg=array();

		$tabCol[1]="Très Probable";
		$tabCol[2]="Probable";
		$tabCol[3]="Très improbable";
		$tabCol[4]="Improbable";
		$tabCol[5]="Non défini";

		$tabLig[1]="Accident matériel et corporel";
		$tabLig[2]="Accident corporel";
		$tabLig[3]="Accident matériel";
		$tabLig[4]="Incident grave";
		$tabLig[5]="Incident";
		$tabLig[6]="Annomalie";
		$tabLig[7]="Non défini";

		$tabBg["11"]="#ff0000"; $tabBg["12"]="#ff0000"; $tabBg["13"]="#ff0000"; $tabBg["14"]="#ffff00"; $tabBg["15"]="#ffffff";
		$tabBg["21"]="#ff0000"; $tabBg["22"]="#ff0000"; $tabBg["23"]="#ff0000"; $tabBg["24"]="#ffff00"; $tabBg["25"]="#ffffff";
		$tabBg["31"]="#ff0000"; $tabBg["32"]="#ff0000"; $tabBg["33"]="#ffc529"; $tabBg["34"]="#ffff00"; $tabBg["35"]="#ffffff";
		$tabBg["41"]="#ff0000"; $tabBg["42"]="#ffc529"; $tabBg["43"]="#ffff00"; $tabBg["44"]="#00ff00"; $tabBg["45"]="#ffffff";
		$tabBg["51"]="#ffc529"; $tabBg["52"]="#ffff00"; $tabBg["53"]="#ffff00"; $tabBg["54"]="#00ff00"; $tabBg["55"]="#ffffff";
		$tabBg["61"]="#ffff00"; $tabBg["62"]="#00ff00"; $tabBg["63"]="#00ff00"; $tabBg["64"]="#00ff00"; $tabBg["65"]="#ffffff";
		$tabBg["71"]="#ffffff"; $tabBg["72"]="#ffffff"; $tabBg["73"]="#ffffff"; $tabBg["74"]="#ffffff"; $tabBg["75"]="#ffffff";

		if ($render=="form")
		{
			if ($key=="uid_avion")
		  	{
				$ret="<select id='".$key."' class='form-control' name=\"".$formname."[$key]\">";
				$lstress=ListeRessources($this->sql,array("oui"));

				foreach($lstress as $i=>$rid)
				{
					$resr=new ress_class($rid,$this->sql);
					$ret.="<option value='".$rid."' ".(($this->data["uid_avion"]==$rid) ? "selected" : "").">".$resr->val("immatriculation")."</option>";
				}
				$ret.="</select>";
			}
			else if ($key=="risque")
			{
				$ret ="<style>.risqueREX td { width:140px; text-align: center; border: 1px solid #000000; font-weight: bold; }</style>";

				$ret.="<table class='risqueREX'>";
				$ret.="<tr><th></th>";
				foreach($tabCol as $i=>$d)
				{
					$ret.="<th>".$d."</th>";
				}
				$ret.="</tr>";

				foreach($tabLig as $l=>$ln)
				{
					$ret.="<tr><th>".$ln."</th>";
					foreach($tabCol as $c=>$cn)
					{
						$ret.="<td style='background-color:".$tabBg[$l.$c].";'>"."<input type='radio' name='".$formname."[$key]' value='".$l.$c."' ".(($this->data[$key]==$l.$c) ? "checked='checked'" : "")."></td>";
					}
					$ret.="</tr>";
				}

				$ret.="</table>";
			}
		}
		else
		{
			if ($key=="uid_avion")
			{
				$resr=new ress_class($this->data["uid_avion"],$sql);
				$ret=$resr->aff("immatriculation");
			}
			else if ($key=="risque")
			{
				$ret ="<style>.risqueREX td { width:140px; text-align: center; border: 1px solid #000000; font-weight: bold; }</style>";

				$ret.="<table class='risqueREX'>";
				$ret.="<tr><th></th>";
				foreach($tabCol as $i=>$d)
				{
					$ret.="<th>".$d."</th>";
				}
				$ret.="</tr>";

				foreach($tabLig as $l=>$ln)
				{
					$ret.="<tr><th>".$ln."</th>";
					foreach($tabCol as $c=>$cn)
					{
						$ret.="<td style='background-color:".$tabBg[$l.$c].";'>".(($this->data[$key]==$l.$c) ? "x" : "")."</td>";
					}
					$ret.="</tr>";
				}

				$ret.="</table>";
			}
		}
	
		return $ret;
	}
}



function ListRex($sql,$fields=array())
{ global $MyOpt;

	$f=implode(",",$fields);
	$lst=array();
 
	$query="SELECT id".((count($fields)>0) ? ",".$f : "")." FROM ".$MyOpt["tbl"]."_rex WHERE actif='oui'";
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