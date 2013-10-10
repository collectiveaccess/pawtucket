<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_occurrences_detail_html.php : 
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
	$t_occurrence 			= $this->getVar('t_item');
	$vn_occurrence_id 	= $t_occurrence->getPrimaryKey();
	
	$vs_title 					= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');
	# --- if mode is set to print, display text md in form fields so user can edit before downloading
	$ps_mode = $this->request->getParameter('mode', pString);

if (!$this->request->isAjax()) {
?>
	<div id="detailBody">
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_occurrences', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}else{
					print "&lsaquo; "._t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				
				if ($this->getVar('next_id') > 0) {
					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('next_id')), array('id' => 'next'));
				}else{
					print _t("Next")." &rsaquo;";
				}
			}
?>
		</div><!-- end nav -->
		<h1><?php print $vs_title; ?></h1>	
<?php
			if((!$this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks')){
?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
<?php
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}else{
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			if($ps_mode == "print"){
				print '<form method="post" action="'.caNavUrl($this->request, 'nysa', 'Download', 'Index', array('occurrence_id' => $vn_occurrence_id)).'" name="lessonPlan" id="lessonPlan" enctype="multipart/form-data">';
				print "<H4>Use the form below to make modifications to this lesson before downloading.  Use the button at the bottom of the page to download.</H4>";
			}else{
				print "<div class='unit'>".caNavLink($this->request, _t("Download PDF"), 'button', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id, 'mode' => 'print'))."</div><!-- end unit -->";
			}
			print "<div class='unit'><b>"._t("Lesson type")."</b>: ".unicode_ucfirst($t_occurrence->getTypeName())."</div><!-- end unit -->";
			# --- attributes in label: value format
			$va_attributes = array("gradelevel", "lessonTopic", "learning_standard", "commonCore", "skills", "EdProject", "funder");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertCodesToDisplayText" => true, "returnAsArray" => true))){
						$va_output_parts = array();
						foreach($va_values as $k => $va_value){
							$vs_value = "";
							if($vs_value = trim($va_value[$vs_attribute_code])){
								$va_output_parts[] = $vs_value;
							}
						}
						if(sizeof($va_output_parts)){
							print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}").":</b> ";
							print join(", ", $va_output_parts);
							print "</div><!-- end unit -->";
						}
					}
				}
			}
			$va_options = array();
			if($ps_mode == "print"){
				$va_options = array("delimiter" => "\n");
			}else{
				$va_options = array("convertLineBreaks" => true, "delimiter" => "<br/>");
			}
			# --- attributes in label<br/>value format
			$va_attributes = array("task", "theme", "guidelines", "sure", "directions", "context", "task", "glossary");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_options)){
						print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
						if($ps_mode == "print"){
							print "<textarea name='{$vs_attribute_code}'>".str_replace("*", "&bull; ", $vs_value)."</textarea>";
						}else{
							print str_replace("*", "&bull; ", $vs_value);
						}
						print "</div><!-- end unit -->";
					}
				}
			}
			# --- related objects
			$va_related_objects_links = $t_occurrence->get("ca_objects_x_occurrences.relation_id", array("returnAsArray" => true));
			if(sizeof($va_related_objects_links)){
				$t_objects_x_occurrences = new ca_objects_x_occurrences();
				foreach($va_related_objects_links as $vn_relation_id){
					print $vn_relation_id;
					$t_objects_x_occurrences->load($vn_relation_id);
					print "relation id: ".$t_objects_x_occurrences->get("relation_id")." - ";
					print "object id: ".$t_objects_x_occurrences->get("object_id")."<br/>";
					print $t_objects_x_occurrences->get("ca_objects_x_occurrences.representation_list");
					if($t_objects_x_occurrences->get("ca_objects_x_occurrences.caption")){
						print $t_objects_x_occurrences->get("ca_objects_x_occurrences.caption")."<br/>";
					}
					if($t_objects_x_occurrences->get("transcription")){
						print $t_objects_x_occurrences->get("transcription")."<br/>";
					}
					if($t_objects_x_occurrences->get("ca_objects_x_occurrences.translation")){
						print $t_objects_x_occurrences->get("ca_objects_x_occurrences.translation")."<br/>";
					}
					if($t_objects_x_occurrences->get("ca_objects_x_occurrences.description")){
						print $t_objects_x_occurrences->get("ca_objects_x_occurrences.description")."<br/>";
					}
					if($t_objects_x_occurrences->get("ca_objects_x_occurrences.questions")){
						print $t_objects_x_occurrences->get("ca_objects_x_occurrences.questions")."<br/>";
					}
				}
			}
			$va_objects = $t_occurrence->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			#print_r($va_objects);
			
			
			# --- attributes in label<br/>value format
			$va_attributes = array("transcription", "translation", "instructions", "essay", "essential", "check");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_options)){
						print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
						if($ps_mode == "print"){
							print "<textarea name='{$vs_attribute_code}'>".str_replace("*", "&bull; ", $vs_value)."</textarea>";
						}else{
							print str_replace("*", "&bull; ", $vs_value);
						}
						print "</div><!-- end unit -->";
					}
				}
			}
			if($ps_mode == "print"){
				print "<div class='unit'>";
				print '<a href="#" name="save" class="cabutton cabuttonSmall" onclick="jQuery(\'#lessonPlan\').submit(); return false;">'._t("Download").'</a>';
				print "</div>";
				print "<input type='hidden' name='occurrence_id' value='".$vn_occurrence_id."'>";
				print "</form>";
			}
?>
</div><!-- end detailBody -->
<?php
}
?>