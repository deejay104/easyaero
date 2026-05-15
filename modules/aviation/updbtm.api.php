<?php
// ---- Refuse l'accès en direct
//	if ((!isset($token)) || ($token==""))
//	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ── Configuration ──────────────────────────────────────────────────────────

$stripeKey=$MyOpt["btm_stripeCle"];
$apiSharedSecret=$MyOpt["btm_apiSecret"];


// ──────────────────────────────────────────────────────────────────────────

// ── Authentification ──────────────────────────────────────────────────────
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== $apiSharedSecret) {
    http_response_code(401);
    echo json_encode(['status'=>401,'error' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>405,'error' => 'Méthode non autorisée']);
    exit();
}

// ── Lecture du body ───────────────────────────────────────────────────────
/*
$body = json_decode(file_get_contents('php://input'), true);


if (!$body) {
    http_response_code(400);
    echo json_encode(['status'=>400,'error' => 'Invalid body']);
    exit();
}

$nom         = trim($body['nom']      ?? '');
$prenom      = trim($body['prenom']      ?? '');
$email       = trim($body['email']       ?? '');
$telephone   = trim($body['telephone']   ?? '');
$passagers   = (int)($body['passagers']  ?? 0);
$type   	 = trim($body['type']   ?? '');
$bonCadeau   = (bool)($body['bon_cadeau'] ?? false);
$commentaire = trim($body['commentaire'] ?? '');

*/


$ref=checkVar("ref","varchar",10);

if (!isset($prestationsBapteme[$ref]["tarif"]))
{
	http_response_code(404);
    echo json_encode(['status'=>404,'ref'=>$ref,'error' => 'Unknown reference']);
    exit();
}

$nom=checkVar("nom","varchar",50);
$prenom=checkVar("prenom","varchar",50);
$email=checkVar("email","varchar",100);
$telephone=checkVar("telephone","varchar",20);
$passagers=checkVar("passagers","numeric");
$bonkdo=checkVar("bon_cadeau","bool");
$commentaire=checkVar("commentaire","text");

$montant = $prestationsBapteme[$ref]["tarif"];
$type = $prestationsBapteme[$ref]["type"];


// ── Mise à jour baptème ────────────────────────────────────────────────────
require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$btm = new bapteme_class(0,$sql);
$btm->Create();
$btm->Valid("nom",$prenom." ".$nom);
$btm->Valid("mail",$email);
$btm->Valid("telephone",$telephone);
$btm->Valid("nb",$passagers);
$btm->Valid("type",$type);
$btm->Valid("bonkdo",($bonkdo) ? "oui" : "non");
$btm->Valid("description",$commentaire);
$btm->Valid("montant",$montant);
$btm->Valid("uuid",$btm->genuuid());
$btm->Valid("status",8);
$btm->Save();


// ── Création du PaymentIntent Stripe ─────────────────────────────────────
// Downloaded from https://github.com/stripe/stripe-php/releases
require_once "external/stripe-php/init.php";

\Stripe\Stripe::setApiKey($stripeKey);

$commandeId=$btm->val("num");

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount'               => $montant,
        'currency'             => 'eur',
        'receipt_email'        => $email,
        'description'          => ($type=="vi") ? 'Vol initiation' : 'Vol decouverte',
        'metadata'             => [
            'commande_id' => $commandeId,
            'prenom'      => $prenom,
            'nom'         => $nom,
            'type'        => $type,
        ],
        'automatic_payment_methods' => ['enabled' => true],
    ]);

	$btm->Valid("stripe_id",$paymentIntent->id);
	$btm->Save();
} 
catch (\Stripe\Exception\ApiErrorException $e)
{
    error_log('[init.php] Stripe error: ' . $e->getMessage());

    // Supprime la commande créée si Stripe échoue
 	$btm->Valid("status",6);
	$btm->Save();

    http_response_code(502);
    echo json_encode(['status'=>502,'error' => 'Erreur Stripe : ' . $e->getMessage()]);
    exit();
}

// ── Notification de la création d'un nouveau baptème ─────────────────────
$tabvar=array(
	"id"=>$btm->id,
	"num"=>$btm->Aff("num"),
	"type"=>$btm->Aff("type"),
);
$tabUser=array();
$lst=ListActiveUsers($sql,"",array("NotifBapteme"),"non");

foreach($lst as $i=>$id)
{
	$usr = new user_class($id,$sql,false,true);
	if ($usr->data["mail"]!="")
	{
		$res["mail"]=SendMailFromFile("noreply@easy-aero.fr",$usr->data["mail"],"","",$tabvar,"btm_create");
	}
}


// ── Réponse au site public ────────────────────────────────────────────────
http_response_code(200);
echo json_encode([
	'status'       => 200,
	'success'      => true,
	'commande_id'  => $commandeId,
	'clientSecret' => $paymentIntent->client_secret,
	'montant'      => $montant,
]);

exit;







	$res=array();
	$msg_erreur="";
	$msg_confirmation="";

	$data=json_decode(file_get_contents('php://input'), true);
	if ($data=="")
	{
		$data=array( 
			"data"=>checkVar("data","array")
		);
	}

	$id=0;
	$num=0;
	if (isset($data["data"]["bid"]))
	{
		preg_match("/^#?([0-9]{6})$/",$data["data"]["bid"],$t);
		$num=(isset($t[1])) ? $t[1] : "";
	}
	if (isset($data["data"]["id"]) && ($data["data"]["id"]>0))
	{
		$id=$data["data"]["id"];
	}

	$res["rid"]=$id;
	$res["bid"]=$num;

	$btm = new bapteme_class($id,$sql);

	if ($id>0)
	{
		if (isset($data["data"]["description"]))
		{
			$btm->data["description"].="\r\n\r\n** ".date("d/m/Y H:i")." - ".$myuser->fullname." :\r\n";
			$btm->data["description"].=$data["data"]["description"];
			$res["description"]="** ".date("d/m/Y H:i")." - ".$myuser->fullname." :<br />".nl2br($data["data"]["description"]);
		}
		$name="";
	}
	else if ($num>0)
	{
		$id=$btm->getid($num);
		$btm->load($id);

		if (isset($data["data"]["description"]))
		{
	//		$msg_erreur.=$btm->Valid("description", $btm->data["description"]."\\n".$data["data"]["description"]);
			$btm->data["description"].="\r\n\r\n** ".date("d/m/Y H:i")." - ".$data["data"]["nom"]." (".$data["data"]["mail"].") :\r\n";
			$btm->data["description"].=$data["data"]["description"];
		}
		$name="btm_update";
	}
	else
	{
		$btm->Create();
		if (count($data["data"])>0)
		{
			foreach($data["data"] as $k=>$v)
			{
				$msg_erreur.=$btm->Valid($k,$v);
			}
		}
		$name="btm_create";
	}
	
	$btm->Save();

	if ($name!="")
	{
		$tabvar=array(
			"id"=>$btm->id,
			"num"=>$btm->Aff("num"),
			"type"=>$btm->Aff("type"),
		);
		$tabUser=array();
		$lst=ListActiveUsers($sql,"",array("NotifBapteme"),"non");

		foreach($lst as $i=>$id)
		{
			$usr = new user_class($id,$sql,false,true);
			if ($usr->data["mail"]!="")
			{
				$res["mail"]=SendMailFromFile("noreply@easy-aero.fr",$usr->data["mail"],"","",$tabvar,$name);
			}
		}
	}

	// Send JSON to the client.
	$res["status"]=200;
	$res["id"]=$btm->id;
	$res["num"]=$btm->data["num"];
	$res["error"]=$msg_erreur;
	echo json_encode($res);
  
?>