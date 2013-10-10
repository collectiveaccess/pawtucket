<?php
/* ----------------------------------------------------------------------
 * /views/nysa/ca_occ_pdf_html.php 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */	
	$va_occ_info = $this->getVar("occ_info");
	$vs_title = $this->getVar("title");
?>
<HTML>
	<HEAD>
		<style type="text/css">
			<!--
			p, div{font-size: 14px; font-weight:500; line-height: 20px; color: rgb(46, 34, 31); font-family: Helvetica, sans-serif;}
			.pageHeader { background-color: #FFFFFF; margin: 0px; padding: 0px; width: 100%; height: 133px;}
			.divide{ clear:both; border-top:1px solid #828282; }
			.unit { color: #000; margin: 0px 0px 10px 0px; }
			.headerText { color: #000; margin: 0px 0px 10px 20px; }
			H1 {margin-top:10px; font-family: 'Crimson Text',†serif; font-weight: normal; font-size: 30px; line-height: 40px; color: rgb(0, 103, 148);}
			
			-->
		</style>
	</HEAD>
	<BODY>
		<div class='pageHeader'>
<?php
			if(file_exists($this->request->getThemeDirectoryPath().'/graphics/NYSA_Logo.jpg')){
				print '<div style="float:left;"><img src="'.$this->request->getThemeDirectoryPath().'/graphics/NYSA_Logo.jpg"/></div>';
 			}
			if(file_exists($this->request->getThemeDirectoryPath().'/graphics/NYSA_HeaderType.png')){
				print '<div style="float:left; margin: 70px 30px 0px 220px;"><img src="'.$this->request->getThemeDirectoryPath().'/graphics/NYSA_HeaderType.png"/></div>';
 			}
?>
		</div>
		<div class="divide"><!-- empty --></div>

<?php
		print "<H1>".$vs_title."</H1>";
		foreach($va_occ_info as $vs_attribute => $vs_info){
			print "<div class='unit'>".$vs_info."</div>";
		}
?>

	</BODY>
</HTML>