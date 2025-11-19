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

// Livret de formation
class formation_class extends objet_core
{
	protected $table="formation";
	protected $mod="aviation";
	protected $rub="syntheses";

	protected $fields = Array(
		"description" => Array("type" => "varchar", "len"=>20 ),
	);

}

function ListFormation($sql)
{
	return ListeObjets($sql,"formation",array("id"),array("actif"=>"oui"));
}


class livret_class extends objet_core
{
	protected $table="livret";
	protected $mod="aviation";
	protected $rub="syntheses";

	protected $fields = Array(
		"idformation" => Array("type" => "number", "default" => "0", "index" => "1" ),
		"iduser" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"dte_deb" => Array("type" => "date"),
		"dte_fin" => Array("type" => "date"),
		"tpsdc" => Array("type" => "number"),
		"tpssolo" => Array("type" => "number"),
		"cr" => Array("type" => "text"),
	);

	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		if ($this->id>0)
		{
			if ($key=="idformation")
			{
				$render="html";
				$typeaff="html";
			}
		}

		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);

		if (($key=="idformation") && ($typeaff=="form"))
		{
			$txt=$this->val($key);
			$lst=ListFormation($this->sql);

			$ret ="<select id='".(($formid!="") ? $formid : "").$key."' class='form-control' name='".$formname."[".$key."]'>";
			foreach($lst as $i=>$tmp)
			{
				$res=new formation_class($tmp["id"],$this->sql);
				$ret.="<option value=\"".$tmp["id"]."\" ".(($txt==$tmp["id"]) ? "selected" : "").">".$res->val("description")."</option>";
			}
			$ret.="</select>";			
		}
		return $ret;
	}

	function displayDescription()
	{
		$r=new formation_class($this->data["idformation"],$this->sql);
		return $r->val("description")." (".sql2date($this->val("dte_deb")).")";
	}

	function NbHeures($dte,$type)
	{
		$sql=$this->sql;
		$total=0;
		if ($type=="dc")
		{
			$total=$total+$this->val("tpsdc");
		}
		else if ($type=="solo")
		{
			$total=$total+$this->val("tpssolo");
		}

		$query ="SELECT SUM(resa.tpsreel) AS nb FROM ".$this->tbl."_synthese AS synt ";
		$query.="LEFT JOIN ".$this->tbl."_calendrier AS resa ON resa.id=synt.idvol ";
		$query.="WHERE synt.idlivret='".$this->id."' AND type='".$type."' AND synt.actif='oui' ".(($dte!="now") ? "AND synt.dte_vol<='".$dte."' " :"")."AND synt.uid_pilote=".$this->data["iduser"];
		$res=$sql->QueryRow($query);
		$total=$total+(($res["nb"]>0) ? $res["nb"] : "0");
		
		return $total;
	}

	function AffNbHeures($dte,$type)
	{
		$t=$this->NbHeures($dte,$type);

		if ($t>0)
		  { $ret=AffTemps($t); }
		else
		  { $ret="0h 00"; }
		return "<a href='index.php?mod=aviation&rub=vols&id=".$this->id."'>".$ret."</a>";
	}

	function NbAtt($dte,$type)
	{
		$sql=$this->sql;
		$query="SELECT SUM(nb_att) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE idlivret='".$this->id."' AND uid_pilote=".$this->data["iduser"]." ".(($dte!="now") ? "AND synt.dte_vol<='".$dte."' " :"")."AND actif='oui' ".(($type!="") ? " AND type='".$type."'" : "");
		$res=$sql->QueryRow($query);
		
		return (is_numeric($res["nb"])) ? $res["nb"] : 0;
	}
	function NbRmg($dte,$type)
	{
		$sql=$this->sql;
		$query="SELECT SUM(nb_rmg) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE idlivret='".$this->id."' AND uid_pilote=".$this->data["iduser"]." ".(($dte!="now") ? "AND synt.dte_vol<='".$dte."' " :"")."AND actif='oui' ".(($type!="") ? " AND type='".$type."'" : "");
		$res=$sql->QueryRow($query);

		return (is_numeric($res["nb"])) ? $res["nb"] : 0;
	}
}

function ListeLivret($sql,$uid)
{
	return ListeObjets($sql,"livret",array("id"),array("actif"=>"oui","iduser"=>$uid),array("dte_deb"));
}

// Class Synthese
class synthese_class extends objet_core
{
	protected $table="synthese";
	protected $mod="aviation";
	protected $rub="synthese";

	protected $fields = Array(
		"idlivret" => Array("type" => "number", "default" => "0", "index" => "1", "nomodif"=>1 ),
		"idvol" => Array("type" => "number", "default" => "0", "index" => "1" ),
		"status" => Array("type" => "enum","index"=>1, "default" => "edit","show"=>"tag"),
		"conclusion" => Array("type" => "enum","index"=>1, "default" => "ok" ,"show"=>"tag"),
		"type" => Array("type" => "radio","index"=>1, "default" => "dc"),
		"uid_pilote" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_instructeur" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_avion" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"dte_vol" => Array("type" => "datetime", ),
		"module" => Array("type" => "enum","default"=>"maniabilite", "nomodif"=>1),
		"refffa" => Array("type" => "uppercase","len"=>10, "nomodif"=>1),

		"themes" => Array("type" => "text"),
		"bilan_gen" => Array("type" => "text"),

		"mto" => Array("type" => "varchar","len"=>250),

		"info_1" => Array("type" => "radio", "default" => "NA"),
		"info_2" => Array("type" => "radio", "default" => "NA"),
		"info_3" => Array("type" => "radio", "default" => "NA"),
		"info_4" => Array("type" => "radio", "default" => "NA"),
		"info_5" => Array("type" => "radio", "default" => "NA"),

		"nb_att" => Array("type" => "number", "default" => "1"),
		"nb_rmg" => Array("type" => "number", "default" => "0"),
		"tps_theo" => Array("type" => "number", "default" => "0"),

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
			"ca"=>array('edit'=>'Rédaction','signed'=>'Signé','cancel'=>'Annulé'),
		),
		"conclusion"=>array(
			"fr"=>array('ok'=>'Validé','nok'=>'Non Validé'),
			"en"=>array('ok'=>'Validé','nok'=>'Non Validé'),
			"ca"=>array('ok'=>'Validé','nok'=>'Non Validé'),
		),
		"type" =>array(
			"fr"=>array('dc'=>'Double Commande','solo'=>'Solo'),	
			"en"=>array('dc'=>'Double Commande','solo'=>'Solo'),
			"ca"=>array('dc'=>'Double Commande','solo'=>'Solo'),	
		),
		"module" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation','panne'=>'Panne'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation','panne'=>'Panne'),
			"ca"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation','panne'=>'Panne'),
		),
		"info_1" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
			"ca"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
		),
		"info_2" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
			"ca"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
		),
		"info_3" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
			"ca"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
		),
		"info_4" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
			"ca"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
		),
		"info_5" =>array(
			"fr"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
			"en"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),
			"ca"=>array('NA'=>'N/A','I'=>'Insuffisant','S'=>'Suffisant'),	
		),
	);

	protected $color=array(
		"status"=>array("edit"=>"orange","signed"=>"green","cancel"=>"gray-light"),
		"conclusion"=>array("nok"=>"red","ok"=>"green"),
	);

	# Constructor
	function __construct($id=0,$sql="")
	{
		global $gl_uid,$MyOpt;

		if ($id==0)
		{
			$curl = curl_init();
			curl_setopt_array($curl, [
			  CURLOPT_URL => 'https://aviationweather.gov/api/data/metar?ids='.$MyOpt["oacimetar"],
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => ['X-API-Key: ']
			]);
			
			$metar = curl_exec($curl);
			curl_close($curl);
	
			$this->fields["mto"]["default"]=$metar;
		}

		parent::__construct($id,$sql);
	}	
	
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);

		if (($key=="refffa") && ($render=="form"))
		{
			$txt=$this->val($key);

			$livret=new livret_class($this->data["idlivret"],$this->sql);
			$fid=$livret->val("idformation");

			$t=ListeObjets($this->sql,"reference",array("id","refffa"),array("actif"=>"oui","idformation"=>($fid>0)?$fid:0),array("refffa"));
			
			$ret ="<select id='".(($formid!="") ? $formid : "").$key."' class='form-control' name=\"".$formname."[$key]\">";
			$ret.="<option value=\"\" ".(($txt=="") ? "selected" : "").">Aucun</option>";
			foreach($t as $k=>$v)
			{
				$ret.="<option value=\"".$v["refffa"]."\" ".(($txt==$v["refffa"]) ? "selected" : "").">".$v["refffa"]."</option>";
			}
			$ret.="</select>";			
		}
		else if (($key=="idlivret") && ($render=="form"))
		{
			$txt=$this->val($key);
			$lst=ListeLivret($this->sql,$this->data["uid_pilote"]);

			$ret ="<select id='".(($formid!="") ? $formid : "").$key."' class='form-control' name=\"".$formname."[$key]\" OnChange=\"document.location='".geturl("aviation","synthese","id=".$this->id."&idvol=".$this->val("idvol"))."&lid='+document.getElementById('idlivret').value;\">";
			foreach($lst as $i=>$tmp)
			{
				$res=new livret_class($tmp["id"],$this->sql);
				$ret.="<option value=\"".$tmp["id"]."\" ".(($txt==$tmp["id"]) ? "selected" : "").">".$res->displayDescription()."</option>";
			}
			$ret.="</select>";			
		}		
		else if (($key=="idlivret") && ($render=="read"))
		{
			$txt=$this->val($key);
			$res=new livret_class($txt,$this->sql);
			$ret="<input readonly class='form-control' value=\"".$res->displayDescription()."\">";
		}
		else if (($key=="idlivret") && ($render!="form"))
		{
			$txt=$this->val($key);
			$res=new livret_class($txt,$this->sql);
			$ret="<div class='py-2'>".$res->displayDescription()."</div>";
		}
		else if (($key=="uid_pilote") && ($render=="form"))
		{
			$txt=$this->val($key);
			$res=new user_class($txt,$this->sql);
			$ret="<input readonly class='form-control' value=\"".$res->val("fullname")."\">";
		}		
		else if (($key=="uid_pilote") && ($render=="read"))
		{
			$txt=$this->val($key);
			$res=new user_class($txt,$this->sql);
			$ret="<input readonly class='form-control' value=\"".$res->val("fullname")."\">";
		}
		else if (($key=="uid_pilote") && ($render!="form"))
		{
			$txt=$this->val($key);
			$res=new user_class($txt,$this->sql);
			$ret="<div class='py-2'>".$res->val("fullname")."</div>";
		}
		else if (($key=="module") && ($render!="form"))
		{
			$ret="<div class='py-2'>".$ret."</div>";
		}		
		else if (($key=="refffa") && ($render!="form"))
		{
			$ret="<div class='py-2'>".$ret."</div>";
		}
		else if (($key=="type") && ($render!="form"))
		{
			$ret="<div class='py-2'>".$ret."</div>";
		}	
		else if (($key=="conclusion") && ($render!="form"))
		{
			$ret="<div class='py-2'>".$ret."</div>";
		}

		return $ret;
	}
	
	function temps()
	{
		if ($this->id==0)
		{
			return 0;
		}
		$sql=$this->sql;
		$query="SELECT tpsreel FROM ".$this->tbl."_calendrier AS cal WHERE id=".$this->data["idvol"]."";
		$res=$sql->QueryRow($query);
		
		return $res["tpsreel"];
	}
	function NbAtt()
	{
		if ($this->id==0)
		{
			return 0;
		}
		$sql=$this->sql;
		// $query="SELECT SUM(nb_att) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND id<='".$this->id."'";
		$query="SELECT SUM(nb_att) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND dte_vol<='".$this->val("dte_vol")."'";
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
		// $query="SELECT SUM(nb_rmg) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND id<='".$this->id."'";
		$query="SELECT SUM(nb_rmg) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND dte_vol<='".$this->val("dte_vol")."'";
		$res=$sql->QueryRow($query);
		
		return $res["nb"];
	}
	function TotalTheorie()
	{
		if ($this->id==0)
		{
			return 0;
		}
		$sql=$this->sql;
		$query="SELECT SUM(tps_theo) AS nb FROM ".$this->tbl."_synthese AS fiche WHERE uid_pilote=".$this->data["uid_pilote"]." AND dte_vol<='".$this->val("dte_vol")."'";
		$res=$sql->QueryRow($query);
		
		return $res["nb"];
	}
	function listeTerrain()
	{
		return listeTerrain($this->data["idvol"],$this->id);
	}
}



function ListeSyntheseVol($sql,$idvol)
{
	return ListeObjets($sql,"synthese",array("id","module","refffa"),array("actif"=>"oui","idvol"=>$idvol));
}
function ListeMySynthese($sql,$uid,$lid)
{
	return ListeObjets($sql,"synthese",array("id"),array("actif"=>"oui","uid_pilote"=>$uid,"idlivret"=>$lid));
}




class exercice_conf_class extends objet_core
{
	protected $table="exercice_conf";
	protected $mod="aviation";
	protected $rub="";

	protected $fields = Array(
		"idformation" => array("type"=>"number","default"=>0,"index"=>1),
		"focus" => Array("type"=>"bool", "default"=>"non", "index" => "1", ),
		"description" => array("type"=>"varchar","len"=>200, "formlen"=>400),
		"competence" => array("type"=>"varchar","len"=>100, "formlen"=>400),
		"compcat" => array("type"=>"varchar","len"=>100, "formlen"=>400),
		"type" => Array("type" => "enum","default"=>"peda", "index"=>1),
		"module" => Array("type" => "enum","default"=>"maniabilite", "index"=>1),
		"refffa" => Array("type" => "varchar","len"=>10,"formlen"=>100),
		"refenac" => Array("type" => "number"),
	);
	
	protected $tabList=array(
		"module" => array(
			"fr"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation'),
			"en"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation'),
			"ca"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','avance'=>'Navigation avancée','evaluation'=>'Vol d\'évaluation','solo'=>'Solo Supervisé','prorogation'=>'Prorogation'),
		),
		"type" => array(
			"fr"=>Array('peda'=>'Pédagogique','exercice'=>'Exercice','panne'=>'Panne'),
			"en"=>Array('peda'=>'Pédagogique','exercice'=>'Exercice','panne'=>'Panne'),
			"ca"=>Array('peda'=>'Pédagogique','exercice'=>'Exercice','panne'=>'Panne'),
		)
	);
}

function ListeExercicesConf($sql,$s=array("actif"=>"oui"),$order=array("refffa","id"))
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
			"ca"=>array('E'=>'Etude','A'=>'Acquis'),	
		),
	);
}

// Liste les progressions pour un exercice
function ListeProgression($sql,$id)
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
			"ca"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),	
		),
		"progref" =>array(
			"fr"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),	
			"en"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),
			"ca"=>array('V'=>'Non Vu','E'=>'Etude','A'=>'Acquis'),	
		),
	);
}

function ListeExercices($sql,$id,$uid)
{
//function ListeObjets($sql,$table,$champs=array(),$crit=array(),$order=array())

//	return ListeObjets($sql,"exercice",array("idexercice","progression","progref"),array("actif"=>"oui","idsynthese"=>$id),array("progref DESC","id"));
	global $MyOpt;

	$lst=array();

	$q="SELECT id,idexercice,progression,progref FROM ".$MyOpt["tbl"]."_exercice WHERE actif='oui' AND idsynthese=".$id." ORDER BY progref DESC,id";
	$sql->Query($q);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["idexercice"]]["id"]=$sql->data["id"];
		$lst[$sql->data["idexercice"]]["idexercice"]=$sql->data["idexercice"];
		$lst[$sql->data["idexercice"]]["progression"]=$sql->data["progression"];
		$lst[$sql->data["idexercice"]]["progref"]=$sql->data["progref"];
	}

	return $lst;
}

// Liste des exercices non aquis pour un élève
function ListeExercicesNonAcquis($sql,$id,$uid)
{
	global $MyOpt;

	$lst=array();

#	$q="SELECT id,idexercice,progression,progref FROM ".$MyOpt["tbl"]."_exercice WHERE actif='oui' AND uid='".$uid."' and idsynthese<>".$id." AND progression='E' AND progref='A' ORDER BY progref DESC,id";
	$q ="SELECT id,idexercice,progression,progref FROM ".$MyOpt["tbl"]."_exercice AS exo ";
	$q.="WHERE actif='oui' AND uid='".$uid."' AND (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.progression='A')=0 AND (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.idexercice=prog.idexercice AND prog.progref='A')=1 ";
	$q.="GROUP BY idexercice";
	$sql->Query($q);

	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["idexercice"]]["id"]=$sql->data["id"];
		$lst[$sql->data["idexercice"]]["idexercice"]=$sql->data["idexercice"];
		$lst[$sql->data["idexercice"]]["progression"]=$sql->data["progression"];
		$lst[$sql->data["idexercice"]]["progref"]=$sql->data["progref"];
	}

	return $lst;
}

// Liste des exercices avec la progression pour un membre
function ListeExercicesProg($sql,$lid,$uid,$type="",$focus="")
{
	global $MyOpt;

	// $q.="(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."') AS dte_acquis,";
	// $q.="IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,";
	// $q.="IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref,";
	// $q.="(SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.actif='oui' AND prog.uid='".$uid."' AND prog.progref='A') AS nb ";

	$livret=new livret_class($lid,$sql);
	// $formation=new formation_class($livret->val("idformation"),$sql);
	
	$q ="SELECT id FROM ".$MyOpt["tbl"]."_exercice_conf AS exo WHERE actif='oui' AND idformation='".$livret->val("idformation")."'";
	if ($type!="")
	{
		$q.=" AND exo.type='".$type."' ";
	}
	if ($focus!="")
	{
		$q.=" AND exo.focus='".$focus."' ";
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
		$q ="SELECT prog.id,prog.idsynthese,prog.progression,prog.progref FROM ".$MyOpt["tbl"]."_exercice AS prog ";
		$q.="LEFT JOIN ".$MyOpt["tbl"]."_synthese AS synt ON prog.idsynthese=synt.id ";
		$q.="WHERE synt.idlivret='".$lid."' AND prog.idexercice='".$id."' AND prog.actif='oui' AND prog.uid='".$uid."'";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			
			$lst[$id]["nb"]=$lst[$id]["nb"]+1;
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

// Liste des compétences avec la progression pour un membre
function ListeCompetences($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT id ";
	$q.="FROM ".$MyOpt["tbl"]."_exercice_conf AS exo WHERE actif='oui' AND exo.competence<>''";
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
		"idformation" => Array("type" => "number", "default"=>"0","index"=>1),
		"refffa" => Array("type" => "varchar","len"=>10, "formlen"=>100,"index"=>1),
		"theme" => Array("type" => "text"),
	);
}
function ListeReference($sql,$fid)
{
	return ListeObjets($sql,"reference",array("id"),array("actif"=>"oui","idformation"=>$fid));
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
			"ca"=>Array('maniabilite'=>'Maniabilité','navigation'=>'Navigation','panne'=>'Panne','urgence'=>'Situation d\'urgence'),
		),
	);
}
function ListeRefEnac($sql)
{
	return ListeObjets($sql,"refenac",array("id"),array("actif"=>"oui"));
}

// Liste des exercices avec la progression pour un élève
function ListeProgressionEnac($sql,$uid)
{
	global $MyOpt;
	
	$q ="SELECT id,(SELECT MAX(dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice AND prog.uid='".$uid."') AS dte_acquis,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progression='A')>0,'A','E') AS progression,IF((SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog WHERE exo.id=prog.idexercice  AND prog.uid='".$uid."' AND prog.progref='A')>0,'A','E') AS progref ";

	$q ="SELECT ref.*, (SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice_conf AS conf WHERE ref.refenac=conf.refenac) AS nbenac ";
	// $q.="(SELECT COUNT(*) FROM ".$MyOpt["tbl"]."_exercice AS prog LEFT JOIN ".$MyOpt["tbl"]."_exercice_conf AS conf ON prog.idexercice=conf.id WHERE conf.refenac=ref.refenac AND prog.uid='".$uid."' AND prog.progression='A')  AS nbprog, ";
	// $q.="(SELECT MAX(prog.dte_maj) FROM ".$MyOpt["tbl"]."_exercice AS prog LEFT JOIN ".$MyOpt["tbl"]."_exercice_conf AS conf ON prog.idexercice=conf.id WHERE conf.refenac=ref.refenac AND prog.uid='".$uid."' AND prog.progression='A')  AS dteprog ";
	$q.="FROM ".$MyOpt["tbl"]."_refenac AS ref ";
	$q.="ORDER BY module, phase ";
	$sql->Query($q);

	$lst=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$lst[$sql->data["id"]]["refenac"]=$sql->data["refenac"];
		$lst[$sql->data["id"]]["nbenac"]=$sql->data["nbenac"];
		$lst[$sql->data["id"]]["nbprog"]=0;
		$lst[$sql->data["id"]]["dteprog"]="0000-00-00 00:00:00";
	}


	foreach($lst as $id=>$d)
	{
		$q ="SELECT prog.id,prog.idsynthese,prog.progression,prog.progref,exo.refenac,synthese.dte_vol FROM ".$MyOpt["tbl"]."_exercice AS prog ";
		$q.="LEFT JOIN ".$MyOpt["tbl"]."_exercice_conf AS exo ON prog.idexercice=exo.id ";
		$q.="LEFT JOIN ".$MyOpt["tbl"]."_synthese AS synthese ON prog.idsynthese=synthese.id ";
		$q.="WHERE exo.refenac='".$d["refenac"]."' AND prog.actif='oui' AND prog.uid='".$uid."'";

		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			// $lst[$id]["idsynthese"]=$sql->data["idsynthese"];
			
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
