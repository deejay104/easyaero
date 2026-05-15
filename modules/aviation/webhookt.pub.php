<?php
/**
 * /ext/webhook.php — Site PRIVÉ
 * Reçoit les événements Stripe et met à jour les commandes.
 * À déclarer dans : dashboard.stripe.com → Webhooks → Add endpoint
 *   URL     : https://membres.polygone67.com/ext/webhook.php
 *   Événements à écouter :
 *     - payment_intent.succeeded
 *     - payment_intent.payment_failed
 *     - payment_intent.canceled
 *
 * Dépendances : composer require stripe/stripe-php
 */

// ── Pas de header JSON ici : Stripe attend HTTP 200 vide ──────────────────
require_once "external/stripe-php/init.php";

// -- Charge les classes
require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$myuser = new user_core($MyOpt["uid_bapteme"],$sql,true);


// ── Configuration ──────────────────────────────────────────────────────────
define('STRIPE_SECRET_KEY',    getenv('STRIPE_SECRET_KEY')    ?: 'sk_test_51TVC98HECzBV3f1yLlsemPHBqso5dLVBh6WbNzCAYctmqW6KQndYAJgZgvE1V31GsMPAtTLt4VR1BdUJ6wiV6FRq00gYLIZJf2');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_UKLkHz0RJheOksGbYFFZ8aqOAh6oFE3j');


// Email de notification interne
define('NOTIF_EMAIL', 'matthieu@les-mnms.net');

$intent="";
updateCommande("pi_3TWjPCHECzBV3f1y10kYQOV1", 2, $intent);
envoyerConfirmation("pi_3TWjPCHECzBV3f1y10kYQOV1",$intent);

http_response_code(200);
exit();


// ── Fonctions helper ──────────────────────────────────────────────────────

/**
 * Met à jour le statut de la commande en base.
 */
function updateCommande(string $piId, string $status, string $intent): void
{
    global $sql;

    error_log("[webhook.php] PI: {$piId} Status: {$status} ");
    try {

        $btm = new bapteme_class(0,$sql);
        $btm->loadFromField(array("stripe_id"=>$piId));

        if ($btm->id==0) 
        {
            error_log("[webhook.php] Aucune commande trouvée pour PI: {$piId}");
        }

        $btm->Valid("status",2);
        $btm->Valid("paye","oui");
        $btm->Valid("dte_paye",date("Y-m-d"));
        $btm->Save();    
    }
    catch(Exception $e)
    {
        error_log('[webhook.php] updateCommande error: ' . $e->getMessage());
    }
}

/**
 * Envoie un e-mail de confirmation au client.
 */
function envoyerConfirmation(string $piId, string $intent): void
{
    global $sql;

    try {
        /*
        $stmt = $pdo->prepare("
            SELECT * FROM commandes
            WHERE stripe_payment_intent_id = :pi_id
            LIMIT 1
        ");
        $stmt->execute([':pi_id' => $intent->id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);
*/
        $btm = new bapteme_class(0,$sql);
        $btm->loadFromField(array("stripe_id"=>$piId));

        if ($btm->id==0) 
        {
            return;
        }

        $montant   = number_format($btm->val('montant') / 100, 2, ',', ' ');

		$tabvar=array(
			"id"=>$btm->id,
			"uuid"=>$btm->Val("uuid"),
			"num"=>$btm->Val("num"),
			"type"=>($btm->Val("type")=="vi") ? "vol d'initiation" : "vol découverte",
			"montant"=>$montant,
		);

        $from=array(
            "name"=>"Aéroclub Polygone 67",
            "mail"=>"noreply@polygone67.com"
        );
        SendMailFromFile($from,$btm->val("mail"),"","Confirmation de votre réservation #".$btm->val("num"),$tabvar,"btm_confirm");

        error_log($btm->val("mail"));
//"id,num,type,prenom,uuid"

        /*
        $sujet = "Confirmation de votre réservation – Aéroclub Polygone 67";

        $message = "Bonjour {$prenom},\n\n"
            . "Nous avons bien reçu votre réservation et votre paiement de {$montant} €.\n\n"
            . "Récapitulatif :\n"
            . "  Passagers : {$passagers}\n"
            . "  Bon cadeau : {$bonCadeau}\n"
            . "  Commentaire : {$dateVol}\n\n"
            . "Notre équipe vous contactera prochainement pour confirmer la date et l'heure de votre vol.\n\n"
            . "À bientôt à l'aérodrome de Strasbourg Neuhof !\n"
            . "L'équipe Polygone 67\n"
            . "https://www.polygone67.com";

        $entetes = "From: Aéroclub Polygone 67 <noreply@polygone67.com>\r\n"
            . "Reply-To: contact@polygone67.com\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($commande['email'], $sujet, $message, $entetes);
*/

    } catch (Exception $e) {
        error_log('[webhook.php] envoyerConfirmation error: ' . $e->getMessage());
    }
}


?>