<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ca_entities_map_html.php :
 * 		full search results
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
 
 	
$vo_result 				= $this->getVar('result');
$vn_items_per_page		= $this->getVar('current_items_per_page');
$va_access_values	= $this->getVar('access_values');

$va_entity_results = array();

?>	
	<div id="rightcol_featured"></div><!-- rightcol_featured -->	
<?php



if($vo_result) {
	$t_entity = new ca_entities();
	while($vo_result->nextHit()) {
		$vn_entity_id = $vo_result->get('ca_entities.entity_id');
		$va_labels = $vo_result->getDisplayLabels($this->request);
		
		//print_R($va_labels);
	}
}

$o_viz = new Visualizer('ca_entities');
$o_viz->addData($vo_result);
print $o_viz->render('map');
?>
<script type="text/javascript">
$(document).ready(function() {
	//load featured artist slideshow
	//jQuery("#rightcol_featured").load("<?php print caNavUrl($this->request, 'eastend', 'ArtistBrowser', 'getFeaturedArtistSlideshow'); ?>");
});
</script>
