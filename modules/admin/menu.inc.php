<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Affiche les menus

	if ((GetDroit("AccesConfigComptes")) || (GetDroit("AccesConfigPostes")) || (GetDroit("AccesConfigTarifs")) || (GetDroit("AccesConfigPrevisions")))
	{
		addPageMenu("",$mod,"Comptabilité",geturl("admin","postes",""),"icn32_tarifs.png",(($rub=="comptes") || ($rub=="postes") || ($rub=="tarifs") || ($rub=="previsions")) ? true : false);

		if (($rub=="comptes") || ($rub=="postes") || ($rub=="tarifs") || ($rub=="previsions"))
		{
			$ok=0;
			if (GetDroit("AccesConfigComptes"))
			{
				addSubMenu("","Comptes",geturl("admin","comptes",""),"icn32_tarifs.png",($rub=="comptes") ? true : false);
				$ok=1;
			}
			if (GetDroit("AccesConfigPostes"))
			{
				addSubMenu("","Postes",geturl("admin","postes",""),"icn32_tarifs.png",($rub=="postes") ? true : false);
				$ok=1;
			}
			if (GetDroit("AccesConfigTarifs"))
			{
				addSubMenu("","Tarifs",geturl("admin","tarifs",""),"icn32_tarifs.png",($rub=="tarifs") ? true : false);
				$ok=1;
			}
			if (GetDroit("AccesConfigPrevisions"))
			{
				addSubMenu("","Prévisions",geturl("admin","previsions",""),"icn32_tarifs.png",($rub=="previsions") ? true : false);
				$ok=1;
			}

			if ($ok==1)
			{
				affSubMenu();
			}
		}
	}
	
	if (GetDroit("AccesConfigNavigation"))
	{
		addPageMenu("",$mod,"Navigation",geturl("admin","navigation",""),"icn32_navigation.png",($rub=="navigation") ? true : false);
	}

	if (GetDroit("AccesConfigInstruction"))
	{
		$aff=false;
		if (($rub=="exercices")|| ($rub=="references") || ($rub=="refenac")) 
		{
			addSubMenu("","Exercices",geturl("admin","exercices",""),"icn32_synthese.png",($rub=="exercices") ? true : false);
			addSubMenu("","Vols",geturl("admin","references",""),"icn32_synthese.png",($rub=="references") ? true : false);
			addSubMenu("","Prog ENAC",geturl("admin","refenac",""),"icn32_synthese.png",($rub=="refenac") ? true : false);

			if ($rub=="exercices")
			{
				addSubMenu("","Ajouter","#","icn32_synthese.png",false,"","\" id=\"edit_new");
			}
			affSubMenu();
			$aff=true;
		}
		addPageMenu("",$mod,"Instruction",geturl("admin","exercices",""),"icn32_synthese.png",$aff);

	}
	

?>