<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/customize_worksheet_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
	$va_worksheet_metadata = $this->getVar("worksheet_attributes");
	$va_images = $this->getVar("images");
	$vs_image_info = $this->getVar("image_info");

if (!$this->request->isAjax()) {
?>
	<div id="detailBody">
		<div id="pageNav">
<?php
			if ($this->getVar('occurrence_id')) {
				print caNavLink($this->request, "&lsaquo; "._t("Back"), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('occurrence_id')), array('id' => 'back'));
			}
?>
		</div><!-- end nav -->
		<h1><?php print $vs_title; ?></h1>	
<?php
			print '<form method="post" action="'.caNavUrl($this->request, 'nysa', 'Download', 'downloadCustomWorksheet').'" name="customWorksheet" id="customWorksheet" enctype="multipart/form-data">';
			print "<H4>Use the form below to make a custom worksheet for the resource.  Use the button at the bottom of the page to download.</H4>";
			foreach($va_worksheet_metadata as $vs_code => $vs_title){
				switch($vs_code){
					case "translation":
					 	if($this->getVar("translation")){
					 		print "<div class='unit'><b>".$vs_title."</b><br/><textarea name='".$vs_code."'>".$this->getVar("translation")."</textarea></div>";
						}
					break;
					# -------
					case "transcription":
					 	if($this->getVar("transcription")){
							print "<div class='unit'><b>".$vs_title."</b><br/><textarea name='".$vs_code."'>".$this->getVar("transcription")."</textarea></div>";
						}
					break;
					# -------
					case "questions":
					 	# --- put the images out before the questions
					 	foreach($va_images as $vn_rep_id => $vs_image){
					 		print "<div class='unit'>";
					 		print "<input type='checkbox' checked name='print_reps[]' value='".$vn_rep_id."'> ";
					 		print $vs_image."</div>";
						}
						if($vs_image_info){
							print "<div class='unit'>".$vs_image_info."</div>";
						}
					 	print "<div class='unit'><b>".$vs_title."</b><br/><textarea name='".$vs_code."'></textarea></div>";
					break;
					# -------
					default:
						print "<div class='unit'><b>".$vs_title."</b><br/><textarea name='".$vs_code."'></textarea></div>";
					break;
					# -------
				}
			}
			
			print '<div class="unit"><a href="#" name="download" class="cabutton cabuttonSmall" onclick="jQuery(\'#customWorksheet\').submit(); return false;">'._t("Download").'</a></div>';
			print "<input type='hidden' name='occurrence_id' value='".$this->getVar("occurrence_id")."'>";
			print "<input type='hidden' name='relation_id' value='".$this->getVar("relation_id")."'>";
			print "</form>";
?>
</div><!-- end detailBody -->
<?php
}
?>