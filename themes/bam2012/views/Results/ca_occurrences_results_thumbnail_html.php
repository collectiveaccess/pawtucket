<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ca_occurrences_full_html.php :
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
 
 	
$vo_result 				= $this->getVar('result');
$vn_items_per_page		= $this->getVar('current_items_per_page');

if($vo_result) {
	print '<div id="occurrenceResults">';
	
	$vn_item_count = 0;
	$va_tooltips = array();
	$t_list = new ca_lists();
	$vn_i = 0;
	while(($vn_i < $vn_items_per_page) && ($vo_result->nextHit())) {
		$vs_dates = $vo_result->get('ca_occurrences.productionDate');
		$vs_class = "";
		$vn_item_count++;
		if($vn_item_count == 2){
			$vs_class = "resultBg";
			$vn_item_count = 0;
		}
		
		$vn_occurrence_id = $vo_result->get('ca_occurrences.occurrence_id');
		
		if ($depicts = $vo_result->get('ca_objects.object_id', array('restrictToRelationshipTypes' => array('depicts')))) {
			$object_id = $depicts;
		} else {
			$object_id = $vo_result->get('ca_objects.object_id');
		}
		$t_object = new ca_objects($object_id);
		
		$va_object_media = $t_object->getMediaTag('ca_object_representations.media', 'widepreview');
		
		
		
		$va_labels = $vo_result->getDisplayLabels($this->request);
		print "<div class='prodResult searchThumbnail{$vn_occurrence_id}'>";
#		if ($va_object_media){
#			print caNavLink($this->request, "<div class='resultIcon'>".$va_object_media."</div>", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id));
#			print "<div class='resultText' style='width:140px;'> ".caNavLink($this->request, $vo_result->get('ca_occurrences.preferred_labels'), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id));
#			if($vs_dates){
#				print "<br/> ".$vs_dates;
#			}
#			print "</div>";
#		} else {
			print "<div style='height:75px; width:10px; display:inline-block;'></div>";
			print "<div class='resultText' style='width:230px;'> ".caNavLink($this->request, $vo_result->get('ca_occurrences.preferred_labels'), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id));
			if($vs_dates){
				print "<br/> ".$vs_dates;
			}
			print "</div>";
			
			if ($va_object_media){
				// set view vars for tooltip
				$this->setVar('tooltip_representation', $va_object_media);
				TooltipManager::add(
					".searchThumbnail{$vn_occurrence_id}", $this->render('Results/ca_objects_result_tooltip_html.php')
				);
			}
#		}
		print "</div>\n";
		$vn_i++;
		if ($vn_i == 3){
			print "<div style='width:100%; clear:both'></div>";
			$vn_i = 0;
		}
		
	}
	print "</div>\n";
}
?>