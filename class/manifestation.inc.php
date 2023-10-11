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


// Class Manifestation
class manip_class extends objet_core
{
	protected $table="manips";
	protected $mod="manifestations";
	protected $rub="detail";

	// protected $type=array("titre"=>"varchar","dte_manip"=>"date","dte_limite"=>"date","facture"=>"bool","comment"=>"text","type"=>"multi","cout"=>"price");
	
	protected $fields=array(
		"titre" => Array("type" => "varchar", "len"=>100, "default"=>"Nouvelle manifestation"),
		"comment" => Array("type" => "text"),
		"type" => Array("type" => "multi", "len"=>100, ),
		"cout" => Array("type" => "price", "default" => "0.00", ),
		"facture" => Array("type" => "bool", "default" => "non", ),
		"actif" => Array("type" => "bool", "default" => "oui", "index"=>1),
		"dte_manip" => Array("type" => "date", "default"=>"now"),
		"dte_limite" => Array("type" => "date", "default"=>"0000-00-00"),
		"test" => Array("type" => "date", "default"=>"0000-00-00")
	);
	
	protected $tabList=array(
		"type"=>array(),
	);
	
	# Constructor
	function __construct($id=0,$sql="")
	{
		global $MyOpt;

		$query="SELECT id,groupe,description FROM ".$MyOpt["tbl"]."_groupe WHERE principale='oui' ORDER BY description";
		$sql->Query($query);

		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			if ($sql->data["groupe"]!="SYS")
			{
				$this->tabList["type"]["fr"][strtolower($sql->data["groupe"])]=$sql->data["description"];
				$this->tabList["type"]["en"][strtolower($sql->data["groupe"])]=$sql->data["description"];
			}
		}
		
		// $this->data["dte_manip"]=date("Y-m-d");
	
		parent::__construct($id,$sql);
	}

}



function GetActiveManips($sql,$ress,$jour="")
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_manips WHERE actif='oui' AND dte_manip='$jour'";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	if (($jour!="") && (count($res)>0))
	  { return 1; }
	else if ($jour!="")
	  { return 0; }
	else
	  { return $res; }
}

function GetManifestation($sql,$start,$end)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_manips WHERE actif='oui' AND dte_manip>='$start' AND dte_manip<='$end'";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	}

	return $res;
}

?>
