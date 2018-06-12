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
	$tabMails["decouvert"]=Array
	(
		"titre"=>"Compte à découvert",
		"balise"=>"solde",
		"mail"=>
"Bonjour,

Sauf erreur de notre part, le solde ton compte est de {solde} €.
Conformément à notre règlement intérieur et afin de préserver la santé financière de notre association, je te demande de faire le nécessaire au plus vite afin de régulariser ta situation. Dans un souci de rapidité, nous te demandons de privilégier dans le mesure du possible un règlement par virement bancaire sans oublier de m'en informer.

Nous comptons sur toi.

A bientôt

Le Trésorier"
	);
// *****************************************************************

?>