<?php

    $id=checkVar("id", "numeric");
    $data=checkVar("data", "array");
    $comment=$data["comment"];

    require_once ($appfolder."/class/bapteme.inc.php");
    require_once ($appfolder."/class/user.inc.php");
    require_once ("class/document.inc.php");

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



    $usr=new user_core($btm_note->uid_creat,$sql);
    $author=$usr->Val("fullname");

    $lstdoc=ListDocument($sql,$btm_note->uid_creat,"avatar");

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
        "id"=>$btm_note->id,
        "author"=>$author,
        "avatar"=>$avatar,
        "dte_create"=> date("d/m/Y H:i",strtotime($btm_note->dte_creat)),
        "customer"=>$btm->val("mail"),
    );

    if (($btm->val("uuid")!="") && ($btm->val("mail")!="")) 
    {
        $tabType = array(
            "btm" => "Baptême de l'air",
            "vi" => "Vol d'initiation",
        );

        $tabvar=array(
            "id"=>$btm->id,
            "num"=>$btm->val("num"),
            "type"=>$tabType[$btm->val("type")],
            "uuid"=>$btm->val("uuid"),
        );
        $from=array(
            "name"=>"Aéroclub Polygone 67",
            "mail"=>"noreply@polygone67.com"
        );
        $res["mail"]=SendMailFromFile($from,$btm->val("mail"),"","Nouveau message pour votre réservation #".$btm->val("num"),$tabvar,"btm_comment");

    }

    echo json_encode($res);

?>