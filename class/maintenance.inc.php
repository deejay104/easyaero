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

// Class Maintenance
class maint_class extends objet_core
{
	protected $table="maintenance";
	protected $mod="ressources";
	protected $rub="detailmaint";

	protected $droit=array();
	protected $type=array("dte_deb"=>"date","dte_fin"=>"date","status"=>"enum","potentiel"=>"duration","commentaire"=>"text");
	
	protected $tabList=array(
		"status"=>array('planifie'=>'Planifié','confirme'=>'Confirmé','effectue'=>'Effectué','cloture'=>'Cloturé','supprime'=>'Supprimé'),
	);

	# Constructor
	function __construct($id=0,$sql)
	{
		global $gl_uid;
		
		$this->data["actif"]="oui";
		$this->data["dte_deb"]=date("Y-m-d");
		$this->data["dte_fin"]=date("Y-m-d");
		$this->data["potentiel"]=0;
		$this->data["status"]="planifie";
		$this->data["commentaire"]="";
		$this->data["uid_ressource"]=0;
		$this->data["uid_atelier"]=0;
		$this->data["uid_lastresa"]=0;

		parent::__construct($id,$sql);
	}
	
	function aff($key,$typeaff="html",$formname="form_data")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render);
		$sql=$this->sql;
		if ($render=="form")
		{
			if ($key=="uid_ressource")
			{
				$lst=ListeRessources($sql);

				$ret="<select name='".$formname."[".$key."]' OnChange='document.maintenance.submit();'>";

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

				$ret="<select name='".$formname."[".$key."]' OnChange='document.maintenance.submit();'>";

				foreach($lst as $i=>$aid)
				{
					$atelier=new atelier_class($aid,$sql);
					$ret.="<option value='".$aid."' ".(($aid==$this->data["uid_atelier"]) ? "selected" : "").">".$atelier->aff("nom")."</option>";
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


	function SetIntervention()
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
class fichemaint_class{
	# Constructor
	function __construct($id=0,$sql)
	{ global $MyOpt;
		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];

		$this->id=0;
		$this->uid_avion=0;
		$this->uid_valid=0;
		$this->dte_valid="";
		$this->traite="";
		$this->uid_planif=0;
		$this->uid_creat=0;
		$this->dte_creat="";
		$this->uid_maj=0;
		$this->dte_maj="";
		$this->description="";
		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_maintfiche WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->uid_avion=$res["uid_avion"];
		$this->uid_valid=$res["uid_valid"];
		$this->dte_valid=$res["dte_valid"];
		$this->traite=$res["traite"];
		$this->uid_planif=$res["uid_planif"];
		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
		$this->description=$res["description"];
	}

	function Valid($k,$v) 
	{
	}


	function Save()
	{ global $gl_uid;
		$sql=$this->sql;

		if (!is_numeric($this->uid_avion))
		  { return "Il faut sélectionner un avion.<br />"; }
		if ($this->id==0)
		  {
			$query="INSERT INTO ".$this->tbl."_maintfiche SET uid_creat=".$gl_uid.", dte_creat='".now()."'";
			$this->id=$sql->Insert($query);

			$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES (NULL , 'maintenance', '".$this->tbl."_maintfiche', '".$this->id."', '$gl_uid', '".now()."', 'ADD', 'Create maintenance sheet')";
			$sql->Insert($query);
		  }

		// Met à jour les infos
		$query ="UPDATE ".$this->tbl."_maintfiche SET ";
		$query.="uid_avion='$this->uid_avion',";
		$query.="description='".addslashes($this->description)."',";
		$query.="uid_valid='$this->uid_valid',";
		$query.="dte_valid='$this->dte_valid',";
		$query.="traite='$this->traite',";
		$query.="uid_planif='$this->uid_planif',";
		$query.="uid_maj=$gl_uid, dte_maj='".now()."' ";
		$query.="WHERE id=$this->id";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', '".$this->tbl."_maintfiche', '".$this->id."', '$gl_uid', '".now()."', 'MOD', 'Modify maintenance sheet')";
		$sql->Insert($query);

		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE ".$this->tbl."_maintfiche SET actif='non', uid_maj=$uid, dte_maj='".now()."' WHERE id='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', '".$this->tbl."_maintfiche', '".$this->id."', '$uid', '".now()."', 'DEL', 'Delete maintenance sheet')";
		$sql->Insert($query);
	}

	function Affecte($id)
	{ global $uid;
		$sql=$this->sql;

		$this->uid_planif=$id;
		$this->Save();

		return "";
	}

}


function GetActiveFiche($sql,$ress=0,$maint=0)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_maintfiche WHERE uid_valid>0 AND (traite='non' ".(($maint>0) ? " OR uid_planif='$maint'" : "").") ".(($ress>0) ? " AND uid_avion='$ress'" : "")." ORDER BY dte_creat DESC";
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
	$query="SELECT id FROM ".$MyOpt["tbl"]."_maintfiche WHERE uid_valid=0 ORDER BY dte_creat DESC";

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
	protected $type=array("dte_deb"=>"date","dte_fin"=>"date","status"=>"enum","potentiel"=>"number","commentaire"=>"text");
	
	protected $tabList=array(
		"status"=>array('planifie'=>'Planifié','confirme'=>'Confirmé','effectue'=>'Effectué','cloture'=>'Cloturé','supprime'=>'Supprimé'),
	);

	# Constructor
	function __construct($id=0,$sql)
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
