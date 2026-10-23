<?php

	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }
  
	if  (!GetDroit("ModifEvent"))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }
  
// ----
   

    $id=checkVar("id", "numeric");
    $data=checkVar("data", "array");
    $comment=$data["comment"];

    require_once ($appfolder."/class/evenement.inc.php");
    require_once ($appfolder."/class/user.inc.php");
    require_once ("class/document.inc.php");

    $evt = new event_class($id,$sql);
    if ($evt->id==0) {
        http_response_code(404);
        echo json_encode(['status'=>404,'error' => 'Event not found']);
        exit();
    }

    $evt_note = new event_comment_class(0,$sql);
    $evt_note->Create();
    $evt_note->Valid("id_event",$evt->id);
    $evt_note->Valid("comment",$comment);
    $evt_note->Save();


// ---- Génère la réponse
    $usr=new user_core($evt_note->uid_creat,$sql);
    $author=$usr->Val("fullname");

    $lstdoc=ListDocument($sql,$evt_note->uid_creat,"avatar");

    if (count($lstdoc)>0)
    {
        $img=new document_core($lstdoc[0],$sql);
        $avatar=$img->GenerePath(64,64);
    }
    else
    {
        $avatar="static/images/icn64_membre.png";
    }

    $res=array(
        "status"=>200,
        "id"=>$evt_note->id,
        "author"=>$author,
        "avatar"=>$avatar,
        "dte_create"=> date("d/m/Y H:i",strtotime($evt_note->dte_creat)),
    );


    echo json_encode($res);

?>