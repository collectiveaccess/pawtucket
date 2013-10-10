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
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	require_once(__CA_LIB_DIR__.'/ca/ResultContext.php');
 
 	class DownloadController extends ActionController {
 		# -------------------------------------------------------
 		private $opo_plugin_config;			// plugin config file
 		private $ops_theme;						// current theme
 		private $opo_result_context;			// current result context
 		
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
			$this->opo_plugin_config = Configuration::load($this->request->getAppConfig()->get('application_plugins').'/nysa/conf/nysa.conf');
 			
 			if (!(bool)$this->opo_plugin_config->get('enabled')) { die(_t('nysa plugin is not enabled')); }
 			
 			$this->ops_theme = __CA_THEME__;																		// get current theme
 			if(!is_dir(__CA_APP_DIR__.'/plugins/nysa/views/'.$this->ops_theme)) {		// if theme is not defined for this plugin, try to use "default" theme
 				$this->ops_theme = 'default';
 			}
 		}
 		# -------------------------------------------------------
 		public function Index() {
 			$pn_occurrence_id = $this->request->getParameter('occurrence_id', pString);
 			$t_occurrence = new ca_occurrences($pn_occurrence_id);
 			$va_access_values = caGetUserAccessValues($this->request);
 			$va_occ_info[] = "<b>"._t("Lesson type")."</b>: ".$t_occurrence->getTypeName();
 			foreach(array("gradelevel", "lessonTopic", "learning_standard", "commonCore", "skills", "EdProject", "funder") as $vs_attribute_code){
 				if($va_values = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}", array("convertCodesToDisplayText" => true, "returnAsArray" => true))){
					$va_output_parts = array();
					foreach($va_values as $k => $va_value){
						$vs_value = "";
						if($vs_value = trim($va_value[$vs_attribute_code])){
							$va_output_parts[] = $vs_value;
						}
					}
					if(sizeof($va_output_parts)){
						$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b>: ".join(", ", $va_output_parts);
					}
				}
 			}
 			# --- get the attributes the user may have altered
 			$va_altered_attributes = array("task", "theme", "guidelines", "sure", "directions", "context", "task", "glossary", "transcription", "translation", "instructions", "essay", "essential", "check");
 			foreach($va_altered_attributes as $vs_attribute_code){
 				if($vs_value = str_replace("\n", "<br/>", $this->request->getParameter($vs_attribute_code, pString))){
 					$va_occ_info[$vs_attribute_code] = "<b>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</b><br/>".$vs_value;
 				}
 			}
 			
 			$this->view->setVar('title', $t_occurrence->getLabelForDisplay());
 			$this->view->setVar('occ_info', $va_occ_info);
 					
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
 	}
 ?>
