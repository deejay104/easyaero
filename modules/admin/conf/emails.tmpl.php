<?

// *****************************************************************
	$tabMails["resa_inst"]=Array
	(
		"titre"=>"Notification de réservation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote",
		"mail"=>
"Bonjour,

Je t'ai ajouté en tant qu'instructeur sur la réservation suivante :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>Détail</a>

A bientôt au club
{pilote}"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["resa_modif"]=Array
	(
		"titre"=>"Modification de votre réservation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote,editeur",
		"mail"=>
"Bonjour,

La réservation suivante a été modifiée par {editeur} :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>Voir plus de détail</a>

A bientôt au club
"
	);
// *****************************************************************
// *****************************************************************
	$tabMails["resa_supp"]=Array
	(
		"titre"=>"Suppression de votre réservation",
		"balise"=>"avion,dte_deb,dte_fin,url,pilote,editeur",
		"mail"=>
"Bonjour,

La réservation suivante a été supprimée par {editeur} :

{avion} du {dte_deb} au {dte_fin}

<a href='{url}'>Voir plus de détail</a>

A bientôt au club
"
	);
// *****************************************************************


// *****************************************************************
	$tabMails["decouvert"]=Array
	(
		"titre"=>"Compte à découvert",
		"balise"=>"solde",
		"mail"=>
"Bonjour,

Sauf erreur de notre part, le solde ton compte est de {solde}.
Conformément à notre règlement intérieur et afin de préserver la santé financière de notre association, je te demande de faire le nécessaire au plus vite afin de régulariser ta situation. Dans un souci de rapidité, nous te demandons de privilégier dans le mesure du possible un règlement par virement bancaire sans oublier de m'en informer.

Nous comptons sur toi.

A bientôt

Le Trésorier"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["rex"]=Array
	(
		"titre"=>"",
		"balise"=>"url,editeur",
		"mail"=>
"Bonjour,

Un nouveau REX a été publié. Je t'invite à en prendre connaissance.
{url}

A bientôt
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

Il me reste des places dans mon vol du {dte_deb} de {dte_deb_heure} à {dte_fin_heure}. Faites moi savoir si cela vous intéresse.

A bientôt
{pilote}
"
	);
// *****************************************************************

// *****************************************************************
	$tabMails["maintenance"]=Array
	(
		"titre"=>"Maintenance avion à planifier",
		"balise"=>"immatriculation,potentiel,dte_maint",
		"mail"=>
"Bonjour,

L'avion {immatriculation} arrive en fin de potentiel.
Potentiel restant : {potentiel}
Estimation de la date de la prochaine maintenance : {dte_maint}

A bientôt
"
	);
// *****************************************************************
	

?>