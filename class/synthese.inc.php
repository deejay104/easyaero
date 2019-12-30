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
	protected $mod="aviation";
	protected $rub="synthese";

	protected $fields = Array(
		"idvol" => Array("type" => "number", "default" => "0", "index" => "1" ),
		"status" => Array("type" => "enum","index"=>1, "default" => "edit"),
		"type" => Array("type" => "radio","index"=>1, "default" => "dc"),
		"uid_pilote" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_instructeur" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_avion" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"dte_vol" => Array("type" => "datetime", ),
		"module" => Array("type" => "enum","default"=>"maniabilite"),
		"refffa" => Array("type" => "uppercase","len"=>10),

		"themes" => Array("type" => "text"),
		"bilan_gen" => Array("type" => "text"),

		"mto" => Array("type" => "varchar","len"=>50),

		"info_1" => Array("type" => "radio", "default" => "NA"),
		"info_2" => Array("type" => "radio", "default" => "NA"),
		"info_3" => Array("type" => "radio", "default" => "NA"),
		"info_4" => Array("type" => "radio", "default" => "NA"),
		"info_5" => Array("type" => "radio", "default" => "NA"),

		"nb_att" => Array("type" => "number", "default" => "1"),
		"nb_rmg" => Array("type" => "number", "default" => "0"),

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
			"fr"=>array('dc'=>'Double Commande','solo'=>'Solo'),	
			"en"=>array('dc'=>'Double Commande','solo'=>'Solo'),
		),
		"module" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation','panne'=>'Panne'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation','panne'=>'Panne')
		),
		"info_1" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
		),
		"info_2" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
		),
		"info_3" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
		),
		"info_4" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
		),
		"info_5" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
		),
	);

	# Constructor
	function __construct($id=0,$sql)
	{
		global $gl_uid;

		$this->data["idvol"]=0;
		$this->data["status"]="edit";

		parent::__construct($id,$sql);
	}	
	
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		if ($this->id>0)
		{
			if ($key=="refffa")
			{
				$render="html";
				$typeaff="html";
			}
			else if ($key=="module")
			{
				$render="html";
				$typeaff="html";
			}
		}

		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);

		if (($key=="refffa") && ($typeaff=="form"))
		{
			$txt=$this->val($key);
			$t=ListeObjets($this->sql,"reference",array("id","refffa"),array("actif"=>"oui"),array("refffa"));
			
			$ret ="<select id='".(($formid!="") ? $formid : "").$key."'  name=\"".$formname."[$key]\">";
				$ret.="<option value=\"\" ".(($txt=="") ? "selected" : "").">Aucun</option>";
			foreach($t as $k=>$v)
			{
				$ret.="<option value=\"".$v["refffa"]."\" ".(($txt==$v["refffa"]) ? "selected" : "").">".$v["refffa"]."</option>";
			}
			$ret.="</select>";			
		}
		
		return $ret;
	}
	
	function NbAtt()
	{
		if ($this->id==0)
		{
			return 0;
		}
		$sql=$this->sql;
		$query="SELECT SUM(nb_att) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND id<'".$this->id."'";

		$sql=$this->sql;
		$res=$sql->QueryRow($query);
		
		return $res["nb"];
	}
	function NbRmg()
	{
		if ($this->id==0)
		{
			return 0;
		}
		$sql=$this->sql;
		$query="SELECT SUM(nb_rmg) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND id<'".$this->id."'";

		$sql=$this->sql;
		$res=$sql->QueryRow($query);
		
		return $res["nb"];
	}
	
}



function ListSyntheseVol($sql,$idvol)
{
	return ListeObjets($sql,"synthese",array("id","module","refffa","progression"),array("actif"=>"oui","idvol"=>$idvol));
}
function ListMySynthese($sql,$uid)
{
	return ListeObjets($sql,"synthese",array("id"),array("actif"=>"oui","uid_pilote"=>$uid));
}






class exercice_conf_class extends objet_core
{
	protected $table="exercice_conf";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array(
		"description" => array("type"=>"varchar","len"=>100, "formlen"=>400),
		"competence" => array("type"=>"varchar","len"=>100, "formlen"=>400),
		"type" => Array("type" => "enum","default"=>"peda"),
		"module" => Array("type" => "enum","default"=>"maniabilite"),
		"refffa" => Array("type" => "varchar","len"=>10,"formlen"=>100),
		"refenac" => Array("type" => "number"),
	);
	
	protected $tabList=array(
		"module" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation')
		),
		"type" => array(
			"fr"=>Array('peda'=>'Pédagogique','exercice'=>'Exercice','panne'=>'Panne'),
			"fr"=>Array('peda'=>'Pédagogique','exercice'=>'Exercice','panne'=>'Panne'),
		)
	);
}

function ListExercicesConf($sql,$s=array("actif"=>"oui"),$order=array("refffa","id"))
{
	return ListeObjets($sql,"exercice_conf",array("id"),$s,$order);
}

class exercice_prog_class extends objet_core
{
	protected $table="exercice_prog";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array(
		"idexercice" => Array("type"=>"number", "index"=>1),
		"refffa" => Array("type" => "varchar","len"=>10, "formlen"=>100),
		"progression" => Array("type" => "radio","default"=>"A"),
	);
	
	protected $tabList=array(
		"progression" =>array(
			"fr"=>array('E'=>'Etude','A'=>'Acquis'),	
			"en"=>array('E'=>'Etude','A'=>'Acquis'),
		),
	);
}

// Liste les progressions pour un exercice
function ListProgression($sql,$id)
{
	return ListeObjets($sql,"exercice_prog",array("id"),array("actif"=>"oui","idexercice"=>$id),array("refffa","id"));
}


class exercice_class extends objet_core
{
	protected $table="exercice";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array	(
		"idsynthese" => array("type"=>"number", "default" => "0", "index" => "1" ),
		"idexercice" => array("type"=>"number", "default" => "0", "index" => "1" ),
		"uid" => array("type"=>"number", "default" => "0", "index" => "1" ),
		"progression" => Array("type" => "radio","default"=>"A"),
		"progref" => Array("type" => "enum","default"=>"A"),
	);

	
	protected $tabList=array(
		"progression" =>array(
			"fr"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),	
			"en"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),
		),
		"progref" =>array(
			"fr"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),	
			"en"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),
		),
	);
}

function ListExercices($sql,$id)
{
	return ListeObjets($sql,"exercice",array("idexercice","progression","progref"),array("actif"=>"oui","idsynthese"=>$id),array("progref DESC","id"));
}

// Liste des exercices non aquis pour un élève
function ListExercicesNonAcquis($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT idexercice AS id,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref ";

	$q ="SELECT idexercice AS id FROM ".$MyOpt["tbl"]."_exercice AS exo ";
	$q.="WHERE uid='".$uid."' AND (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.progression='A')=0 AND (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.progref='A')=1 ";
	$q.="GROUP BY idexercice";
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]=$sql->data;
	}
	return $lst;
}

// Liste des exercices avec la progression pour un élève
function ListExercicesProg($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT id,(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice) AS dte_acquis,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref ";
	$q.="FROM ".$MyOpt["tbl"]."_exercice_conf AS exo ";
	// $q.="WHERE uid='".$uid."' ";
//	$q.="GROUP BY idexercice";
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]=$sql->data;
	}
	return $lst;
}


class reference_class extends objet_core
{
	protected $table="reference";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array(
		"refffa" => Array("type" => "varchar","len"=>10, "formlen"=>100),
		"theme" => Array("type" => "text"),
	);
	
	protected $tabList=array(
		"progression" =>array(
			"fr"=>array('E'=>'Etude','A'=>'Acquis'),	
			"en"=>array('E'=>'Etude','A'=>'Acquis'),
		),
	);
}
function ListReference($sql)
{
	return ListeObjets($sql,"reference",array("id"),array("actif"=>"oui"));
}

  
?>