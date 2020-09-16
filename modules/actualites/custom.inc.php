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

	$tmpl_custom = LoadTemplate("custom");
	$tmpl_custom->assign("path_module",$corefolder."/".$module."/".$mod);
	$tmpl_custom->assign("corefolder",$corefolder);

	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/echeance.inc.php");
	require_once ($appfolder."/class/ressources.inc.php");
	require_once ($appfolder."/class/manifestation.inc.php");

// ---- Charge l'utilisateur
	$myuser = new user_class($gl_uid,$sql,true);

	$tmpl_custom->assign("solde", $myuser->AffSolde());
	$nbmois=floor($MyOpt["maxDernierVol"]/30);

	$tmpl_custom->assign("nb_vols", $myuser->NombreVols($nbmois));
	$tmpl_custom->assign("nb_mois", $nbmois);

// ---- Affiche les manifestations
	$lstmanip=GetManifestation($sql,date("Y-m-d"),date("Y-m-d",time()+24*3600*30));
	if ((is_array($lstmanip)) && (count($lstmanip)>0))
	{
		foreach($lstmanip as $i=>$did)
		  {
			$manip = new manip_class($did,$sql,$gl_uid);
			$tmpl_custom->assign("form_manifestation",$manip->Aff("titre")." le ".$manip->Aff("dte_manip"));
			$tmpl_custom->parse("custom.manifestation.lst_manifestation");
		  }
			$tmpl_custom->parse("custom.manifestation");
	}


// ---- Affichage des informations
  	if (GetModule("compta"))
	{
	  	$tmpl_custom->parse("custom.mod_compta_detail");
	}  	
	if (GetModule("aviation"))
	{
	  	$tmpl_custom->parse("custom.mod_aviation_detail");

		$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
		$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";

		$tmpl_custom->assign("form_jour",date("Y-m-d"));
		$tmpl_custom->assign("defaultView","agendaDay");
		$tmpl_custom->assign("form_debjour",$debjour);
		$tmpl_custom->assign("form_finjour",$finjour);

	  	$tmpl_custom->parse("custom.aff_reservation");
		
		
		$lstdte=VerifEcheance($sql,0,"ressources",true);
	
		$txt="";
		$nb=0;
		if (count($lstdte)>0)
		{
			foreach($lstdte as $ii=>$d)
			{
				if ($d["id"]>0)
				{
					$dte=new echeance_class($d["id"],$sql);
					$ress=new ress_class($dte->uid,$sql);

					$txt="Pour ".$ress->aff("immatriculation")." :".$dte->Affiche();
					$tmpl_custom->assign("form_echeance_avion", $txt);
					$tmpl_custom->parse("custom.aff_echeance_avion.lst_echeance");

					$nb++;
				}
				else
				{
					if ($d["resa"]=="obligatoire")
					{
						$txt="Aucune échéance pour ".$d["description"];
						$tmpl_custom->assign("form_echeance_avion", $txt);
						$tmpl_custom->parse("custom.aff_echeance_avion.lst_echeance");
						$nb++;
					}
				}
			}
		}

		if ($nb>0)
		{
			$tmpl_custom->parse("custom.aff_echeance_avion");
		}
	
	}
	
// ---- Affiche la page
	$tmpl_custom->parse("custom");
	$custom=$tmpl_custom->text("custom");

?>
