<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://www.polygone67.com');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


define('API_SHARED_SECRET',  getenv('API_SHARED_SECRET')  ?: $MyOpt["btm_apiSecret"]);

// ── Authentification ──────────────────────────────────────────────────────
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

/*
if ($apiKey !== API_SHARED_SECRET) {
    http_response_code(401);
    echo json_encode(['status'=>401,'error' => 'Bad token']);
    exit();
}
*/
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status'=>405,'error' => 'Unauthorized method']);
    exit();
}

// ──────────────────────────────────────────────────────────────────────────
require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$uuid=checkVar("uuid", "uuid");

$btm = new bapteme_class(0,$sql);
$btm->loadFromField(array("uuid"=>$uuid));

if ($btm->val("paye")=="non") {
    http_response_code(404);
    echo json_encode(['status'=>404,'error' => 'Flight not found']);
    exit();
}

$res=array(
    "status"=>200,
    "data"=>array()
);

$lst=ListBaptemeComment($sql,$btm->id);

foreach($lst as $item)
{
    $comment=new bapteme_comment_class($item["id"],$sql);

    if ($comment->uid_creat>0)
    {
        $usr=new user_core($comment->uid_creat,$sql);
        $author=$usr->val("prenom");
    }
    else
    {
        $author="Passager";
    }
    $res["data"][]=array(
        "comment"=>$comment->val("comment"),
        "date"=>$comment->dte_creat,
        "author"=>$author,
    );
}



echo json_encode($res);

?>