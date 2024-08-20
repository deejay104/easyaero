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

// Classe Utilisateur

class user_class extends user_core
{
	protected $table="utilisateurs";
	protected $mod="membres";
	protected $rub="detail";

	protected $fields_loc = Array
	(
		"idcpt" => Array("type" => "number", "default" => "0"),
		"sexe" => Array("type" => "enum", "default" => "NA", ),
		"pere" => Array("type" => "number", "default" => "0", "index" => "1"),
		"mere" => Array("type" => "number", "default" => "0", "index" => "1"),
		"disponibilite" => Array("type" => "enum", "default" => "dispo", ),
		"tel_fixe" => Array("type" => "tel", ),
		"tel_portable" => Array("type" => "tel", ),
		"tel_bureau" => Array("type" => "tel", ),
		"adresse1" => Array("type" => "varchar","len"=>255, ),
		"adresse2" => Array("type" => "varchar","len"=>255, ),
		"ville" => Array("type" => "varchar","len"=>100, ),
		"codepostal" => Array("type" => "varchar","len"=>10, ),
		"zone" => Array("type" => "varchar","len"=>3, ),
		"profession" => Array("type" => "varchar","len"=>50, ),
		"avatar" => Array("type" => "varchar","len"=>50, ),
		"lache" => Array("type" => "varchar","len"=>2, ),
		"type" => Array("type" => "enum", "default" => "pilote", "index" => "1", ),
		"decouvert" => Array("type" => "number", "default" => "0", "placeholder"=>1),
		"tarif" => Array("type" => "number", "default" => "0", "placeholder"=>1 ),
		"dte_naissance" => Array("type" => "date", "default" => "0000-00-00", ),
		"dte_inscription" => Array("type" => "date", "default" => "now"),
		"poids" => Array("type" => "number", "default" => "75", ),
		"aff_rapide" => Array("type" => "varchar","len"=>1, "default" => "n", ),
		"aff_mois" => Array("type" => "varchar","len"=>1, ),
		"aff_jour" => Array("type" => "date", "default" => "0000-00-00", ),
		"aff_msg" => Array("type" => "number", "default" => "0", ),
		"licence" => Array("type" => "varchar", "len"=>10, "default" => "", ),
	);

	public $tabList_loc=array(
		"type"=>array(
			"fr"=>array('pilote'=>'Pilote','eleve'=>'Elève','instructeur'=>'Instructeur','membre'=>'Membre','invite'=>'Invité','employe'=>'Employé'),
			"en"=>array('pilote'=>'Pilote','eleve'=>'Elève','instructeur'=>'Instructeur','membre'=>'Membre','invite'=>'Invité','employe'=>'Employé'),
		),
		"disponibilite"=>array(
			"fr"=>array('dispo'=>'Disponible','occupe'=>'Occupé'),
			"en"=>array('dispo'=>'Disponible','occupe'=>'Occupé'),
		),
		"sexe"=>array(
			"fr"=>array("M"=>"Masculin","F"=>"Féminin","NA"=>"Non renseigné"),
			"en"=>array("M"=>"Male","F"=>"Female","NA"=>"Non set")
		),

			
	);
	protected $droit_loc=array(
		"dte_inscription"=>"ModifUserDteInscription",
		"decouvert"=>"ModifUserDecouvert",
		"idcpt"=>"ModifUserIdCpt",
		"tarif"=>"ModifUserTarif",
		"lache"=>"ModifUserLache",
		"sexe"=>array("ownerid","ModifUserInfos"),
		"tel_fixe"=>array("ownerid","ModifUserInfos"),
		"tel_portable"=>array("ownerid","ModifUserInfos"),
		"tel_bureau"=>array("ownerid","ModifUserInfos"),
		"adresse1"=>array("ownerid","ModifUserInfos"),
		"adresse2"=>array("ownerid","ModifUserInfos"),
		"ville"=>array("ownerid","ModifUserInfos"),
		"codepostal"=>array("ownerid","ModifUserInfos"),
		"dte_naissance"=>array("ownerid","ModifUserInfos"),
		"poids"=>array("ownerid","ModifUserInfos"),
	);

	// protected $type=array("description"=>"text","status"=>"enum","module"=>"enum");
	
	
	# Constructor
	function __construct($id=0,$sql="",$me=false,$setdata=true)
	{
		$this->data["idcpt"]="0";
		$this->data["decouvert"]="0";
		$this->data["tarif"]="0";
		$this->data["pere"]="0";
		$this->data["mere"]="0";
		$this->data["tel_fixe"]="";
		$this->data["tel_portable"]="";
		$this->data["tel_bureau"]="";
		$this->data["adresse1"]="";
		$this->data["adresse2"]="";
		$this->data["ville"]="";
		$this->data["codepostal"]="";
		$this->data["avatar"]="";
		$this->data["decouvert"]="0";
		$this->data["zone"]="";
		$this->data["dte_inscription"]=date("Y-m-d");
		$this->data["dte_naissance"]=date("Y-m-d");
		$this->data["poids"]="75";
		$this->data["disponibilite"]="dispo";
		$this->data["aff_rapide"]="n";
		$this->data["aff_mois"]="";
		$this->data["aff_jour"]=date("Y-m-d");
		$this->data["aff_msg"]="0";
		$this->data["sexe"]="NA";
		$this->data["lache"]=array();


		// $this->type=array_merge($this->typeea, $this->type); 
		$this->fields=array_merge($this->fields,$this->fields_loc); 
		$this->tabList=array_merge($this->tabList, $this->tabList_loc); 
		$this->droit=array_merge($this->droit, $this->droit_loc); 

		parent::__construct($id,$sql);
		
		$this->idcpt=$this->data["idcpt"];
		
		if ($setdata)
		{
			$this->LoadRoles();
		}
	}
	
	# Laché
	function loadLache()
	{
		$query = "SELECT avion.id AS aid, lache.id AS lid, lache.uid_creat AS uid FROM ".$this->tbl."_utilisateurs AS usr ";
		$query.= "LEFT JOIN ".$this->tbl."_ressources AS avion ON 1=1 ";
		$query.= "LEFT JOIN ".$this->tbl."_lache AS lache ON avion.id=lache.id_avion AND usr.id=lache.uid_pilote  AND lache.actif='oui' ";
		$query.= "WHERE usr.id='".$this->id."' AND avion.actif='oui'";
		$sql=$this->sql;
		$sql->Query($query);

		$this->data["lache"]=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$this->data["lache"][$i]=array();
			$this->data["lache"][$i]["idlache"]=$sql->data["lid"];
			$this->data["lache"][$i]["idavion"]=$sql->data["aid"];
			$this->data["lache"][$i]["idusr"]=$sql->data["uid"];
		}

		if (is_array($this->data["lache"]))
		{
			foreach($this->data["lache"] as $i=>$val)
			{
				$this->data["lache"][$i]["avion"]=new ress_class($val["idavion"],$sql);
				$this->data["lache"][$i]["usr"]=new user_core($val["idusr"],$sql,false,false);
			}
		}
	}

	function SaveLache($tablache)
	{
		global $gl_uid;
		$sql=$this->sql;
		// Charge les enregistrements
		$query = "SELECT id,id_avion FROM ".$this->tbl."_lache WHERE uid_pilote='".$this->id."' AND actif='oui'";

		$sql->Query($query);
		$tlache=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$tlache[$sql->data["id_avion"]]["bd"]=$sql->data["id"];
			$tlache[$sql->data["id_avion"]]["new"]="";
		}
		
		// Charge les nouvelles valeurs
		if (is_array($tablache))
		{
			foreach($tablache as $avion=>$lid)
			{
				$tlache[$avion]["new"]=$lid;
				if (!isset($tlache[$avion]["bd"]))
				{
					$tlache[$avion]["bd"]=0;
				}
			}
		}

		// Vérifie la différence
		foreach($tlache as $avion=>$v)
		{
			if (($v["bd"]==0) && ($v["new"]=="N"))
			{
				$t=array("id_avion"=>$avion, "uid_pilote"=>$this->id, "actif"=>"oui","uid_creat"=>$gl_uid, "dte_creat"=>now());
				$sql->Edit("user",$this->tbl."_lache",0,$t);
			}
			else if (($v["bd"]>0) && ($v["new"]==""))
			{
				$sql->Edit("user",$this->tbl."_lache",$v["bd"],array("actif"=>"non"));
			}
		}
		return "";
	}

	
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$render=$typeaff;
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);

		$sql=$this->sql;
		$txt="";
		if (isset($this->data[$key]))
		{
			$txt=$this->data[$key];
		}
		if ($render=="form")
		{
			if ($key=="lache")
			{
				$ret="";
		  	  	foreach($this->data[$key] as $avion)
		  	  	  {
		  	  		$ret.="<div class='form-check form-check-success'><label class='form-check-label'><input type='checkbox' name='form_lache[".$avion["idavion"]."]' ".(($avion["idlache"]>0) ? "checked" : "")." value='".(($avion["idlache"]>0) ? $avion["idlache"] : "N")."' /> ".$avion["avion"]->val("immatriculation")."<i class='input-helper'></i></label></div>";
		  	  	  }
			}
			else if ($key=="idcpt")
		    {
		    	$ret ="<select id='".$key."' class='form-control' name=\"".$formname."[$key]\">";
		    	$ret.="<option value=\"".$this->id."\" ".(($txt==$this->id)?"selected":"").">".$this->aff("fullname")."</option>";
				if ((isset($this->data["enfant"])) && (is_array($this->data["enfant"])))
				{
				  	foreach($this->data["enfant"] as $enfant)
					{
						if ($enfant["id"]>0)
				  	  	{
						  	$ret.="<option value=\"".$enfant["id"]."\" ".(($txt==$enfant["id"])?"selected":"").">".$enfant["usr"]->aff("fullname")."</option>";
				  	  	}
					}
				}
				$ret.="</select>";
			}		
		}
		else
		{
			if ($key=="lache")
			{
				$ret="";
				if (is_array($this->data[$key]))
				{
					foreach($this->data[$key] as $avion)
					{
						if ($avion["idlache"]>0)
						{ $ret.=$avion["avion"]->aff("immatriculation")." <font size=1><i>(par ".$avion["usr"]->aff("fullname").")</i></font><br />"; }
					}
				}
				if ($ret=="") { $ret="Aucun"; }
			}
			else if ($key=="idcpt")
			{
				if ($txt==$this->id)
				{
					$ret=$this->fullname;
				}
				else if ((isset($this->data["enfant"])) && (is_array($this->data["enfant"])))
				{
					foreach($this->data["enfant"] as $enfant)
					{
						if ($enfant["id"]==$txt)
						{ $ret="<a href=\"index.php?mod=membres&rub=detail&id=".$enfant["id"]."\">".$enfant["usr"]->fullname."</a>"; }
					}
				}
			}

		}
		return $ret;
	}
	
	function AffSolde()
	{
		$s=$this->CalcSolde();
		return "<a href=\"index.php?mod=comptes&id=".$this->data["idcpt"]."\">".(($s<0) ? "<FONT color=red><B>".AffMontant($s)."</B></FONT>" : AffMontant($s))."</a>";
	}

	function CalcSolde()
	{
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='".$this->data["idcpt"]."'";
		$res=$sql->QueryRow($query);

		$solde=((is_numeric($res["total"])) ? $res["total"] : "0");

		return $solde;
	}


	function CalcSoldeTemp()
	{
		$sql=$this->sql;

		$query = "SELECT SUM(montant) AS total FROM ".$this->tbl."_comptetemp WHERE tiers='".$this->data["idcpt"]."' AND status='brouillon'";
		$res=$sql->QueryRow($query);
		
		return $res["total"];
	}


	function isSoldeNegatif()
	{
		$sql=$this->sql;
		$s=$this->CalcSolde();
		$s=$s+$this->data["decouvert"];

		$query = "SELECT SUM(montant) AS total FROM ".$this->tbl."_comptetemp WHERE tiers='".$this->data["idcpt"]."' AND status='brouillon'";
		$res=$sql->QueryRow($query);
		
		$s=$s+$res["total"];

		if ($s>=0)
		{
			return false;
		}
		return true;
	}
	
	function CalcAge($dte){
		global $uid;
		if ($this->dte_naissance!="0000-00-00")
		  {
			$age=floor((strtotime($dte)-strtotime($this->dte_naissance))/(365.25*24*3600));
		  }
		else
		  {
			$age=0;
		  }
		
		return $age;
	}	

	function CheckLache($ress)
	{
		$query = "SELECT id FROM ".$this->tbl."_lache WHERE uid_pilote='".$this->id."' AND id_avion='".$ress."' AND actif='oui'";
		$sql=$this->sql;
		$res=$sql->QueryRow($query);

		if ((is_array($res)) && ($res["id"]>0))
		{
			return true;
		}
		return false;
	}

	function CheckDisponibilite($deb,$fin)
	{
		$this->load($this->id,true,$this->me);
		if ($this->data["disponibilite"]=="dispo")
		{
			$nb=false;
			$zero=true;
		}
		else
		{
			$nb=true;
			$zero=false;
		}
		
		$query = "SELECT * FROM ".$this->tbl."_disponibilite AS dispo ";
		$query.= "WHERE uid='".$this->id."' ";
		$query.= "AND dte_deb<='".$fin."' ";
		$query.= "AND dte_fin>='".$deb."' ";

		$sql=$this->sql;
		$sql->Query($query);

		if ($sql->rows>0)
		{ 
			return $nb;
		}
		else
		{
			return $zero;
		}	
	}
	
	function NombreVols($nbmois="3",$type="aff")
	{
		$sql=$this->sql;
		// Compte le nombre de vols
		$query="SELECT COUNT(*) AS nb FROM `".$this->tbl."_calendrier` WHERE actif='oui' AND (uid_pilote = ".$this->id." OR uid_instructeur = ".$this->id.") AND (prix>0 OR tpsreel>0) AND dte_deb>'".((date("n")<=$nbmois)?(date("Y")-1):date("Y"))."-".((date("n")<=$nbmois)?(12+date("n")-$nbmois):(date("n")-$nbmois))."-".date("d")."'";
		$res=$sql->QueryRow($query);

		$ret=$res["nb"];

		if ($type=="val")
		  { return $ret; }
		else
		  {
			if ($ret>=3)
			  { $ret="<font color='green'>$ret</font>"; }
			else
			  { $ret="<font color='red'><b>$ret</b></font>"; }
		  	return "<a href='index.php?mod=aviation&rub=vols&id=$this->id'>".$ret."</a>";
		  }
	}

	function NbHeures($dte,$dtef="",$type="")
	{
		if ($type=="cdb")
		{
			$q="(uid_pilote = '".$this->id."' AND uid_instructeur = '0')";
		}
		else if ($type=="dc")
		{
			$q="(uid_pilote = '".$this->id."' AND uid_instructeur <> '0'  AND uid_instructeur <> '".$this->id."')";
		}
		else if ($type=="inst")
		{
			$q="uid_instructeur = '".$this->id."'";
		}
		else
		{
			$q="(uid_pilote = '".$this->id."' OR uid_instructeur = '".$this->id."')";
		}
		
		if ($dtef=="")
		{
			$dtef=date("Y-m-d");
		}
		
		$sql=$this->sql;
		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE ".$q." AND dte_deb>='".$dte."' AND dte_deb<'".$dtef."' AND (prix<>0 OR tpsreel<>0) AND actif='oui'";

		$res=$sql->QueryRow($query);

		return (($res["nb"]>0) ? $res["nb"] : "0");
	}

	function AffNbHeures($dte)
	{
		$t=$this->NbHeures($dte);

		if ($t>0)
		  { $ret=AffTemps($t); }
		else
		  { $ret="0h 00"; }
		return "<a href='index.php?mod=aviation&rub=vols&id=".$this->id."'>".$ret."</a>";
	}

	function AffNbHeures12mois($type="")
	{
		$dte=(date("Y")-1)."-".date("m")."-".date("d");
		$t=$this->NbHeures($dte);

		if ($type=="val")
			{
			return $t;
		}
		
		if ($t>30*60)
		  { $ret="<font color=green>".AffTemps($t)."</font>"; }
		else if ($t>0)
		  { $ret=AffTemps($t); }
		else
		  { $ret="0h 00"; }
		
		return "<a href='index.php?mod=aviation&rub=vols&id=".$this->id."'>".$ret."</a>";
	}

	function AffDernierVol()
	{
		global $MyOpt;
		
		$sql=$this->sql;

		$res=$this->DernierVol("",0);
		
		if ($res["dte"]=="0000-00-00")
		{
			return "NA";
		}
		
		$dc = (($res["ins"]>0) && ($res["ins"]!=$this->id)) ? " (DC)" : "";
		$l=floor((time()-strtotime($res["dte"]))/86400);
		$d=sql2date($res["dte"],"jour");

		if ($this->type=="eleve")
		{
			$ret=(($l<30) ? $d.$dc : (($l<45) ? "<font color=orange>".$d.$dc."</font>" : "<font color=red>$d $dc</font>"));
		}
		else if (($this->type!="invite") && ($this->type!="membre"))
		{
			$ret=(($l<floor($MyOpt["maxDernierVol"]*2/3)) ? $d.$dc : (($l<$MyOpt["maxDernierVol"]) ? "<font color=orange>".$d.$dc."</font>" : "<font color=red><b>".$d."</b></font>"));
		}

		return $ret;
	}

	function DernierVol($type="",$tps=0)
	{
		$sql=$this->sql;
		if ($type=="DC")
		{
			// Dernier vol en DC
			$query="SELECT id, tpsreel, dte_deb AS dte, uid_instructeur AS ins FROM `".$this->tbl."_calendrier` WHERE uid_pilote = ".$this->id." AND uid_instructeur>0 AND ".(($tps>0) ? "tpsreel>='".$tps."'" : "tpsreel>0")." ORDER BY dte_deb DESC LIMIT 0,1";
			$res=$sql->QueryRow($query);
		}
		else
		{
			$query="SELECT id, tpsreel, dte_deb AS dte, uid_instructeur AS ins FROM `".$this->tbl."_calendrier` WHERE (uid_pilote = '".$this->id."' OR uid_instructeur = '".$this->id."') AND ".(($tps>0) ? "tpsreel>='".$tps."'" : "tpsreel>0")." ORDER BY dte_deb DESC LIMIT 0,1";
			$res=$sql->QueryRow($query);
		}

		if (!is_array($res))
		{
			$res=array(
				"id"=>0,
				"tpsreel"=>0,
				"dte"=>"0000-00-00",
				"ins"=>0
			);
		}
		return $res;
	}

	function AffTel()
	{
		if (($this->data["aff_infos"]=="non") && (!GetDroit("ModifUserInfos")))
		{
			return "-";
		}

		if ($this->data["tel_fixe"]!="")
		  { $tel=$this->data["tel_fixe"]; }
		else if ($this->data["tel_portable"]!="")
		  { $tel=$this->data["tel_portable"]; }
		else if ($this->data["tel_bureau"]!="")
		  { $tel=$this->data["tel_bureau"]; }
		else
		  { $tel="-"; }

		return AffTelephone($tel);
	}
	
}


?>