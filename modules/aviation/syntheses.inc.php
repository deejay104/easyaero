<?
/*
	Easy Aero
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
	// if (!GetDroit("AccesSynthese")) { FatalError("Accès non autorisé (AccesSynthese)"); }

	require_once ($appfolder."/class/synthese.inc.php");
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x->assign("path_module",$module."/".$mod);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Initialise les variables
	$id=checkVar("id","numeric");

	if ($id==0)
	{
		$id=$gl_uid;
	}
	if ((!GetDroit("AccesSynthese")) && ($id!=$gl_uid))
	{
		$id=$gl_uid;
	}
		
// ---- Liste des membres
	if (GetDroit(AccesSynthese))
	{
			$lst=ListActiveUsers($sql,"std","","");
		
			foreach($lst as $i=>$tmpuid)
			{
			  	$resusr=new user_class($tmpuid,$sql);
	
				$tmpl_x->assign("id_compte", $resusr->id);
				$tmpl_x->assign("chk_compte", ($resusr->id==$id) ? "selected" : "") ;
				$tmpl_x->assign("nom_compte", $resusr->aff("fullname"));
				$tmpl_x->parse("corps.users.lst_users");
			}
			$tmpl_x->parse("corps.users");
	}

	
// ---- Affiche la liste	
	$lst=ListMySynthese($sql,$id);

	$tabTitre=array(
		"ress" => array("aff"=>"Avion","width"=>80),
		"dte" => array("aff"=>"Date","width"=>100),
		"inst" => array("aff"=>"Instructeur","width"=>200),
		"vol" => array("aff"=>"Vol","width"=>50),
		"type" => array("aff"=>"Type","width"=>100),
	);
	$tabValeur=array();
	foreach($lst as $fid=>$d)
	{
		// $tabValeur[$id]["sid"]["val"]=$id;
		// $tabValeur[$id]["sid"]["aff"]="<a href='index.php?mod=aviation&rub=synthese&id=$id'>".$id."</a>";
		$fiche = new synthese_class($fid,$sql);
		$resa=new resa_class($fiche->val("idvol"),$sql);
		$ress=new ress_class($fiche->val("uid_avion"),$sql);
		$inst=new user_class($fiche->val("uid_instructeur"),$sql);
		
	// $pil=new user_class($resa->uid_pilote,$sql);
	// $ins=new user_class($resa->uid_instructeur,$sql);
		
		$tabValeur[$fid]["ress"]["val"]=$ress->val("immatriculation");
		$tabValeur[$fid]["ress"]["aff"]="<a href='index.php?mod=aviation&rub=synthese&id=".$fid."&uid=".$id."'>".$ress->val("immatriculation")."</a>";
		// $tabValeur[$id]["ress"]["aff"]="<a href='index.php?mod=aviation&rub=synthese&id=$id'>".$id."</a>";
		$tabValeur[$fid]["dte"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$fid]["dte"]["aff"]="<a href='index.php?mod=aviation&rub=synthese&id=".$fid."&uid=".$id."'>".sql2date($resa->dte_deb,"jour")."</a>";
		
		$tabValeur[$fid]["inst"]["val"]=$inst->val("fullname");
		$tabValeur[$fid]["inst"]["val"]="<a href='index.php?mod=aviation&rub=synthese&id=".$fid."&uid=".$id."'>".$inst->val("fullname")."</a>";
	

		$tabValeur[$fid]["vol"]["val"]=$fiche->val("nbvol");
		$tabValeur[$fid]["vol"]["val"]=$fiche->aff("nbvol");
		$tabValeur[$fid]["type"]["val"]=$fiche->val("type");
		$tabValeur[$fid]["type"]["val"]=$fiche->aff("type");
	}

	if ((!isset($order)) || ($order=="")) { $order="dte"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"",0,"",0,"action"));

	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>