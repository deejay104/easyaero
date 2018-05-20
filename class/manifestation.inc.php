<?
/*
    SoceIt v2.0
    Copyright (C) 2005 Matthieu Isorez

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

// Class Manifestation
class manip_class extends objet_core
{
	protected $table="manips";
	protected $mod="manifestations";
	protected $rub="detail";

	protected $type=array("dte_manip"=>"date","dte_limite"=>"date","facture"=>"bool");

	# Constructor
	function __construct($id=0,$sql)
	{
		$this->data["titre"]="Nouvelle manifestation";
		$this->data["comment"]="";
		$this->data["type"]="";
		$this->data["cout"]="0";
		$this->data["facture"]="non";
		$this->data["dte_manip"]=date("Y-m-d");
		$this->data["dte_limite"]="0000-00-00";
	
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
