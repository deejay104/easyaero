<?
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

	protected $typeea=array("tel_fixe"=>"tel","tel_portable"=>"tel","tel_bureau"=>"tel","ville"=>"uppercase","type"=>"enum","dte_inscription"=>"date","dte_naissance"=>"date","disponibilite"=>"enum",'poids'=>'number',"tarif"=>"number","decouvert"=>"number","sexe"=>"enum");
	protected $droitea=array(
		"dte_inscription"=>"ModifUserDteInscription",
		"decouvert"=>"ModifUserDecouvert",
		"idcpt"=>"ModifUserIdCpt",
		"tarif"=>"ModifUserTarif",
		"type"=>"ModifUserType",
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
	
	public $tabList=array(
			"type"=>array('pilote'=>'Pilote','eleve'=>'Elève','instructeur'=>'Instructeur','membre'=>'Membre','invite'=>'Invité','employe'=>'Employé'),
			"disponibilite"=>array('dispo'=>'Disponible','occupe'=>'Occupé'),
			"sexe"=>array("M"=>"Masculin","F"=>"Féminin")
			);
	
	# Constructor
	function __construct($id=0,$sql,$me=false,$setdata=true)
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
		$this->data["type"]="pilote";
		$this->data["decouvert"]="0";
		$this->data["zone"]="";
		$this->data["dte_inscription"]=date("Y-m-d");
		$this->data["dte_naissance"]="0000-00-00";
		$this->data["poids"]="75";
		$this->data["disponibilite"]="dispo";
		$this->data["aff_rapide"]="n";
		$this->data["aff_mois"]="";
		$this->data["aff_jour"]=date("Y-m-d");
		$this->data["aff_msg"]="0";
		$this->data["sexe"]="NA";
		$this->data["lache"]=array();


		$this->type=array_merge($this->typeea, $this->type); 
		$this->droit=array_merge($this->droitea, $this->droit); 

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

		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
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
		}
		
		// Charge les nouvelles valeurs
		if (is_array($tablache))
		{
			foreach($tablache as $avion=>$lid)
			{
				$tlache[$avion]["new"]=$lid;
			}
		}

		// Vérifie la différence
		foreach($tlache as $avion=>$v)
		{
			if (($v["bd"]=="") && ($v["new"]=="N"))
			{
				$t=array("id_avion"=>$avion, "uid_pilote"=>$this->id, actif=>"oui","uid_creat"=>$gl_uid, "dte_creat"=>now());
				$sql->Edit("user",$this->tbl."_lache",0,$t);
			}
			else if (($v["bd"]>0) && ($v["new"]==""))
			{
				$sql->Edit("user",$this->tbl."_lache",$v["bd"],array("actif"=>"non"));
			}
		}
		return "";
	}

	
	function aff($key,$typeaff="html",$formname="form_data",&$render="")
	{
		$render=$typeaff;
		$ret=parent::aff($key,$typeaff,$formname,$render);

		$sql=$this->sql;

		if ($render=="form")
		{
			if ($key=="lache")
			{
				$ret="";
		  	  	foreach($this->data[$key] as $avion)
		  	  	  {
		  	  		$ret.="<input type='checkbox' name='form_lache[".$avion["idavion"]."]' ".(($avion["idlache"]>0) ? "checked" : "")." value='".(($avion["idlache"]>0) ? $avion["idlache"] : "N")."' /> ".$avion["avion"]->immatriculation."<br />";
		  	  	  }
			}
			else if ($key=="idcpt")
		    {
		    	$ret ="<select id='".$key."'  name=\"".$formname."[$key]\">";
		    	$ret.="<option value=\"".$this->id."\" ".(($txt==$this->id)?"selected":"").">".$this->aff("fullname")."</option>";
				if (is_array($this->data["enfant"]))
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
			$txt=$this->data[$key];
			if ($key=="lache")
			{
				$ret="";
				foreach($this->data[$key] as $avion)
				{
					if ($avion["idlache"]>0)
					{ $ret.=$avion["avion"]->immatriculation." <font size=1><i>(par ".$avion["usr"]->fullname.")</i></font><br />"; }
				}
				if ($ret=="") { $ret="Aucun"; }
			}
			else if ($key=="idcpt")
			{
				if ($txt==$this->id)
				{
					$ret=$this->fullname;
				}
				else if (is_array($this->data["enfant"]))
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
	
	function AffSolde(){
		global $MyOpt;
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='".$this->data["idcpt"]."'";
		$res=$sql->QueryRow($query);

		$solde=(($res["total"]=="") ? AffMontant(0) : (($res["total"]<0) ? "<FONT color=red><B>".AffMontant($res["total"])."</B></FONT>" : AffMontant($res["total"])));

		return "<a href=\"index.php?mod=comptes&id=".$this->data["idcpt"]."\">$solde</a>";
	}

	function CalcSolde(){
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='".$this->data["idcpt"]."'";
		$res=$sql->QueryRow($query);

		$solde=((is_numeric($res["total"])) ? $res["total"] : "0");

		return $solde;
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
		$query = "SELECT * FROM ".$this->tbl."_lache WHERE uid_pilote='".$this->id."' AND id_avion='".$ress."' AND actif='oui'";
		$sql=$this->sql;
		$res=$sql->QueryRow($query);

		if (!is_numeric($res["id"]))
		  { return false; }
		return true;
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
		$query="SELECT COUNT(*) AS nb FROM `".$this->tbl."_calendrier` WHERE (uid_pilote = ".$this->id." OR uid_instructeur = ".$this->id.") AND (prix>0 OR tpsreel>0) AND dte_deb>'".((date("n")<=$nbmois)?(date("Y")-1):date("Y"))."-".((date("n")<=$nbmois)?(12+date("n")-$nbmois):(date("n")-$nbmois))."-".date("d")."'";
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

	function NbHeures($dte)
	{
		$sql=$this->sql;
		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE (uid_pilote = '".$this->id."' OR uid_instructeur = '".$this->id."') AND dte_deb>'".$dte."' AND dte_deb<='".date("Y-m-d")."' AND (prix<>0 OR tpsreel<>0)";

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
		$sql=$this->sql;

		$res=$this->DernierVol("",0);
		$dc = (($res["ins"]>0) && ($res["ins"]!=$this->id)) ? " (DC)" : "";
		$l=floor((time()-strtotime($res["dte"]))/86400);
		$d=sql2date($res["dte"],"jour");

		if ($this->type=="eleve")
		{
			$ret=(($l<30) ? $d.$dc : (($l<45) ? "<font color=orange>".$d.$dc."</font>" : "<font color=red>$d $dc</font>"));
		}
		else if (($this->type!="invite") && ($this->type!="membre"))
		{
			$ret=(($l<60) ? $d.$dc : (($l<90) ? "<font color=orange>".$d.$dc."</font>" : "<font color=red>$d</font>"));
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

		return $res;
	}
}


?>