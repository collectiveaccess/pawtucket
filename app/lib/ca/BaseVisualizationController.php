<?php
/** ---------------------------------------------------------------------
 * app/lib/ca/BaseVisualizationController.php : 
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
 * @package CollectiveAccess
 * @subpackage UI
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
 
 /**
  *
  */
  
	require_once(__CA_LIB_DIR__."/core/Controller/ActionController.php");
	require_once(__CA_LIB_DIR__."/ca/Visualizer.php");
 	
	class BaseVisualizationController extends ActionController {
		# -------------------------------------------------------
		/**
		 * Plugin instance cache
		 */
		public static $s_plugin_cache = array();
 		# -------------------------------------------------------
 		#
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			// we don't want headers or footers output for feeds
 			$app = AppController::getInstance();
 			$app->removeAllPlugins();	// kills the pageFormat plugin added in /index.php
 			
 			// set http content-type header to JSON
 			$this->response->addHeader('Content-type', 'text/json', true);
 		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public function __call($ps_plugin, $pa_params) {
			$vs_table = $this->request->getActionExtra();
			$va_ids = explode(";", $this->request->getParameter('ids', pString));
	
			$o_viz = new Visualizer($vs_table);
			$o_viz->addData($va_ids);
			$this->view->setVar('data', $o_viz->getDataForVisualization($this->request->getParameter('viz', pString)));
	
 			$this->render("Visualization/ajax_visualization_data_json.php");
		}
 		# -------------------------------------------------------
	}
?>