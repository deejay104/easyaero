<?php

header('Content-Type: application/json');


define('API_SHARED_SECRET',  getenv('API_SHARED_SECRET')  ?: $MyOpt["btm_apiSecret"]);

// ── Authentification ──────────────────────────────────────────────────────
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

/*
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>405,'error' => 'Unauthorized method']);
    exit();
}
*/

// ──────────────────────────────────────────────────────────────────────────

$uuid=checkVar("uuid", "uuid");
$comment=checkVar("commentaire", "text");

if ($uuid == '') {
    http_response_code(404);
    echo json_encode(['status'=>404,'error' => 'Flight not found']);
    exit();
}


require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");


$btm = new bapteme_class(0,$sql);
$btm->loadFromField(array("uuid"=>$uuid));

if ($btm->id==0) {
    http_response_code(404);
    echo json_encode(['status'=>404,'error' => 'Flight not found']);
    exit();
}

$btm_note = new bapteme_comment_class(0,$sql);
$btm_note->Create();
$btm_note->Valid("id_btm",$btm->id);
$btm_note->Valid("comment",$comment);
$btm_note->Save();

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
        $res["mail"]=SendMailFromFile("noreply@easy-aero.fr",$usr->data["mail"],"","",$tabvar,"btm_update");
    }
}

$res=array(
    "status"=>200,
    "uuid"=>$uuid,
);
echo json_encode($res);

?>