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
	$tabMails["resa_modif"]=Array
	(
		"titre"=>"Modification de votre r�servation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote,editeur",
		"mail"=>
"Bonjour,

La r�servation suivante a �t� modifi�e par {editeur} :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>Voir plus de d�tail</a>

A bient�t au club
"
	);
// *****************************************************************
// *****************************************************************
	$tabMails["resa_supp"]=Array
	(
		"titre"=>"Suppression de votre r�servation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote,editeur",
		"mail"=>
"Bonjour,

La r�servation suivante a �t� supprim�e par {editeur} :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>Voir plus de d�tail</a>

A bient�t au club
"
	);
// *****************************************************************


// *****************************************************************
	$tabMails["decouvert"]=Array
	(
		"titre"=>"Compte � d�couvert",
		"balise"=>"solde",
		"mail"=>
"Bonjour,

Sauf erreur de notre part, le solde ton compte est de {solde}.
Conform�ment � notre r�glement int�rieur et afin de pr�server la sant� financi�re de notre association, je te demande de faire le n�cessaire au plus vite afin de r�gulariser ta situation. Dans un souci de rapidit�, nous te demandons de privil�gier dans le mesure du possible un r�glement par virement bancaire sans oublier de m'en informer.

Nous comptons sur toi.

A bient�t

Le Tr�sorier"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["rex"]=Array
	(
		"titre"=>"",
		"balise"=>"url,editeur",
		"mail"=>
"Bonjour,

Un nouveau REX a �t� publi�. Je t'invite � en prendre connaissance.
{url}

A bient�t
{editeur}
"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["invite"]=Array
	(
		"titre"=>"Recherche passager(s)",
		"balise"=>"dte_deb,dte_deb_heure,dte_fin_heure,pilote",
		"mail"=>
"Bonjour,

Il me reste des places dans mon vol du {dte_deb} de {dte_deb_heure} � {dte_fin_heure}. Faites moi savoir si cela vous int�resse.

A bient�t
{pilote}
"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["maintenance"]=Array
	(
		"titre"=>"Maintenance avion � planifier",
		"balise"=>"immatriculation,potentiel,dte_maint",
		"mail"=>
"Bonjour,

L'avion {immatriculation} arrive en fin de potentiel.
Potentiel restant : {potentiel}
Estimation de la date de la prochaine maintenance : {dte_maint}

A bient�t
"
	);
// *****************************************************************
	

?>