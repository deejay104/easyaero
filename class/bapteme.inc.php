<?php
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

	protected $droit=array("paye"=>"ModifBaptemePaye","dte_paye"=>"ModifBaptemePaye");
	// protected $type=array("nom"=>"varchar","telephone"=>"tel","mail"=>"email","dte"=>"datetime","nb"=>"enum","type"=>"enum","status"=>"enum","paye"=>"bool","dte_paye"=>"date","description"=>"text");

	protected $fields=array(
		"num" => Array("type" => "varchar", "len"=>20,"readonly"=>1 ),
		"nom" => Array("type" => "varchar", "len"=>50 ),
		"passager" => Array("type" => "varchar", "len"=>50 ),
		"telephone" => Array("type" => "tel" ),
		"mail" => Array("type" => "email" ),
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
		"status"=>array("0"=>"Nouveau","1"=>"Contacté","2"=>"A affecter","3"=>"Affecté","4"=>"Planifié","5"=>"Effectué","6"=>"Annulé","7"=>"Bon envoyé"),
		"type"=>array("btm"=>"Baptème","vi"=>"VI"),
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

			while((isset($res["num"])) && ($res["num"]!=""))
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



function ListeBaptemes($sql,$actif=array("oui"),$status=-2,$type="",$search="",$order=array(),$ts=0,$tl=0)
{ global $MyOpt;
	$txt="1=0";
	foreach($actif as $a)
	{
	  	$txt.=" OR actif='$a'";
	}

	if (!is_numeric($status))
	{
		$status=-2;
	}

	if (($status==-2) && (GetDroit("ModifBapteme")))
	{
	  	$st="status<>'5' AND status<>'6' ";
	}
	else if ($status==-2)
	{
	  	$st="status<>'0' AND status<>'1' AND status<>'5' AND status<>'6' ";
	}
	else if ($status==-1)
	{
	  	$st="1=1 ";
	}
	else
	{
	  	$st="status='".$status."' ";
	}

	if ($type!="")
	{
		$st.="AND type='".$type."' ";
	}

	$c="";
	if ($search!="")
	{
		$c.="AND (";
		$c.="num LIKE '%".$search."%'";
		$c.="OR nom LIKE '%".$search."%'";
		$c.="OR passager LIKE '%".$search."%'";
		$c.="OR telephone LIKE '%".$search."%'";
		$c.="OR mail LIKE '%".$search."%'";
		$c.="OR dte LIKE '%".$search."%'";
		$c.="OR mail LIKE '%".$search."%'";
		$c.="OR status LIKE '%".$search."%'";
		$c.="OR description LIKE '%".$search."%'";
		$c.=")";
	}

	$query = "SELECT id FROM ".$MyOpt["tbl"]."_bapteme WHERE ($txt) AND ($st) ".$c;

	if ((isset($order)) && (count($order)>0))
	{
		$query.=" ORDER BY ".$order["name"]." ".$order["dir"].",id";
	}

	if (($ts>0) || ($tl>0))
	{
		$query.=" LIMIT ".$ts.",".$tl;
	}

	$sql->Query($query);
// echo $query;
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	}

	return $res;
}

function TotalBaptemes($sql,$actif=array("oui"),$status=-2,$crit="")
{ global $MyOpt;
	$txt="1=0";
	foreach($actif as $a)
	{
	  	$txt.=" OR actif='$a'";
	}

	if (!is_numeric($status))
	{
		$status=-2;
	}

	if (($status==-2) && (GetDroit("ModifBapteme")))
	{
	  	$st="status<>'5' AND status<>'6'";
	}
	else if ($status==-2)
	{
	  	$st="status<>'0' AND status<>'1' AND status<>'5' AND status<>'6'";
	}
	else if ($status==-1)
	{
	  	$st="1=1";
	}
	else
	{
	  	$st="status='".$status."'";
	}

	$c="";
	if ($crit!="")
	{
		$c.="AND (";
		$c.="num LIKE '%".$crit."%'";
		$c.="OR nom LIKE '%".$crit."%'";
		$c.="OR passager LIKE '%".$crit."%'";
		$c.="OR telephone LIKE '%".$crit."%'";
		$c.="OR mail LIKE '%".$crit."%'";
		$c.="OR dte LIKE '%".$crit."%'";
		$c.="OR mail LIKE '%".$crit."%'";
		$c.="OR status LIKE '%".$crit."%'";
		$c.="OR description LIKE '%".$crit."%'";
		$c.=")";
	}

	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_bapteme WHERE ($txt) AND ($st) ".$c;
	$res=$sql->QueryRow($query);

	return $res["nb"];

}

?>
