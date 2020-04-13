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
class resa_class extends objet_core
{
	protected $table="calendrier";
	protected $mod="reservations";
	protected $rub="reservation";

	
	protected $fields=array(
		"description" => Array("type" => "text", ),
		"dte_deb" => Array("type" => "datetime", "default" => "0000-00-00 00:00:00", ),
		"dte_fin" => Array("type" => "datetime", "default" => "0000-00-00 00:00:00", ),
		"uid_pilote" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_debite" => Array("type" => "number", "default" => "0", ),
		"uid_instructeur" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"uid_avion" => Array("type" => "number", "default" => "0", "index" => "1", ),
		"destination" => Array("type" => "varchar","len"=>50, ),
		"taxe" => Array("type" => "enum","default"=>"none", ),
		"taxeok" => Array("type" => "bool","default"=>"non", ),
		"nbpersonne" => Array("type" => "number", "default" => "1", ),
		"invite" => Array("type" => "bool", "default" => "non", ),
		"accept" => Array("type" => "bool", "default" => "non", ),
		"temps" => Array("type" => "number", "default" => "0", ),
		"tarif" => Array("type" => "varchar", "len"=>2 ),
		"prix" => Array("type" => "price" ),
		"tpsestime" => Array("type" => "number", ),
		"tpsreel" => Array("type" => "number", ),
		"horadeb" => Array("type" => "varchar", "len"=>10, "default" => "0", ),
		"horafin" => Array("type" => "varchar", "len"=>10, "default" => "0", ),
		"idmaint" => Array("type" => "number"),
		"potentiel" => Array("type" => "number"),
		"carburant" => Array("type" => "varchar", "len"=>8 ),
		"carbavant" => Array("type" => "number"),
		"carbapres" => Array("type" => "number"),
		"prixcarbu" => Array("type" => "varchar", "len"=>8 ),
		"reel" => Array("type" => "bool", "default" => "oui", "index"=>1),
		"edite" => Array("type" => "bool", "default" => "non", "index"=>1),
		"dte_maj" => Array("type" => "datetime" ),
		"uid_maj" => Array("type" => "number" ),
		"actif" => Array("type" => "bool", "default" => "oui", "index"=>1),
		"uid_creat" => Array("type" => "number"),
		"dte_creat" => Array("type" => "datetime"),
		"uid_modif" => Array("type" => "number"),
		"dte_modif" => Array("type" => "datetime")
	);

	protected $tabList=array(
		"taxe"=>array("none"=>"Pas de taxe","notpaid"=>"Taxe pas payée","paid"=>"Taxe payée sur place"),
	);
	
	# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		$this->tbl=$MyOpt["tbl"];

		$this->sql=$sql;
		$this->id=0;
		$this->destination="LOCAL";
		$this->tarif="";
		$this->actif="oui";
		$this->reel="oui";
		$this->edite="oui";
		$this->description="";
		$this->horadeb="0";
		$this->horafin="0";
		$this->temps="";
		$this->tpsestime="";
		$this->tpsreel=0;
		$this->destination="LOCAL";
		$this->nbpersonne=1;
		$this->invite="non";
		$this->carburant="";
		$this->prixcarbu="";
		$this->accept="non";
		$this->carbavant="";
		$this->carbapres="";
		$this->potentiel=0;
		$this->potentielh=0;
		$this->potentielm=0;
		$this->uid_pilote=0;
		$this->uid_debite=0;
		$this->uid_instructeur=0;
		$this->uid_ressource=0;

		parent::__construct($id,$sql);

		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id)
	{
		parent::load($id);

		$this->id=$id;
		$sql=$this->sql;
		// $query = "SELECT * FROM ".$this->tbl."_calendrier WHERE id='$id'";
		// $res = $sql->QueryRow($query);

		// Charge les variables
		$this->description=$this->data["description"];
		$this->actif=$this->data["actif"];
		$this->reel=$this->data["reel"];
		$this->tarif=$this->data["tarif"];
		$this->prix=$this->data["prix"];
		$this->temps=$this->data["temps"];
		$this->tpsestime=$this->data["tpsestime"];
		$this->tpsreel=$this->data["tpsreel"];
		$this->horadeb=$this->data["horadeb"];
		$this->horafin=$this->data["horafin"];
		$this->edite=$this->data["edite"];
		$this->dte_deb=$this->data["dte_deb"];
		$this->dte_fin=$this->data["dte_fin"];
		$this->uid_pilote=$this->data["uid_pilote"];
		$this->uid_debite=$this->data["uid_debite"];
		$this->uid_instructeur=$this->data["uid_instructeur"];
		$this->uid_ressource=$this->data["uid_avion"];
		$this->destination=$this->data["destination"];
		$this->nbpersonne=$this->data["nbpersonne"];
		$this->invite=$this->data["invite"];
		$this->accept=$this->data["accept"];
		$this->carbavant=$this->data["carbavant"];
		$this->carbapres=$this->data["carbapres"];
		$this->prixcarbu=$this->data["prixcarbu"];
		$this->uid_maj=$this->data["uid_maj"];
		$this->dte_maj=$this->data["dte_maj"];

		
		$this->potentiel=$this->data["potentiel"];
		$this->potentielh=floor($this->potentiel/60);
		$this->potentielm=$this->potentiel-$this->potentielh*60;

		if ($this->horadeb==0)
		  {
				$query = "SELECT horafin FROM ".$this->tbl."_calendrier WHERE dte_fin<='".$this->dte_deb."' AND uid_avion='".$this->uid_ressource."' ORDER BY dte_fin DESC LIMIT 0,1";
				$res2 = $sql->QueryRow($query);
				$this->horadeb=$res2["horafin"];
				$this->horadeb_orig=$res2["horafin"];
		  }

	}

	function AffTemps(){
		$t=abs($this->temps);

		$signe="";
		if ($this->temps<0)
		  { $signe="-"; }
		  	
		return $signe.AffTemps($t);
	}

	function AffTempsReel(){
		$t=abs($this->tpsreel);

		$signe="";
		if ($this->tpsreel<0)
		  { $signe="-"; }
		  	
		return $signe.AffTemps($t);
	}

	function AffPrix(){
		return AffMontant($this->prix);
	}

	function AffDate(){
		$t1=sql2date($this->dte_deb,"jour");
		$t2=sql2date($this->dte_fin,"jour");

		if ($t1!=$t2)
		  { $dte=$t1." - ".$t2; }
		else if (sql2time($this->dte_deb)!="00:00:00")
		  { $dte=$t1." (".sql2time($this->dte_deb,"nosec")." à ".sql2time($this->dte_fin,"nosec").")"; }
		else
		  { $dte=$t1." (N/A)"; }
		return "<a href='index.php?mod=reservations&rub=reservation&id=".$this->id."'>".$dte."</a>";
	}

	function AffPotentiel($affvol="deb")
	{ global $MyOpt;
		$sql=$this->sql;

		$query="SELECT alertpotentiel,maxpotentiel FROM ".$this->tbl."_ressources WHERE id='".$this->uid_ressource."'";
		$res=$sql->QueryRow($query);

		$t=$res["maxpotentiel"]*60-$this->TempsVols($affvol);
		if (floor($t/60)<0)
		{
			$ret="<font color=red>".AffTemps($t)."</font>";
		}
		else if (floor($t/60)<$res["alertpotentiel"])
		{
			$ret="<font color=orange>".AffTemps($t)."</font>";
		}
		else
		{
			$ret=AffTemps($t);
		}
		return $ret;
	}

	function AffTempsVols($affvol="deb")
	{ global $MyOpt;
		$sql=$this->sql;

		$query="SELECT alertpotentiel,maxpotentiel FROM ".$this->tbl."_ressources WHERE id='".$this->uid_ressource."'";
		$res=$sql->QueryRow($query);

		$t=$this->TempsVols($affvol);
		if ($t>$res["maxpotentiel"]*60)
		{
			$ret="<font color=red>".AffTemps($t)."</font>";
		}
		else if ($t>($res["maxpotentiel"]-$res["alertpotentiel"])*60)
		{
			$ret="<font color=orange>".AffTemps($t)."</font>";
		}
		else
		{
			$ret=AffTemps($t);
		}
		return $ret;
	}	
	
	
	
	function TempsVols($affvol="deb")
	{ global $MyOpt;
		$sql=$this->sql;
		if (($affvol!="prev") && ($this->potentiel>0))
		{
			return $this->potentiel;
		}

		$query ="SELECT cal.dte_deb,cal.dte_fin,cal.idmaint, maint.potentiel AS tot FROM ".$this->tbl."_calendrier AS cal ";
		$query.="LEFT JOIN ".$this->tbl."_maintenance AS maint ON cal.idmaint=maint.id ";
		$query.="WHERE cal.idmaint>0 AND maint.actif='oui' AND ".(($affvol=="fin") ? "cal.dte_deb" : "cal.dte_fin")."<'".$this->dte_deb."' AND cal.uid_avion='".$this->uid_ressource."' ORDER BY cal.dte_fin DESC LIMIT 0,1";
		$resvol=$sql->QueryRow($query);

		$query="SELECT dte_fin,potentiel AS tot FROM ".$this->tbl."_calendrier WHERE potentiel>0 AND dte_deb>'".$resvol["dte_deb"]."' AND ".(($affvol=="fin") ? "dte_deb" : "dte_fin")."<='".$this->dte_deb."' AND uid_avion='".$this->uid_ressource."' ORDER BY dte_fin DESC LIMIT 0,1";
		$respot=$sql->QueryRow($query);

		if ($respot["tot"]>0)
		{
			$query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".$this->dte_deb."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->uid_ressource."'";
			$resreel=$sql->QueryRow($query);

			$t=$respot["tot"]+$resreel["tot"];
		}
		else
		{
			$query="SELECT SUM(tpsreel) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>'".$resvol["dte_deb"]."' AND dte_deb<'".$this->dte_deb."' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$this->uid_ressource."'";
			$resreel=$sql->QueryRow($query);
			$t=$resvol["tot"]+$resreel["tot"];
		}

		if (($affvol=="prev") || ($affvol=="estime"))
		{
			$query="SELECT dte_fin FROM ".$this->tbl."_calendrier WHERE tpsreel<>0 AND dte_deb>='".$respot["dte_fin"]."' AND dte_fin<='".$this->dte_deb."' AND uid_avion='".$this->uid_ressource."' ORDER BY dte_fin DESC LIMIT 0,1";
			$reslast=$sql->QueryRow($query);
			if ($reslast["dte_fin"]=="")
			  {
				$reslast=array();
				$reslast["dte_fin"]=$respot["dte_fin"];
			  }

			$query="SELECT SUM(tpsestime) AS tot FROM ".$this->tbl."_calendrier WHERE dte_deb>='".$reslast["dte_fin"]."' AND dte_fin<='".$this->dte_deb."' AND tpsreel=0 AND actif='oui' AND uid_avion='".$this->uid_ressource."'";
			$resestim=$sql->QueryRow($query);

			$t=$t+$resestim["tot"];
		}

		if (($affvol=="estime") && ($this->tpsestime>0) && ($this->tpsreel==0))
		{
			$t=$t+$this->tpsestime;
		}		

		if ((($affvol=="fin") || ($affvol=="estime")) && ($this->tpsreel>0))
		{
			$t=$t+$this->tpsreel;
		}		

		return $t;
	}
	
	function Save($ValidResa=false)
	{ global $gl_uid,$MyOpt;

		$sql=$this->sql;

		if (($this->dte_deb=="") && ($this->dte_fin==""))
		  { return "La date est obligatoire"; }

		if ($this->dte_deb=="")
		  { $this->dte_fin=$this->dte_deb; }
		else if ($this->dte_fin=="")
		  { $this->dte_deb=$this->dte_fin; }

		// Vérifie la date/heure de début
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4} ?[^$]*$/",$this->dte_deb)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [^$]*$/",$this->dte_deb)) )
		  { return "La date de début n'a pas un format correct (".$this->dte_deb."<>jj/mm/aaaa).<br />"; }
		if (!preg_match("/^[^ ]* [0-9]{1,2}(:[0-9]{1,2}(:[0-9]{1,2})?)?$/",$this->dte_deb))
		  { return "L'heure de début n'a pas un format correct (".$this->dte_deb."<>hh:mm:ss).<br />"; }

		$hdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\1', $this->dte_deb);
		$mdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\2', $this->dte_deb);
		$dte=date2sql($this->dte_deb,"jour")." $hdeb".(($mdeb!="") ? ":$mdeb" : ":00");
		$this->dte_deb=$dte;

		// Vérifie la date/heure de fin
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4} [^$]*$/",$this->dte_fin)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [^$]*$/",$this->dte_fin)) )
		  { return "La date de fin n'a pas un format correct (".$this->dte_fin."<>jj/mm/aaaa).<br />"; }
		if (!preg_match("/^[^ ]* [0-9]{1,2}(:[0-9]{1,2}(:[0-9]{1,2})?)?$/",$this->dte_fin))
		  { return "L'heure de fin n'a pas un format correct (".$this->dte_fin."<>hh:mm:ss).<br />"; }

		$hdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\1', $this->dte_fin);
		$mdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\2', $this->dte_fin);
		$dte=date2sql($this->dte_fin,"jour")." $hdeb".(($mdeb!="") ? ":$mdeb" : ":00");
		$this->dte_fin=$dte;

		if (date_diff_txt($this->dte_deb,$this->dte_fin)<0) 
		  {
			$dte=$this->dte_fin;
			$this->dte_fin=$this->dte_deb;
			$this->dte_deb=$dte;
		  }

		if (!is_numeric($this->uid_pilote))
		  { return "Erreur avec l'id pilote"; }

		if (!is_numeric($this->uid_debite))
		  { return "Erreur avec l'id du debite"; }

		if (!is_numeric($this->uid_instructeur))
		  { return "Erreur avec l'id instructeur"; }

		if (!is_numeric($this->uid_ressource))
		  { return "Il faut sélectionner un avion.<br />"; }

		if ($this->destination=="")
		  { return "La destination est obligatoire"; }

		if (!is_numeric($this->tpsestime))
	  	  { $this->tpsestime=0; }

		if ($this->tpsestime==0)
	  	  { return "Vous devez saisir un temps de vol estimé.<br />"; }

		if (!is_numeric($this->tpsreel))
	  	  { $this->tpsreel=0; }

		if (!is_numeric($this->potentielh))
	  	  { $this->potentielh=0; }
		if (!is_numeric($this->potentielm))
	  	  { $this->potentielm=0; }


  		if (!is_numeric($this->carbavant))
	  	  { $this->carbavant=0; }
		else
	  	  { $this->carbavant=round($this->carbavant,1); }
  		if (!is_numeric($this->carbapres))
	  	  { $this->carbapres=0; }
		else
	  	  { $this->carbapres=round($this->carbapres,1); }

  		if (!is_numeric($this->prixcarbu))
	  	  { $this->prixcarbu=0; }
		else
	  	  { $this->prixcarbu=round($this->prixcarbu,2); }


		$this->potentiel=$this->potentielh*60+$this->potentielm;
		if (!is_numeric($this->potentiel))
	  	  { $this->potentiel=0; }

		// Vérifie si la saisi d'horametre est cohérente
		$this->horadeb=preg_replace("/,/",".",$this->horadeb);
		$this->horafin=preg_replace("/,/",".",$this->horafin);
		if (!is_numeric($this->horadeb))
	  	  { $this->horadeb=0; }
		if (!is_numeric($this->horafin))
	  	  { $this->horafin=0; }

  		$query ="SELECT horadeb FROM ".$this->tbl."_calendrier AS cal WHERE id=".$this->id;
		$res=$sql->QueryRow($query);

		// if ((($res["horadeb"]==$this->horadeb) || ($res["horadeb"]==0)) && ($this->horafin==0))
		if (isset($this->horadeb_orig))
		{
			if (($this->horadeb==$this->horadeb_orig) && ($this->horafin==0))
			  { $this->horadeb=0; }
		}

		// Tps horametre >= Tps réel
		// Deb horametre >= dernier vol

		// Vérifie si les conditions ont été acceptées
		if ($this->accept!="oui")
		  { $this->accept="non"; }

		if (($MyOpt["ChkValidResa"]=="on") && ($this->accept!="oui") && ($ValidResa==false))
		  { return "Vous devez accepter les conditions de vol.<br />"; }



		// Vérifie si la réservation n'empiète pas sur une autre
		$query ="SELECT cal.*,usr.nom AS nom ,usr.prenom AS prenom,usr.initiales,ins.nom AS insnom,ins.prenom AS insprenom,avion.immatriculation ";
		$query.="FROM ".$this->tbl."_calendrier AS cal ";
		$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS usr ON cal.uid_pilote=usr.id ";
		$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS ins ON cal.uid_instructeur=ins.id ";
		$query.="LEFT JOIN ".$this->tbl."_ressources AS avion ON cal.uid_avion=avion.id ";
		$query.="WHERE cal.actif='oui' ";
		$query.="AND cal.uid_avion='$this->uid_ressource' ";
		$query.="AND cal.dte_deb<'$this->dte_fin' ";
		$query.="AND cal.dte_fin>'$this->dte_deb' ";
		$query.="AND cal.id<>'$this->id' ORDER BY dte_deb";
	

		$sql->Query($query);
		$msg_err_t="<U>L'avion est déja réservé</U> :<BR>";
		$okresa=0;
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$msg_err_t.="<p style='padding-left:20px;'>*&nbsp;".strtoupper($sql->data["immatriculation"])." - ".ucwords($sql->data["prenom"])." ".strtoupper($sql->data["nom"])." ";
			if ($sql->data["insnom"]!="")
				{
					$msg_err_t.="avec ".ucwords($sql->data["insprenom"])." ".strtoupper($sql->data["insnom"])." ";
				}
			$msg_err_t.="<br />&nbsp;&nbsp;de ".sql2date($sql->data["dte_deb"])." à ".sql2date($sql->data["dte_fin"]).".</p>";

		  	return $msg_err_t;
			$okresa=1;
		  }


		// Vérifie si l'instructeur est disponible
		$msg_err_t="";
		$okresa=0;
		if ($this->uid_instructeur>0)
		{
			$query ="SELECT cal.*,usr.nom AS nom ,usr.prenom AS prenom,usr.initiales,ins.nom AS insnom,ins.prenom AS insprenom,avion.immatriculation ";
			$query.="FROM ".$this->tbl."_calendrier AS cal ";
			$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS usr ON cal.uid_pilote=usr.id ";
			$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS ins ON cal.uid_instructeur=ins.id ";
			$query.="LEFT JOIN ".$this->tbl."_ressources AS avion ON cal.uid_avion=avion.id ";
			$query.="WHERE cal.actif='oui' ";
			$query.="AND ((cal.uid_instructeur>0 AND cal.uid_instructeur='$this->uid_instructeur') OR (cal.uid_pilote='$this->uid_instructeur')) ";
			$query.="AND cal.dte_deb<'$this->dte_fin' ";
			$query.="AND cal.dte_fin>'$this->dte_deb' ";
			$query.="AND cal.id<>'$this->id' ORDER BY dte_deb";

			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$msg_err_t.="<p style='padding-left:20px;'>*&nbsp;".strtoupper($sql->data["immatriculation"])." - ".ucwords($sql->data["prenom"])." ".strtoupper($sql->data["nom"])." ";
				if ($sql->data["insnom"]!="")
				{
					$msg_err_t.="avec ".ucwords($sql->data["insprenom"])." ".strtoupper($sql->data["insnom"])." ";
				}
				$msg_err_t.="<br />&nbsp;&nbsp;de ".sql2date($sql->data["dte_deb"])." à ".sql2date($sql->data["dte_fin"]).".</p>";
	
				$okresa=1;
			}
			if ($okresa==1)
			{
				return "<U>L'instructeur a déjà une réservation</U> :<BR>".$msg_err_t;
			}
		}
			  
		if ($this->uid_instructeur>0)
		{
			$usr_inst=new user_class($this->uid_instructeur,$sql,false,true);
			if (!$usr_inst->CheckDisponibilite($this->dte_deb,$this->dte_fin))
			{
				$okresa=1;
				return "<U>L'instructeur n'est pas disponible</U><BR>";
			}
		}

		// Traite les erreurs
		
		if ($okresa==1)
		  {
		  	return $msg_err_t;
		  }

		$t=array(
			"edite"=>$this->edite,
			"description"=>addslashes($this->description),
			"dte_deb"=>$this->dte_deb,
			"dte_fin"=>$this->dte_fin,
			"uid_pilote"=>$this->uid_pilote,
			"uid_debite"=>$this->uid_debite,
			"uid_instructeur"=>$this->uid_instructeur,
			"uid_avion"=>$this->uid_ressource,
			"destination"=>$this->destination,
			"taxe"=>$this->data["taxe"],
			"taxeok"=>$this->data["taxeok"],
			"nbpersonne"=>$this->nbpersonne,
			"invite"=>$this->invite,
			"accept"=>$this->accept,
			"reel"=>$this->reel,
			"temps"=>$this->temps,
			"tarif"=>$this->tarif,
			"tpsestime"=>$this->tpsestime,
			"tpsreel"=>$this->tpsreel,
			"horadeb"=>$this->horadeb,
			"horafin"=>$this->horafin,
			"potentiel"=>$this->potentiel,
			"carbavant"=>$this->carbavant,
			"carbapres"=>$this->carbapres,
			"prixcarbu"=>$this->prixcarbu,
			"uid_maj"=>$gl_uid,
			"dte_maj"=>now()
		);

		$this->id=$sql->Edit("reservation",$MyOpt["tbl"]."_calendrier",$this->id,$t);

		$query ="UPDATE ".$this->tbl."_masses SET uid_pilote=".$this->uid_pilote." ";
		$query.="WHERE uid_vol='".$this->id."' AND uid_place=1";
		$sql->Update($query);

		if ($this->uid_instructeur>0)
		{
			$query ="UPDATE ".$this->tbl."_masses SET uid_pilote=".$this->uid_instructeur." ";
			$query.="WHERE uid_vol='$this->id' AND uid_place=2";
			$sql->Update($query);
		}

		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE ".$this->tbl."_calendrier SET actif='non', uid_maj=$uid, dte_maj='".now()."' WHERE id='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'reservation', '".$this->tbl."_calendrier', '".$this->id."', '$uid', '".now()."', 'DEL', 'Delete schedule')";
		$sql->Insert($query);
	}

	function Historique()
	{
		$sql=$this->sql;
		$query="SELECT * FROM ".$this->tbl."_historique WHERE class='reservation' AND `table`='".$this->tbl."_calendrier' AND idtable='$this->id'";
		$sql->Query($query);

		$res=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$res[$i]["uid"]=$sql->data["uid_maj"];
			$res[$i]["dte"]=$sql->data["dte_maj"];
			$res[$i]["type"]=$sql->data["type"];
			$res[$i]["comment"]=$sql->data["comment"];
		  }
	
		return $res;
	}	
}



function GetReservation($sql,$jstart,$jend,$ress)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id,".$MyOpt["tbl"]."_calendrier.dte_deb,".$MyOpt["tbl"]."_calendrier.dte_fin FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' AND dte_fin>='".$jstart." 00:00:00' AND dte_deb<='".$jend." 00:00:00' AND uid_avion='$ress' ORDER BY ".$MyOpt["tbl"]."_calendrier.dte_deb";
	$res=array();
	$sql->Query($query);
	$iii=0;
	for($i=0; $i<$sql->rows; $i++)
	  {
			$sql->GetRow($i);

			$nbj=date_diff_txt(date("Y-m-d",strtotime($sql->data["dte_deb"])),date("Y-m-d",strtotime($sql->data["dte_fin"])))/86400;

			$res[$iii]["id"]=$sql->data["id"];
	  	$res[$iii]["deb"]=$sql->data["dte_deb"];
	  	$res[$iii]["fin"]=$sql->data["dte_fin"];
			$iii=$iii+1;
	  }

	return $res;
}	


function ListReservation($sql,$id,$idavion=0,$top=0,$where="")
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' ".(($id>0) ? "(".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id')" :"")." ".(($idavion>0) ? "AND ".$MyOpt["tbl"]."_calendrier.uid_avion=$idavion" : "")." ORDER BY dte_deb DESC ".(($top>0) ? "LIMIT 0,$top" : "");
	$lstress=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstress[$i]=$sql->data["id"];
	  }

	return $lstress;
}	

function RechercheReservation($sql,$where="")
{ global $MyOpt;
		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier WHERE $where ORDER BY dte_deb";
		$sql->Query($query);
		$lstress=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$lstress[$i]=$sql->data["id"];
		  }

	return $lstress;
}

function ListLastReservation($sql,$id,$idavion=0,$top=0,$dte)
{ global $MyOpt;
	$dte=date2sql(sql2date($dte,"jour"));

	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' ".(($id>0) ? "(".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id')" :"")." ".(($idavion>0) ? "AND ".$MyOpt["tbl"]."_calendrier.uid_avion=$idavion" : "")." AND dte_deb<='$dte 23:59:59' ORDER BY dte_deb DESC ".(($top>0) ? "LIMIT 0,$top" : "");

	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	return $res;
}	


function ListReservationPaye($sql,$id)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE (".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id') AND ".$MyOpt["tbl"]."_calendrier.edite='non' AND actif='oui' ORDER BY dte_deb DESC";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	}

	return $res;
}	

function ListReservationVols($sql,$id,$order="dte_deb",$trie="asc",$ts=0,$tl=0)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE (".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id') AND ".$MyOpt["tbl"]."_calendrier.tpsreel>0 AND actif='oui' ".(($order!="") ? "ORDER BY ".$order." ".$trie : "")." ".(($tl>0) ? "LIMIT $ts,$tl" : "");

	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	}

	return $res;
}	

function ListCarnetVols($sql,$id,$order="dte_deb",$trie="i",$ts=0,$tl=0)
{ global $MyOpt;
	$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier WHERE uid_avion='$id' AND tpsreel>0 AND actif='oui' ".(($order!="") ? "ORDER BY ".$order." ".((($trie=="i") || ($trie=="")) ? "ASC" : "DESC") : "")." ".(($tl>0) ? "LIMIT $ts,".($tl+1) : "");

	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	}

	return $res;
}	

function ListReservationNbLignes($sql,$id)
{ global $MyOpt;
	$query="SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE (".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id') AND ".$MyOpt["tbl"]."_calendrier.tpsreel>0 AND actif='oui'";
	$res=$sql->QueryRow($query);
	return $res["nb"];
}

function ListCarnetNbLignes($sql,$id)
{ global $MyOpt;
	$query="SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_calendrier WHERE uid_avion='$id' AND tpsreel>0 AND actif='oui'";
	$res=$sql->QueryRow($query);
	return $res["nb"];
}




/**
 * ICS.php
 * =======
 * Use this class to create an .ics file.
 *
 * Usage
 * -----
 * Basic usage - generate ics file contents (see below for available properties):
 *   $ics = new ICS($props);
 *   $ics_file_contents = $ics->to_string();
 *
 * Setting properties after instantiation
 *   $ics = new ICS();
 *   $ics->set('summary', 'My awesome event');
 *
 * You can also set multiple properties at the same time by using an array:
 *   $ics->set(array(
 *     'dtstart' => 'now + 30 minutes',
 *     'dtend' => 'now + 1 hour'
 *   ));
 *
 * Available properties
 * --------------------
 * description
 *   String description of the event.
 * dtend
 *   A date/time stamp designating the end of the event. You can use either a
 *   DateTime object or a PHP datetime format string (e.g. "now + 1 hour").
 * dtstart
 *   A date/time stamp designating the start of the event. You can use either a
 *   DateTime object or a PHP datetime format string (e.g. "now + 1 hour").
 * location
 *   String address or description of the location of the event.
 * summary
 *   String short summary of the event - usually used as the title.
 * url
 *   A url to attach to the the event. Make sure to add the protocol (http://
 *   or https://).
 */
class ICS {
  const DT_FORMAT = 'Ymd\THis\Z';
  protected $properties = array();
  private $available_properties = array(
    'description',
    'dtend',
    'dtstart',
    'location',
    'summary',
    'url'
  );
  public function __construct($props) {
    $this->set($props);
  }
  public function set($key, $val = false) {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
        $this->set($k, $v);
      }
    } else {
      if (in_array($key, $this->available_properties)) {
        $this->properties[$key] = $this->sanitize_val($val, $key);
      }
    }
  }
  public function to_string() {
    $rows = $this->build_props();
    return implode("\r\n", $rows);
  }
  private function build_props() {
    // Build ICS properties - add header
    $ics_props = array(
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
      'CALSCALE:GREGORIAN',
      'BEGIN:VEVENT'
    );
    // Build ICS properties - add header
    $props = array();
    foreach($this->properties as $k => $v) {
      $props[strtoupper($k . ($k === 'url' ? ';VALUE=URI' : ''))] = $v;
    }
    // Set some default values
    $props['DTSTAMP'] = $this->format_timestamp('now');
    $props['UID'] = uniqid();
    // Append properties
    foreach ($props as $k => $v) {
      $ics_props[] = "$k:$v";
    }
    // Build ICS properties - add footer
    $ics_props[] = 'END:VEVENT';
    $ics_props[] = 'END:VCALENDAR';
    return $ics_props;
  }
  private function sanitize_val($val, $key = false) {
    switch($key) {
      case 'dtend':
      case 'dtstamp':
      case 'dtstart':
        $val = $this->format_timestamp($val);
        break;
      default:
        $val = $this->escape_string($val);
    }
    return $val;
  }
  private function format_timestamp($timestamp) {
    $dt = new DateTime($timestamp);
    return $dt->format(self::DT_FORMAT);
  }
  private function escape_string($str) {
    return preg_replace('/([\,;])/','\\\$1', $str);
  }
}

?>
