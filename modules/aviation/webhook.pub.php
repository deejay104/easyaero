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


// ── Load Stripe library ───────────────────────────────────────────────────
require_once "external/stripe-php/init.php";

// -- Charge les classes
require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$myuser = new user_core($MyOpt["uid_bapteme"],$sql,true);

// ── Configuration ──────────────────────────────────────────────────────────
$stripeKey=$MyOpt["btm_stripeCle"];
$stripeWebhook=$MyOpt["btm_stripeWebhook"];

// ──────────────────────────────────────────────────────────────────────────

\Stripe\Stripe::setApiKey($stripeKey);

// ── Lecture du payload brut (obligatoire pour la vérification de signature) ──
$payload   = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// ── Vérification de la signature Stripe ───────────────────────────────────
try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $stripeWebhook
    );
} catch (\UnexpectedValueException $e) {
    error_log('[webhook.php] Payload invalide: ' . $e->getMessage());
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    error_log('[webhook.php] Signature invalide: ' . $e->getMessage());
    http_response_code(400);
    exit();
}

// ── Traitement des événements ─────────────────────────────────────────────
$intent = $event->data->object; // PaymentIntent


switch ($event->type) {

    // ✅ Paiement réussi
    case 'payment_intent.succeeded':
        updateCommande($intent->id, 2, $intent);
        envoyerConfirmation($intent);
        break;

    // ❌ Paiement échoué
    case 'payment_intent.payment_failed':
        $raison = $intent->last_payment_error->message ?? 'inconnue';
        error_log("[webhook.php] Paiement échoué pour {$intent->id} : {$raison}");
        updateCommande($intent->id, 9, $intent);
        break;

    // 🚫 Annulé
    case 'payment_intent.canceled':
        updateCommande($intent->id, 6, $intent);
        break;

    default:
        // Événement ignoré — on répond 200 quand même
        break;
}

http_response_code(200);
exit();


// ── Fonctions helper ──────────────────────────────────────────────────────

/**
 * Met à jour le statut de la commande en base.
 */
function updateCommande(string $piId, string $status, object $intent): void
{
    global $sql;

    error_log("[webhook.php] PI: {$piId} Status: {$status} ");
    try {
        $charge  = $intent->latest_charge ?? null;
        $recu    = $charge ? "https://pay.stripe.com/receipts/{$charge}" : null;

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
function envoyerConfirmation(object $intent): void
{
    global $sql;

    try {
        $btm = new bapteme_class(0,$sql);
        $btm->loadFromField(array("stripe_id"=>$intent->id));

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
            "prenom"=>$intent->metadata->prenom ?? '',
            "nom"=>$intent->metadata->nom ?? '',
		);
        $from=array(
            "name"=>"Aéroclub Polygone 67",
            "mail"=>"noreply@polygone67.com"
        );

        SendMailFromFile($from,$btm->val("mail"),"","Confirmation de votre réservation #".$btm->val("num"),$tabvar,"btm_confirm");

    } catch (Exception $e) {
        error_log('[webhook.php] envoyerConfirmation error: ' . $e->getMessage());
    }
}



?>