<?

// *****************************************************************
	$tabMails["resa_inst"]=Array
	(
		"titre"=>"Notification de r�servation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote",
		"mail"=>
"Bonjour,

Je t'ai ajout� en tant qu'instructeur sur la r�servation suivante :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>D�tail</a>

A bient�t au club
{pilote}"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["decouvert"]=Array
	(
		"titre"=>"Compte � d�couvert",
		"balise"=>"solde",
		"mail"=>
"Bonjour,

Sauf erreur de notre part, le solde ton compte est de {solde} �.
Conform�ment � notre r�glement int�rieur et afin de pr�server la sant� financi�re de notre association, je te demande de faire le n�cessaire au plus vite afin de r�gulariser ta situation. Dans un souci de rapidit�, nous te demandons de privil�gier dans le mesure du possible un r�glement par virement bancaire sans oublier de m'en informer.

Nous comptons sur toi.

A bient�t

Le Tr�sorier"
	);
// *****************************************************************

?>