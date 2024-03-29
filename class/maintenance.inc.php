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

// Class Maintenance
class maint_class extends objet_core
{
	protected $table="maintenance";
	protected $mod="ressources";
	protected $rub="detailmaint";

	protected $droit=array();
	// protected $type=array("dte_deb"=>"date","dte_fin"=>"date","status"=>"enum","potentiel"=>"duration","commentaire"=>"text","cout"=>"price");
	protected $fields=array(
		"uid_ressource" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_atelier" => Array("type" => "number", "default" => "0", "index"=>1),
		"status" => Array("type" => "enum", "default" => "planifie", "index"=>1),
		"dte_deb" => Array("type" => "date", "default" => "now", ),
		"dte_fin" => Array("type" => "date", "default" => "now", ),
		"potentiel" => Array("type" => "number", "default" => "0", ),
		"cout" => Array("type" => "price", "default"=>0, ),
		"commentaire" => Array("type" => "text" ),
		"uid_lastresa" => Array("type" => "number", "default" => "0", ),
	);
	
	protected $tabList=array(
		"status"=>array('planifie'=>'Planifié','confirme'=>'Confirmé','effectue'=>'Effectué','cloture'=>'Cloturé','supprime'=>'Supprimé'),
	);

	# Constructor
	// function __construct($id=0,$sql)
	// {
		// global $gl_uid;
		

		// parent::__construct($id,$sql);
	// }
	
	function aff($key,$typeaff="html",$formname="form_data",&$render="",$formid="")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render,$formid);
		$sql=$this->sql;
		if ($render=="form")
		{
			if ($key=="uid_ressource")
			{
				$lst=ListeRessources($sql,array("oui"));

				$ret="<select name='".$formname."[".$key."]' class='form-control' OnChange='document.maintenance.submit();'>";

				foreach($lst as $i=>$rid)
				{
					$resr=new ress_class($rid,$sql);
					$ret.="<option value='".$rid."' ".(($rid==$this->data["uid_ressource"]) ? "selected" : "").">".$resr->aff("immatriculation")."</option>";
				}
				$ret.="</select>";
			}
			else if ($key=="uid_atelier")
			{
				$lst=GetActiveAteliers($sql);

				$ret="<select name='".$formname."[".$key."]' class='form-control'  OnChange='document.maintenance.submit();'>";

				foreach($lst as $i=>$aid)
				{
					$atelier=new atelier_class($aid,$sql);
					$ret.="<option value='".$aid."' ".(($aid==$this->data["uid_atelier"]) ? "selected" : "").">".$atelier->val("nom")."</option>";
				}
				$ret.="</select>";
			}
		}
		else
		{
			if ($key=="uid_ressource")
			{
				$resr=new ress_class($this->data["uid_ressource"],$sql);
				$ret=$resr->aff("immatriculation");
			}
			else if ($key=="uid_atelier")
			{
				$atelier=new atelier_class($this->data["uid_atelier"],$sql);
				$ret=$atelier->aff("nom");
			}
		}
		return $ret;
	}


	function SetIntervention2()
	{
		$sql=$this->sql;

		$query="UPDATE ".$this->tbl."_calendrier SET idmaint='0' WHERE idmaint='".$this->id."'";
		$res=$sql->Update($query);

		$query="SELECT dte_deb FROM ".$this->tbl."_maintenance WHERE dte_deb>'".$this->dte_fin."' ORDER BY dte_deb LIMIT 0,1";
		$res=$sql->QueryRow($query);
		if (sql2date($res["dte_deb"])!=$res["dte_deb"])
		  { $lastresa="AND dte_fin<'".$res["dte_deb"]."'"; }
		else
		  { $lastresa=""; }

		$query="SELECT dte_fin FROM ".$this->tbl."_calendrier WHERE id='".$this->uid_lastresa."'";
		$res=$sql->QueryRow($query);

		$query="UPDATE ".$this->tbl."_calendrier SET idmaint='".$this->id."' WHERE uid_avion='".$this->uid_ressource."' AND dte_deb>='".$res["dte_fin"]."' ".$lastresa;
		$res=$sql->Update($query);

		echo "'$query'";
	}

	function SetIntervention()
	{
		$sql=$this->sql;
		
		$query="UPDATE ".$this->tbl."_calendrier SET idmaint='0' WHERE idmaint='".$this->id."'";
		$res=$sql->Update($query);

		if ($this->data["uid_lastresa"]>0)
		{
			$query="SELECT tpsreel FROM ".$this->tbl."_calendrier WHERE id='".$this->data["uid_lastresa"]."'";
			$res=$sql->QueryRow($query);

			// $query="UPDATE ".$this->tbl."_calendrier SET idmaint='".$this->id."',potentiel='".($this->data["potentiel"]+$res["tpsreel"])."' WHERE id='".$this->data["uid_lastresa"]."'";
			$query="UPDATE ".$this->tbl."_calendrier SET idmaint='".$this->id."' WHERE id='".$this->data["uid_lastresa"]."'";
			$sql->Update($query);
			// echo $query;
		}
	}
}



function GetActiveMaintenace($sql,$ress,$jour="")
{ global $MyOpt;
	$query="SELECT id,status FROM ".$MyOpt["tbl"]."_maintenance WHERE (status<>'cloture' OR status<>'supprime') AND actif='oui' AND dte_deb<'$jour 23:59:59' AND dte_fin>='$jour' AND uid_ressource='$ress' ORDER BY dte_deb";
	$res=array();
	$sql->Query($query);
	$status=0;
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
		if ($sql->data["status"]=="planifie")
		  { $status=1; }
		else if ($sql->data["status"]=="confirme")
		  { $status=2; }
		else if ($sql->data["status"]=="effectue")
		  { $status=2; }
		else if ($sql->data["status"]=="cloture")
		  { $status=2; }
		
	  }

	if (($jour!="") && (count($res)>0))
	  { return $status; }
	else if ($jour!="")
	  { return 0; }
	else
	  { return $res; }
}

function GetAllMaintenance($sql,$ress)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_maintenance WHERE actif='oui' ".(($ress>0) ? "AND uid_ressource='$ress'" : "" )." ORDER BY dte_deb DESC";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	return $res;
}



// Class Fiche
class fichemaint_class extends objet_core
{
	protected $table="maintfiche";
	protected $mod="";
	protected $rub="";

	protected $droit=array();


	protected $fields=array
	(
		"uid_avion" => Array("type" => "number", "default" => "0", "Index"=>1),
		"description" => Array("type" => "text", ),
		"uid_valid" => Array("type" => "number", "default" => "0", ),
		"dte_valid" => Array("type" => "datetime", "default" => "0000-00-00 00:00:00"),
		"traite" => Array("type" => "enum('oui','non','ann','ref')", "default" => "non", ),
		"uid_planif" => Array("type" => "number", ),
	);
	
	protected $tabList=array(
	);

	# Constructor
	function __construct($id=0,$sql="")
	{
		global $gl_uid;

		parent::__construct($id,$sql);
	}

	function Affecte($id)
	{ global $gl_uid;
		$sql=$this->sql;

		$this->data["uid_planif"]=$id;
		$this->Save();

		return "";
	}
}


function GetActiveFiche($sql,$ress=0,$maint=0)
{ global $MyOpt;
	$query ="SELECT fiche.id FROM ".$MyOpt["tbl"]."_maintfiche AS fiche ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_ressources AS ress ON fiche.uid_avion=ress.id ";
	$query.="WHERE fiche.actif='oui' AND fiche.uid_valid>0 AND (fiche.traite='non' ".(($maint>0) ? " OR fiche.uid_planif='$maint'" : "").") ".(($ress>0) ? " AND fiche.uid_avion='$ress'" : "")." AND ress.actif='oui' ORDER BY fiche.dte_creat DESC";
	$lstfiche=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstfiche[$i]=$sql->data["id"];
	  }

	return $lstfiche;
}

function GetValideFiche($sql,$ress)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_maintfiche WHERE actif='oui' AND uid_valid=0 ORDER BY dte_creat DESC";

	$lstfiche=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstfiche[$i]=$sql->data["id"];
	  }

	return $lstfiche;
}


class atelier_class extends objet_core
{
	protected $table="maintatelier";
	protected $mod="";
	protected $rub="";

	protected $droit=array();
	protected $fields=array(
		"nom" => Array("type" => "varchar", "len"=>200 ),
		"mail" => Array("type" => "email"),
	);
	
	protected $tabList=array(
	);

	# Constructor
	function __construct($id=0,$sql="")
	{
		global $gl_uid;
		
		$this->data["nom"]="";
		$this->data["mail"]=0;
		$this->data["actif"]="oui";

		parent::__construct($id,$sql);
	}
}

function GetActiveAteliers($sql)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_maintatelier WHERE actif='oui' ORDER BY nom";
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
