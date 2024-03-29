<?
// ---------------------------------------------------------------------------------------------
//   Facturation
//     (@Revision: $usr$ )
// ---------------------------------------------------------------------------------------------
//   Variables  :
//	$usr - id user
//	$facid - id facture	
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2
    Copyright (C) 2012 Matthieu Isorez

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
	if ((!GetDroit("AccesFactures")) && (!GetMyId($fac->uid)))
	  { FatalError("Accès non autorisé (AccesFactures)"); }

  	require_once ($appfolder."/class/facture.inc.php");
  	require_once ($appfolder."/class/user.inc.php");

	$facid=checkVar("facid","varchar",10);

	$tmpl_prg = new XTemplate (MyRep("vide.htm"));
	if ($facid!="")
	{
		$fac = new facture_class($facid,$sql);
		$fac->ChargeLignes();
		$fac->ChargeReglements();
		$fac->FacturePDF();
	}

?>
