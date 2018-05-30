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
	require_once ($appfolder."/class/manifestation.inc.php");
	require_once ($appfolder."/class/compte.inc.php");
	require_once ($appfolder."/class/user.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module",$module."/".$mod);

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


	$id=checkVar("id","numeric");

// ---- Sauvegarde
	$manip=new manip_class($id,$sql);

	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$type="";
	  	$s="";
	  	foreach($mtype as $t=>$v)
	  	  {
	  	  	$type.=$s.$t;
	  	  	$s=",";
	  	  }

		if (count($form_data)>0)
		{
			foreach($form_data as $k=>$v)
		  	{
		  		$msg_erreur.=$manip->Valid($k,$v);
		  	}
			$msg_confirmation.="Vos données ont été enregistrées.<BR>";
		}

		$manip->Save();
		if ($id==0)
		{
			$id=$manip->id;
		}
		
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Facture la manifestation aux participants
	if (($fonc=="facture") && ($id>0) && GetDroit("FactureManips"))
	  {
		$query="SELECT * FROM ".$MyOpt["tbl"]."_manips WHERE id='$id'";
		$res=$sql->QueryRow($query);

		$txt="1=0 ";

		$query ="SELECT usr.id,usr.nom, usr.prenom, usr.idcpt, participants.participe, participants.nb ";
		$query.="FROM ".$MyOpt["tbl"]."_utilisateurs AS usr ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_participants AS participants ON usr.id=participants.idusr AND participants.idmanip=$id ";
		$query.="WHERE usr.actif='oui' AND usr.virtuel='non' AND participants.participe='Y'";
	 	$sql->Query($query);

		$tabParticipant=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			
			$tabParticipant[$i]=$sql->data;
		}

		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("aff_mouvement_detail", $mvt->AfficheEntete());
		$tmpl_x->parse("corps.msg_enregistre.lst_enregistre");

		$ret="";
		$nbmvt="";
		$ok=0;
		foreach ($tabParticipant as $i=>$v)
		{
			$val=$res["cout"]*$v["nb"];
			$dte=date("Y-m-d");

			$form_commentaire=addslashes($res["titre"])." du ".sql2date($res["dte_manip"])." (".$v["nb"]."x".$res["cout"]."€)";
			
			$mvt = new compte_class(0,$sql);
			$mvt->Generate($v["idcpt"],$MyOpt["id_PosteManip"],$form_commentaire,date("Y-m-d"),$val,array());
			$mvt->Save();
			$nbmvt=$nbmvt+$mvt->Debite();

			// A voir si cette partie est nécessaire ?
			$tmpl_x->assign("aff_mouvement_detail", $mvt->Affiche());
			$tmpl_x->parse("corps.msg_enregistre.lst_enregistre");
			
			if ($mvt->erreur!="")
			{
				$ret.=$mvt->erreur;
				$ok=1;
			}
		}
		
		if ($ret!="")
		{
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,"error");
		}
		else
		{
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : ""),"ok");

			$query="UPDATE ".$MyOpt["tbl"]."_manips SET facture='oui' WHERE id='$id'";
			$sql->Update($query);
		}

		$tmpl_x->parse("corps.msg_enregistre");
	  }

// ---- Suppression

	if (($fonc=="supprimer") && ($id>0))
	{
		$manip->Delete();
		$query= "DELETE FROM ".$MyOpt["tbl"]."_participants WHERE idmanip='".$id."'";
		$sql->Delete($query);
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Inscription à la manifestation
	if ( (!GetDroit("ModifParticipant")) || (!is_numeric($idusr)) )
	{
		$idusr=$gl_uid;
	}

	if (($fonc=="ok") && ($id>0))
	{
		$query="SELECT * FROM ".$MyOpt["tbl"]."_participants WHERE idmanip='$id' AND idusr='$idusr' AND participe='Y'";
		$ins=$sql->QueryRow($query);

		$query="DELETE FROM ".$MyOpt["tbl"]."_participants WHERE idmanip='$id' AND idusr='$idusr' AND participe='N'";
		$sql->Delete($query);

		if ($ins["idusr"]>0)
		  {
				$query="UPDATE ".$MyOpt["tbl"]."_participants SET nb='".($ins["nb"]+1)."', uid_creat='$uid', dte_creat='".now()."' WHERE idmanip='$id' AND idusr='$idusr' AND participe='Y'";
				$sql->Update($query);
		  }
		else
		  {
				$query="INSERT INTO ".$MyOpt["tbl"]."_participants SET idmanip='$id', idusr='$idusr', participe='Y', nb='1', uid_creat='$uid', dte_creat='".now()."'";
				$sql->Insert($query);
		  }
	}
	elseif (($fonc=="nok") && ($id>0))
	{
		$query="DELETE FROM ".$MyOpt["tbl"]."_participants WHERE idmanip='$id' AND idusr='$idusr'";
		$sql->Delete($query);
		$query="INSERT INTO ".$MyOpt["tbl"]."_participants SET idmanip='$id', idusr='$idusr', participe='N', uid_creat='$uid', dte_creat='".now()."'";
		$sql->Insert($query);
	}

// ---- Affiche les infos
	if ($jstart>0)
	{
		$fh=date("O",floor($jstart)/1000+4*3600)/100;
		$dte=date("Y-m-d",floor($jstart)/1000-$fh*3600);
		$manip->Valid("dte_manip",$dte);
	}

	$typeaff="html";
	if (($fonc=="modifier") && (($manip->uid_creat==$gl_uid) || (GetDroit("ModifManifestation"))))
	{
		$typeaff="form";
	}
	if (($id==0) && (GetDroit("CreeManifestation")))
	{
		$typeaff="form";
	}

	
	$manip->Render("form",$typeaff);
	$tmpl_x->assign("aff_id_manip",$id);


	if ($manip->data["cout"]>0) 
	  { $tmpl_x->parse("corps.aff_form_cout"); }



	if ( ($manip->data["facture"]=="non") && ((date_diff_txt($manip->data["dte_limite"],date("Y-m-d"))<=0) || ($manip->data["dte_limite"]=="0000-00-00")) )
	{
		$tmpl_x->parse("infos.bouttons.participe");
	}

	if (($manip->data["facture"]=="non") && (date_diff_txt($manip->data["dte_manip"],date("Y-m-d"))>=0) && ($manip->data["cout"]<>0) && GetDroit("FactureManips"))
	{
		$tmpl_x->parse("infos.bouttons.facture");
	}

	if (($id>0) && (($manip->uid_creat==$gl_uid) || (GetDroit("ModifManifestation"))))
	{
		$tmpl_x->parse("infos.bouttons.modification");
	}
	if (($id>0) && (($manip->uid_creat==$gl_uid) || (GetDroit("SupprimeManifestation"))))
	{
		$tmpl_x->parse("infos.bouttons.suppression");
	}


	if ($id>0)
	{
		$tmpl_x->parse("infos.bouttons");
	}

	if ($typeaff=="form")
	{
		$tmpl_x->parse("corps.submit");
	}

// ---- Affiche la liste des participants

	if ($id>0)
	{
	  	$txt="1=0 ";
		$t=explode(",",$manip->data["type"]);
		$s="";
		foreach($t as $i=>$v)
		{
			$txt.="OR groupe='$v' ";
		}

		$order=(($MyOpt["globalTrie"]=="nom") ? "nom,prenom" : "prenom,nom");

		$query="SELECT usr.id,usr.nom, usr.prenom, participants.participe, participants.nb FROM ".$MyOpt["tbl"]."_utilisateurs AS usr LEFT JOIN ".$MyOpt["tbl"]."_participants AS participants ON usr.id=participants.idusr AND participants.idmanip=$id WHERE usr.actif='oui' AND usr.virtuel='non' AND ($txt) ORDER BY ".$order;
	   	$sql->Query($query);

		$txt_ok="";
		$txt_nok="";
		$txt_na="";

		$nbok=0;
		$nbnok=0;

		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			
			if ($sql->data["participe"]=="Y")
			  {
			  	$txt_ok.="<p>".AffFullname($sql->data["prenom"],$sql->data["nom"])." ".(($sql->data["nb"]>1) ? "(".$sql->data["nb"].")" :"");
					if (GetDroit("ModifParticipant"))
					  {
					  	$txt_ok.=" <a href='index.php?mod=manifestations&rub=detail&id=$id&idusr=".$sql->data["id"]."&fonc=nok'><img src='static/modules/$mod/img/icn12_absent.png' border=0/></a>";
					  }
			  	$txt_ok.="</p>";
			  	$nbok=$nbok+$sql->data["nb"];
			  }
			else if ($sql->data["participe"]=="N")
			  {
			  	$txt_nok.="<p>".AffFullname($sql->data["prenom"],$sql->data["nom"])." ".(($sql->data["nb"]>1) ? "(".$sql->data["nb"].")" :"");
					if (GetDroit("ModifParticipant"))
					  {
					  	$txt_nok.=" <a href='index.php?mod=manifestations&rub=detail&id=$id&idusr=".$sql->data["id"]."&fonc=ok'><img src='static/modules/$mod/img/icn12_participe.png' border=0/></a>";
					  }
			  	$txt_nok.="</p>";
			  	$nbnok=$nbnok+$sql->data["nb"];
			  }
			else if (GetDroit("ModifParticipant"))
			  {
			  	$txt_na.="<p>".AffFullname($sql->data["prenom"],$sql->data["nom"])." ".(($sql->data["nb"]>1) ? "(".$sql->data["nb"].")" :"");
			  	$txt_na.=" <a href='index.php?mod=manifestations&rub=detail&id=$id&idusr=".$sql->data["id"]."&fonc=ok'><img src='static/modules/$mod/img/icn12_participe.png' border=0/></a>&nbsp;";
			  	$txt_na.=" <a href='index.php?mod=manifestations&rub=detail&id=$id&idusr=".$sql->data["id"]."&fonc=nok'><img src='static/modules/$mod/img/icn12_absent.png' border=0/></a>&nbsp;";
			  	$txt_na.="</p>";
			  }
		  }

		$tmpl_x->assign("aff_nbok",$nbok);
		$tmpl_x->assign("aff_nbnok",$nbnok);

		if ($txt_ok!="")
		  {
				$tmpl_x->assign("aff_participants_manip",$txt_ok);
				$tmpl_x->parse("corps.participants");
		  }
		if ($txt_nok!="")
		  {
				$tmpl_x->assign("aff_absents_manip",$txt_nok);
				$tmpl_x->parse("corps.absents");
		  }

		if (GetDroit("ModifParticipant") && ($txt_na!=""))
		  {
				$tmpl_x->assign("aff_na_manip",$txt_na);
				$tmpl_x->parse("corps.na");
		  }

	  }

// ---- Liste des autres manips
	$query="SELECT * FROM ".$MyOpt["tbl"]."_manips WHERE dte_manip='".$manip->data["dte_manip"]."' AND actif='oui' ORDER BY id";
	$sql->Query($query);

	if ($sql->rows>1)
	{
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$tmpl_x->assign("aff_autre_manip",$sql->data["titre"]);
			$tmpl_x->assign("id_autre_manip",$sql->data["id"]);
			if ($sql->data["id"]!=$id)
			{
				$tmpl_x->parse("corps.autre_manip.lst_autre_manip");
			}
		}
		$tmpl_x->parse("corps.autre_manip");
	}

// ---- Affecte les variables d'affichage

	if (($fonc=="supprimer") || ($fonc=="Annuler"))
	{
	  	$affrub="index";
	}
	else if (($id==0) && (!GetDroit("CreeManifestation")))
	{
	  	$affrub="index";
	}
	else
	{
		$tmpl_x->parse("icone");
		$icone=$tmpl_x->text("icone");
		$tmpl_x->parse("infos");
		$infos=$tmpl_x->text("infos");
		$tmpl_x->parse("corps");
		$corps=$tmpl_x->text("corps");
	}
?>