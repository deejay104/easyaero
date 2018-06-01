<?
/*
    Easy-Aero v3.0
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

class bapteme_class extends objet_core
{
	protected $table="bapteme";
	protected $mod="aviation";
	protected $rub="bapteme";

	protected $droit=array();
	protected $type=array("nom"=>"varchar","telephone"=>"tel","mail"=>"email","dte"=>"datetime","nb"=>"enum","type"=>"enum","status"=>"enum","paye"=>"bool","description"=>"text");

	
	protected $tabList=array(
		"status"=>array("0"=>"Nouveau","1"=>"Affecté","2"=>"Planifié","3"=>"Effectué","4"=>"Annulé"),
		"nb"=>array("1"=>"1","2"=>"2","3"=>"3"),
		"type"=>array("btm"=>"Baptème","vi"=>"VI")
	);

			# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		global $gl_uid;

		$this->data["num"]="";
		$this->data["nom"]="";
		$this->data["telephone"]="";
		$this->data["mail"]="";
		$this->data["nb"]=0;
		$this->data["dte"]="";
		$this->data["status"]="0";
		$this->data["type"]="btm";
		$this->data["paye"]="non";
		$this->data["id_pilote"]="0";
		$this->data["id_avion"]="0";
		$this->data["id_resa"]="0";
		$this->data["description"]="";

		parent::__construct($id,$sql);
	}

}



function ListeBaptemes($sql,$actif=array("oui"),$status)
{ global $MyOpt;
	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }

	if (!is_numeric($status))
	  { $status=-2; }
	if ($status==-2)
	  {
	  	$st="status<>4 AND status<>3";
	  }
	else if ($status==-1)
	  {
	  	$st="1";
	  }
	else
	  {
	  	$st="status=".$status;
	  }

	$query = "SELECT id FROM ".$MyOpt["tbl"]."_bapteme WHERE ($txt) AND ($st)";
	$sql->Query($query);
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }
	return $res;
}

function ListeStatus()
{
	$tabStatus=array();
	$tabStatus[0]="Nouveau";
	$tabStatus[1]="Affecté";
	$tabStatus[2]="Planifié";
	$tabStatus[3]="Effectué";
	$tabStatus[4]="Annulé";

	return $tabStatus;
}

?>
