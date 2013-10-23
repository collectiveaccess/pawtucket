<?php
/** ---------------------------------------------------------------------
 * app/lib/core/Plugins/GeographicMap/WLPlugGeographicMapLeaflet.php : generates maps via Leaflet 
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
    
include_once(__CA_LIB_DIR__."/core/Plugins/IWLPlugGeographicMap.php");
include_once(__CA_LIB_DIR__."/core/Plugins/GeographicMap/BaseGeographicMapPlugin.php");

class WLPlugGeographicMapLeaflet Extends BaseGeographicMapPlugIn Implements IWLPlugGeographicMap {
	
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->info['NAME'] = 'Leaflet';
		
		$this->description = _t('Generates maps using the Leaflet API');
	}
	# ------------------------------------------------
	/**
	 * Generate Leaflet output in specified format
	 *
	 * @param $ps_format - specifies format to generate output in. Currently only 'HTML' is supported.
	 * @param $pa_options - array of options to use when rendering output. Supported options are:
	 *		mapType - type of map to render; valid values are 'ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'; if not specified 'google_maps_default_type' setting in app.conf is used; if that is not set default is 'SATELLITE'
	 *		showNavigationControls - if true, navigation controls are displayed; default is to use 'google_maps_show_navigation_controls' setting in app.conf
	 *		showScaleControls -  if true, scale controls are displayed; default is to use 'google_maps_show_scale_controls' setting in app.conf
	 *		showMapTypeControls -  if true, map type controls are displayed; default is to use 'google_maps_show_map_type_controls' setting in app.conf
	 *		cycleRandomly - if true, map cycles randomly through markers; default is false
	 *		cycleRandomlyInterval - Interval between movement between markers; specify in milliseconds or seconds followed by 's' (eg. 4s); default is 2s
	 *		stopAfterRandomCycles - Stop cycling after a number of movements; set to zero to cycle forever; default is zero.
	 *		delimiter - Delimiter to use to separate content for different items being plotted in the same location (and therefore being put in the same marker detail balloon); default is an HTML line break tag ("<br/>")
	 *		minZoomLevel - Minimum zoom level to allow; leave null if you don't want to enforce a limit
	 *		maxZoomLevel - Maximum zoom level to allow; leave null if you don't want to enforce a limit
	 *		zoomLevel - Zoom map to specified level rather than fitting all markers into view; leave null if you don't want to specify a zoom level. IF this option is set minZoomLevel and maxZoomLevel will be ignored.
	 *		pathColor - 
	 *		pathWeight -
	 *		pathOpacity - 
	 */
	public function render($ps_format, $pa_options=null) {
		JavascriptLoadManager::register("leaflet");
		
		list($vn_width, $vn_height) = $this->getDimensions();
		$vn_width = intval($vn_width);
		$vn_height = intval($vn_height);
		if ($vn_width < 1) { $vn_width = 200; }
		if ($vn_height < 1) { $vn_height = 200; }
		
		$va_map_items = $this->getMapItems();
		$va_extents = $this->getExtents();
		
		$vs_delimiter = isset($pa_options['delimiter']) ? $pa_options['delimiter'] : "<br/>";
		$vn_zoom_level = (isset($pa_options['zoomLevel']) && ((int)$pa_options['zoomLevel'] > 0)) ? (int)$pa_options['zoomLevel'] : null;
		$vn_min_zoom_level = (isset($pa_options['minZoomLevel']) && ((int)$pa_options['minZoomLevel'] > 0)) ? (int)$pa_options['minZoomLevel'] : null;
		$vn_max_zoom_level = (isset($pa_options['maxZoomLevel']) && ((int)$pa_options['maxZoomLevel'] > 0)) ? (int)$pa_options['maxZoomLevel'] : null;
		
		$vs_path_color = (isset($pa_options['pathColor'])) ? $pa_options['pathColor'] : '#cc0000';
		$vn_path_weight = (isset($pa_options['pathWeight']) && ((int)$pa_options['pathWeight'] > 0)) ? (int)$pa_options['pathWeight'] : 2;
		$vn_path_opacity = (isset($pa_options['pathOpacity']) && ((int)$pa_options['pathOpacity'] >= 0)  && ((int)$pa_options['pathOpacity'] <= 1)) ? (int)$pa_options['pathOpacity'] : 0.5;
		
		
		$vs_type = (isset($pa_options['mapType'])) ? strtoupper($pa_options['mapType']) : strtoupper($this->opo_config->get('google_maps_default_type'));
		if (!in_array($vs_type, array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'))) {
			$vs_type = 'SATELLITE';
		}
		if (!$vs_id = trim($this->get('id'))) { $vs_id = 'map'; }
		
		switch(strtoupper($ps_format)) {
			# ---------------------------------
			case 'JPEG':
			case 'PNG':
			case 'GIF':
				$va_markers = array();
				$va_paths = array();
				$va_center = null;
				foreach($va_map_items as $o_map_item) {
					$va_coords = $o_map_item->getCoordinates();
					if (!($vs_color = $o_map_item->getColor())) { $vs_color = 'red'; }
					if (sizeof($va_coords) > 1) {
						// is path
						$va_path = array();
						foreach($va_coords as $va_coord) {
							$va_path[] = $va_coord['latitude'].','.$va_coord['longitude'];
						}
						$va_paths[] = "paths=".urlencode("color:{$vs_color}|weight:5|".join("|", $va_path));
						if (!$va_center) { $va_center = $va_coord; }
					} else {
						// is point
						$va_coord = array_shift($va_coords);
						$va_markers[] = "markers=".urlencode("color:{$vs_color}|label:".$o_map_item->getLabel()."|".$va_coord['latitude'].','.$va_coord['longitude']);
						
						if (!$va_center) { $va_center = $va_coord; }
					}
					
				}
				
				$vs_format = strtolower($ps_format);
				if ($vs_format == 'jpeg') { $vs_format = 'jpg'; }
				return "<img src='http://maps.googleapis.com/maps/api/staticmap?format={$vs_format}&maptype=".strtolower($vs_type)."&zoom={$vn_zoom_level}&sensor=false&size={$vn_width}x{$vn_height}&".join("&", array_merge($va_markers, $va_paths))."'/>";
			
				break;
			# ---------------------------------
			case 'HTML':
			default:
				if (isset($pa_options['showNavigationControls'])) {
					$vb_show_navigation_control 	= $pa_options['showNavigationControls'] ? 'true' : 'false';
				} else {
					$vb_show_navigation_control 	= $this->opo_config->get('google_maps_show_navigation_controls') ? 'true' : 'false';
				}
				if (isset($pa_options['showScaleControls'])) {
					$vb_show_scale_control 				= $pa_options['showScaleControls'] ? 'true' : 'false';
				} else {
					$vb_show_scale_control 			= $this->opo_config->get('google_maps_show_scale_controls') ? 'true' : 'false';
				}
				if (isset($pa_options['showMapTypeControls'])) {
					$vb_show_map_type_control 		= $pa_options['showMapTypeControls'] ? 'true' : 'false';
				} else {
					$vb_show_map_type_control 		= $this->opo_config->get('google_maps_show_map_type_controls') ? 'true' : 'false';
				}
				
				$vs_buf = "<div style='width:{$vn_width}px; height:{$vn_height}px' id='{$vs_id}'> </div>\n
<script type='text/javascript'>
jQuery(document).ready(function() {
	var caMap_{$vs_id} = new L.map('{$vs_id}').setView([51.505, -0.09], 13);
	new L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    maxZoom: 18
}).addTo(caMap_{$vs_id});

	var caMap_{$vs_id}_markers = new L.featureGroup();
";

		$va_locs = $va_paths = array();
		foreach($va_map_items as $o_map_item) {
			$va_coords = $o_map_item->getCoordinates();
			if (!($vs_color = $o_map_item->getColor())) { $vs_color = 'red'; }
			if (sizeof($va_coords) > 1) {
				// is path
				$va_path = array();
				foreach($va_coords as $va_coord) {
					//$va_path[] = "new google.maps.LatLng({$va_coord['latitude']},{$va_coord['longitude']})";
				}
				//$va_paths[] = array('path' => $va_coords, 'pathJS' => $va_path, 'label' => $o_map_item->getLabel(), 'content' => $o_map_item->getContent(), 'ajaxContentUrl' => $o_map_item->getAjaxContentUrl(), 'ajaxContentID' => $o_map_item->getAjaxContentID());
			} else {
				// is point
				$va_coord = array_shift($va_coords);
				$va_locs[$va_coord['latitude']][$va_coord['longitude']][] = array('label' => $o_map_item->getLabel(), 'content' => $o_map_item->getContent(), 'ajaxContentUrl' => $o_map_item->getAjaxContentUrl(), 'ajaxContentID' => $o_map_item->getAjaxContentID(), 'color' => $o_map_item->getColor());
			}
		}
		
		$vs_buf .= "
			var caMap_{$vs_id}_points = ".json_encode($va_locs).";
			for(var lat in caMap_{$vs_id}_points) {
				var longs = caMap_{$vs_id}_points[lat];
				for(var long in longs) {
					var info = longs[long].pop();
					console.log(lat, long, info);
					
					var m = new L.CircleMarker([lat, long], { title: info.label, color: '#'+ info.color});
					if (info.content) { m.bindPopup(info.content); }
					caMap_{$vs_id}_markers.addLayer(m);
					
				}
			}
			caMap_{$vs_id}.addLayer(caMap_{$vs_id}_markers);
			caMap_{$vs_id}.fitBounds(caMap_{$vs_id}_markers.getBounds());
					
";

$vs_buf .= "
});
</script>\n";
				break;
			# ---------------------------------
		}
		
		return $vs_buf;
	}
	# ------------------------------------------------
	/**
	 *
	 */
	public function getAttributeBundleHTML($pa_element_info, $pa_options=null) {
 		JavascriptLoadManager::register('maps');
 		
		$o_config = Configuration::load();
		
		if (!in_array($vs_map_type = $o_config->get('google_maps_default_type'), array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'))) {
			$vs_map_type = 'ROADMAP';
		}
		$vs_element = 	'<div id="mapholder_'.$pa_element_info['element_id'].'_{n}" class="mapholder">';
		$vs_element .= 	'<div class="mapCoordInput">';
		$vs_element .= 		'<div class="mapSearchBox">';
		$vs_element .=				'<input type="text" class="mapSearchText" name="searchtext"  id="{fieldNamePrefix}'.$pa_element_info['element_id'].'_{n}" size="60" value="'._t('Search for geographic location').'..." autocomplete="off"/>';
		$vs_element .=				'<div class="mapSearchSuggest"></div>';
		$vs_element .=				'<a href="#" class="button">'._t('Upload KML file').' &rsaquo;</a>';
		$vs_element .= 		'</div>';
		$vs_element .= 	'</div>';
		$vs_element .=		'<div class="mapKMLInput" style="display: none;">';
		$vs_element .=			_t("Select KML or KMZ file").': <input type="file" name="{fieldNamePrefix}'.$pa_element_info['element_id'].'_{n}"/><a href="#" class="button">'._t('Use map').' &rsaquo;</a>';
		$vs_element .=		'</div>';
		$vs_element .=		'<div class="map" style="width:695px; height:300px;"></div>';
		$vs_element .= 		'<script type="text/javascript">';
				$vs_element .= 		"jQuery(document).ready(function() {
				var mID_{n} = ".$pa_element_info['element_id'].";
										var mapdata = {
											mapID : mID_{n},
											mapholder : jQuery('#mapholder_' + mID_{n} + '_{n}'),
											searchDefaultText : '"._t('Search for geographic location')."...',
											searchTextID:  '{fieldNamePrefix}".$pa_element_info['element_id']."_search_text{n}', 
											zoomlevel : 12,
											initialLocation : null,
											map : null,
											geocoder : null,
											marker : null,
											markers : null,
											selectionIndex : -1,
											coordinates : \"{{{".$pa_element_info['element_id']."}}}\"
										};
										
										var mapOptions = {
											zoom: 12,
											mapTypeControl: ".((bool)$o_config->get('google_maps_show_map_type_controls') ? 'true' : 'false').",
											mapTypeControlOptions: {
												style: google.maps.MapTypeControlStyle.DEFAULT
											},
											navigationControl: ".((bool)$o_config->get('google_maps_show_navigation_controls') ? 'true' : 'false').",
											navigationControlOptions: {
												style: google.maps.NavigationControlStyle.DEFAULT
											},
											scaleControl: ".((bool)$o_config->get('google_maps_show_scale_controls') ? 'true' : 'false').",
											scaleControlOptions: {
												style: google.maps.ScaleControlStyle.DEFAULT
											},
											disableDefaultUI: false,
											mapTypeId: google.maps.MapTypeId.{$vs_map_type}
										};
										/* Initialization of the map */
										if ('{n}'.substring(0,3) == 'new') {
											initNewMap(mapdata,mapOptions);
										} else {
											initExistingMap(mapdata,mapOptions);
										}
										initMapsApp(mapdata);
									});";
		$vs_element .= 		'</script>';
		$vs_element .= '<input class="coordinates mapCoordinateDisplay" type="text" name="{fieldNamePrefix}'.$pa_element_info['element_id'].'_{n}" size="80"/>';
		$vs_element .=	'</div>';
	
		return $vs_element;
	}
	# ------------------------------------------------
}
?>