<?php
/*
    MnMs Framework
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
	if (!GetDroit("AccesMembres")) { FatalError($tabLang["lang_accessdenied"]." (AccesMembres)"); }

	require_once ("class/document.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Valide les variables
	$aff=checkVar("aff","varchar");

// ---- Menu
	addPageMenu($corefolder,$mod,$tabLang["lang_list"],geturl("membres","","fonc=&aff=".$aff),"icn32_liste.png",($fonc!="trombi") ? true : false);
	addPageMenu($corefolder,$mod,$tabLang["lang_pictures"],geturl("membres","","fonc=trombi&aff=".$aff),"icn32_trombi.png",($fonc=="trombi") ? true : false);
	addPageMenu($corefolder,$mod,$tabLang["lang_emargement"],geturl("membres","list",""),"icn32_trombi.png",false);

	if (GetDroit("AccesMembresVirtuel"))
	{
		addPageMenu($corefolder,$mod,$tabLang["lang_normal"],geturl("membres","","fonc=".$fonc."&aff="),"",($aff!="virtuel") ? true : false);
		addPageMenu($corefolder,$mod,$tabLang["lang_virtuals"],geturl("membres","","fonc=".$fonc."&aff=virtuel"),"",($aff=="virtuel") ? true : false);
	}

	if (GetDroit("CreeUser"))
	{
		addPageMenu($corefolder,$mod,$tabLang["lang_add"],geturl("membres","detail","id=0"),"icn32_ajouter.png");
	}
	
// ---- Trombino
	if ($fonc=="trombi")
	{
		$lstusr=ListActiveUsers($sql,"nom","");


		foreach($lstusr as $i=>$id)
		{
			$usr = new user_core($id,$sql,false);

			$lstdoc=ListDocument($sql,$id,"avatar");
			if (count($lstdoc)>0)
			{
				$doc=new document_core($lstdoc[0],$sql);
				$tmpl_x->assign("aff_avatar",$doc->GenerePath(200,240));
			}
			else
			{
				$tmpl_x->assign("aff_avatar",$corefolder."/static/images/none.gif");
			}	
			$tmpl_x->assign("id_membre",$id);

			$tmpl_x->parse("corps.trombino.aff_picture");
 		}
		$tmpl_x->parse("corps.trombino");
	}
// ---- Liste les membres
	else
	  {
		$tmpl_x->assign("aff_liste","class='pageTitleSelected'");

		if (!isset($aff))
		{ $aff=""; }
	  
		$lstusr=ListActiveUsers($sql,"std","",($aff=="virtuel") ? "oui" : "non");

		if ($theme=="phone")
		{
			foreach($lstusr as $i=>$id)
			{
				$usr = new user_class($id,$sql);

				$tmpl_x->assign("id_membre",$id);
				$tmpl_x->assign("url_detail",geturl("membres","detail","id=".$id));
				$tmpl_x->assign("aff_membre",$usr->aff("fullname"));
				$tmpl_x->assign("tel_membre",$usr->AffTel());
				$tmpl_x->assign("mail_membre",$usr->aff("mail"));

				$lstdoc=ListDocument($sql,$id,"avatar");
				if (count($lstdoc)>0)
				{
					$doc=new document_core($lstdoc[0],$sql);
					$tmpl_x->assign("aff_avatar",$doc->GenerePath(64,64));
				}
				else
				{
					$tmpl_x->assign("aff_avatar",$corefolder."/static/images/icn64_membre.png");
				}	

				$tmpl_x->assign("id_membre",$usr->id);
				$tmpl_x->parse("corps.lst_ligne");
			}
		}
		else
		{
			$tabTitre=array();
			$tabTitre["prenom"]["aff"]=$tabLang["lang_firstname"];
			$tabTitre["prenom"]["width"]=($theme!="phone") ? 150 : 120;
			$tabTitre["nom"]["aff"]=$tabLang["lang_name"];
			$tabTitre["nom"]["width"]=($theme!="phone") ? 200 : 180;
			$tabTitre["mail"]["aff"]=$tabLang["lang_email"];
			$tabTitre["mail"]["width"]=280;
			$tabTitre["groupe"]["aff"]=$tabLang["lang_group"];
			$tabTitre["groupe"]["width"]=150;
			
			$tabValeur=array();
			foreach($lstusr as $i=>$id)
			  {
				$usr = new user_core($id,$sql);
				$tabValeur[$i]["prenom"]["val"]=$usr->val("prenom");
				$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
				$tabValeur[$i]["nom"]["val"]=$usr->val("nom");
				$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
				$tabValeur[$i]["mail"]["val"]=$usr->val("mail");
				$tabValeur[$i]["mail"]["aff"]=$usr->aff("mail");
				$tabValeur[$i]["groupe"]["val"]=$usr->val("groupe");
				$tabValeur[$i]["groupe"]["aff"]=$usr->aff("groupe");
			  }

			if ((!isset($order)) || ($order=="")) { $order="nom"; }
			if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

			$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));
		  }
	}

			

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
