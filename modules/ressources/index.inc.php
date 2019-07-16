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
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ("class/echeance.inc.php");
	require_once ($appfolder."/class/echeance.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	$order=checkVar("order","varchar");
	$trie=checkVar("trie","varchar");

// ---- Affiche le menu
	$aff_menu="";
	require($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Droit d'accès
	if (!GetDroit("AccesAvions")) { FatalError("Accès non authorisé (AccesAvions)"); }

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["immatriculation"]["aff"]="Immatriculation";
	$tabTitre["immatriculation"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;
	$tabTitre["hora"]["aff"]="Temps Vol";
	$tabTitre["hora"]["width"]=150;
	$tabTitre["potentiel"]["aff"]="Potentiel";
	$tabTitre["potentiel"]["width"]=150;
	$tabTitre["estimemaint"]["aff"]="Estimation Prochaine maintenance";
	$tabTitre["estimemaint"]["width"]=150;
	$tabTitre["echeance"]["aff"]="Echéance(s)";
	$tabTitre["echeance"]["width"]=350;

	$lstusr=ListeRessources($sql);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new ress_class($id,$sql,false);
		$tabValeur[$i]["immatriculation"]["val"]=$usr->val("immatriculation");
		$tabValeur[$i]["immatriculation"]["aff"]=$usr->aff("immatriculation");
		$tabValeur[$i]["nom"]["val"]=$usr->val("nom");
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");


		$tv=$usr->AffTempsVol();
		$tabValeur[$i]["hora"]["val"]=(($tv>0) ? $tv : "0");
		$tabValeur[$i]["hora"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".$tv."</a>";

		$tp=$usr->AffPotentiel();
		$tabValeur[$i]["potentiel"]["val"]=(($tp>0) ? $tp : "0");
		$tabValeur[$i]["potentiel"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".$tp."</a>";

		$t=$usr->EstimeMaintenance();		
		if (strtotime($t)<time())
		{
			// $t=date("Y-m-d",$t);
		}
		$tabValeur[$i]["estimemaint"]["val"]=$t;
		$tabValeur[$i]["estimemaint"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".sql2date($t,"jour")."</a>";
		
		$lstdte=VerifEcheance($sql,$id,"ressources",true);
		$nb=0;
		if (count($lstdte)>0)
		{
			$tabValeur[$i]["echeance"]["val"]="NOK";
			$tabValeur[$i]["echeance"]["aff"]="";
			foreach($lstdte as $ii=>$d)
			{
				if ($d["id"]>0)
				{
					$dte=new echeance_class($d["id"],$sql);
					$tabValeur[$i]["echeance"]["aff"].=$dte->Affiche()."<br/>";
					$nb++;
				}
				else
				{
					if ($d["resa"]=="obligatoire")
					{
						$tabValeur[$i]["echeance"]["aff"].="Aucune échéance pour ".$d["description"]."<br/>";
						$nb++;
					}
				}
			}
		}
		if ($nb==0)
		{
			$tabValeur[$i]["echeance"]["val"]="OK";
		}
						// $tabValeur[$i]["echeance"]["aff"]=print_r($lstdte,true);
	}

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("CreeRessource"))
	{
		$tmpl_x->parse("corps.ajout");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
