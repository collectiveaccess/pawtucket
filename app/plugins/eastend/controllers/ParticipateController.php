<?php
/* ----------------------------------------------------------------------
 * controller/Participate.php
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
 
 	require_once(__CA_MODELS_DIR__.'/ca_sets.php');
 	require_once(__CA_MODELS_DIR__.'/ca_objects.php');
 	require_once(__CA_MODELS_DIR__.'/ca_lists.php');
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 
 	class ParticipateController extends ActionController {
 		# -------------------------------------------------------
 		private $opo_plugin_config;			// plugin config file
 		private $ops_theme;						// current theme
 		private $opo_result_context;			// current result context
 		
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			$this->ops_theme = __CA_THEME__;																	// get current theme
 			if(!is_dir(__CA_APP_DIR__.'/plugins/eastend/themes/'.$this->ops_theme.'/views')) {		// if theme is not defined for this plugin, try to use "default" theme
 				$this->ops_theme = 'default';
 			}
 			
 			parent::__construct($po_request, $po_response, array(__CA_APP_DIR__.'/plugins/eastend/themes/'.$this->ops_theme.'/views'));
 			
 			MetaTagManager::addLink('stylesheet', $po_request->getBaseUrlPath()."/app/plugins/eastend/themes/".$this->ops_theme."/css/eastend.css",'text/css');
 		 	
 		 	$this->opo_plugin_config = Configuration::load($this->request->getAppConfig()->get('application_plugins').'/eastend/conf/eastend.conf');
 			
 			if (!(bool)$this->opo_plugin_config->get('enabled')) { die(_t('eastend plugin is not enabled')); }

			// redirect user if not logged in
			if ($this->request->config->get('pawtucket_requires_login')&&!($this->request->isLoggedIn())) {
                $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            }
            JavascriptLoadManager::register('cycle');
           	$t_list = new ca_lists();
			$this->view->setVar("user_contributed_source_id", $t_list->getItemIDFromList('object_sources', 'user_contributed'));
			$this->view->setVar("user_contributed_other_source_id", $t_list->getItemIDFromList('object_sources', 'user_contributed_other'));
 		}
 		# -------------------------------------------------------
 		public function Index() {
			$va_participate_ids = array();
 			$t_participate = new ca_sets();
 			# --- participate set - set name assigned in eastend.conf - plugin conf file
			$t_participate->load(array('set_code' => $this->opo_plugin_config->get('participate_set_name')));
			 # Enforce access control on set
 			if((sizeof($this->opa_access_values) == 0) || (sizeof($this->opa_access_values) && in_array($t_participate->get("access"), $this->opa_access_values))){
  				$this->view->setVar('participate_set_id', $t_participate->get("set_id"));
 				$va_participate_ids = array_keys(is_array($va_tmp = $t_participate->getItemRowIDs(array('checkAccess' => $this->opa_access_values, 'shuffle' => 1))) ? $va_tmp : array());	// These are the entity ids in the set
 			}
			
			# --- loop through featured ids and grab the object's image
			$t_object = new ca_objects();
			$va_participate_images = array();
			foreach($va_participate_ids as $vn_participate_object_id){
				$va_tmp = array();
				$t_object->load($vn_participate_object_id);
				$va_tmp["object_id"] = $vn_participate_object_id;
				$va_image = $t_object->getPrimaryRepresentation(array("mediumlarge"));
				# --- don't show records with status ars/vaga don't show image
				if($t_object->get("ca_objects.object_status") != 348){
					if($t_object->get("ca_objects.object_status") == 349){
						$va_tmp["vaga_class"] = "vagaDisclaimer";
					}
					$va_tmp["image"] = $va_image["tags"]["mediumlarge"];
					$va_tmp["caption"] = $t_object->get("ca_objects.caption");
					$va_tmp["title"] = $t_object->getLabelForDisplay();
				}
				$va_participate_images[$vn_participate_object_id] = $va_tmp;
			}
			$this->view->setVar("participate_images", $va_participate_images);
			$this->render('participate_html.php');
		}
 		# -------------------------------------------------------
 	}
 ?>
