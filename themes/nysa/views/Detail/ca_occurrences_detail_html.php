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
	
	$t_lists = new ca_lists();
	$vn_original_date = $t_lists->getItemIDFromList("date_types", "dateOriginal");
	$t_list_items = new ca_list_items();
	
	# -------------------------------------------------------
	# --- make arrays of the md fields for each type of occ - need to provide blank forms for these when customizing the lesson
	# --- do not include transcription and translation fields because if they were not filled in they are not important
	$va_attributes_for_occ_type = array();
	$va_rel_obj_attributes_for_occ_type = array();
	switch($t_occurrence->getTypeCode()){
		case "worksheet":
			$va_attributes_for_occ_type = array("context", "glossary", "essential", "check", "questions", "challenge", "connections", "resources");
		break;
		# -------------------------------
		case "CRQ":
			$va_attributes_for_occ_type = array("context", "glossary", "questions");
		break;
		# -------------------------------
		case "DBQ":
			$va_attributes_for_occ_type = array("directions", "context", "task", "glossary", "instructions", "essay");
			$va_rel_obj_attributes_for_occ_type = array("caption", "questions");
		break;
		# -------------------------------
		case "docset":
			$va_attributes_for_occ_type = array("context", "glossary", "essential", "check");
			$va_rel_obj_attributes_for_occ_type = array("caption", "description");
		break;
		# -------------------------------
		case "essay":
			$va_attributes_for_occ_type = array("glossary", "task", "theme", "guidelines", "sure");
		break;
		# -------------------------------
	}

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
				print '<form method="post" action="'.caNavUrl($this->request, 'nysa', 'Download', 'Custom', array('occurrence_id' => $vn_occurrence_id)).'" name="lessonPlan" id="lessonPlan" enctype="multipart/form-data">';
				print "<H4>Use the form below to make modifications to this lesson before downloading.  Select the information you would like to included in your PDF by using the checkboxes.  When you've finished your customizations, use the button at the bottom of the page to download your PDF.</H4>";
			}else{
				print "<div class='unit'>".caNavLink($this->request, _t("Download PDF"), 'cabutton cabuttonSmall', 'nysa', 'Download', 'Full', array('occurrence_id' => $vn_occurrence_id))."&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Customize and Download PDF"), 'cabutton cabuttonSmall', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id, 'mode' => 'print'))."</div><!-- end unit -->";
			}
			if($ps_mode == "print"){
				print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='type'> <b>"._t("Lesson type")."</b>: ".unicode_ucfirst($t_occurrence->getTypeName())."</div><!-- end unit -->";
			}else{
				print "<div class='unit'><b>"._t("Lesson type")."</b>: ".unicode_ucfirst($t_occurrence->getTypeName())."</div><!-- end unit -->";
			}
			# --- attributes in label: value format
			$va_attributes = array("gradelevel", "lessonTopic", "learning_standard", "commonCore", "skills", "EdProject", "funder");
			# --- which of these attributes can be edited when customizing?
			$va_edit_attributes = array("lessonTopic", "learning_standard", "commonCore", "skills");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertCodesToDisplayText" => false, "returnAsArray" => true))){
						$va_output_parts = array();
						foreach($va_values as $k => $va_value){
							if($va_value[$vs_attribute_code]){
								# --- display hierarchy path for "lessonTopic", "learning_standard", "commonCore"
								if(in_array($vs_attribute_code, array("lessonTopic", "learning_standard", "commonCore"))){
									$vs_tmp = "";
									$va_hierarchy_ancestors = $t_list_items->getHierarchyAncestors($va_value[$vs_attribute_code], array("idsOnly" => true, "includeSelf" => true));
									if(is_array($va_hierarchy_ancestors) && sizeof($va_hierarchy_ancestors)){
										# --- remove the root - we don't want to display it
										$va_root = array_pop($va_hierarchy_ancestors);
										if(is_array($va_hierarchy_ancestors) && sizeof($va_hierarchy_ancestors)){
											foreach($va_hierarchy_ancestors as $vni => $vn_list_item_id){
												$vs_tmp = $t_lists->getItemForDisplayByItemID($vn_list_item_id).(($vni > 0) ? " > ".$vs_tmp : "");
											}
											$va_output_parts[] = $vs_tmp;
										}
									}
								}else{							
									$vs_value = "";
									if($vs_value = trim($va_value[$vs_attribute_code])){
										$va_output_parts[] = $t_lists->getItemForDisplayByItemID($vs_value);
									}
								}
							}
						}
						if(sizeof($va_output_parts)){
							if($ps_mode == "print"){
								print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."'> <b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}").":</b> ";
								# --- should this be presented as editable?
								if(in_array($vs_attribute_code, $va_edit_attributes)){
									print "<br/><input type='text' name='".$vs_attribute_code."' value='".join(", ", $va_output_parts)."'>";
								}else{
									print join(", ", $va_output_parts);
								}
								print "</div><!-- end unit -->";
							}else{
								print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}").":</b> ";
								print join(", ", $va_output_parts);
								print "</div><!-- end unit -->";
							}
						}
					}
				}
			}
			print "<hr>";
			$va_options = array();
			if($ps_mode == "print"){
				$va_options = array("delimiter" => "\n");
			}else{
				$va_options = array("convertLineBreaks" => true, "delimiter" => "<br/>");
			}
			$va_glossary_options = $va_options;
			$va_glossary_options["returnAsArray"] = true;
			# --- attributes in label<br/>value format
			$va_attributes = array("theme", "guidelines", "sure", "directions", "context", "task", "glossary", "instructions", "essay", "essential", "check");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_options)){
						if($vs_attribute_code == "glossary"){
							$va_glossary_terms = array();
							$va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_glossary_options);
							foreach($va_values as $va_value){
								$va_glossary_terms[] = $va_value["glossary"];
							}
							sort($va_glossary_terms);
							if($ps_mode == "print"){
								$vs_value = implode("\n", $va_glossary_terms);
							}else{
								$vs_value = implode("<br/>", $va_glossary_terms);
							}
						}
						if($ps_mode == "print"){
							print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."'> <b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
							print "<textarea name='{$vs_attribute_code}'>".str_replace("*", "&bull; ", $vs_value)."</textarea>";
						}else{
							print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
							print str_replace("*", "&bull; ", $vs_value);
						}
						print "</div><!-- end unit -->";
					}else{
						if(($ps_mode == "print") && in_array($vs_attribute_code, $va_attributes_for_occ_type)){
							print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."'> <b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
							print "<textarea name='{$vs_attribute_code}'></textarea>";
							print "</div><!-- end unit -->";
						}
					}
				}
			}
			# --- related objects
			$va_related_objects_links = $t_occurrence->get("ca_objects_x_occurrences.relation_id", array("returnAsArray" => true));
			
			if(sizeof($va_related_objects_links)){
				$t_objects_x_occurrences = new ca_objects_x_occurrences();
				$t_object = new ca_objects();
				foreach($va_related_objects_links as $vn_relation_id){
					$t_objects_x_occurrences->load($vn_relation_id);
					$va_reps = $t_objects_x_occurrences->get("ca_objects_x_occurrences.representation_list", array("returnAsArray" => true, 'idsOnly' => true));
					
					if(is_array($va_reps)) {
						foreach($va_reps as $vn_i => $va_attr) {
							$t_rep = new ca_object_representations($va_attr['representation_list']);
							print "<div class='unit'>";
							if($ps_mode == "print"){
								print "<input type='checkbox' checked name='print_fields[]' value='rep".$t_rep->get("representation_id")."'> ";
							}
							# --- open in media viewer
							print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_objects_x_occurrences->get("ca_objects.object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".$t_rep->getMediaTag('media', 'medium')."</a>";
	
							#print $t_rep->getMediaTag('media', 'medium');
							print "</div><!-- end unit -->";
						}
					}
					print "<div class='unit' style='font-size:11px; font-style:italic;'>".caNavLink($this->request, $t_objects_x_occurrences->get("ca_objects.preferred_labels.name"), '', 'Detail', 'Object', 'Show', array('object_id' => $t_objects_x_occurrences->get("ca_objects.object_id")));
					if($va_dates = $t_objects_x_occurrences->get("ca_objects.date", array("returnAsArray" => true))){
						foreach($va_dates as $va_date_info){
							if($va_date_info["dc_dates_types"] == $vn_original_date){
								print ", ".$va_date_info["dates_value"];
							}
						}
					}
					if($t_objects_x_occurrences->get("ca_objects.repository")){
						print ", ".$t_objects_x_occurrences->get("ca_objects.repository", array('delimiter' => ', ', 'convertCodesToDisplayText' => true));
					}
					print ", ".$t_objects_x_occurrences->get("ca_objects.idno");
					print "</div>";
					
					if($ps_mode != "print"){
						print "<div class='unit'>".caNavLink($this->request, _t("Create a Worksheet for This Document"), '', 'nysa', 'Download', 'BlankWorksheet', array('occurrence_id' => $vn_occurrence_id, 'relation_id' => $vn_relation_id))."</div><!-- end unit -->";
					}
					# --- attributes on objects_x_occurrences record
					$va_attributes = array("caption", "transcription", "translation", "description", "questions");
					foreach($va_attributes as $vs_attribute_code){
						if($vs_value = $t_objects_x_occurrences->get("ca_objects_x_occurrences.{$vs_attribute_code}", $va_options)){
							if($ps_mode == "print"){
								print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."_related_object_".$vn_relation_id."'> <b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b><br/>";
								print "<textarea name='".$vs_attribute_code."_related_object_".$vn_relation_id."'>".str_replace("*", "&bull; ", $vs_value)."</textarea>";
							}else{
								if($vs_attribute_code == "questions"){
									$va_tmp_options = $va_options;
									$va_tmp_options["returnAsArray"] = 1;
									$va_values = $t_objects_x_occurrences->get("ca_objects_x_occurrences.{$vs_attribute_code}", $va_tmp_options);
									print "<div class='unit'><b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b>";
									print "<ol>";
									foreach($va_values as $va_value_info){
										print "<li>".$va_value_info[$vs_attribute_code]."</li>";
									}
									print "</ol>";
								}else{
									print "<div class='unit'><b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b><br/>";
									print str_replace("*", "&bull; ", $vs_value);
								}
							}
							print "</div><!-- end unit -->";
						}else{
							if(($ps_mode == "print") && in_array($vs_attribute_code, $va_rel_obj_attributes_for_occ_type)){
								print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."_related_object_".$vn_relation_id."'> <b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b><br/>";
								print "<textarea name='".$vs_attribute_code."_related_object_".$vn_relation_id."'></textarea>";
								print "</div><!-- end unit -->";
							}
						}
					}
				}
			}
			
			# --- attributes to display after objects -  in label<br/>value format
			$va_attributes = array("questions", "challenge", "connections", "resources", "transcription", "translation", "essay");
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_options)){
						if($ps_mode == "print"){
							print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."'> <b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
							print "<textarea name='{$vs_attribute_code}'>".str_replace("*", "&bull; ", $vs_value)."</textarea>";
						}else{
							if(in_array($vs_attribute_code, array("questions", "resources"))){
								$va_tmp_options = $va_options;
								$va_tmp_options["returnAsArray"] = 1;
								$va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", $va_tmp_options);
								print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b>";
								print "<ol>";
								foreach($va_values as $va_value_info){
									print "<li>".$va_value_info[$vs_attribute_code]."</li>";
								}
								print "</ol>";
							}else{
								print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
								print str_replace("*", "&bull; ", $vs_value);
							}
						}
						print "</div><!-- end unit -->";
					}else{
						if(($ps_mode == "print") && in_array($vs_attribute_code, $va_attributes_for_occ_type)){
							print "<div class='unit'><input type='checkbox' checked name='print_fields[]' value='".$vs_attribute_code."'> <b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>";
							print "<textarea name='{$vs_attribute_code}'></textarea>";
							print "</div><!-- end unit -->";
						}
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