<?php
// ---------------------------------------------------------------------------------------------
//   Définitions des roles
// ---------------------------------------------------------------------------------------------


// ---- Liste des roles disponibles

$tabRoles["TypeMembre"]="Membre";
$tabRoles["TypePilote"]="Pilote";
$tabRoles["TypeInstructeur"]="Instructeur";

$tabRoles["AccesAbonnements"]="Accès à la liste des abonnements";
$tabRoles["AccesExport"]="Accès à la page des exports";
$tabRoles["AccesFactures"]="Accès à la liste des factures";
$tabRoles["AccesReservations"]="Accès au calendrier de réservations";
$tabRoles["AccesMaintenances"]="Accès à la liste des maintenances";

$tabRoles["AccesSuiviClub"]="Accès aux pages de suivi du club";
$tabRoles["AccesSuivi"]="Accès au module de suivi du club";

$tabRoles["AccesSuiviMouvements"]="Accès à la page de saisi d'un mouvement";
$tabRoles["AccesSuiviEcheances"]="Accès à la page de suivi des échéances";
$tabRoles["AccesSuiviBanque"]="Accès à la page de suivi des comptes";
$tabRoles["AccesSuiviListeComptes"]="Accès à la page de la liste des comptes";
$tabRoles["AccesSuiviListeTarifs"]="Accès à la page des tarifs";
$tabRoles["AccesSuiviTableauBord"]="Accès à la page des tableau de bord";
$tabRoles["AccesSuiviBilan"]="Accès à la page du bilan comptable";

$tabRoles["AccesSuiviVolsMembres"]="Accès à la page de suivi des membres par les instructeur pour les vols";
$tabRoles["AccesSuiviHorametre"]="Accès à la page de suivi des horamètres";
$tabRoles["AccesSuiviAnnuel"]="Accès à la page de suivi annuel des vols pour tous les membres";
$tabRoles["AccesSuiviTaxeAT"]="Accès à la page de saisie des taxes d'atterrissage";


$tabRoles["AccesCompte"]="Accès à la page d'affichage du compte";
$tabRoles["AccesTransfert"]="Accès à la page de transfert d'un compte à l'autre";
$tabRoles["AccesCredite"]="Accès à la page de crédit de son compte";
$tabRoles["AccesIndicateurs"]="Accès à la page d'indicateurs";

$tabRoles["AccesPresence"]="Accès à la page des présences";
$tabRoles["AccesPresences"]="Accès au tableau des présences";
$tabRoles["AccesPresencePonctuelle"]="Accès à la page de saisie d'une présence ponctuelle";
$tabRoles["AccesMembre"]="Accès au détail d'un membre";
$tabRoles["AccesMembres"]="Accès à la liste des membres";
$tabRoles["AccesFamille"]="Accès à l'affichage des détails d'une famille";
$tabRoles["AccesFamilles"]="Accès à la liste des familles";
$tabRoles["AccesComptabilite"]="Accès aux pages de comptabilités";
$tabRoles["AccesVacances"]="Accès à la page de saisie des vacances";
$tabRoles["AccesPlage"]="Accès à la page de configuration des plages horaires";
$tabRoles["AccesVols"]="Accès au suivi des vols";
$tabRoles["AccesSuiviVols"]="Accès au suivi des vols des membres";
$tabRoles["AccesDisponibilites"]="Accès à la page de saisi des disponibilités";

$tabRoles["AccesConfiguration"]="Accès au module de configuration";
$tabRoles["AccesConfigComptes"]="Accès à la page de modification des numéro de comptes";
$tabRoles["AccesConfigPostes"]="Accès à la page de modification des postes";
$tabRoles["AccesConfigTarifs"]="Accès à la page de configuration des tarifs";
$tabRoles["AccesConfigPrevisions"]="Accès à la page de gestion des prévisions";
$tabRoles["AccesConfigNavigation"]="Accès à la page de configuration des points de navigation";
$tabRoles["AccesConfigInstruction"]="Accès à la page de configuration des synthèses de vol";


$tabRoles["AccesConfigExercices"]="Accès à la page de configuration de la liste des exercices (Synthèse de vol)";
$tabRoles["AccesConfigReferences"]="Accès à la page de configuration de la liste des références d'exercice (Synthèse de vol)";
$tabRoles["AccesConfigRefEnac"]="Accès à la page de configuration de la liste des références ENAC (Synthèse de vol)";



$tabRoles["AffUserComptes"]="Afficher le solde du compte sur la fiche du membre";
$tabRoles["AffUserHeures"]="Afficher les heure de vol sur la fiche du membre";

$tabRoles["ModifUserLache"]="Lacher des membres sur les ressources (avions)";
$tabRoles["ModifUserDecouvert"]="Modifier le découvert d'un membre";
$tabRoles["ModifUserTarif"]="Modifier le taux de remboursement horaire d'un instructeur";
$tabRoles["ModifUserIdCpt"]="Modifier le compte de saisie";
$tabRoles["ModifUserDisponibilite"]="Modifier les disponibilités";
$tabRoles["ModifUserDteInscription"]="Modifier la date d'inscription";

$tabRoles["ModifParents"]="Modifier les parents";
$tabRoles["ModifRessourceSauve"]="Sauvegarde une ressource (avion)";
$tabRoles["ModifRessource"]="Modifier les ressources (avion)";
$tabRoles["ModifRessourceParametres"]="Modification des paramètres d'une ressource";
$tabRoles["ModifMessage"]="Modification d'un message dans le module document";
$tabRoles["ModifClasseur"]="Modification d'un classeur dans le module document";

$tabRoles["ModifFamilleCree"]="Créer une famille";

$tabRoles["ModifForum"]="Modifier la liste de forum (et suppression)";
$tabRoles["ModifFamilleSauve"]="Sauvegarder une famille";
$tabRoles["ModifActualite"]="Modifier une actualite";
$tabRoles["ModifWaypoint"]="Import de waypoints";
$tabRoles["ModifNavigation"]="Modification d'une navigation";
$tabRoles["ModifWeb"]="Modification de la partie publique";

$tabRoles["AccesRex"]="Accès à la liste des REX";
$tabRoles["CreeRex"]="Création d'un REX";
$tabRoles["ModifRex"]="Modification des REX";
$tabRoles["ModifRexAll"]="Modification REX e tous les champs";
$tabRoles["ModifRexSynthese"]="Modification de la synthèse d'un REX";
$tabRoles["ModifRexStatus"]="Modication du status d'un REX";
$tabRoles["SupprimeRex"]="Suppression d'un REX";


$tabRoles["AccesSynthese"]="Accès à toutes les fiches de synthèse de vol";
$tabRoles["CreeSynthese"]="Création d'un fiche de synthèse de vol";
$tabRoles["SupprimeSynthese"]="Suppression d'une fiche de synthèse de vol";
$tabRoles["SignSynthese"]="Signature Instructeur d'une fiche de synthèse de vol";
$tabRoles["ModifExercice"]="Modification des exercices du livret de progression";
$tabRoles["SupprimeExercice"]="Suppression d'un exercice dans une fiche de synthèse";




$tabRoles["CreeFacture"]="Créer une nouvelle facture";

$tabRoles["CreeFamille"]="Créer une famille";
$tabRoles["CreeRessource"]="Créer une nouvelle ressource (avion)";
$tabRoles["CreeClasseur"]="Créer un nouveau classeur dans le module document";
$tabRoles["CreeNavigation"]="Créer une navigation";

$tabRoles["SupprimeActualite"]="Suppression d'une actualite";
$tabRoles["SupprimeUser"]="Supprimer un membre";
$tabRoles["SupprimeRessource"]="Supprimer une ressource (avion)";
$tabRoles["SupprimeMessage"]="Supprimer un message dans le module document";
$tabRoles["SupprimeGroupe"]="Supprimer un groupe";

$tabRoles["EnregistreFacture"]="Droit d'enregistrer une facture";

$tabRoles["ListeVols"]="Autorise l'affichage des vols des membres";
$tabRoles["ListeUserSupprime"]="Autorise l'affichage des membres supprimés";
$tabRoles["ListeUserDesactive"]="Autorise l'affichage des membres désactivés";

$tabRoles["AccesAvions"]="Accès au suivi des avions";
$tabRoles["AccesFichesMaintenance"]="Accès à la page de saisi d'une fiche de maintenance";
$tabRoles["AccesFichesValidation"]="Accès à la page de validation des fiches de maintenance";

$tabRoles["CreeMaintenance"]="Création d'une fiche de maintenance";
$tabRoles["ModifMaintenance"]="Modification d'une fiche de maintenance";
$tabRoles["PlanifieMaintenance"]="Planification d'une maintenance";
$tabRoles["ValideFichesMaintenance"]="Valider une fiche de maintenance";
$tabRoles["RefuserFicheMaintenance"]="Refuser une fiche de maintenance";
$tabRoles["SupprimeMaintenance"]="Suppression d'une maintenance";


$tabRoles["AccesManifestations"]="Accès au calendrier des manifestations";
$tabRoles["CreeManifestation"]="Création d'une manifestation";
$tabRoles["ModifManifestation"]="Modification des manifestations";
$tabRoles["ModifParticipant"]="Ajout/Suppression de participants à une manifestation";
$tabRoles["SupprimeManifestation"]="Suppression d'une manifestation";


$tabRoles["ListeFactures"]="Lister l'ensemble des factures";
$tabRoles["PayerFacture"]="Payer des factures";
$tabRoles["FactureManips"]="Facture une manifestation";
$tabRoles["AfficheDetailMouvement"]="Affiche le détail d'un mouvement";
$tabRoles["AfficheSignatureCompte"]="Vérifie la signature des transactions";

// Baptèmes
$tabRoles["CreeBapteme"]="Création d'un baptème";
$tabRoles["AccesBaptemes"]="Accès à la page des baptèmes";
$tabRoles["AccesBapteme"]="Accès à la page d'un baptème";
$tabRoles["ModifBapteme"]="Modifier un baptème";
$tabRoles["ModifBaptemePaye"]="Modification de la date de paiement d'un baptème";
$tabRoles["SupprimeBapteme"]="Suppression d'un baptème";



$tabRoles["NotifDecouvert"]="Personne à mettre en copie des notifications de découvert";
$tabRoles["NotifMaintenance"]="Personne à mettre à notifier lorsqu'un avion est proche de la prochaine maintenance";



?>
