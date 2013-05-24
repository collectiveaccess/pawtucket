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
	 * Generate network output in specified format
	 *
	 * @param array $pa_viz_settings Array of visualization settings taken from visualization.conf
	 * @param string $ps_format Specifies format to generate output in. Currently only 'HTML' is supported.
	 * @param array $pa_options Array of options to use when rendering output. Supported options are:
	 *		width =
	 *		height =
	 *		request = current request; required for generation of editor links
	 */
	public function render($pa_viz_settings, $ps_format='HTML', $pa_options=null) {
		if (!($vo_data = $this->getData())) { return null; }
		$this->opn_num_items_rendered = 0;
		
		$po_request = (isset($pa_options['request']) && $pa_options['request']) ? $pa_options['request'] : null;
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
		
		
		$o_dm = Datamodel::load();
		
		// generate events
		$va_events = array();
		$va_sources = $pa_viz_settings['sources'];
		
		$vs_table = $vo_data->tableName();
		$vs_pk = $o_dm->getTablePrimaryKeyName($vs_table);
		
		$va_nodes = $va_edges = array();
		
		//$va_nodes[] = array(
		//	'id' => $t_instance->get('ca_entities.entity_id'),
		//	'name' => $t_instance->get('ca_entities.preferred_labels')
		//);
		$vn_c = 0;
		while($vo_data->nextHit()) {
			//foreach($va_sources as $vs_source_code => $va_source_info) {
				
			//}
			
			$vn_id = $vo_data->get('ca_entities.entity_id');
			$va_nodes[$vn_id] = array(
				'id' => $vn_id,
				'focus' => 1,
				'name' => $vo_data->get('ca_entities.preferred_labels'),
				'media' => $vo_data->get('ca_entities.agentMedia', array('version' => 'icon', 'return' => 'url'))
			);
			$vn_source = $vn_c;
			
			$t_entity = new ca_entities($vn_id);
			$qr_entities = $t_entity->getRelatedItemsAsSearchResult("ca_entities");
			
			while($qr_entities->nextHit()) {
				$vn_id = $qr_entities->get('ca_entities.entity_id');
				if (isset($va_nodes[$vn_id])) { continue; }
				$vn_c++;
				$va_nodes[$vn_id] = array(
					'id' => $vn_id,
					'focus' => 0,
					'name' => $qr_entities->get('ca_entities.preferred_labels'),
					'media' => $qr_entities->get('ca_entities.agentMedia', array('version' => 'icon', 'return' => 'url'))
				);
				$va_edges[$vn_id] = array(
					'source' => $vn_source,
					'target' => $vn_c,
					'value' => 1
				);
			}
			
		}
		
		$va_nodes = array_values($va_nodes);
		$va_edges = array_values($va_edges);
		
		$this->opn_num_items_rendered = sizeof($va_events);
		
		$vs_buf = "
	<style>
		.link {
		  stroke: #ccc;
		}

		.node text {
		  pointer-events: none;
		  font: 10px sans-serif;
		}
	</style>
	<div id='caResultNetwork' style='width: {$vn_width}; height: {$vn_height}; border: 1px solid #aaa'></div>

<script type='text/javascript'>
		
	var width = 690,
    height = 500

	var svg = d3.select('#caResultNetwork').append('svg')
		.attr('width', '100%')
		.attr('height', '450');

	var force = d3.layout.force()
		.gravity(.06)
		.distance(180)
		.charge(-250)
		.size([width, height]);

	var json = {
		'nodes':".json_encode($va_nodes).",
		'links':".json_encode($va_edges)."
	};
	  force
		  .nodes(json.nodes)
		  .links(json.links)
		  .start();

	  var link = svg.selectAll('.link')
		  .data(json.links)
		.enter().append('line')
		  .attr('class', 'link');

	  var node = svg.selectAll('.node')
		  .data(json.nodes)
		.enter().append('g')
		  .attr('class', 'node')
		  .call(force.drag);

	  node.append('circle')
		  .attr('x', -8)
		  .attr('y', -8)
		  .attr('r', 4)
		  .attr('color', '0000cc')
		  .attr('width', function(d) { return (d.focus == 1) ? 32 : 16; } )
		  .attr('height', function(d) { return (d.focus == 1) ? 32 : 16; });

	  node.append('image')
		  .attr('xlink:href', function(d) { return d.media; })
		  .attr('x', -8)
		  .attr('y', -8)
		  .attr('width', function(d) { return (d.focus == 1) ? 32 : 16; } )
		  .attr('height', function(d) { return (d.focus == 1) ? 32 : 16; });

	  node.append('text')
		  .attr('dx', 8)
		  .attr('dy', '-4')
		  .text(function(d) { return d.name });

	  force.on('tick', function() {
		link.attr('x1', function(d) { return d.source.x; })
			.attr('y1', function(d) { return d.source.y; })
			.attr('x2', function(d) { return d.target.x; })
			.attr('y2', function(d) { return d.target.y; });

		node.attr('transform', function(d) { return 'translate(' + d.x + ',' + d.y + ')'; });
	  });
</script>	
";
		
		return $vs_buf;
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