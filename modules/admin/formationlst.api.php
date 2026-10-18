<?php

// ---- Refuse l'accès en direct
    if ((!isset($token)) || ($token==""))
    { header("HTTP/1.0 401 Unauthorized"); exit; }

    require_once ($appfolder."/class/synthese.inc.php");

// ---- Variable
    $id=checkVar("id","numeric");
    $fonc=checkVar("fonc","varchar");

// ----

    if (($_SERVER['REQUEST_METHOD']=="GET") && ($id>0))
    {
        $formation=new formation_class($id,$sql);
        $result=$formation->export();
    }
    else if ($_SERVER['REQUEST_METHOD']=="GET")
    {
        $result=array();

        $c=checkVar("columns","array");
        $s=checkVar("order","array");
        $draw=checkVar("draw","numeric");
        $ts=checkVar("start","numeric");
        $tl=checkVar("length","numeric");
        $search=checkVar("search","array");

        
        $lst=ListFormation($sql);

        $result["draw"]=$draw;
        $result["recordsTotal"]=count($lst);
        $result["recordsFiltered"]=count($lst);
        $result["data"]=array();

        $ii=0;
        foreach($lst as $i=>$d)
        {
            $ref=new formation_class($d["id"],$sql);

            $result["data"][$ii]["id"]=$d["id"];
/*
            $result["data"][$i]["description"]="<div onclick='editFormation(".$d["id"].");return false;'>".$ref->aff("description")."</div>";
            $result["data"][$i]["version"]="<div onclick='editFormation(".$d["id"].");return false;'>".$ref->aff("version")."</div>";
            $result["data"][$i]["status"]="<div onclick='editFormation(".$d["id"].");return false;'>".$ref->aff("status")."</div>";
            $result["data"][$i]["encours"]="<div onclick='editFormation(".$d["id"].");return false;'>".$ref->EnCours()."</div>";
*/
            $result["data"][$ii]["description"]="<a onclick='editFormation(".$d["id"].");'>".$ref->aff("description")."</a>";
            $result["data"][$ii]["version"]="<a onclick='editFormation(".$d["id"].");'>".$ref->aff("version")."</a>";
            $result["data"][$ii]["status"]="<a onclick='editFormation(".$d["id"].");'>".$ref->aff("status")."</a>";
            $result["data"][$ii]["encours"]="<a onclick='editFormation(".$d["id"].");'>".$ref->EnCours()."</a>";
            $ii++;
        }

    }
    else if ($_SERVER['REQUEST_METHOD']=="POST")
    {
        $data=checkVar("data","array");
        $formation=new formation_class($id,$sql);
        $formation->Valid("description",$data["description"]);
        $formation->Valid("status",$data["status"]);
        $formation->Save();

        $result=array("status"=>200);
    }
    else if ($_SERVER['REQUEST_METHOD']=="PATCH")
    {
        // Créer une nouvelle version de la formation
        $formation_src=new formation_class($id,$sql);
        $formation_dst=new formation_class(0,$sql);
        $formation_dst->Valid("description",$formation_src->val("description"));
        $formation_dst->Valid("version",$formation_src->val("version")+1);
        $formation_dst->Valid("status",'draft');
        $formation_dst->Save();

        $formation_src->Valid("status",'closed');
        $formation_src->Save();

        $newid=$formation_dst->id;

        $result=array("status"=>200,"id"=>$newid);

        // Copie la liste des séances (référence)
        $lst=ListeReference($sql,$id);
	
        foreach($lst as $i=>$d)
        {
            $ref_src=new reference_class($d["id"],$sql);
            $ref_dst=new reference_class(0,$sql);

            $ref_dst->valid("idformation",$newid);
            $ref_dst->valid("refffa", $ref_src->val("refffa"));
            $ref_dst->valid("theme", $ref_src->val("theme"));
            $ref_dst->save();
        }
        $result["count"]["reference"]=count($lst);

        // Copie la liste des exercices
        $search=array("actif"=>"oui","idformation"=>$id);
        $lst=ListeExercicesConf($sql,$search,array("id"));

        $result["count"]["progression"]=0;
        foreach($lst as $i=>$d)
        {
            $exo_src=new exercice_conf_class($d["id"],$sql);
            $exo_dst=new exercice_conf_class(0,$sql);

            $exo_dst->valid("idformation", $newid);
            $exo_dst->valid("focus", $exo_src->val("focus"));
            $exo_dst->valid("description", $exo_src->val("description"));
            $exo_dst->valid("competence", $exo_src->val("competence"));
            $exo_dst->valid("compcat", $exo_src->val("compcat"));
            $exo_dst->valid("type", $exo_src->val("type"));
            $exo_dst->valid("module", $exo_src->val("module"));
            $exo_dst->valid("refffa", $exo_src->val("refffa"));
            $exo_dst->valid("refenac", $exo_src->val("refenac"));    
            $exo_dst->save();

            // Copie les progressions
            $lst_prog=ListeProgression($sql,$exo_src->id);
            foreach($lst_prog as $ii=>$dd)
            {
                $prog_src=new exercice_prog_class($dd["id"],$sql);
                $prog_dst=new exercice_prog_class(0,$sql);

                $prog_dst->valid("idexercice", $exo_dst->id);
                $prog_dst->valid("refffa", $prog_src->val("refffa"));
                $prog_dst->valid("progression", $prog_src->val("progression"));    
                $prog_dst->save();
            }
            $result["count"]["progression"]=$result["count"]["progression"]+count($lst_prog);
        }
        $result["count"]["exercice"]=count($lst);



    }


	echo json_encode($result);
?>