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
?>

<?php
	require_once ($appfolder."/class/user.inc.php");


// ---- Vérifie si l'on veut quitter la page
	if ($fonc=="Retour")  	
	{
		$mod="reservations";
		$affrub="reservation";
	}

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("centrage.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$id=checkVar("id","numeric",0,isset($idvol) ? $idvol : 0);
	$form_passager_pilote=checkVar("form_passager_pilote","array");
	$form_passager_poids=checkVar("form_passager_poids","array");
	$maj=checkVar("maj","numeric");
	

// ---- Vérifie les variables
	if (!is_numeric($id)) { FatalError("Les paramètres de la page sont incorrectes."); }


// ---- Affiche les valeurs enregistrée pour la page

	$tmpl_x->assign("id", $id);

	// Récupère les informations sur le vol
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_calendrier WHERE id='$id'";
	$res=$sql->QueryRow($query);

	// Charge les données de l'avion
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_ressources WHERE id='".$res["uid_avion"]."'";

	$resavion=$sql->QueryRow($query);
	$data=json_decode($resavion["centrage"],true);

	foreach ($data as $key=>$t)
	{
		$tabplace[$key]["id"]=$key;
		$tabplace[$key]["name"]=(isset($t["name"])) ? $t["name"] : "";
		$tabplace[$key]["bras"]=(isset($t["bras"])) ? $t["bras"] : 1;
		$tabplace[$key]["coef"]=(isset($t["coef"])) ? $t["coef"] : 1;
		$tabplace[$key]["type"]=(isset($t["type"])) ? $t["type"] : "";
		$tabplace[$key]["poids"]=(isset($t["poids"])) ? $t["poids"] : 0;
		$tabplace[$key]["idpilote"]=0;
		$tabplace[$key]["idenr"]=0;
	}

	// Récupère la liste des passagers
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_masses WHERE uid_vol='$id'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabplace[$sql->data["uid_place"]]["idpilote"]=$sql->data["uid_pilote"];
		$tabplace[$sql->data["uid_place"]]["poids"]=$sql->data["poids"];
		$tabplace[$sql->data["uid_place"]]["idenr"]=$sql->data["id"];
	  }

	// Met à jour avec les nouvelles infos
	if (is_array($form_passager_poids))
	  {
		foreach($form_passager_poids as $k=>$v)
		  {
			$tabplace[$k]["poids"]=$v;
		  }
	  }

	if ($maj>0)
	  {
		$tabplace[$maj]["poids"]="";
		if ($form_passager_pilote[$maj]==65535)
		  {
			$tabplace[$maj]["poids"]="75";
			$tabplace[$maj]["idpilote"]="65535";
		  }
		if ($form_passager_pilote[$maj]==0)
		  {
			$tabplace[$maj]["idpilote"]="0";
		  }
	  }

	// Affiche la liste des places de l'avion
	$lst=ListActiveUsers($sql,"prenom,nom",array("TypePilote"));


	$tot=0;
	foreach($tabplace as $k=>$tv)
	  {
		$tmpl_x->assign("passager_id", $k);
		$tmpl_x->assign("passager_txt", $tv["name"]);

		$tmpl_x->reset("corps.lst_pilote");
		$tmpl_x->assign("passager_unite", $MyOpt["unitPoids"]);
		$coef=((isset($tv["coef"])) && ($tv["coef"]>0)) ? $tv["coef"] : 1;

		$poids=0;

		// Liste des pilotes
		if (isset($tv["type"]))
		{
			$poids=(is_numeric($tabplace[$k]["poids"]) ? $tabplace[$k]["poids"] : 0);
			$poids=round($poids*$coef,0);
			if ( ($tv["type"]=="pilote") || ($tv["type"]=="copilote") || ($tv["type"]=="passager") )
			{
				$tmpl_x->assign("chk_pax", "");
				if ($tv["idpilote"]==65535)
				  {
					$tabplace[$k]["idpilote"]=65535;
					$tmpl_x->assign("chk_pax", "selected");
				  }


				foreach($lst as $i=>$tmpuid)
				{
					// $sql->GetRow($i);
					$resusr=new user_class($tmpuid,$sql,false,true);
					$tmpl_x->assign("uid_pilote", $resusr->id);
					$tmpl_x->assign("nom_pilote", $resusr->aff("fullname","val"));
					
					// $tmpl_x->assign("uid_pilote", $sql->data["id"]);
					// $tmpl_x->assign("nom_pilote", AffInfo($sql->data["prenom"],"prenom")." ".AffInfo($sql->data["nom"],"nom"));
					if ( (isset($form_passager_pilote[$k])) && ($form_passager_pilote[$k]==$resusr->id) )
					{
						$tmpl_x->assign("chk_pilote", "selected");
						if (($tv["poids"]=="") || ($tv["poids"]==0))
						  {
							$tabplace[$k]["poids"]=$resusr->data["poids"];
						  }
						$tabplace[$k]["idpilote"]=$resusr->id;
					}
					else if ( ( (($res["uid_pilote"]==$resusr->id) && ($tv["type"]=="pilote"))
						   || ($tv["idpilote"]==$resusr->id)
						   || (($res["uid_instructeur"]==$resusr->id) && ($tv["type"]=="copilote") && ($tv["idpilote"]==0)) )
						   )
					{
						if (!isset($form_passager_pilote[$k]) || ($form_passager_pilote[$k]==""))
						{
							$tmpl_x->assign("chk_pilote", "selected");
							if ((!isset($tv["poids"])) || ($tv["poids"]=="") || ($tv["poids"]==0))
							  {
								$tabplace[$k]["poids"]=$resusr->data["poids"];
							  }
							$tabplace[$k]["idpilote"]=$resusr->id;
						}
					}
					else
					{
						$tmpl_x->assign("chk_pilote", "");
					}

					$tmpl_x->parse("corps.lst_passager.aff_pilote.lst_pilote");
				  }
		
				$tmpl_x->parse("corps.lst_passager.aff_pilote");
			}
			else if ($tv["type"]=="essence")
			{
				if (isset($tabplace[$k]["poids"]))
				{
					$poids=round($tabplace[$k]["poids"]*$coef*(($resavion["typegauge"]=="G") ? 3.78541 : 1),0);
					$tmpl_x->assign("passager_unite", $resavion["typegauge"]." (=".$poids." ".$MyOpt["unitPoids"].")");
				}
				else
				{
					$poids=0;
					$tmpl_x->assign("passager_unite", $resavion["typegauge"]." (=0 ".$MyOpt["unitPoids"].")");
				}
			}
		}
		if (isset($tabplace[$k]["poids"]))
		{
			$tmpl_x->assign("passager_poids", $tabplace[$k]["poids"]);
			$tot=$tot+$poids;
		}
		else
		{
			$tmpl_x->assign("passager_poids", "");
		}
		$tmpl_x->parse("corps.lst_passager");
	  }

	$tmpl_x->assign("masse_totale", $tot);

	if ($tot<=$resavion["massemax"])
	  {
		$tmpl_x->assign("masse_max", $MyOpt["unitPoids"]." <font color=\"green\"> &lt; ".$resavion["massemax"]." ".$MyOpt["unitPoids"]."</font>");
	  }
	else
	  {
		$tmpl_x->assign("masse_max", $MyOpt["unitPoids"]." <font color=\"red\"> &gt; ".$resavion["massemax"]." ".$MyOpt["unitPoids"]."</font>");
	  }


// ---- Enregistre les données


	foreach($tabplace as $k=>$v)
	  {
		if ($v["idenr"]>0)
		  {
		  	$query="UPDATE ".$MyOpt["tbl"]."_masses SET uid_vol='$id', uid_pilote='".$v["idpilote"]."', uid_place=$k, poids='".$v["poids"]."', uid_modif='$uid', dte_modif='".now()."' WHERE id='".$v["idenr"]."'";
			$sql->Update($query);
		  	//echo $query."<br>\n";
		  }
		else if ((isset($v["poids"])) && ($v["poids"]>0))
		  {
		  	if (!is_numeric($v["idpilote"]))
		  	  { $v["idpilote"]=0; }
		  	$query="INSERT INTO ".$MyOpt["tbl"]."_masses SET uid_vol='$id', uid_pilote='".$v["idpilote"]."', uid_place='$k', poids='".$v["poids"]."', uid_creat='$uid', dte_creat='".now()."', uid_modif='$uid', dte_modif='".now()."'";
			$sql->Insert($query);
		  	//echo $query."<br>\n";
		  }
	  }

// ---- Affecte les variables d'affichage
	if ($fonc!="Retour")
	{
		$tmpl_x->parse("icone");
		$icone=$tmpl_x->text("icone");
		$tmpl_x->parse("infos");
		$infos=$tmpl_x->text("infos");
		$tmpl_x->parse("corps");
		$corps=$tmpl_x->text("corps");
	}
?>
