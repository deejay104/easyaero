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

// Class Synthese
class synthese_class extends objet_core
{
	protected $table="synthese";
	protected $mod="aviation";
	protected $rub="synthese";

	protected $fields = Array(
		"idvol" => Array("type" => "number", "default" => "0", "index" => "1" ),
		"status" => Array("type" => "enum","index"=>1, "default" => "edit"),
		"conclusion" => Array("type" => "enum","index"=>1, "default" => "ok"),
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
		"conclusion"=>array(
			"fr"=>array('ok'=>'Validé','nok'=>'Non Validé'),
			"en"=>array('ok'=>'Validé','nok'=>'Non Validé'),
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
	return ListeObjets($sql,"synthese",array("id","module","refffa"),array("actif"=>"oui","idvol"=>$idvol));
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

// Liste des exercices avec la progression pour un membre
function ListExercicesProg($sql,$uid,$type="")
{
	global $MyOpt;

	// $q.="(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."') AS dte_acquis,";
	// $q.="IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,";
	// $q.="IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref,";
	// $q.="(SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progref='A') AS nb ";

	
	$q ="SELECT id FROM ".$MyOpt["tbl"]."_exercice_conf AS exo ";
	if ($type!="")
	{
		$q.="WHERE exo.type='".$type."' ";
	}
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]["id"]=$sql->data["id"];
		$lst[$sql->data["id"]]["idsynthese"]=0;
		$lst[$sql->data["id"]]["dte_acquis"]="";
		$lst[$sql->data["id"]]["progression"]="";
		$lst[$sql->data["id"]]["progref"]="";
		$lst[$sql->data["id"]]["nb"]=0;
	}

	foreach($lst as $id=>$d)
	{
		$q="SELECT id,idsynthese,progression,progref FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE prog.idexercice='".$id."' AND prog.actif='oui' AND prog.uid='".$uid."'";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			
			if ($sql->data["progression"]=="A")
			{
				$lst[$id]["idsynthese"]=$sql->data["idsynthese"];
				$lst[$id]["progression"]="A";
				$lst[$id]["nb"]=$lst[$id]["nb"]+1;
			}
			if ($sql->data["progref"]=="A")
			{
				$lst[$id]["progref"]="A";
			}
			else if (($sql->data["progref"]=="E") && ($d["progref"]!="A"))
			{
				$lst[$id]["progref"]="E";
			}
			if ($lst[$id]["idsynthese"]==0)
			{
				$lst[$id]["idsynthese"]=$sql->data["idsynthese"];
			}
		}
	}

	foreach($lst as $id=>$d)
	{
		if ($d["idsynthese"]>0)
		{
			$q="SELECT synthese.dte_vol,cal.dte_deb FROM ".$MyOpt["tbl"]."_synthese AS synthese LEFT JOIN ".$MyOpt["tbl"]."_calendrier AS cal ON synthese.idvol=cal.id WHERE synthese.id='".$d["idsynthese"]."'";
			$res=$sql->QueryRow($q);

			if ($res["dte_vol"]!="0000-00-00 00:00:00")
			{
				$lst[$id]["dte_acquis"]=$res["dte_vol"];
			}
			else
			{
				$lst[$id]["dte_acquis"]=$res["dte_deb"];
			}				

		}
	}

	return $lst;
}

// Liste des compétences avec la progression pour un membre
function ListCompetences($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT id ";
	$q.="FROM ".$MyOpt["tbl"]."_exercice_conf AS exo WHERE exo.competence<>''";
	$sql->Query($q);

	// ,(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.uid='".$uid."') AS dte_acquis,
	//IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,
	//IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref ";


	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]["id"]=$sql->data["id"];
		$lst[$sql->data["id"]]["idsynthese"]=0;
		$lst[$sql->data["id"]]["dte_acquis"]="";
		$lst[$sql->data["id"]]["progression"]="";
		$lst[$sql->data["id"]]["progref"]="";
	}

	foreach($lst as $id=>$d)
	{
		$q="SELECT id,idsynthese,progression,progref FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE prog.idexercice='".$id."' AND prog.actif='oui' AND prog.uid='".$uid."'";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			
			if ($sql->data["progression"]=="A")
			{
				$lst[$id]["idsynthese"]=$sql->data["idsynthese"];
				$lst[$id]["progression"]="A";
			}
			if ($sql->data["progref"]=="A")
			{
				$lst[$id]["progref"]="A";
			}
			else if (($sql->data["progref"]=="E") && ($d["progref"]!="A"))
			{
				$lst[$id]["progref"]="E";
			}
			if ($lst[$id]["idsynthese"]==0)
			{
				$lst[$id]["idsynthese"]=$sql->data["idsynthese"];
			}
		}
	}

	foreach($lst as $id=>$d)
	{
		if ($d["idsynthese"]>0)
		{
			$q="SELECT synthese.dte_vol,cal.dte_deb FROM ".$MyOpt["tbl"]."_synthese AS synthese LEFT JOIN ".$MyOpt["tbl"]."_calendrier AS cal ON synthese.idvol=cal.id WHERE synthese.id='".$d["idsynthese"]."'";
			$res=$sql->QueryRow($q);

			if ($res["dte_vol"]!="0000-00-00 00:00:00")
			{
				$lst[$id]["dte_acquis"]=$res["dte_vol"];
			}
			else
			{
				$lst[$id]["dte_acquis"]=$res["dte_deb"];
			}				

		}
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
}
function ListReference($sql)
{
	return ListeObjets($sql,"reference",array("id"),array("actif"=>"oui"));
}

class refenac_class extends objet_core
{
	protected $table="refenac";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array(
		"description" => Array("type" => "varchar", "len"=>100, "formlen"=>490),
		"refenac" => Array("type" => "number"),
		"module" => Array("type" => "enum", "default"=>"maniabilite"),
		"phase" => Array("type" => "varchar", "len"=>50, "formlen"=>300),
	);
	
	protected $tabList=array(
		"module" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','panne'=>'Panne','urgence'=>'Situation d\'urgence'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','panne'=>'Panne','urgence'=>'Situation d\'urgence'),
		),
	);
}
function ListRefEnac($sql)
{
	return ListeObjets($sql,"refenac",array("id"),array("actif"=>"oui"));
}

// Liste des exercices avec la progression pour un élève
function ListProgressionEnac($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT id,(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.uid='".$uid."') AS dte_acquis,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref ";

	$q ="SELECT ref.*, (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice_conf AS conf WHERE ref.refenac=conf.refenac)  AS nbenac ";
	// $q.="(SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog LEFT JOIN ".$MyOpt["tbl"]."_exercice_conf AS conf ON prog.idexercice=conf.id WHERE conf.refenac=ref.refenac AND prog.uid='".$uid."' AND prog.progression='A')  AS nbprog, ";
	// $q.="(SELECT MAX(prog.dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog LEFT JOIN ".$MyOpt["tbl"]."_exercice_conf AS conf ON prog.idexercice=conf.id WHERE conf.refenac=ref.refenac AND prog.uid='".$uid."' AND prog.progression='A')  AS dteprog ";
	$q.="FROM ".$MyOpt["tbl"]."_refenac AS ref ";
	$q.="ORDER BY module, phase ";
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]["id"]=$sql->data["id"];
		$lst[$sql->data["id"]]["module"]=$sql->data["module"];
		$lst[$sql->data["id"]]["phase"]=$sql->data["phase"];
		$lst[$sql->data["id"]]["description"]=$sql->data["description"];
		$lst[$sql->data["id"]]["nbenac"]=$sql->data["nbenac"];
		$lst[$sql->data["id"]]["nbprog"]=0;
		$lst[$sql->data["id"]]["dteprog"]="0000-00-00 00:00:00";
	}


	foreach($lst as $id=>$d)
	{
		$q="SELECT prog.id,prog.idsynthese,prog.progression,prog.progref,synthese.dte_vol FROM ".$MyOpt["tbl"]."_exercice AS prog LEFT JOIN ".$MyOpt["tbl"]."_synthese AS synthese ON prog.idsynthese=synthese.id WHERE prog.idexercice='".$id."' AND prog.actif='oui' AND prog.uid='".$uid."'";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$lst[$id]["idsynthese"]=$sql->data["idsynthese"];
			
			if ($sql->data["progression"]=="A")
			{
				$lst[$id]["nbprog"]=$lst[$id]["nbprog"]+1;
				
				if (strtotime($sql->data["dte_vol"])>strtotime($lst[$id]["dteprog"]))
				{
					$lst[$id]["dteprog"]=$sql->data["dte_vol"];
				}
			}
		}
	}

	return $lst;
}


?>
