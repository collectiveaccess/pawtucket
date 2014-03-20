<?php
/* ----------------------------------------------------------------------
 * includes/ShowController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2013 Whirl-i-Gig
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
 
 	require_once(__CA_MODELS_DIR__.'/ca_occurrences.php');
 	require_once(__CA_MODELS_DIR__.'/ca_objects_x_occurrences.php');
 	require_once(__CA_MODELS_DIR__.'/ca_objects.php');
 	require_once(__CA_MODELS_DIR__.'/ca_object_representations.php');
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	require_once(__CA_LIB_DIR__.'/ca/ResultContext.php');
	require_once(__CA_LIB_DIR__."/core/Parsers/htmlpurifier/HTMLPurifier.standalone.php");
 
 	class DownloadController extends ActionController {
 		# -------------------------------------------------------
 		private $opo_plugin_config;			// plugin config file
 		private $ops_theme;						// current theme
 		private $opo_result_context;			// current result context
 		private $opa_worksheet_attributes;
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
			$this->opo_plugin_config = Configuration::load($this->request->getAppConfig()->get('application_plugins').'/nysa/conf/nysa.conf');
 			
 			if (!(bool)$this->opo_plugin_config->get('enabled')) { die(_t('nysa plugin is not enabled')); }
 			
 			$this->ops_theme = __CA_THEME__;																		// get current theme
 			if(!is_dir(__CA_APP_DIR__.'/plugins/nysa/views/'.$this->ops_theme)) {		// if theme is not defined for this plugin, try to use "default" theme
 				$this->ops_theme = 'default';
 			}
 			$this->opa_worksheet_attributes = array("title" => "Title", "historical_context" => "Historical Context", "glossary" => "Glossary", "essential" => "Essential Question", "check" => "Check for Understanding", "questions" => "Questions", "challenge" => "Historical Challenge", "connections" => "Interdisciplinary Connections", "resources" => "Resources", "transcription" => "Transcription", "translation" => "Translation");
 		
 		}
 		# -------------------------------------------------------
 		public function Custom() {
 			$o_purifier = new HTMLPurifier();
 			$pa_print_fields = $this->request->getParameter('print_fields', pArray);
 			$pn_occurrence_id = $this->request->getParameter('occurrence_id', pString);
 			$t_occurrence = new ca_occurrences($pn_occurrence_id);
 			$va_access_values = caGetUserAccessValues($this->request);
 			$va_occ_info[] = "<b>"._t("Lesson type")."</b>: ".$t_occurrence->getTypeName();
 			$va_occ_info2 = array();
 			
 			# --- attributes that can be edited when customizing?
			$va_edit_attributes = array("lessonTopic", "learning_standard", "commonCore", "skills");
 			foreach(array("gradelevel", "lessonTopic", "learning_standard", "commonCore", "skills", "EdProject", "funder") as $vs_attribute_code){
 				if(in_array($vs_attribute_code, $pa_print_fields) && ($va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertCodesToDisplayText" => true, "returnAsArray" => true)))){
					$va_output_parts = array();
					foreach($va_values as $k => $va_value){
						$vs_value = "";
						if($vs_value = trim($va_value[$vs_attribute_code])){
							$va_output_parts[] = $vs_value;
						}
					}
					if(sizeof($va_output_parts)){
						if(in_array($vs_attribute_code, $va_edit_attributes)){
							$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b>: ".$o_purifier->purify($this->request->getParameter($vs_attribute_code, pString));
						}else{
							$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b>: ".join(", ", $va_output_parts);
						}
					}
				}
 			}
 			$va_occ_info["HR"] = "<HR>";
 			# --- get the attributes the user may have altered
 			$va_altered_attributes = array("theme", "guidelines", "sure", "directions", "context", "task", "glossary", "instructions", "essay", "essential", "check");
 			foreach($va_altered_attributes as $vs_attribute_code){
 				if(in_array($vs_attribute_code, $pa_print_fields) && ($vs_value = str_replace("\n", "<br/>", $o_purifier->purify($this->request->getParameter($vs_attribute_code, pString))))){
 					$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value;
 				}
 			}
 			
 			# --- get the 2nd block of attributes the user may have altered
 			$va_altered_attributes = array("questions", "challenge", "connections", "resources", "transcription", "translation", "essay");
 			foreach($va_altered_attributes as $vs_attribute_code){
 				if(in_array($vs_attribute_code, $pa_print_fields) && ($vs_value = str_replace("\n", "<br/>", $o_purifier->purify($this->request->getParameter($vs_attribute_code, pString))))){
 					$va_occ_info2[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value;
 				}
 			}
 			
 			# --- related objects
			$va_related_objects_links = $t_occurrence->get("ca_objects_x_occurrences.relation_id", array("returnAsArray" => true));
			$va_related_objects_info = array();
			if(sizeof($va_related_objects_links)){
				$t_objects_x_occurrences = new ca_objects_x_occurrences();
				foreach($va_related_objects_links as $vn_relation_id){
					$va_object_info = array();
					$t_objects_x_occurrences->load($vn_relation_id);
					$va_reps = $t_objects_x_occurrences->get("ca_objects_x_occurrences.representation_list", array("returnAsArray" => true, 'idsOnly' => true));
					$vn_rep_displayed = 0;
					$va_reps_info = array();
					if(is_array($va_reps)) {
						foreach($va_reps as $vn_i => $va_attr) {
							$t_rep = new ca_object_representations($va_attr['representation_list']);
							if(in_array("rep".$t_rep->get("representation_id"), $pa_print_fields)){
								$vn_rep_displayed = 1;
								$va_media_info = $t_rep->getMediaInfo('media');
								$vn_height = $va_media_info["large"]["HEIGHT"];
								$vn_width = $va_media_info["large"]["WIDTH"];
								if($vn_height > 900){
									$vn_new_width = (900 * $vn_width)/$vn_height;
									$va_reps_info[] = "<img src='".$t_rep->getMediaUrl('media', 'large')."' style='height:900px; width:".$vn_new_width."px;'>";
								}else{
									$va_reps_info[] = $t_rep->getMediaTag('media', 'large');
								}
							}
						}
					}
					$va_object_info["reps"] = $va_reps_info;
					# --- attributes on objects_x_occurrences record
					$va_attributes = array("caption", "transcription", "translation", "description", "questions");
					$va_md = array();
					foreach($va_attributes as $vs_attribute_code){
						if(in_array($vs_attribute_code."_related_object_".$vn_relation_id, $pa_print_fields) && ($vs_value = str_replace("\n", "<br/>", $o_purifier->purify($this->request->getParameter($vs_attribute_code."_related_object_".$vn_relation_id, pString))))){
							$va_md[$vs_attribute_code] = "<b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value;
						}
					}
					# --- info from the related object record - only if at least one rep has been selected to print
					$vs_related_object_caption_info = "";		
					if($vn_rep_displayed == 1){
						$t_lists = new ca_lists();
						$vn_original_date = $t_lists->getItemIDFromList("date_types", "dateOriginal");
			
							
						$vs_related_object_caption_info = "<div style='font-size:11px; font-style:italic;'>".$t_objects_x_occurrences->get("ca_objects.preferred_labels.name");
						if($va_dates = $t_objects_x_occurrences->get("ca_objects.date", array("returnAsArray" => true))){
							foreach($va_dates as $va_date_info){
								if($va_date_info["dc_dates_types"] == $vn_original_date){
									$vs_related_object_caption_info .= ", ".$va_date_info["dates_value"];
								}
							}
						}
						if($t_objects_x_occurrences->get("ca_objects.repository")){
							$vs_related_object_caption_info .= ", ".$t_objects_x_occurrences->get("ca_objects.repository", array('delimiter' => ', ', 'convertCodesToDisplayText' => true));
						}
						$vs_related_object_caption_info .= ", ".$t_objects_x_occurrences->get("ca_objects.idno")."</div>";	
					}					
					$va_object_info["object_caption_info"] = $vs_related_object_caption_info;
					$va_object_info["md"] = $va_md;
					$va_related_objects_info[$vn_relation_id] = $va_object_info;
				}
			}
			$this->view->setVar('related_objects_info', $va_related_objects_info);
 			$this->view->setVar('title', $t_occurrence->getLabelForDisplay());
 			$this->view->setVar('occ_info', $va_occ_info);
 			$this->view->setVar('occ_info2', $va_occ_info2);
 					
 			require_once(__CA_LIB_DIR__.'/core/Parsers/dompdf/dompdf_config.inc.php');
			$vs_output_filename = $t_occurrence->getLabelForDisplay();
			$vs_output_file_name = preg_replace("/[^A-Za-z0-9\-]+/", '_', $vs_output_filename);
			header("Content-Disposition: attachment; filename=export_results.pdf");
			header("Content-type: application/pdf");
			$vs_content = $this->render($this->ops_theme.'/ca_occ_pdf_html.php');
			$o_pdf = new DOMPDF();
			// Page sizes: 'letter', 'legal', 'A4'
			// Orientation:  'portrait' or 'landscape'
			$o_pdf->set_paper("letter", "portrait");
			$o_pdf->load_html($vs_content, 'utf-8');
			$o_pdf->render();
			$o_pdf->stream($vs_output_file_name.".pdf");
			return;
 		}
 		# -------------------------------------------------------
 		public function Full() {
 			$t_lists = new ca_lists();
			$t_list_items = new ca_list_items();
 			$o_purifier = new HTMLPurifier();
 			$pn_occurrence_id = $this->request->getParameter('occurrence_id', pString);
 			$t_occurrence = new ca_occurrences($pn_occurrence_id);
 			$va_access_values = caGetUserAccessValues($this->request);
 			$va_occ_info[] = "<b>"._t("Lesson type")."</b>: ".$t_occurrence->getTypeName();
 			$va_occ_info2 = array();
 			foreach(array("gradelevel", "lessonTopic", "learning_standard", "commonCore", "skills", "EdProject", "funder") as $vs_attribute_code){
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
						$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b>: ".join(", ", $va_output_parts);
					}
				}
 			}
 			$va_occ_info["HR"] = "<HR>";
 			$va_attributes = array("theme", "guidelines", "sure", "directions", "context", "task", "glossary", "instructions", "essay", "essential", "check");
 			foreach($va_attributes as $vs_attribute_code){
				if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertLineBreaks" => true, "delimiter" => "<br/>"))){
					if($vs_attribute_code == "glossary"){
						$va_glossary_terms = array();
						$va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertLineBreaks" => true, "returnAsArray" => true));
						foreach($va_values as $va_value){
							$va_glossary_terms[] = $va_value["glossary"];
						}
						sort($va_glossary_terms);
						$vs_value = implode("<br/>", $va_glossary_terms);
						$va_occ_info[$vs_attribute_code] = "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value."</div><!-- end unit -->";
					}else{
						$va_occ_info[$vs_attribute_code] = "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".(str_replace("*", "&bull; ", $vs_value))."</div><!-- end unit -->";
					}
				}
			}
 			$va_attributes = array("questions", "challenge", "connections", "resources", "transcription", "translation", "essay");
 			foreach($va_attributes as $vs_attribute_code){
				if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertLineBreaks" => true, "delimiter" => "<br/>"))){
					if(in_array($vs_attribute_code, array("questions", "resources"))){
						$va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertLineBreaks" => true, "delimiter" => "<br/>", "returnAsArray" => true));
						$vs_tmp = "";
						$vs_tmp .= "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><ol>";
						foreach($va_values as $va_value_info){
							$vs_tmp .= "<li>".$va_value_info[$vs_attribute_code]."</li>";
						}
						$vs_tmp .= "</ol></div><!-- end unit -->";
						$va_occ_info2[$vs_attribute_code] = $vs_tmp;
					}else{
						$va_occ_info2[$vs_attribute_code] = "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".(str_replace("*", "&bull; ", $vs_value))."</div><!-- end unit -->";
					}
				}
			}
 			
 			# --- related objects
			$va_related_objects_links = $t_occurrence->get("ca_objects_x_occurrences.relation_id", array("returnAsArray" => true));
			$va_related_objects_info = array();
			if(sizeof($va_related_objects_links)){
				$t_objects_x_occurrences = new ca_objects_x_occurrences();
				foreach($va_related_objects_links as $vn_relation_id){
					$va_object_info = array();
					$t_objects_x_occurrences->load($vn_relation_id);
					$va_reps = $t_objects_x_occurrences->get("ca_objects_x_occurrences.representation_list", array("returnAsArray" => true, 'idsOnly' => true));
					
					$va_reps_info = array();
					if(is_array($va_reps)) {
						foreach($va_reps as $vn_relation_id => $va_attr) {
							$t_rep = new ca_object_representations($va_attr['representation_list']);
							$va_media_info = $t_rep->getMediaInfo('media');
							$vn_height = $va_media_info["large"]["HEIGHT"];
							$vn_width = $va_media_info["large"]["WIDTH"];
							if($vn_height > 900){
								$vn_new_width = (900 * $vn_width)/$vn_height;
								$va_reps_info[] = "<img src='".$t_rep->getMediaUrl('media', 'large')."' style='height:900px; width:".$vn_new_width."px;'>";
							}else{
								$va_reps_info[] = $t_rep->getMediaTag('media', 'large');
							}
						}
					}
					$va_object_info["reps"] = $va_reps_info;
					# --- attributes on objects_x_occurrences record
					$va_attributes = array("caption", "transcription", "translation", "description", "questions");
					$va_md = array();
					
					foreach($va_attributes as $vs_attribute_code){
						if($vs_value = $t_objects_x_occurrences->get("ca_objects_x_occurrences.{$vs_attribute_code}", array("convertLineBreaks" => true, "delimiter" => "<br/>"))){
							$va_md[$vs_attribute_code] = "<b>".$t_objects_x_occurrences->getDisplayLabel("ca_objects_x_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value;
						}
					}
					
					# --- info from the related object record
					$t_lists = new ca_lists();
					$vn_original_date = $t_lists->getItemIDFromList("date_types", "dateOriginal");
		
						
					$vs_related_object_caption_info = "<div style=\"font-size:11px; font-style:italic;\">".$t_objects_x_occurrences->get("ca_objects.preferred_labels.name");
					if($va_dates = $t_objects_x_occurrences->get("ca_objects.date", array("returnAsArray" => true))){
						foreach($va_dates as $va_date_info){
							if($va_date_info["dc_dates_types"] == $vn_original_date){
								$vs_related_object_caption_info .= ", ".$va_date_info["dates_value"];
							}
						}
					}
					if($t_objects_x_occurrences->get("ca_objects.repository")){
						$vs_related_object_caption_info .= ", ".$t_objects_x_occurrences->get("ca_objects.repository", array('delimiter' => ', ', 'convertCodesToDisplayText' => true));
					}
					$vs_related_object_caption_info .= ", ".$t_objects_x_occurrences->get("ca_objects.idno")."</div>";	
				
					$va_object_info["object_caption_info"] = $vs_related_object_caption_info;
					$va_object_info["md"] = $va_md;
					$va_related_objects_info[$vn_relation_id] = $va_object_info;
				}
			}
			
 			$this->view->setVar('related_objects_info', $va_related_objects_info);
 			$this->view->setVar('title', $t_occurrence->getLabelForDisplay());
 			$this->view->setVar('occ_info', $va_occ_info);
 			$this->view->setVar('occ_info2', $va_occ_info2);
 					
 			require_once(__CA_LIB_DIR__.'/core/Parsers/dompdf/dompdf_config.inc.php');
			$vs_output_filename = $t_occurrence->getLabelForDisplay();
			$vs_output_file_name = preg_replace("/[^A-Za-z0-9\-]+/", '_', $vs_output_filename);
			header("Content-Disposition: attachment; filename=export_results.pdf");
			header("Content-type: application/pdf");
			$vs_content = $this->render($this->ops_theme.'/ca_occ_pdf_html.php');
			$o_pdf = new DOMPDF();
			// Page sizes: 'letter', 'legal', 'A4'
			// Orientation:  'portrait' or 'landscape'
			$o_pdf->set_paper("letter", "portrait");
			$o_pdf->load_html($vs_content, 'utf-8');
			$o_pdf->render();
			$o_pdf->stream($vs_output_file_name.".pdf");
			return;
 		}
 		# -------------------------------------------------------
 		public function BlankWorksheet() {
 			# --- used for back button
 			$pn_occurrence_id = $this->request->getParameter('occurrence_id', pInteger);
 			$this->view->setVar("occurrence_id", $pn_occurrence_id);
 			$t_occurrence = new ca_occurrences($pn_occurrence_id);
 			
 			$pn_relation_id = $this->request->getParameter('relation_id', pInteger);
 			$this->view->setVar("relation_id", $pn_relation_id);
 			$t_objects_x_occurrences = new ca_objects_x_occurrences($pn_relation_id);
 			$t_object = new ca_objects($t_objects_x_occurrences->get("object_id"));
 			
 			# --- get the images
 			$va_reps = $t_object->getRepresentations(array("medium"));					
			$va_images = array();
			if(is_array($va_reps)) {
				foreach($va_reps as $vn_i => $va_rep) {
					$va_rep["representation_id"];
					$va_images[$va_rep["representation_id"]] = $va_rep["tags"]["medium"];
				}
			}
 			$this->view->setVar("images", $va_images);
 			
 			$t_lists = new ca_lists();
			$vn_original_date = $t_lists->getItemIDFromList("date_types", "dateOriginal");
			$vs_image_info = "<div style='font-size:11px; font-style:italic;'>".$t_objects_x_occurrences->get("ca_objects.preferred_labels.name");
			if($va_dates = $t_objects_x_occurrences->get("ca_objects.date", array("returnAsArray" => true))){
				foreach($va_dates as $va_date_info){
					if($va_date_info["dc_dates_types"] == $vn_original_date){
						$vs_image_info .= ", ".$va_date_info["dates_value"];
					}
				}
			}
			if($t_objects_x_occurrences->get("ca_objects.repository")){
				$vs_image_info .= ", ".$t_objects_x_occurrences->get("ca_objects.repository", array('delimiter' => ', ', 'convertCodesToDisplayText' => true));
			}
			$vs_image_info .= ", ".$t_objects_x_occurrences->get("ca_objects.idno")."</div>";
			$this->view->setVar("image_info", $vs_image_info);
 			
 			# --- try to get the tranlation/transcription for the rep your making the blank worksheet for
 			# --- those fields can be on either the occurrence or ca_object_x_occurrences record
 			$vs_translation = "";
 			if($t_occurrence->get("translation")){
 				$vs_translation = $t_occurrence->get("translation");
 			}elseif($t_objects_x_occurrences->get("translation")){
 				$vs_translation = $t_objects_x_occurrences->get("translation");
 			}
 			$this->view->setVar("translation", $vs_translation);
 			$vs_transcription = "";
 			if($t_occurrence->get("ca_occurrences.transcription")){
 				$vs_transcription = $t_occurrence->get("ca_occurrences.transcription");
 			}elseif($t_objects_x_occurrences->get("ca_objects_x_occurrences.transcription")){
 				$vs_transcription = $t_objects_x_occurrences->get("ca_objects_x_occurrences.transcription");
 			}
 			$this->view->setVar("transcription", $vs_transcription);
 			
 			# --- attributes used in worksheets
 			# --- these will be displayed as title and form elements so user can enter their own text
 			$this->view->setVar("worksheet_attributes", $this->opa_worksheet_attributes);
 			$this->render('nysa/customize_worksheet_html.php');
 		}
 		# -------------------------------------------------------
 		public function downloadCustomWorksheet() {
 			$o_purifier = new HTMLPurifier();
 			$pn_occurrence_id = $this->request->getParameter('occurrence_id', pInteger);
 			$pn_relation_id = $this->request->getParameter('relation_id', pInteger);
 			$t_objects_x_occurrences = new ca_objects_x_occurrences($pn_relation_id);
 			
 			# --- get the images
 			$pa_print_rep = $this->request->getParameter('print_reps', pArray);
 			$va_images = array();
			if(is_array($pa_print_rep)) {
				$t_rep = new ca_object_representations();
				foreach($pa_print_rep as $vn_i => $vn_rep_id) {
					$t_rep->load($vn_rep_id);
					$va_media_info = $t_rep->getMediaInfo('media');
					$vn_height = $va_media_info["large"]["HEIGHT"];
					$vn_width = $va_media_info["large"]["WIDTH"];
					if($vn_height > 900){
						$vn_new_width = (900 * $vn_width)/$vn_height;
						$vs_image = "<img src='".$t_rep->getMediaUrl('media', 'large')."' style='height:900px; width:".$vn_new_width."px;'>";
					}else{
						$vs_image = $t_rep->getMediaTag("media", "large");
					}
					$va_images[] = $vs_image;
				}
			}
 			$this->view->setVar("images", $va_images);
 			
 			$t_lists = new ca_lists();
			$vn_original_date = $t_lists->getItemIDFromList("date_types", "dateOriginal");
			$vs_image_info = "<div style='font-size:11px; font-style:italic;'>".$t_objects_x_occurrences->get("ca_objects.preferred_labels.name");
			if($va_dates = $t_objects_x_occurrences->get("ca_objects.date", array("returnAsArray" => true))){
				foreach($va_dates as $va_date_info){
					if($va_date_info["dc_dates_types"] == $vn_original_date){
						$vs_image_info .= ", ".$va_date_info["dates_value"];
					}
				}
			}
			if($t_objects_x_occurrences->get("ca_objects.repository")){
				$vs_image_info .= ", ".$t_objects_x_occurrences->get("ca_objects.repository", array('delimiter' => ', ', 'convertCodesToDisplayText' => true));
			}
			$vs_image_info .= ", ".$t_objects_x_occurrences->get("ca_objects.idno")."</div>";
			$this->view->setVar("image_info", $vs_image_info);
 			
 			# --- get the attributes the user may have altered
 			$va_info = array();
 			$va_attributes = $this->opa_worksheet_attributes;
 			foreach($va_attributes as $vs_attribute_code => $vs_title){
				if($vs_value = str_replace("\n", "<br/>", $o_purifier->purify($this->request->getParameter($vs_attribute_code, pString)))){
 					if($vs_attribute_code == "title"){
 						$va_info[$vs_attribute_code] = $vs_value;
 					}else{
 						$va_info[$vs_attribute_code] = "<b>".$vs_title."</b><br/>".$vs_value;
 					}
 				}else{
 					$va_info[$vs_attribute_code] = "";
 				}
			}
			$this->view->setvar("worksheet_info", $va_info);
 			
 			require_once(__CA_LIB_DIR__.'/core/Parsers/dompdf/dompdf_config.inc.php');
			if($vs_title = $o_purifier->purify($this->request->getParameter($vs_attribute_code, pString))){
				$vs_output_filename = $vs_title;
			}else{
				$vs_output_filename = "NYSA_Custom_WorkSheet";
			}
			$vs_output_file_name = preg_replace("/[^A-Za-z0-9\-]+/", '_', $vs_output_filename);
			header("Content-Disposition: attachment; filename=export_results.pdf");
			header("Content-type: application/pdf");
			$vs_content = $this->render($this->ops_theme.'/custom_worksheet_html.php');
			$o_pdf = new DOMPDF();
			// Page sizes: 'letter', 'legal', 'A4'
			// Orientation:  'portrait' or 'landscape'
			$o_pdf->set_paper("letter", "portrait");
			$o_pdf->load_html($vs_content, 'utf-8');
			$o_pdf->render();
			$o_pdf->stream($vs_output_file_name.".pdf");
			return;
 		}
 		# -------------------------------------------------------
 	}
 ?>
