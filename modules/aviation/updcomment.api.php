<?php

$id=checkVar("id", "numeric");
$data=checkVar("data", "array");
$comment=$data["comment"];

require_once ($appfolder."/class/bapteme.inc.php");
require_once ($appfolder."/class/user.inc.php");

$btm = new bapteme_class($id,$sql);
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

$res=array(
    "status"=>200,
    "id"=>$btm_note->id,
);

echo json_encode($res);

?>