<?
/*
    Easy-Aero
    Copyright (C) 2025 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?
	require_once ($appfolder."/class/reservation.inc.php");
	require_once ($appfolder."/class/user.inc.php");
	require_once ($appfolder."/class/navigation.inc.php");

// ---- Vérifie les variables
	$id=checkVar("id","numeric");
	$order=checkVar("order","varchar",10,"nb");
	$trie=checkVar("trie","varchar",1,"i");
	$ts=checkVar("ts","numeric");

// ---- Affiche la liste des membres
	if (GetDroit("ListeTerrains"))
	{
		if ($id==0)
		  { $id=$gl_uid; }
	}
	else
	{
		$id=$gl_uid;
	}

// ---- Récupère la liste des terrains
    $usr=new user_class($id,$sql);
    $lst=$usr->ListeTerrains();

    $tabValeur=array();

    $i=0;
    foreach ($lst as $id=>$d)
    {
        $tabValeur["data"][$i]["nom"]=$d["nom"];
        $tabValeur["data"][$i]["description"]=$d["description"];
        $tabValeur["data"][$i]["nb"]=$d["nb"];
        $tabValeur["data"][$i]["last"]=$d["last"];
        $tabValeur["data"][$i]["latitude"]=$d["latitude"];
        $tabValeur["data"][$i]["longitude"]=$d["longitude"];
        $i++;
    }

    echo json_encode($tabValeur);
	exit;
?>
