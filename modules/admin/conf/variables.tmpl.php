<?
// ---------------------------------------------------------------------------------------------
//   Variables
// ---------------------------------------------------------------------------------------------

// Titre du site
$MyOptTmpl["site_title"]="Easy-Aero";

// email par défault d'envoie des mails
$MyOptTmpl["from_email"]="noreply@easy-aero.fr";


// Uid Banque
$MyOptTmpl["uid_banque"]=3;
$MyOptHelp["uid_banque"]="ID du compte Banque";

// Uid Club
$MyOptTmpl["uid_club"]=4;
$MyOptHelp["uid_club"]="ID du compte club";

// UID Bapteme
$MyOptTmpl["uid_bapteme"]=53;
$MyOptHelp["uid_bapteme"]="ID du compte bapteme";

// Compte par défaut pour le tableau de bord
$MyOptTmpl["uid_tableaubord"]=$MyOptTmpl["uid_club"];
$MyOptHelp["uid_tableaubord"]="Compte par défaut pour le tableau de bord";

// ID poste pour facturation manifestation
$MyOptTmpl["id_PosteManip"]=0;
$MyOptHelp["id_PosteManip"]="ID du poste pour facturation manifestation";

// ID poste pour factures
$MyOptTmpl["id_PosteFacture"]=0;
$MyOptHelp["id_PosteFacture"]="ID du poste pour le crédit des factures";

// ID poste pour les transfère
$MyOptTmpl["id_PosteTransfere"]=0;
$MyOptHelp["id_PosteTransfere"]="ID du poste pour le transfère vers le compte d'un autre membre";

// ID poste pour les crédits de compte
$MyOptTmpl["PosteCredite"]["virement"]=0;
$MyOptHelp["PosteCredite"]["virement"]="ID du poste pour le crédit de son comptepar virement";
$MyOptTmpl["PosteCredite"]["cheque"]=0;
$MyOptHelp["PosteCredite"]["cheque"]="ID du poste pour le crédit de son compte par chèque";
$MyOptTmpl["PosteCredite"]["espece"]=0;
$MyOptHelp["PosteCredite"]["espece"]="ID du poste pour le crédit de son compte par espèces";
$MyOptTmpl["PosteCredite"]["vacances"]=0;
$MyOptHelp["PosteCredite"]["vacances"]="ID du poste pour le crédit de son compte par chèque vacances";

// Coordonnées terrain
$MyOptTmpl["terrain"]["nom"]="Neuhof";
$MyOptHelp["terrain"]["nom"]="Nom du terrain d'origine";
$MyOptTmpl["terrain"]["longitude"]=7.77750;
$MyOptHelp["terrain"]["longitude"]="Longitude du terrain (négatif si à l'est)";
$MyOptTmpl["terrain"]["latitude"]=48.55360;
$MyOptHelp["terrain"]["latitude"]="Latitude du terrain";

// Début et fin de la journée de réservation
$MyOptTmpl["debjour"]="6";
$MyOptHelp["debjour"]="Heure du début de la journée (pour l'affichage du calendrier)";
$MyOptTmpl["finjour"]="22";
$MyOptHelp["finjour"]="Heure de fin de la journée";

// Unités
$MyOptTmpl["unitPoids"]="kg";
$MyOptHelp["unitPoids"]="Unité des poids";
$MyOptTmpl["unitVol"]="L";
$MyOptHelp["unitVol"]="Unité des volumes";

// Texte à accepter pour une réservation
$MyOptTmpl["TxtValidResa"]="Pilote, il est de votre responsabilité de vérifier que vous respectez bien les conditions d’expérience récente pour voler sur cet avion. Au delà de 3 mois maximum sans voler : un vol avec un instructeur du club est obligatoire.<br />Confirmer que vous avez volé depuis moins de 3 mois sur cet avion ou assimilé.";
$MyOptHelp["TxtValidResa"]="Texte à afficher si les conditions de vol pour le pilote ne sont pas satisfaites";
$MyOptTmpl["ChkValidResa"]="on";
$MyOptHelp["ChkValidResa"]="Active l'affichage du texte à confirmer (on=Activé)";


// Nombre de lignes affichées pour la ventilation
$MyOptTmpl["ventilationNbLigne"]="4";
$MyOptHelp["ventilationNbLigne"]="Nombre de lignes à afficher lors d'une ventilation de mouvement";


// Modules
// on : Affiché et actif
$MyOptTmpl["module"]["aviation"]="on";
$MyOptHelp["module"]["aviation"]="on : Affiche et active le module aviation";
$MyOptTmpl["module"]["compta"]="on";
$MyOptHelp["module"]["compta"]="on : Affiche et active le module comptabilité";
$MyOptTmpl["module"]["creche"]="";
$MyOptHelp["module"]["creche"]="on : Affiche et active le module crèche";
$MyOptTmpl["module"]["facture"]="";
$MyOptHelp["module"]["facture"]="on : Affiche et active le module facture";
$MyOptTmpl["module"]["abonnement"]="";
$MyOptHelp["module"]["abonnement"]="on : Affiche et active le module abonnement";

// Restreint la liste des membres pour les famille. Types séparés par des virgules (pilote,eleve)
$MyOptTmpl["restrict"]["famille"]="";
$MyOptHelp["restrict"]["famille"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les factures.
$MyOptTmpl["restrict"]["facturation"]="";
$MyOptHelp["restrict"]["facturation"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les comptes.
$MyOptTmpl["restrict"]["comptes"]="";
$MyOptHelp["restrict"]["comptes"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";

// Saisir les vols en facturation
$MyOptTmpl["facturevol"]="";
$MyOptHelp["facturevol"]="Saisi les vols en facturation (on=Activé)";

// Compense le compte CLUB lors du remboursement d'une facture
$MyOptTmpl["CompenseClub"]="";
$MyOptHelp["CompenseClub"]="Compense le compte CLUB lors du remboursement d'une facture (on=Activé)";


// Couleurs du calendrier
$MyOptTmpl["tabcolresa"]["own"]="A0E2AF";
$MyOptHelp["tabcolresa"]["own"]="Couleur pour ses réservations";
$MyOptTmpl["tabcolresa"]["booked"]="A9D7FE";
$MyOptHelp["tabcolresa"]["booked"]="Couleur pour une réservation autre que les siennes";
$MyOptTmpl["tabcolresa"]["meeting"]="AAFC8F";
$MyOptHelp["tabcolresa"]["meeting"]="Couleur pour une manifestation";
$MyOptTmpl["tabcolresa"]["maintconf"]="dfacac";
$MyOptHelp["tabcolresa"]["maintconf"]="Couleur pour une maintenance confirmée";
$MyOptTmpl["tabcolresa"]["maintplan"]="eec89e";
$MyOptHelp["tabcolresa"]["maintplan"]="Couleur pour ses maintenance planifiée";

// Maintiens l'heure bloc égale à l'horametre
$MyOptTmpl["updateBloc"]="";
$MyOptHelp["updateBloc"]="Maintiens le temps bloc égale à celui de l'horametre";


?>
