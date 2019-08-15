<?
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

// Class Synthese
class synthese_class extends objet_core
{
	protected $table="synthese";
	protected $mod="reservations";
	protected $rub="synthese";

	// protected $droit=array("status"=>"ModifRexStatus","planaction"=>"ModifRexSynthese","synthese"=>"ModifRexSynthese");
	// protected $type=array(
		// "status" => "enum",
		// "nbvol"=>"number",
		// "type" => "enum",
		// "phase" => "radio",
		// "nature" => "enum",
		// "remtech" => "text",
		// "remnotech" => "text",
		// "menace" => "text",
		// "erreur" => "text",
		// "remnotech" => "text",
		// "travail" => "text",
		// "nbatt" => "number",
		// "sdte_pilote" => "datetime",
		// "sdte_instructeur" => "datetime"
	// );

	protected $fields = Array	(
		"idvol" => Array("type" => "number", "default" => "0", "index" => "1" ),
		"status" => Array("type" => "enum","index"=>1 ),
		"uid_pilote" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_instructeur" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_avion" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"type" => Array("type" => "enum", "default"=>"double"),
		"phase" => Array("type" => "radio","default"=>"perfectionnement"),
		// "nature" => Array("type" => "enum","default"=>"maniabilite"),
		"lecon" => Array("type" => "varchar","len"=>6),
		"remtech" => Array("type" => "text"),
		"remnotech" => Array("type" => "text"),
		"menace" => Array("type" => "text"),
		"erreur" => Array("type" => "text"),
		"remnotech" => Array("type" => "text"),
		"travail" => Array("type" => "text"),
		"nbvol" => Array("type" => "number", "default"=>0),
		"nbatt" => Array("type" => "number", "default"=>1),
		"sid_pilote" => Array("type" => "number", "default" => 0),
		"sdte_pilote" => Array("type" => "datetime"),
		"skey_pilote" => Array("type" => "varchar","len"=>64),
		"sid_instructeur" => Array("type" => "number", "default" => 0),
		"sdte_instructeur" => Array("type" => "datetime"),
		"skey_instructeur" => Array("type" => "varchar","len"=>64),
	);

	
	protected $tabList=array(
		"status"=>array(
			"fr"=>array('edit'=>'Rédaction','signed'=>'Signé','cancel'=>'Annulé'),
			"en"=>array('edit'=>'Edit','signed'=>'Signed','cancel'=>'Canceled'),
		),
		"type" =>array(
			"fr"=>array('double'=>'Double','solo'=>'Solo'),	
			"en"=>array('double'=>'Double','solo'=>'Solo'),
		),
		"phase" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','perfectionnement'=>'Perfectionnement'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','perfectionnement'=>'Perfectionnement')
		),
		"nature" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','autre'=>'Autre'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','autre'=>'Autre')
		),
	);

	# Constructor
	function __construct($id=0,$sql)
	{
		global $gl_uid;

		$this->data["idvol"]=0;
		$this->data["status"]="edit";
		$this->data["uid_pilote"]=0;
		$this->data["uid_instructeur"]=0;
		$this->data["uid_avion"]=0;
		$this->data["type"]="double";
		$this->data["phase"]="";
		$this->data["nature"] ="";
		$this->data["lecon"] = "";
		$this->data["remtech"] = "";
		$this->data["remnotech"] ="";
		$this->data["menace"] ="";
		$this->data["erreur"] ="";
		$this->data["remnotech"] ="";
		$this->data["travail"] ="";
		$this->data["nbvol"] ="0";
		$this->data["nbatt"] ="1";
		$this->data["sid_pilote"] =0;
		$this->data["sdte_pilote"] ="0000-00-00 00:00:00";
		$this->data["skey_pilote"] ="";
		$this->data["sid_instructeur"] =0;
		$this->data["sdte_instructeur"] ="0000-00-00 00:00:00";
		$this->data["skey_instructeur"] ="";
		$this->data["actif"] = "oui";

		parent::__construct($id,$sql);
	}	
	
}



function ListSyntheseVol($sql,$idvol)
{
	return ListeObjets($sql,"synthese",array("id","nbvol","type"),array("actif"=>"oui","idvol"=>$idvol));
}
function ListMySynthese($sql,$uid)
{
	return ListeObjets($sql,"synthese",array("id"),array("actif"=>"oui","uid_pilote"=>$uid));
}

  
?>