<?php
/* ----------------------------------------------------------------------
 * controller/ObjectSlideshowController.php
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
 
 	require_once(__CA_MODELS_DIR__.'/ca_objects.php');
 	require_once(__CA_MODELS_DIR__.'/ca_entities.php');
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 
 	class ObjectSlideshowController extends ActionController {
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
            
            if($this->request->config->get("dont_enforce_access_settings")){
 				$this->opa_access_values = array();
 			}else{
 				$this->opa_access_values = caGetUserAccessValues($this->request);
 			}
 			$this->view->setVar('access_values', $va_access_values);
            
            $this->opo_result_context = new ResultContext($po_request, 'ca_objects', ResultContext::getLastFind($po_request, 'ca_objects'));
            
            JavascriptLoadManager::register('cycle');
 		}
 		# -------------------------------------------------------
 		public function Index() {
			$pn_object_id = $this->request->getParameter('object_id', pInteger);
			$t_object = new ca_objects($pn_object_id);
			$this->view->setVar("object_id", $pn_object_id);
			$this->view->setVar("object", $t_object);
			$this->view->setVar("next_id", $this->opo_result_context->getNextID($pn_object_id));
			$this->view->setVar("previous_id", $this->opo_result_context->getPreviousID($pn_object_id));
			$va_image = $t_object->getPrimaryRepresentation(array("mediumlarge"));
			$this->view->setVar("image", $va_image["tags"]["mediumlarge"]);
			
			$this->render('object_slideshow_html.php');
		}
 		# -------------------------------------------------------
 	}
 ?>
