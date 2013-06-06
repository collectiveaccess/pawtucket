<?php
/** ---------------------------------------------------------------------
 * app/lib/core/Plugins/Visualizer/WLPlugVisualizerNetwork.php : visualizes data as a timeline 
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
 * @subpackage Geographic
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

  /**
    *
    */ 
    
include_once(__CA_LIB_DIR__."/core/Plugins/IWLPlugVisualizer.php");
include_once(__CA_LIB_DIR__."/core/Plugins/Visualizer/BaseVisualizerPlugin.php");
include_once(__CA_APP_DIR__."/helpers/gisHelpers.php");

class WLPlugVisualizerNetwork Extends BaseVisualizerPlugIn Implements IWLPlugVisualizer {
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->info['NAME'] = 'Network';
		
		$this->description = _t('Visualizes data as a network graph');
	}
	# ------------------------------------------------
	/**
	 * Get data
	 *
	 */
	private function _getVisualizationData($pa_viz_settings) {
		if (!($vo_data = $this->getData())) { return null; }
		
		$o_dm = Datamodel::load();
		
		$vs_table = $vo_data->tableName();
		$vs_pk = $o_dm->getTablePrimaryKeyName($vs_table);
		
		$va_sources = $pa_viz_settings['sources'];
		
		$va_nodes = $va_links = array();
		
		$vn_c = 0;
		
		if (!($t_instance = $o_dm->getInstanceByTableName($vs_table, true))) { throw new Exception(_t("Invalid data table %1", $vs_table)); }
		$vs_rel_table = $t_instance->tableName();
		$vs_rel_pk = $t_instance->primaryKey();
		
		while($vo_data->nextHit()) {
			foreach($va_sources as $vs_source_code => $va_source_info) {
				$vn_source_id = $vo_data->get("{$vs_table}.{$vs_pk}");
				$va_nodes[$vn_source_id] = array(
					'id' => $vn_source_id,
					'table' => $vs_table,
					'focus' => 1,
					'name' => $vo_data->get($pa_viz_settings['display']['title']),
					'description' => $vo_data->get($pa_viz_settings['display']['description']),
					'media' => $vo_data->get($pa_viz_settings['display']['media'], array('version' => 'icon', 'return' => 'url'))
				);
				$vn_source = $vn_c;
				if (!$t_instance->load($vn_source_id)) { continue; }
				if (!($qr_rel = $t_instance->getRelatedItemsAsSearchResult($va_source_info['data']))) { continue; }
			
				$va_related_items = $t_instance->getRelatedItems($va_source_info['data']);
				$va_rel_info = array();
				foreach($va_related_items as $vs_k => $va_rel) {
					$vn_rel_id = $va_rel[$vs_rel_pk];
					$va_rel_info["{$vs_rel_table}.{$vn_rel_id}/{$vs_table}.{$vn_source_id}"] = $va_rel_info["{$vs_table}.{$vn_source_id}/{$vs_rel_table}.{$vn_rel_id}"][] = array(
						'relationship_typename' => $va_rel['relationship_typename'],
						'relationship_type_code' => $va_rel['relationship_type_code'],
						'relationship_type_id' => $va_rel['relationship_type_id'],
						'direction' => $va_rel['direction']
					);
				}
				$va_ids = array();
				while($qr_rel->nextHit()) {
					$vn_id = $qr_rel->get("{$vs_rel_table}.{$vs_rel_pk}");
					if (isset($va_nodes[$vn_id])) { continue; }
					$va_ids[] = $vn_id;
				
					$vn_c++;
					$va_nodes[$vn_id] = array(
						'id' => $vn_id,
						'table' => $vs_rel_table,
						'focus' => 0,
						'name' => $qr_rel->get($va_source_info['display']['title']),
						'description' => $qr_rel->get($va_source_info['display']['description']),
						'media' => $qr_rel->get($va_source_info['display']['media'], array('version' => 'icon', 'return' => 'url'))
					);
					$va_links[$vn_id] = array_merge(array(
						'source_id' => "{$vs_table}-{$vn_source_id}",
						'target_id' => "{$vs_rel_table}-{$vn_id}"
						),
						$va_rel_info["{$vs_rel_table}.{$vn_id}/{$vs_table}.{$vn_source_id}"]
					);
				}
			}
		}
		
		$this->opn_num_items_rendered = sizeof($va_ids);
		
		$va_nodes = array_values($va_nodes);
		$va_links = array_values($va_links);
		
		return array(
			'nodes' => $va_nodes,
			'links' => $va_links
		);
	}
	# ------------------------------------------------
	/**
	 * Generate network output in specified format
	 *
	 * @param array $pa_viz_settings Array of visualization settings taken from visualization.conf
	 * @param string $ps_format Specifies format to generate output in. Currently only 'HTML' is supported.
	 * @param array $pa_options Array of options to use when rendering output. Supported options are:
	 *		width =
	 *		height =
	 *		request = current request; required for generation of editor links
	 *		nodeLinkURL = 
	 *		linkColors = 
	 *		linkWeights = 
	 *		linkColorDefault = 
	 *		linkWeightDefault = 
	 */
	public function render($pa_viz_settings, $ps_format='HTML', $pa_options=null) {
		if (!($vo_data = $this->getData())) { return null; }
		$this->opn_num_items_rendered = 0;
		
		$po_request = (isset($pa_options['request']) && $pa_options['request']) ? $pa_options['request'] : null;
		$ps_node_link_url = (isset($pa_options['nodeLinkURL']) && $pa_options['nodeLinkURL']) ? $pa_options['nodeLinkURL'] : null;
			
		$vn_width = (isset($pa_options['width']) && $pa_options['width']) ? $pa_options['width'] : 690;
		$vn_height = (isset($pa_options['height']) && $pa_options['height']) ? $pa_options['height'] : 300;
		
		if (!preg_match('!^[\d]+%$!', $vn_width)) {
			$vn_width = intval($vn_width)."px";
			if ($vn_width < 1) { $vn_width = 690; }
		}
		if (!preg_match('!^[\d]+%$!', $vn_height)) {
			$vn_height = intval($vn_height)."px";
			if ($vn_height < 1) { $vn_height = 300; }
		}
		
		$vs_table = $vo_data->tableName();
		$va_data = $this->_getVisualizationData($pa_viz_settings);
		
		$vs_buf = "
	<div id='caResultNetwork' style='width: {$vn_width}; height: {$vn_height}; border: 1px solid #aaa'></div>

	<script type='text/javascript'>
		var networkVisualization = caUI.initNetworkVisualization({
			container: 'caResultNetwork',
			dataURL: '".caNavUrl($po_request, '', 'Visualization', "Network/{$vs_table}", array('viz' => 'network', 'ids' => ''))."',
			".(($ps_node_link_url) ? "nodeLinkURL: '{$ps_node_link_url}'," : '')."
			initialData: ".json_encode($va_data).",
			linkKey: 'relationship_type_code',
			".((isset($pa_options['linkColorDefault']) && $pa_options['linkColorDefault']) ? "linkColorDefault: '".$pa_options['linkColorDefault']."'," : '')."
			".((isset($pa_options['linkWeightDefault']) && $pa_options['linkWeightDefault']) ? "linkWeightDefault: '".$pa_options['linkWeightDefault']."'," : '')."
			linkColors: ".json_encode((isset($pa_options['linkColors']) && is_array($pa_options['linkColors'])) ? $pa_options['linkColors'] : array()).",
			linkWeights: ".json_encode((isset($pa_options['linkWeights']) && is_array($pa_options['linkWeights'])) ? $pa_options['linkWeights'] : array())."
		});
	</script>	
";
		
		return $vs_buf;
	}
	# ------------------------------------------------
	/**
	 * Return data for use with visualization. This is typically used for loading via AJAX of data for visualization.
	 * 
	 * @param array $pa_viz_settings Array of visualization settings taken from visualization.conf
	 */
	public function getDataForVisualization($pa_viz_settings, $pa_options=null) {
		return $this->_getVisualizationData($pa_viz_settings);
	}
	# ------------------------------------------------
	/**
	 * 
	 */
	public function getJSONForVisualization($pa_viz_settings, $pa_options=null) {
		return json_encode($this->getDataForVisualization($pa_viz_settings, $pa_options));
	}
	# ------------------------------------------------
	/**
	 * Determines if there is any data in the data set that can be visualized by this plugin using the provided settings
	 *
	 * @param SearchResult $po_data
	 * @param array $pa_viz_settings Visualization settings
	 *
	 * @return bool True if data can be visualized
	 */
	public function canHandle($po_data, $pa_viz_settings) {
		$vn_cur_pos = $po_data->currentIndex();
		if ($vn_cur_pos < 0) { $vn_cur_pos = 0; }
		$po_data->seek(0);
		
		$va_sources = $pa_viz_settings['sources'];
		while($po_data->nextHit()) {
			foreach($va_sources as $vs_source_code => $va_source_info) {
				if (trim($po_data->get($va_source_info['data']))) {
					$po_data->seek($vn_cur_pos);
					return true;
				}
			}
		}
		$po_data->seek($vn_cur_pos);
		return false;
	}
	# ------------------------------------------------
}
?>