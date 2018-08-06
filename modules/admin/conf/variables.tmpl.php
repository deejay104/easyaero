<?
// ---------------------------------------------------------------------------------------------
//   Variables
// ---------------------------------------------------------------------------------------------

// Titre du site
$MyOptTmpl["site_title"]="Easy-Aero";

// email par d�fault d'envoie des mails
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

// Compte par d�faut pour le tableau de bord
$MyOptTmpl["uid_tableaubord"]=$MyOptTmpl["uid_club"];
$MyOptHelp["uid_tableaubord"]="Compte par d�faut pour le tableau de bord";

// ID poste pour facturation manifestation
$MyOptTmpl["id_PosteManip"]=0;
$MyOptHelp["id_PosteManip"]="ID du poste pour facturation manifestation";

// ID poste pour factures
$MyOptTmpl["id_PosteFacture"]=0;
$MyOptHelp["id_PosteFacture"]="ID du poste pour le cr�dit des factures";

// ID poste pour les transf�re
$MyOptTmpl["id_PosteTransfere"]=0;
$MyOptHelp["id_PosteTransfere"]="ID du poste pour le transf�re vers le compte d'un autre membre";

// ID poste pour les cr�dits de compte
$MyOptTmpl["PosteCredite"]["virement"]=0;
$MyOptHelp["PosteCredite"]["virement"]="ID du poste pour le cr�dit de son comptepar virement";
$MyOptTmpl["PosteCredite"]["cheque"]=0;
$MyOptHelp["PosteCredite"]["cheque"]="ID du poste pour le cr�dit de son compte par ch�que";
$MyOptTmpl["PosteCredite"]["espece"]=0;
$MyOptHelp["PosteCredite"]["espece"]="ID du poste pour le cr�dit de son compte par esp�ces";
$MyOptTmpl["PosteCredite"]["vacances"]=0;
$MyOptHelp["PosteCredite"]["vacances"]="ID du poste pour le cr�dit de son compte par ch�que vacances";

// Coordonn�es terrain
$MyOptTmpl["terrain"]["nom"]="Neuhof";
$MyOptHelp["terrain"]["nom"]="Nom du terrain d'origine";
$MyOptTmpl["terrain"]["longitude"]=7.77750;
$MyOptHelp["terrain"]["longitude"]="Longitude du terrain (n�gatif si � l'est)";
$MyOptTmpl["terrain"]["latitude"]=48.55360;
$MyOptHelp["terrain"]["latitude"]="Latitude du terrain";

// D�but et fin de la journ�e de r�servation
$MyOptTmpl["debjour"]="6";
$MyOptHelp["debjour"]="Heure du d�but de la journ�e (pour l'affichage du calendrier)";
$MyOptTmpl["finjour"]="22";
$MyOptHelp["finjour"]="Heure de fin de la journ�e";

// Unit�s
$MyOptTmpl["unitPoids"]="kg";
$MyOptHelp["unitPoids"]="Unit� des poids";
$MyOptTmpl["unitVol"]="L";
$MyOptHelp["unitVol"]="Unit� des volumes";

// Texte � accepter pour une r�servation
$MyOptTmpl["TxtValidResa"]="Pilote, il est de votre responsabilit� de v�rifier que vous respectez bien les conditions d�exp�rience r�cente pour voler sur cet avion. Au del� de 3 mois maximum sans voler : un vol avec un instructeur du club est obligatoire.<br />Confirmer que vous avez vol� depuis moins de 3 mois sur cet avion ou assimil�.";
$MyOptHelp["TxtValidResa"]="Texte � afficher si les conditions de vol pour le pilote ne sont pas satisfaites";
$MyOptTmpl["ChkValidResa"]="on";
$MyOptHelp["ChkValidResa"]="Active l'affichage du texte � confirmer (on=Activ�)";


// Nombre de lignes affich�es pour la ventilation
$MyOptTmpl["ventilationNbLigne"]="4";
$MyOptHelp["ventilationNbLigne"]="Nombre de lignes � afficher lors d'une ventilation de mouvement";


// Modules
// on : Affich� et actif
$MyOptTmpl["module"]["aviation"]="on";
$MyOptHelp["module"]["aviation"]="on : Affiche et active le module aviation";
$MyOptTmpl["module"]["compta"]="on";
$MyOptHelp["module"]["compta"]="on : Affiche et active le module comptabilit�";
$MyOptTmpl["module"]["creche"]="";
$MyOptHelp["module"]["creche"]="on : Affiche et active le module cr�che";
$MyOptTmpl["module"]["facture"]="";
$MyOptHelp["module"]["facture"]="on : Affiche et active le module facture";
$MyOptTmpl["module"]["abonnement"]="";
$MyOptHelp["module"]["abonnement"]="on : Affiche et active le module abonnement";

// Restreint la liste des membres pour les famille. Types s�par�s par des virgules (pilote,eleve)
$MyOptTmpl["restrict"]["famille"]="";
$MyOptHelp["restrict"]["famille"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les factures.
$MyOptTmpl["restrict"]["facturation"]="";
$MyOptHelp["restrict"]["facturation"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les comptes.
$MyOptTmpl["restrict"]["comptes"]="";
$MyOptHelp["restrict"]["comptes"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";

// Saisir les vols en facturation
$MyOptTmpl["facturevol"]="";
$MyOptHelp["facturevol"]="Saisi les vols en facturation (on=Activ�)";

// Compense le compte CLUB lors du remboursement d'une facture
$MyOptTmpl["CompenseClub"]="";
$MyOptHelp["CompenseClub"]="Compense le compte CLUB lors du remboursement d'une facture (on=Activ�)";


// Couleurs du calendrier
$MyOptTmpl["tabcolresa"]["own"]="A0E2AF";
$MyOptHelp["tabcolresa"]["own"]="Couleur pour ses r�servations";
$MyOptTmpl["tabcolresa"]["booked"]="A9D7FE";
$MyOptHelp["tabcolresa"]["booked"]="Couleur pour une r�servation autre que les siennes";
$MyOptTmpl["tabcolresa"]["meeting"]="AAFC8F";
$MyOptHelp["tabcolresa"]["meeting"]="Couleur pour une manifestation";
$MyOptTmpl["tabcolresa"]["maintconf"]="dfacac";
$MyOptHelp["tabcolresa"]["maintconf"]="Couleur pour une maintenance confirm�e";
$MyOptTmpl["tabcolresa"]["maintplan"]="eec89e";
$MyOptHelp["tabcolresa"]["maintplan"]="Couleur pour ses maintenance planifi�e";

// Maintiens l'heure bloc �gale � l'horametre
$MyOptTmpl["updateBloc"]="";
$MyOptHelp["updateBloc"]="Maintiens le temps bloc �gale � celui de l'horametre";


?>
