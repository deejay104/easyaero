<?php
/*
    Easy-Aero
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

<?php
	$page=checkVar("page","varchar");
	
	addPageMenu("",$mod,"Formations",geturl("admin","formations","page=formationslst"),"",($page=="formationslst") ? true : false);
	addPageMenu("",$mod,"Vols",geturl("admin","formations","page=references"),"",($page=="references") ? true : false);
	addPageMenu("",$mod,"Exercices",geturl("admin","formations","page=exercices"),"",($page=="exercices") ? true : false);
	addPageMenu("",$mod,"Prog ENAC",geturl("admin","formations","page=refenac"),"",($page=="refenac") ? true : false);

	$tmpl_x = LoadTemplate($page);
	$tmpl_x->assign("path_root",$MyOpt["host"]);
	$tmpl_x->assign("path_core",$corefolder);
	$tmpl_x->assign("path_module",$module."/".$mod);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	include($appfolder."/modules/admin/".$page.".inc.php");
	
?>
