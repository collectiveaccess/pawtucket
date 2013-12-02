<?php
/* ----------------------------------------------------------------------
 * /views/nysa/custom_worksheet_html.php 
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
	$va_worksheet_info = $this->getVar("worksheet_info");
	$va_images = $this->getVar("images");
	$vs_image_info = $this->getVar("image_info");
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
			H1 {margin-top:10px; font-family: 'Crimson Text',†serif; font-weight: normal; font-size: 24px; line-height: 30px; color: rgb(0, 103, 148);}
			H2 {margin-top:10px; font-family: 'Crimson Text',†serif; font-weight: normal; font-size: 20px; line-height: 20px; color: rgb(0, 103, 148);}
			#footer {
			  position: fixed;
			  bottom: -15px;
			  left: 0px;
			  right: 0px;
			  height: 15px;
			  text-align: center;
			  border-top: 2px solid gray;
			}
			-->
		</style>
	</HEAD>
	<BODY>
		<div id='footer'>
			<b>New York State Archives</b> - http://www.archives.nysed.gov
		</div>
<?php
		foreach($va_worksheet_info as $vs_attribute => $vs_info){
			switch($vs_attribute){
				case "title":
					if($vs_info){
						print "<H1>".$vs_info."</H1>";
					}
				break;
				# -------
				case "questions":
					# --- put the images out before the questions
					if(is_array($va_images) && sizeof($va_images)){
						foreach($va_images as $vs_image){
							print "<div class='unit'>".$vs_image."</div>";
						}
						if($vs_image_info){
							print "<div class='unit'>".$vs_image_info."</div>";
						}
					}
					if($vs_info){
						print "<div class='unit'>".$vs_info."</div>";
					}
				break;
				# -------
				default:
					if($vs_info){
						print "<div class='unit'>".$vs_info."</div>";
					}
				break;
				# -------
			}
		}
?>

	</BODY>
</HTML>