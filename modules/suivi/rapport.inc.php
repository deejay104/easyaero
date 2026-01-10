<?php
// ---- Vérifie les droits d'accès
	if (!GetDroit("AccesSuiviRapportAnnuel")) { FatalError("Accès non autorisé (AccesSuiviRapportAnnuel)"); }

    require_once ($appfolder."/class/user.inc.php");

// ---- Affiche le menu
	$aff_menu="";
	require_once($appfolder."/modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Affiche les infos
	$tabTitre=array();
	$tabTitre["age"]["aff"]="";
	$tabTitre["nbeleve"]["aff"]="NB Elèves";
	$tabTitre["nbpilote"]["aff"]="NB Pilote";
	$tabTitre["nbfemme"]["aff"]="Femmes Pilotes";
	$tabTitre["nbhomme"]["aff"]="Hommes Pilotes";
	$tabTitre["nbmembre"]["aff"]="Membres";

    $tabTitreInstructeur=array();
	$tabTitreInstructeur["nom"]["aff"]="Nom";
	$tabTitreInstructeur["heures"]["aff"]="Heures";

    $tabTitreActivite=array();
	$tabTitreActivite["type"]["aff"]="";
	$tabTitreActivite["vols"]["aff"]="Hors Instruction";
	$tabTitreActivite["inst"]["aff"]="Instruction";

// ---- Récupère la liste

    $tabValeur=array();
    $tabValeur[0]=array("age"=>array("aff"=>"Jeunes (-21 ans)","val"=>0),"nbeleve"=>array("val"=>0),"nbpilote"=>array("val"=>0),"nbfemme"=>array("val"=>0),"nbhomme"=>array("val"=>0),"nbmembre"=>array("val"=>0));
    $tabValeur[1]=array("age"=>array("aff"=>"Adultes","val"=>1),"nbeleve"=>array("val"=>0),"nbpilote"=>array("val"=>0),"nbfemme"=>array("val"=>0),"nbhomme"=>array("val"=>0),"nbmembre"=>array("val"=>0));
    $tabValeur[2]=array("age"=>array("aff"=>"Seniors (+65 ans)","val"=>2),"nbeleve"=>array("val"=>0),"nbpilote"=>array("val"=>0),"nbfemme"=>array("val"=>0),"nbhomme"=>array("val"=>0),"nbmembre"=>array("val"=>0));


    $tabActivite=array();
    $tabActivite[0]=array("type"=>array("aff"=>"VFR Local","val"=>0),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[1]=array("type"=>array("aff"=>"VFR Navigation","val"=>1),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[2]=array("type"=>array("aff"=>"VFR Nuit","val"=>2),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[3]=array("type"=>array("aff"=>"IFR","val"=>3),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[4]=array("type"=>array("aff"=>"Baptème","val"=>4),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[5]=array("type"=>array("aff"=>"BIA","val"=>5),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[6]=array("type"=>array("aff"=>"Heures femme jeune","val"=>6),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[7]=array("type"=>array("aff"=>"Heures femme adulte","val"=>7),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[8]=array("type"=>array("aff"=>"Heures homme jeune","val"=>8),"vols"=>array("val"=>0),"inst"=>array("val"=>0));
    $tabActivite[9]=array("type"=>array("aff"=>"Heures homme adulte","val"=>9),"vols"=>array("val"=>0),"inst"=>array("val"=>0));


    $tabInstructeur=array();

    $lstusr=ListActiveUsers($sql,"std","","non");

    
    $dte_deb='2025-01-01';
    $dte_fin='2026-01-01';

    $iii=0;

    foreach($lstusr as $i=>$id)
    {
        $usr = new user_class($id,$sql);

        $age=$usr->CalcAge($dte_deb);

        $cdb=$usr->NbHeures($dte_deb,$dte_fin,"cdb");
        $ins=$usr->NbHeures($dte_deb,$dte_fin,"dc");

        $tabActivite[0]["vols"]["val"]++;

        if ($age<=21)
        {
            $ii=0;
        }
        else if ($age>=65)
        {
            $ii=2;
        }
        else     
        {
            $ii=1;
        }

        $pilot=0;
        if ($usr->val("groupe")=="ELE")
        {
            $tabValeur[$ii]["nbeleve"]["val"]++;
            $pilot=1;
        }
        else if ($usr->val("groupe")=="MEM")
        {
            $tabValeur[$ii]["nbmembre"]["val"]++;
        }
        else if ($usr->val("groupe")=="INS")
        {
            $tabValeur[$ii]["nbpilote"]["val"]++;
            $pilot=1;

            $tabInstructeur[$iii]["nom"]["val"]=$usr->fullname;
            $tabInstructeur[$iii]["heures"]["val"]=floor($usr->NbHeures($dte_deb,$dte_fin,"inst")/60);
            $iii++;
        }
        else if ($usr->val("groupe")=="INV")
        {
            continue;
        }
        else
        {
            $tabValeur[$ii]["nbpilote"]["val"]++;
            $pilot=1;
        }

        if ($pilot==1)
        {

            if ($usr->val("sexe")=="F")
            {
                $tabValeur[$ii]["nbfemme"]["val"]++;


            }
            else
            {
                $tabValeur[$ii]["nbhomme"]["val"]++;
            }
        }
    }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,"age","d",""));
	$tmpl_x->assign("aff_instructeur",AfficheTableau($tabInstructeur,$tabTitreInstructeur,"nom","d",""));


    // Rapport des vols
    $query = "SELECT cal.temps,cal.destination,cal.uid_instructeur, usr.dte_naissance,usr.sexe FROM `".$MyOpt["tbl"]."_calendrier` AS cal LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON cal.uid_pilote=usr.id WHERE cal.actif='oui' AND cal.dte_deb>='".$dte_deb."' AND cal.dte_fin<'".$dte_fin."'";
	$sql->Query($query);

    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

        if ($sql->data["destination"]=="LOCAL")
        {
            if ($sql->data["uid_instructeur"]>0)
            {
                $tabActivite[0]["inst"]["val"]=$tabActivite[0]["inst"]["val"]+$sql->data["temps"];
    
            }
            else
            {
                $tabActivite[0]["vols"]["val"]=$tabActivite[0]["vols"]["val"]+$sql->data["temps"];
            }
        }
        else
        {
            if ($sql->data["uid_instructeur"]>0)
            {
                $tabActivite[1]["inst"]["val"]=$tabActivite[1]["inst"]["val"]+$sql->data["temps"];
            }
            else
            {
                $tabActivite[1]["vols"]["val"]=$tabActivite[1]["vols"]["val"]+$sql->data["temps"];
            }
        }

        $age=floor(strtotime($dte_deb)-strtotime($sql->data["dte_naissance"]))/(365.25*24*3600);

        if ($sql->data["sexe"]=="F")
        {
            if ($age<=21)
            {
                if ($sql->data["uid_instructeur"]>0)
                {
                    $tabActivite[6]["inst"]["val"]=$tabActivite[6]["inst"]["val"]+$sql->data["temps"];
        
                }
                else
                {
                    $tabActivite[6]["vols"]["val"]=$tabActivite[6]["vols"]["val"]+$sql->data["temps"];
                }
            }
            else
            {
                if ($sql->data["uid_instructeur"]>0)
                {
                    $tabActivite[7]["inst"]["val"]=$tabActivite[7]["inst"]["val"]+$sql->data["tpsreel"];
        
                }
                else
                {
                    $tabActivite[7]["vols"]["val"]=$tabActivite[7]["vols"]["val"]+$sql->data["tpsreel"];
                }
            }
        }
        else
        {
            if ($age<=21)
            {
                if ($sql->data["uid_instructeur"]>0)
                {
                    $tabActivite[8]["inst"]["val"]=$tabActivite[8]["inst"]["val"]+$sql->data["temps"];
        
                }
                else
                {
                    $tabActivite[8]["vols"]["val"]=$tabActivite[8]["vols"]["val"]+$sql->data["temps"];
                }
            }
            else
            {
                if ($sql->data["uid_instructeur"]>0)
                {
                    $tabActivite[9]["inst"]["val"]=$tabActivite[9]["inst"]["val"]+$sql->data["temps"];
        
                }
                else
                {
                    $tabActivite[9]["vols"]["val"]=$tabActivite[9]["vols"]["val"]+$sql->data["temps"];
                }
            }
        }
    }


    $tabActivite[0]["vols"]["val"]=round($tabActivite[0]["vols"]["val"]/60,1);
    $tabActivite[0]["inst"]["val"]=round($tabActivite[0]["inst"]["val"]/60,1);
    $tabActivite[1]["vols"]["val"]=round($tabActivite[1]["vols"]["val"]/60,1);
    $tabActivite[1]["inst"]["val"]=round($tabActivite[1]["inst"]["val"]/60,1);

    $tabActivite[6]["vols"]["val"]=round($tabActivite[6]["vols"]["val"]/60,1);
    $tabActivite[6]["inst"]["val"]=round($tabActivite[6]["inst"]["val"]/60,1);
    $tabActivite[7]["vols"]["val"]=round($tabActivite[7]["vols"]["val"]/60,1);
    $tabActivite[7]["inst"]["val"]=round($tabActivite[7]["inst"]["val"]/60,1);
    $tabActivite[8]["vols"]["val"]=round($tabActivite[8]["vols"]["val"]/60,1);
    $tabActivite[8]["inst"]["val"]=round($tabActivite[8]["inst"]["val"]/60,1);

    $totpil=$tabActivite[0]["vols"]["val"]+$tabActivite[1]["vols"]["val"]-$tabActivite[6]["vols"]["val"]-$tabActivite[7]["vols"]["val"]-$tabActivite[8]["vols"]["val"];
    $totins=$tabActivite[0]["inst"]["val"]+$tabActivite[1]["inst"]["val"]-$tabActivite[6]["inst"]["val"]-$tabActivite[7]["inst"]["val"]-$tabActivite[8]["inst"]["val"];

    $tabActivite[9]["vols"]["val"]=round($tabActivite[9]["vols"]["val"]/60,1)." / ".$totpil;
    $tabActivite[9]["inst"]["val"]=round($tabActivite[9]["inst"]["val"]/60,1)." / ".$totins;

    $query = "SELECT SUM(cal.temps) AS nb FROM `".$MyOpt["tbl"]."_bapteme` AS btm LEFT JOIN ".$MyOpt["tbl"]."_calendrier AS cal ON btm.id_resa=cal.id WHERE cal.dte_deb>='2025-01-01' AND cal.dte_fin<'2026-01-01' AND cal.actif='oui' AND btm.actif='oui' AND btm.type='btm'";
	$res=$sql->QueryRow($query);
    $tabActivite[4]["vols"]["val"]=floor($res["nb"]/60);

    $query = "SELECT SUM(cal.temps) AS nb FROM `".$MyOpt["tbl"]."_bapteme` AS btm LEFT JOIN ".$MyOpt["tbl"]."_calendrier AS cal ON btm.id_resa=cal.id WHERE cal.dte_deb>='2025-01-01' AND cal.dte_fin<'2026-01-01' AND cal.actif='oui' AND btm.actif='oui' AND btm.type='vi'";
	$res=$sql->QueryRow($query);
    $tabActivite[4]["inst"]["val"]=floor($res["nb"]/60);


	$tmpl_x->assign("aff_activite",AfficheTableau($tabActivite,$tabTitreActivite,"type","d",""));


	$query = "SELECT COUNT(*) AS total FROM ".$MyOpt["tbl"]."_calendrier AS cal ";
	$query.= "WHERE dte_deb>='".$dte_deb."' AND dte_deb<'".$dte_fin."' AND (prix<>0 OR temps<>0) AND actif='oui'";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("nb_mouvement",$res["total"]);
    $tmpl_x->assign("nb_pilote",$tabActivite[0]["vols"]["val"]+$tabActivite[1]["vols"]["val"]);
    $tmpl_x->assign("nb_instruction",$tabActivite[0]["inst"]["val"]+$tabActivite[1]["inst"]["val"]);
    $tmpl_x->assign("nb_total",$tabActivite[0]["vols"]["val"]+$tabActivite[1]["vols"]["val"]+$tabActivite[0]["inst"]["val"]+$tabActivite[1]["inst"]["val"]);

// ---- Instructeur
// NB heures double commande


// Activité
/*
nb vol total année


        Hors instruction / Instruction
VFR local
VFR NAV (destination<>LOCAL)
Baptèmes

Heures femme jeune
Heures femme adulte
Heures homme jeune
Heures homme adulte

*/

// ---- Avions
    $tabTitreAvions=array();
    $tabTitreAvions["immat"]["aff"]="Immatriculation";
    $tabTitreAvions["heures"]["aff"]="Heures  Totales";
    $tabTitreAvions["inst"]["aff"]="Instruction";

    $tabAvions=array();

    $query = "SELECT cal.uid_avion AS id,res.immatriculation,cal.temps,cal.uid_instructeur FROM `".$MyOpt["tbl"]."_calendrier` AS cal LEFT JOIN ".$MyOpt["tbl"]."_ressources AS res ON cal.uid_avion=res.id WHERE cal.actif='oui' AND cal.dte_deb>='".$dte_deb."' AND cal.dte_fin<'".$dte_fin."'";
    $sql->Query($query);

    for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

        if (!isset($tabAvions[$sql->data["id"]]["immat"]))
        {
            $tabAvions[$sql->data["id"]]["immat"]["val"]=$sql->data["immatriculation"];
            $tabAvions[$sql->data["id"]]["heures"]["val"]=0;
            $tabAvions[$sql->data["id"]]["inst"]["val"]=0;
        }
        $tabAvions[$sql->data["id"]]["heures"]["val"]=$tabAvions[$sql->data["id"]]["heures"]["val"]+$sql->data["temps"];
        if ($sql->data["uid_instructeur"]>0)
        {
            $tabAvions[$sql->data["id"]]["inst"]["val"]=$tabAvions[$sql->data["id"]]["inst"]["val"]+$sql->data["temps"];
        }
    }

    foreach($tabAvions as $i=>$d)
    {
        $tabAvions[$i]["heures"]["val"]=round($tabAvions[$i]["heures"]["val"]/60,1);
        $tabAvions[$i]["inst"]["val"]=round($tabAvions[$i]["inst"]["val"]/60,1);
    }

	$tmpl_x->assign("aff_avions",AfficheTableau($tabAvions,$tabTitreAvions,"immat","d",""));

    
// ---- Affecte les variables d'affichage

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	  
?>