<?php
/* ----------------------------------------------------------------------
 * pawtucket2/app/controllers/ListController.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
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
 	require_once(__CA_MODELS_DIR__."/ca_relationship_types.php");
 	require_once(__CA_LIB_DIR__.'/ca/Browse/ObjectBrowse.php');
 	require_once(__CA_LIB_DIR__.'/pawtucket/BaseDetailController.php');
 	require_once(__CA_APP_DIR__.'/helpers/browseHelpers.php');
 	
 	class ListController extends BaseDetailController {
 		# -------------------------------------------------------
 		/** 
 		 * Number of similar items to show
 		 */
 		 protected $opn_similar_items_per_page = 12;
 		 /**
 		 * Name of subject table (ex. for an occurrence search this is 'ca_occurrences')
 		 */
 		protected $ops_tablename = null;
 		
 		protected $opo_instance = null;
 		
 		protected $opa_sorts;
 		
 		/**
 		 * Name of application (eg. providence, pawtucket, etc.)
 		 */
 		protected $ops_appname = 'pawtucket2';
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			$this->ops_theme = __CA_THEME__;																	// get current theme
 			if(!is_dir(__CA_APP_DIR__.'/plugins/FindingAids/themes/'.$this->ops_theme.'/views')) {		// if theme is not defined for this plugin, try to use "default" theme
 				$this->ops_theme = 'default';
 			}
 				
			parent::__construct($po_request, $po_response, array(__CA_APP_DIR__.'/plugins/FindingAids/themes/'.$this->ops_theme.'/views'));
		 	MetaTagManager::addLink("stylesheet", $this->request->getBaseUrlPath()."/app/plugins/FindingAids/themes/".$this->ops_theme."/css/findingaids.css", "text/css");
			
 			// redirect user if not logged in
			if (($this->request->config->get('pawtucket_requires_login')&&!($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only')&&!($this->request->isLoggedIn()))) {
                $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            } elseif (($this->request->config->get('show_bristol_only'))&&($this->request->isLoggedIn())) {
            	$this->response->setRedirect(caNavUrl($this->request, "bristol", "Show", "Index"));
            }
            
            if ((!$this->ops_tablename = $po_request->getParameter('table', pString)) || (!in_array($this->ops_tablename, array('ca_collections')))) {
            	$this->ops_tablename = 'ca_collections';
            }
            
            $o_dm = Datamodel::load();
            if (!($this->opo_instance = $o_dm->getInstanceByTableName($this->ops_tablename, true))) {
            	die("Invalid table");
            }
 		}
 		# -------------------------------------------------------
 		/**
 		 * show detail
 		 */ 
 		public function Index() {
 			$ps_letter = $this->request->getParameter('l', pString);
 			
 			$vs_pk = $this->opo_instance->primaryKey();
 			$pn_id = $this->request->getParameter($vs_pk, pString);
 		
 			// Get browse instance for current table
 			if (!($o_browse = caGetBrowseInstance($this->ops_tablename))) { die("Invalid table"); }
 			
 			// Force addition of title facet
 			$o_browse->addFacetConfiguration("_finding_aids_title_facet", array(
 				"type" => "label",
 				"preferred_labels_only" => 1,
 				"indefinite_article" => "a",
 				"label_singular" => "Title",
 				"label_plural" => "Titles",
 				"restrict_to_types" => ["collection"]
 			));
 			
 			// Get the title 
 			$va_facet = $o_browse->getFacetContent("_finding_aids_title_facet", array('checkAccess' => caGetUserAccessValues($this->request)));
 			
 			
 			// Sort facet labels
 			usort($va_facet, "ListController::_facetArrayCMP");
 			
 			$va_titles = array();
 			$va_letters = array();
 			
 			foreach($va_facet as $vn_label_id => $va_facet) {
 				$vs_first_letter = strtolower(substr($va_facet['label'], 0, 1));
 				if (!$ps_letter) { $ps_letter = $vs_first_letter; }
 				
 				$va_letters[$vs_first_letter]++;
 				
 				if ($ps_letter == $vs_first_letter) { 
 					$va_titles[$va_facet[$vs_pk]] = $va_facet['label'];
 					if (!$pn_id) {
 						$pn_id = $va_facet[$vs_pk];
 						$this->request->setParameter($vs_pk, $pn_id);
 					}
 				}
 			}
 			
 			$this->view->setVar('titles', $va_titles);
 			$this->view->setVar('letters', $va_letters);
 			$this->view->setVar('selected_letter', $ps_letter);
 			
 			// Render standard detail with list view, which will include extra navigation for Finding Aids "list" mode
 			// (since we inherit from BaseDetailController Show() generates a detail)
 			parent::Show(array('view' => "list_html.php"));
 		}
 		# -------------------------------------------------------
 		static function _facetArrayCMP($a, $b) {
 			return strcasecmp($a['label'], $b['label']);
 		}
 		# -------------------------------------------------------
 	}
 ?>
