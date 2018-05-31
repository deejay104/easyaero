<?
/*
    SoceIt v3.0
    Copyright (C) 2018 Matthieu Isorez

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
	
	if ((GetDroit("AccesSuiviMouvements")) && ($theme!="phone"))
	{
	  	$affrub="mouvement";
	}
	else if (GetDroit("AccesSuiviEcheances"))
	{
	  	$affrub="echeances";
	}
	else if ((GetDroit("AccesSuiviVols")) && ($theme!="phone"))
	{
	  	$affrub="vols";
	}	
	else if (GetDroit("AccesSuiviBanque"))
	{
	  	$affrub="suivi";
	}
	else if (GetDroit("AccesSuiviListeComptes"))
	{
	  	$affrub="liste";
	}
	else if (GetDroit("AccesSuiviTableauBord"))
	{
	  	$affrub="tableaubord";
	}
	else if (GetDroit("AccesSuiviBilan"))
	{
	  	$affrub="bilan";
	}
?>
