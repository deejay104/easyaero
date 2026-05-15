<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


// ── Authentification ──────────────────────────────────────────────────────
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status'=>405,'error' => 'Unauthorized method']);
    exit();
}

// ──────────────────────────────────────────────────────────────────────────
$get=checkVar("get", "varchar",10);


$prestationsBapteme=json_decode(preg_replace("/'/",'"',$MyOpt["btm_articles"]));

if ($get=="reflist")
{
    $res=array(
        "status"=>200,
        "data"=>$prestationsBapteme,
        "raw"=>$MyOpt["btm_articles"],
    );
    echo json_encode($res);     
    exit;
}

// ──────────────────────────────────────────────────────────────────────────

$uuid=checkVar("uuid", "uuid");


require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$myuser = new user_core($MyOpt["uid_bapteme"],$sql,true);


$btm = new bapteme_class(0,$sql);
$btm->loadFromField(array("uuid"=>$uuid));


if ($btm->val("paye")=="non") {
    http_response_code(404);
    echo json_encode(['status'=>404,'error' => 'Flight not found']);
    exit();
}


$res["status"]=200;


$usr=new user_core($btm->val("id_pilote"),$sql);
$res["data"]['pilote'] = $usr->val("prenom");

$res["data"]['num'] = $btm->val("num");
$res["data"]['nom'] = $btm->val("nom");
$res["data"]['passager'] = $btm->val("passager");
$res["data"]['mail'] = maskMail($btm->val("mail"));
$res["data"]['telephone'] = maskPhone($btm->val("telephone"));
$res["data"]['nb'] = $btm->val("nb");
$res["data"]['dte'] = $btm->val("dte");
$res["data"]['status'] = $btm->val("status");
$res["data"]['type'] = $btm->val("type");
$res["data"]['bonkdo'] = $btm->val("bonkdo");
$res["data"]['paye'] = $btm->val("paye");


echo json_encode($res);
?>