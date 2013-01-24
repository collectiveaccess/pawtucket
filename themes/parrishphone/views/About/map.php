<?php
/* ----------------------------------------------------------------------
 * themes/default/views/Results/ca_objects_results_map_html.php :
 * 		full search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
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
 	// Load libraries (code modules) we're going to need
	include_once(__CA_LIB_DIR__."/core/Db.php");
	include_once(__CA_LIB_DIR__."/core/GeographicMap.php");
	include_once(__CA_LIB_DIR__."/ca/Search/ObjectSearch.php");
	include_once(__CA_MODELS_DIR__."/ca_lists.php");
	include_once(__CA_MODELS_DIR__."/ca_objects.php");
	 	
$o_object_search = new ObjectSearch();
$qr_objects = $o_object_search->search("*");

$vn_num_hits = $qr_objects->numHits();

if($qr_objects){
	$o_map = new GeographicMap(320, 210, 'map');
	$va_map_stats = $o_map->mapFrom($qr_objects, 'ca_places.georeference', array('contentView' => 'About/ca_objects_results_map_balloon_html.php', 'request' => $this->request)); 
	// map_stats is an array with two keys: 'points' = number of unique markers; 'items' = number of results hits than were plotted at least once on the map
	
	if ($va_map_stats['points'] > 0) {
		if($va_map_stats['items'] < $vn_num_hits){
?>
			<script type="text/javascript">
				jQuery('div.searchNav').html('<?php print _t("%1 of %2 results have been mapped.  To see all results chose a different display by clicking the \"Options\" link below.", $va_map_stats['items'], $vn_num_hits)."</div>"; ?>');
			</script>
<?php
		} else {
?>
			<script type="text/javascript">
				jQuery('div.searchNav').html('<?php print _t("Found %1 results.", $va_map_stats['items'])."</div>"; ?>');
			</script>
<?php		
		}

		
		print '<div class="resultCount">'._t('You found %2 %3', $this->getVar('mode_type_singular'), $vn_num_hits, ($vn_num_hits == 1) ? _t('result') : _t('results'))."</div>";

		print "<div>".$o_map->render('HTML', array('delimiter' => "<br/>"))."</div>";
	} else {
?>
	<div>
		<?php print _t('It is not possible to show a map of the results because none of the items found have map coordinates.'); 
		print caNavLink($this->request, ' View as List', 'modebutton', 'Search', 'Index', 'view/thumbnail', array('search' => $vs_search));
		?>
<?php
	}
}
?>