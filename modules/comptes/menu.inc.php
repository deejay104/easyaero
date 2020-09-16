<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

	// <p><A href="index.php?mod=comptes" {class_index}><IMG src="{path_module}/img/icn32_compte.png" />Mon compte</A></p>
	addPageMenu("",$mod,$tabLang["lang_myaccount"],geturl("comptes","",""),"icn32_compte.png",($rub=="index") ? true : false);

	// <!-- BEGIN: liste_compte -->
		// <p><A href="index.php?mod=suivi&rub=liste"><IMG src="{path_module}/img/icn32_comptes.png" />Liste des comptes</A></p>
	// <!-- END: liste_compte -->
	if (GetDroit("AccesSuiviListeComptes"))
	{
		addPageMenu("",$mod,$tabLang["lang_listaccount"],geturl("suivi","liste",""),"icn32_comptes.png",($rub=="liste") ? true : false);
	}
	
	// <!-- BEGIN: transfert -->
		// <p><A href="index.php?mod=comptes&rub=transfert" {class_transfert}><IMG src="{path_module}/img/icn32_transfert.png" />Transfèrer</A></p>
	// <!-- END: transfert -->
	if (GetDroit("AccesTransfert"))
	{
		addPageMenu("",$mod,$tabLang["lang_transfer"],geturl("comptes","transfert",""),"icn32_transfert.png",($rub=="transfert") ? true : false);
	}

	// <!-- BEGIN: credite -->
		// <p><A href="index.php?mod=comptes&rub=credite" {class_credite}><IMG src="{path_module}/img/icn32_credite.png" />Créditer</A></p>
	// <!-- END: credite -->
	if (GetDroit("AccesCredite"))
	{
		addPageMenu("",$mod,$tabLang["lang_credit"],geturl("comptes","credite",""),"icn32_credite.png",($rub=="credite") ? true : false);
	}

	// <!-- BEGIN: affiche_signature -->
		// <p><A href="index.php?mod=comptes&fonc=showhash&id={form_id}"><IMG src="{path_module}/img/icn32_cle.png" />Afficher les signatures</A></p>
	// <!-- END: affiche_signature -->

	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone") && ($rub=="index"))
	{
		addPageMenu("",$mod,$tabLang["lang_showsign"],geturl("comptes","","fonc=showhash&id=".$id),"icn32_cle.png",false);
	}

	
?>