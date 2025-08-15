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

// Class Reservation
class navpoint_class extends objet_core
{
	protected $table="navpoints";
	protected $mod="navigation";
	protected $rub="";

	protected $fields=array(
		"nom"=>array("type"=>"varchar","index"=>1,"len"=>20),
		"description"=>array("type"=>"varchar","len"=>200),
		"lat"=>array("type"=>"varchar","len"=>10),
		"lon"=>array("type"=>"varchar","len"=>10),
		"icone"=>array("type"=>"enum","len"=>20),
		"taxe"=>array("type"=>"price","default"=>0),
		"uid_creat"=>array("type"=>"number"),
		"dte_creat"=>array("type"=>"datetime"),
		"uid_maj"=>array("type"=>"number"),
		"dte_maj"=>array("type"=>"datetime"),
	);
	protected $tabList=array(
		"icone"=>array("Airport"=>"Aéroport","Waypoint"=>"Point de passage","Navaid"=>"Point personalisé"),
	);

}

function ListWaypoints($sql)
{
	return ListeObjets($sql,"navpoints",array("id"),array());
}

class terrain_class extends objet_core
{
	protected $table="terrain";
	protected $mod="navigation";
	protected $rub="";

	protected $fields=array(
		"nom"=>array("type"=>"varchar","index"=>1,"len"=>20),
		"idresa"=>array("type"=>"number","index"=>1),
		"idsynthese"=>array("type"=>"number","index"=>1),
		"type"=>array("type"=>"enum","default"=>"complet"),
	);
	protected $tabList=array(
		"type"=>array("complet"=>"Complet","touche"=>"Touché"),
	);

}

function listeTerrain($idresa,$idsynthese)
{
	global $MyOpt,$sql;
	
	$q ="SELECT id,nom FROM ".$MyOpt["tbl"]."_terrain ";
	$q.="WHERE actif='oui' AND idresa='".$idresa."' AND idsynthese='".$idsynthese."'";
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]=$sql->data["nom"];
	}
	return $lst;
}