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

	protected $droit=array("num"=>"[readonly]", "paye"=>"ModifBaptemePaye","dte_paye"=>"ModifBaptemePaye");
	// protected $type=array("nom"=>"varchar","telephone"=>"tel","mail"=>"email","dte"=>"datetime","nb"=>"enum","type"=>"enum","status"=>"enum","paye"=>"bool","dte_paye"=>"date","description"=>"text");

	protected $fields=array(
		"num" => Array("type" => "varchar", "len"=>20 ),
		"nom" => Array("type" => "varchar", "len"=>50 ),
		"passager" => Array("type" => "varchar", "len"=>50 ),
		"telephone" => Array("type" => "varchar", "len"=>14 ),
		"mail" => Array("type" => "varchar", "len"=>100 ),
		"nb" => Array("type" => "radio", "default"=>1),
		"dte" => Array("type" => "datetime"),
		"actif" => Array("type" => "bool", "default" => "oui", "index"=>1),
		"status" => Array("type" => "enum", "default"=>"0", "index"=>1 ),
		"type" => Array("type" => "enum", "default" => "btm", ),
		"bonkdo" => Array("type" => "bool", "default" => "non", ),
		"paye" => Array("type" => "bool", "default" => "non", ),
		"dte_paye" => Array("type" => "date" ),
		"id_pilote" => Array("type" => "number", "index" => "1", ),
		"id_avion" => Array("type" => "number", "index" => "1", ),
		"id_resa" => Array("type" => "number", "index"=>1),
		"description" => Array("type" => "text" ),
	);

	
	protected $tabList=array(
		"status"=>array("0"=>"Nouveau","1"=>"Analyse","2"=>"A affecter","3"=>"Affect�","4"=>"Planifi�","5"=>"Effectu�","6"=>"Annul�"),
		"nb"=>array("1"=>"1","2"=>"2","3"=>"3"),
		"type"=>array("btm"=>"Bapt�me","vi"=>"VI"),
		"nb"=>array(
			"fr"=>array("1"=>"1","2"=>"2","3"=>"3"),
			"en"=>array("1"=>"1","2"=>"2","3"=>"3")
		)
	);

			# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		global $gl_uid;

		parent::__construct($id,$sql);

		if ($this->data["num"]=="")
		{
			$res["num"]="xxxxxx";
			while($res["num"]!="")
			{
				$this->data["num"]=mt_rand(100000, 999999);
			
				$query="SELECT num FROM ".$MyOpt["tbl"]."_bapteme WHERE num='".$this->data["num"]."'";
				$res=$sql->QueryRow($query);
			}
		}
	}

	function ListeStatus()
	{
		return $this->tabList["status"];
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
	  	$st="status<>'5' AND status<>'6'";
	  }
	else if ($status==-1)
	  {
	  	$st="1=1";
	  }
	else
	  {
	  	$st="status='".$status."'";
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

?>
