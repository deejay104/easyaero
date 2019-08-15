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
?>

<?
	require_once ($appfolder."/class/maintenance.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("fiche.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Vérification des données
	$uid_avion=checkVar("uid_avion","numeric");
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

	
// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Supprime une fiche
	$delid=checkVar("delid","numeric");
	if ($delid>0)
	{
		$fiche = new fichemaint_class($delid,$sql);
		$fiche->Delete();
	}

// ---- Liste des avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$id)
	  {
		$ress=new ress_class($id,$sql);

		$tmpl_x->assign("uid_avion", $ress->id);
		$tmpl_x->assign("nom_avion", $ress->val("immatriculation"));
		if ($uid_avion==$ress->id)
		  { $tmpl_x->assign("chk_avion", "selected"); }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	  }

// ---- Affiche la liste des fiches

	$tabTitre=array(
		"ress"=>array(
			"aff"=>"Avion",
			"width"=>70
		),
		"auteur"=>array(
			"aff"=>"Auteur",
			"width"=>150
		),
		"dtecreat"=>array(
			"aff"=>"Date",
			"width"=>100
		),
		"description"=>array(
			"aff"=>"Description",
			"width"=>350
		),
		"dteresolv"=>array(
			"aff"=>"Prévision",
			"width"=>100
		),
		"action"=>array(
			"aff"=>"&nbsp;",
			"width"=>20
		)
	);


	$tabValeur=array();

	$lstFiche=GetActiveFiche($sql,$uid_avion);
	if (count($lstFiche)>0)
	{
		foreach($lstFiche as $i=>$id)
		{
			$fiche = new fichemaint_class($id,$sql);

			$ress = new ress_class($fiche->data["uid_avion"],$sql,false);
			$tabValeur[$i]["ress"]["val"]=strtoupper($ress->data["immatriculation"]);
			$tabValeur[$i]["ress"]["aff"]=strtoupper($ress->data["immatriculation"]);
			
			$usr = new user_core($fiche->uid_creat,$sql,false);
			$tabValeur[$i]["auteur"]["val"]=$usr->Aff("fullname","val");
			$tabValeur[$i]["auteur"]["aff"]=$usr->Aff("fullname");

			$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
			$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
			$tabValeur[$i]["description"]["val"]=$fiche->data["description"];
			$tabValeur[$i]["description"]["aff"]=$fiche->aff("description");

			if ($fiche->data["uid_planif"]>0)
			{
			 	$maint = new maint_class($fiche->data["uid_planif"],$sql);
				$tabValeur[$i]["dteresolv"]["val"]=sql2date($maint->data["dte_deb"],"jour");
				$tabValeur[$i]["dteresolv"]["aff"]="<a href='maintenance.php?rub=detail&id=".$maint->id."'>".sql2date($maint->data["dte_deb"],"jour")."</a>";
			}
			else
			{
				$tabValeur[$i]["dteresolv"]["val"]="0";
				$tabValeur[$i]["dteresolv"]["aff"]="N/A";
			}

			$tabValeur[$i]["id"]["val"]=$id;
			$tabValeur[$i]["action"]["val"]=$id;
			$tabValeur[$i]["action"]["aff"]="<div id='action_".$id."' style='display:none;'><a id='edit_".$id."' class='imgDelete' href='index.php?mod=ressources&rub=fiche&delid=".$id."'><img src='".$module."/".$mod."/img/icn16_supprimer.png'></a></div>";
			
		}
	}
	else
	{
		$tabValeur[$i]["ress"]["val"]="";
		$tabValeur[$i]["ress"]["aff"]="";
		$tabValeur[$i]["auteur"]["val"]="";
		$tabValeur[$i]["auteur"]["aff"]="";
		$tabValeur[$i]["dtecreat"]["val"]="";
		$tabValeur[$i]["dtecreat"]["aff"]="";
		$tabValeur[$i]["description"]["val"]="-Aucune fiche en cours-";
		$tabValeur[$i]["description"]["aff"]="-Aucune fiche en cours-";
		$tabValeur[$i]["dteresolv"]["val"]="";
		$tabValeur[$i]["dteresolv"]["aff"]="";
		$tabValeur[$i]["id"]["val"]=0;
		$tabValeur[$i]["action"]["val"]="";
		$tabValeur[$i]["action"]["aff"]="";
	}


	if ($order=="") { $order="ress"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,"",0,"action"));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
