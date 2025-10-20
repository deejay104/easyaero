<?
/*
    Easy-Aero
    Copyright (C) 2025 Matthieu Isorez

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
	require_once ($appfolder."/class/synthese.inc.php");
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/navigation.inc.php");

// ---- Vérifie les variables
	$uid=checkVar("uid","numeric");
	$order=checkVar("order","varchar",10,"nb");
	$trie=checkVar("trie","varchar",1,"i");
	$ts=checkVar("ts","numeric");
	$lid=checkVar("lid","numeric");
	$livret=checkVar("livret","numeric");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche la liste des membres
	if (GetDroit("ListeTerrains"))
	{
		if ($uid==0)
		  { $uid=$gl_uid; }
		$tmpl_x->assign("form_lstuser", AffListeMembres($sql,$uid,"form_uid_user","","","std","non",array()));
		$tmpl_x->parse("corps.aff_users");
	}
	else
	{
		$uid=$gl_uid;
	}

// ---- Liste des formations

	$lst=ListeLivret($sql,$uid);

	$lid2=-1;

	if ((count($lst)>0) && ($livret>0))
	{
		foreach($lst as $i=>$tmp)
		{
			$res=new livret_class($tmp["id"],$sql);
	
			$tmpl_x->assign("id_livret", $res->id);
			$tmpl_x->assign("chk_livret", ($res->id==$lid) ? "selected" : "") ;
			$tmpl_x->assign("nom_livret", $res->displayDescription());
			$tmpl_x->parse("corps.aff_livret.lst_livret");

			if ($lid2==-1)
			{
				$lid2=$res->id;
			}
			else if ($res->id==$lid)
			{
				$lid2=$res->id;
			}
		}

		$tmpl_x->parse("corps.aff_livret");
	}
	else
	{
		$tmpl_x->assign("id_livret", -1);
		$tmpl_x->assign("chk_livret", "") ;
		$tmpl_x->assign("nom_livret", "Pas de formation");
		$tmpl_x->parse("corps.aff_livret.lst_livret");
		$tmpl_x->parse("corps.aff_livret");
	}

	$lid=$lid2;

// ---- Menu

	if (($theme!="phone") && ($livret>0))
	{
		addPageMenu("","aviation","Formations",geturl("aviation","syntheses","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Pédagogique",geturl("aviation","exercices","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Pannes",geturl("aviation","pannes","type=panne&lid=".$lid."&&uid=".$uid),"",false);
		addPageMenu("","aviation","Exercices",geturl("aviation","pannes","type=exercice&lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Progression CBT",geturl("aviation","competences","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Progression ENAC",geturl("aviation","progenac","lid=".$lid."&uid=".$uid),"",false);
		addPageMenu("","aviation","Terrains",geturl("aviation","terrains","lid=".$lid."&id=".$uid),"",true);
	}


// ---- Titre

	$tabTitre=array();
	$tabTitre["nom"]["aff"]="OACI";
	$tabTitre["nom"]["width"]=100 ;
	$tabTitre["description"]["aff"]="Terrain";
	$tabTitre["description"]["width"]=150 ;
	$tabTitre["nb"]["aff"]="Nombre";
	$tabTitre["nb"]["width"]=80;
	$tabTitre["last"]["aff"]="Dernière visite";
	$tabTitre["last"]["width"]=150;

// ---- Récupère la liste des terrains
    $usr=new user_class($uid,$sql);
    $lst=$usr->ListeTerrains($lid);

	$tabValeur=array();

    $i=0;
    foreach ($lst as $uid=>$d)
    {
        $tabValeur[$i]["nom"]["val"]=$d["nom"];
        $tabValeur[$i]["description"]["val"]=$d["description"];
        $tabValeur[$i]["nb"]["val"]=$d["nb"];
        $tabValeur[$i]["last"]["val"]=$d["last"];
        $i++;
    }

// ---- Affiche le tableau
    $tmpl_x->assign("tab_liste",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	$tmpl_x->assign("tileLayer_url",'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png');
	$tmpl_x->assign("map_lat",$MyOpt["terrain"]["latitude"]);
	$tmpl_x->assign("map_lon",$MyOpt["terrain"]["longitude"]);

	$tmpl_x->assign("livret",$livret);


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
