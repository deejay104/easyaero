<?
// Classe Utilisateur

class echeance_class extends echeance_core
{
	protected $table="echeance";
	protected $mod="";
	protected $rub="";

	protected $fields_loc=array
	(
		"paye" => Array("type" => "bool", "default" => "non" ),
	);
	
	function __construct($id=0,$sql,$uid=0)
	{

		$this->fields=array_merge($this->fields,$this->fields_loc); 
		parent::__construct($id,$sql,$uid);
		
	}

}


class echeancetype_class extends echeancetype_core
{
	# Constructor
	protected $table="echeancetype";
	protected $mod="";
	protected $rub="";

	protected $fields_loc=array
	(
		"poste" => Array("type" => "number", "index" => "1"),
		"cout" => Array("type" => "price","default"=>"0.00"),
	);

	protected $tabList_loc=array(
		"context"=>array('utilisateurs'=>'Utilisateur','ressources'=>'Avion'),
	);

	function __construct($id=0,$sql)
	{
		$this->fields=array_merge($this->fields,$this->fields_loc); 
		$this->tabList=array_merge($this->tabList,$this->tabList_loc); 
		parent::__construct($id,$sql);
	}

	function aff($key,$typeaff="html",$formname="form_data",&$render="")
	{
		$ret=parent::aff($key,$typeaff,$formname,$render);

		if ($typeaff=="form")
		{
			if ($key=="poste")
			{
				$sql=$this->sql;
				$query = "SELECT id,description FROM ".$this->tbl."_mouvement WHERE actif='oui' ORDER BY ordre,description";
				$sql->Query($query);
				$tabposte=array();
				for($i=0; $i<$sql->rows; $i++)
				{ 
					$sql->GetRow($i);
					$tabposte[$sql->data["id"]]=$sql->data["description"];
				}

				$ret="<select name=\"".$formname."[$key]\">";
				$ret.="<option value='0'>Aucun</option>";
				foreach($tabposte as $id=>$d)
				{
					$ret.="<option value='".$id."' ".(($id==$this->data[$key]) ? "selected" : "").">".$d."</option>";
				}
				$ret.="</select>";
			}
		}
		else
		{
			if ($key=="poste")
			{
				$sql=$this->sql;
				$query = "SELECT description FROM ".$this->tbl."_mouvement WHERE id=".$this->data[$key];
				$res=$sql->QueryRow($query);
				$ret=$res["description"];
			}
		}
		return $ret;
	}
	
}
?>