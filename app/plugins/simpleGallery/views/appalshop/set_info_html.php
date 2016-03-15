<?php
/* ----------------------------------------------------------------------
 * app/plugins/simpleGallery/set_info_html.php : 
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
 
$t_set 				= $this->getVar('t_set');
$va_items 		= $this->getVar('items');
$va_set_id		= $t_set->get("set_id");
$va_set_list 		= $this->getVar('sets');
$va_first_items_from_sets 	= $this->getVar('first_items_from_sets');
$va_access_values = caGetUserAccessValues($this->request);
?>
<div id="gallerySetDetail">
<?php
# --- make column on right with all sets
	if(sizeof($va_set_list) > 1){
?>
	<div id="allSets"><H3><?php print _t("More Projects"); ?></H3>
<?php
	foreach($va_set_list as $vn_set_id => $va_set_info){
		if($vn_set_id == $t_set->get("set_id")){ continue; }
		print "<div class='setInfo'>";
		$va_item = $va_first_items_from_sets[$vn_set_id][array_shift(array_keys($va_first_items_from_sets[$vn_set_id]))];
		print "<div class='setImage'>".caNavLink($this->request, $va_item["representation_tag"], '', 'simpleGallery', 'Show', 'displaySet', array('set_id' => $vn_set_id))."</div><!-- end setImage -->";
		print "<div class='setTitle'>".caNavLink($this->request, (strlen($va_set_info["name"]) > 120 ? substr($va_set_info["name"], 0, 120)."..." : $va_set_info["name"]), '', 'simpleGallery', 'Show', 'displaySet', array('set_id' => $vn_set_id))."</div>";
		print "<div style='clear:left; height:1px;'><!-- empty --></div><!-- end clear --></div><!-- end setInfo -->";
	}
?>
	</div><!-- end allSets -->
<?php
	}
# --- selected set info - descriptiona dn grid of items with links to open panel with more info
?>
	<H1><?php print $this->getVar('set_title'); ?></H1>
<?php
	print "<div id='setItemsGrid'>";
	if($vs_set_description = $t_set->get('set_description')) {
?>
		<div class="textContent"><?php print $vs_set_description; ?></div>
<?php
	}
	if(sizeof($va_items)) {
			print "<div id='relatedItems'>";
				print "<div id='relatedTitle'>Featured Items</div>";	
				foreach($va_items as $va_item){
					$t_rel_object = new ca_objects($va_item['object_id']);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'medium'), null, array('return_with_access' => $va_access_values));
					if(!($vs_title = $t_rel_object->get("ca_objects.nonpreferred_labels.name", array("delimiter" => "<br/>")))){
						$vs_title = $t_rel_object->get('ca_objects.preferred_labels.name');
					}
					$va_rel_title = $vs_title." (".$t_rel_object->get("type_id", array("convertCodesToDisplayText" => true)).")";
					print "<div class='item'>";
					if($va_reps['tags']['icon']){
						print caNavLink($this->request, $va_reps['tags']['icon'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_item["object_id"]));
					}
					# --- only link to object if there is a media linked
					if(is_array($va_reps) && sizeof($va_reps)){
						print caNavLink($this->request, $va_rel_title, '', 'Detail', 'Object', 'Show', array('object_id' => $va_item["object_id"]));
					}else{
						print $va_rel_title;
					}
					$vs_format = $t_rel_object->get("av_format_Hierachical", array("convertCodesToDisplayText" => true)).$t_rel_object->get("photo_format", array("convertCodesToDisplayText" => true)).$t_rel_object->get("erec_format", array("convertCodesToDisplayText" => true)).$t_rel_object->get("paper_format", array("convertCodesToDisplayText" => true));
					$vs_generation_general = $t_rel_object->get("generation_general", array("convertCodesToDisplayText" => true));
					$vs_generation = $t_rel_object->get("generation_element", array("convertCodesToDisplayText" => true));
					print "<br/>";
					if($vs_generation_general || $vs_generation){
						print $vs_generation_general;
						if($vs_generation_general && $vs_generation){
							print ": ";
						}
						print $vs_generation;
						print "; ";
					}
					if($vs_format){
						print $vs_format.". ";
					}
					if($t_rel_object->get("description_w_type", array('convertCodesToDisplayText' => true, 'template' => '^description'))){
						print $t_rel_object->get("description_w_type", array('convertCodesToDisplayText' => true, 'template' => '^description'))." ";
					}
					if($t_rel_object->get("idno")){
						print "#".$t_rel_object->get("idno");
					}
					if($va_collections = $t_rel_object->get("ca_collections", array('returnAsArray' => true, 'checkAccess' => $va_access_values))){
						$va_coll_display = array();
						foreach($va_collections as $va_collection){
							$va_coll_display[$va_collection["collection_id"]] = caNavLink($this->request, $va_collection["label"], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection["collection_id"]));
						}
						print "<br/><b>Collection".((sizeof($va_coll_display) > 1) ? "s" : "").":</b> ".implode("; ",$va_coll_display);
					}
					if($va_occurrences = $t_rel_object->get("ca_occurrences", array('returnAsArray' => true, 'checkAccess' => $va_access_values))){
						$va_occ_display = array();
						foreach($va_occurrences as $va_occurrence){
							$va_occ_display[$va_occurrence["occurrence_id"]] = caNavLink($this->request, $va_occurrence["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $va_occurrence["occurrence_id"]));
						}
						print "<br/><b>Work".((sizeof($va_occ_display) > 1) ? "s" : "").":</b> ".implode("; ",$va_occ_display);
						if($vs_work_desc = $t_rel_object->get("ca_occurrences.work_description_w_type")){
							print "<br/>".$vs_work_desc;
						}
					}
					print "<div style='width:100%; height:1px;clear:both;'></div>";
					print "</div>";	
				}
				print "<div style='width:100%; height:1px;clear:both;'></div>";
				print "</div>";

	}
?>
	</div><!-- end setItemsGrid --></div><!-- end gallerySetDetail -->