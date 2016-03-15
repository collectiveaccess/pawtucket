<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_collections_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010-2011 Whirl-i-Gig
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
	$t_collection 			= $this->getVar('t_item');
	$vn_collection_id 		= $t_collection->getPrimaryKey();	
	$vs_title 				= $this->getVar('label');
	
	$vs_alternate_title = $t_collection->get("ca_collections.nonpreferred_labels.name", array("delimiter" => "<br/>"));
	if($vs_alternate_title){
		$vs_title = $vs_alternate_title;
	}
	
	
	$va_access_values	= $this->getVar('access_values');

?>
	<div id="detailBody" class='collection <?php print ($this->request->getController() == "List") ? "findingAid" : ""; ?>'>
		<div id="pageHeader">
		<div id="pageNav">
			
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_collections', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					if($this->request->getController() == "List"){
						# --- findaid list
						print "<div style='float:left'>".caNavLink($this->request, "&lsaquo; ", '', 'FindingAids', 'List', 'Index', array('table' => 'ca_collections', 'collection_id' => $this->getVar('previous_id'), 'l' => $this->getVar('selected_letter')), array('id' => 'previous'))."</div>";
					}else{
						print "<div style='float:left'>".caNavLink($this->request, "&lsaquo; ", '', 'Detail', 'Collection', 'Show', array('collection_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
					}
				}else{
					print "";
				}
				#print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				if ($this->getVar('next_id') > 0) {
					if($this->request->getController() == "List"){
						# --- findaid list
						print "<div style='float:right'>".caNavLink($this->request, "&rsaquo;", '', 'FindingAids', 'List', 'Index', array('table' => 'ca_collections', 'collection_id' => $this->getVar('next_id'), 'l' => $this->getVar('selected_letter')), array('id' => 'next'))."</div>";
					}else{
						print "<div style='float:right'>".caNavLink($this->request, "&rsaquo;", '', 'Detail', 'Collection', 'Show', array('collection_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
					}
				}else{
					print "";
				}
			}
?>
		</div><!-- end nav -->
		<h1><?php print $vs_title; ?></h1>
		</div>
<?php
		$va_featured_objects = $t_collection->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, "restrictToRelationshipTypes" => array("featured")));
		if(is_array($va_featured_objects) && sizeof($va_featured_objects)){
			foreach($va_featured_objects as $va_featured_object){
				$t_rel_object = new ca_objects($va_featured_object["object_id"]);
				$va_reps = $t_rel_object->getPrimaryRepresentation(array('medium'), null, array('return_with_access' => $va_access_values));
				if($va_reps['tags']['medium']){
					print "<div id='colImage'>".$va_reps['tags']['medium'];
					$va_rel_title = $t_rel_object->get('ca_objects.preferred_labels.name')." (".$t_rel_object->get("type_id", array("convertCodesToDisplayText" => true)).")";
					print "<br/>".caNavLink($this->request, $va_rel_title, '', 'Detail', 'Object', 'Show', array('object_id' => $t_rel_object->get("object_id")));
					$vs_format = $t_rel_object->get("av_format_Hierachical", array("convertCodesToDisplayText" => true)).$t_rel_object->get("photo_format", array("convertCodesToDisplayText" => true)).$t_rel_object->get("erec_format", array("convertCodesToDisplayText" => true)).$t_rel_object->get("paper_format", array("convertCodesToDisplayText" => true));
					print "<br/>".$t_rel_object->get("generation_general", array("convertCodesToDisplayText" => true)).(($vs_format && $t_rel_object->get("generation_general")) ? ": ".$vs_format : "");
					if($vs_format || $t_rel_object->get("generation_general")){
						print ". ";
					}
					if($t_rel_object->get("description_w_type", array('convertCodesToDisplayText' => true, 'template' => '^description'))){
						print $t_rel_object->get("description_w_type", array('convertCodesToDisplayText' => true, 'template' => '^description'))." ";
					}
					if($t_rel_object->get("idno")){
						print "#".$t_rel_object->get("idno");
					}
					print "</div>";
					break;
				}
			}
		}
							
?>
		<div id="leftCol">	
<?php
			if((!$this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks')){
?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
<?php
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_collection_id, 'tablename' => 'ca_collections'));
				}else{
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_collection_id, 'tablename' => 'ca_collections'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			# --- identifier
			if($t_collection->get('idno')){
				print "<div class='unit'><b>"._t("Identifier")."</b>: ".$t_collection->get('idno')."</div><!-- end unit -->";
			}
			if($vs_collection_date = $t_collection->get("ca_collections.collection_date.collection_dates_value")){
				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.collection_date")."</b><br/> {$vs_collection_date}</div><!-- end unit -->";
			}
			if($va_extent = $t_collection->get("ca_collections.extent" , array('convertCodesToDisplayText' => true, 'returnAsArray' => true))){
				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.extent")."</b><br/>";
				$va_display_extent_pieces = array();
				foreach($va_extent as $va_extent_item){
					$va_display_extent_pieces[] = $va_extent_item["extent_amount"]." ".$va_extent_item["extent_type"];
				}
				print join("; ", $va_display_extent_pieces)."</div><!-- end unit -->";
			}
			$va_creators = $t_collection->get("ca_entities", array("restrictToRelationshipTypes" => array("creator"), "returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_creators) > 0){	
?>
				<div class="unit"><b><?php print _t("Creator"); ?></b><br/>
<?php
				foreach($va_creators as $va_creator) {
					print "<div>".caNavLink($this->request, $va_creator["displayname"], '', '', 'Browse', 'clearAndAddCriteria', array('facet' => 'entity_facet', 'id' => $va_creator['entity_id']))."</div>\n";
				}
?>
				</div><!-- end unit -->
<?php
			}			
?>
	</div><!-- end leftCol -->
			
	<div id="bottomSection">
<?php
		if($vs_abstract = $t_collection->get("ca_collections.abstract")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.abstract")."</b><br/> {$vs_abstract}</div><!-- end unit -->";
		}
		if($vs_value = $t_collection->get("ca_collections.scope_contents")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.scope_contents")."</b><br/> {$vs_value}</div><!-- end unit -->";
		}	
		if($vs_historical_note = $t_collection->get("ca_collections.historical_note")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.historical_note")."</b><br/> {$vs_historical_note}</div><!-- end unit -->";
		}
		if($vs_access_restrictions = $t_collection->get("ca_collections.access_restrictions")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.access_restrictions")."</b><br/> {$vs_access_restrictions}</div><!-- end unit -->";
		}
		if($vs_user_restrictions = $t_collection->get("ca_collections.user_restrictions")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.user_restrictions")."</b><br/> {$vs_user_restrictions}</div><!-- end unit -->";
		}
		if($vs_preferred_citation = $t_collection->get("ca_collections.preferred_citation")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.preferred_citation")."</b><br/> {$vs_preferred_citation}</div><!-- end unit -->";
		}
		if($vs_arrangement = $t_collection->get("ca_collections.arrangement")){
			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.arrangement")."</b><br/> {$vs_arrangement}</div><!-- end unit -->";
		}	
		# --- parent
		if($vs_parent = $t_collection->get('ca_collections.parent.preferred_labels.name', array('checkAccess' => $va_access_values))){
			if($t_collection->get('ca_collections.parent.type_id') == 132){
				print "<div class='unit'><b>"._t("Part of")."</b><br/>".caNavLink($this->request, $vs_parent, '', 'FindingAids', 'List', 'Index', array('table' => 'ca_collections', 'collection_id' => $t_collection->get('ca_collections.parent.collection_id'), 'l' => strtolower(mb_substr($vs_parent, 0, 1))))."</div><!-- end unit -->";
			}else{	
				print "<div class='unit'><b>"._t("Part of")."</b><br/>".caNavLink($this->request, $vs_parent, '', 'Detail', 'Collection', 'Show', array('collection_id' => $t_collection->get('ca_collections.parent.collection_id')))."</div><!-- end unit -->";
			}
		}		
		
		# --- children
		$va_children_ids = $t_collection->getHierarchyChildren(null, array('idsOnly' => true, "sort" => "preferred_labels.name", 'checkAccess' => $va_access_values));
		if(is_array($va_children_ids) && sizeof($va_children_ids)){
			$qr_children = $t_collection->makeSearchResult("ca_collections", $va_children_ids);
			if($qr_children->numHits()){
				print "<div class='unit'>";
				print "<b>Container List</b><br/>";
				while($qr_children->nextHit()){
					print caNavLink($this->request, $qr_children->get('ca_collections.preferred_labels.name'), '', 'Detail', 'Collection', 'Show', array('collection_id' => $qr_children->get('ca_collections.collection_id')))."<br/>";
				}
				print "</div><!-- end unit -->";
			}
		}
		# --- vocabulary terms
		$va_terms = $t_collection->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
		if(sizeof($va_terms) > 0){
			print "<div class='unit'><b>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</b><br/>";
			foreach($va_terms as $va_term_info){
				print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['name_singular']))."</div>";
			}
			print "</div><!-- end unit -->";
		}
		if($va_lcsh_topical = $t_collection->get("ca_collections.lcsh_topical", array("returnAsArray" => true))){
			$va_display = array();
			foreach($va_lcsh_topical as $va_lcsh_topical_term){
				$vs_term = $va_lcsh_topical_term["lcsh_topical"];
				$vn_pos1 = strpos($vs_term, "[");
				if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
					$va_display[] = $vs_display_term;
				}
			}
			if(sizeof($va_display)){
				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.lcsh_topical")."</b><br/>";
				print join("<br/>", $va_display);
				print "</div><!-- end unit -->";
			}
		}
		if($va_lcsh_geo = $t_collection->get("ca_collections.lcsh_geo", array("returnAsArray" => true))){
			$va_display = array();
			foreach($va_lcsh_geo as $va_lcsh_geo_term){
				$vs_term = $va_lcsh_geo_term["lcsh_geo"];
				$vn_pos1 = strpos($vs_term, "[");
				if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
					$va_display[] = $vs_display_term;
				}
			}
			if(sizeof($va_display)){
				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.lcsh_geo")."</b><br/>";
				print join("<br/>", $va_display);
				print "</div><!-- end unit -->";
			}
		}
		if($va_links = $t_collection->get('externalLink', array('returnAsArray' => true, 'template' => '<a href="^url_entry" target="_blank">^url_source</a>'))){
			print "<div class='unit'><b>"._t("External Link")."</b>: ";
			$va_link_display = array();
			foreach($va_links as $va_link){
				if($va_link["url_source"]){
					$va_link_display[] = "<a href='".$va_link["url_entry"]."' target='_blank'>".$va_link["url_source"]."</a>";
				}else{
					$va_link_display[] = "<a href='".$va_link["url_entry"]."' target='_blank'>".$va_link["url_entry"]."</a>";
				}
			}
			print join(", ", $va_link_display)."</div>";
		}
				
		# --- collections
		$va_collections = $t_collection->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
		if(sizeof($va_collections) > 0){
			print "<div class='unit'><b>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</b><br/>";
			foreach($va_collections as $va_collection_info){
				print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label']);
				print " (".$va_collection_info['relationship_typename'].")</div>";
			}
			print "</div><!-- end unit -->";
		}
		# --- occurrences
		$va_occurrences = $t_collection->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
		$va_sorted_occurrences = array();
		if(sizeof($va_occurrences) > 0){
			$t_occ = new ca_occurrences();
			$va_item_types = $t_occ->getTypeList();
			foreach($va_occurrences as $va_occurrence) {
				$t_occ->load($va_occurrence['occurrence_id']);
				$va_sorted_occurrences[$va_occurrence['item_type_id']][$va_occurrence['occurrence_id']] = $va_occurrence;
			}
			
			foreach($va_sorted_occurrences as $vn_occurrence_type_id => $va_occurrence_list) {
?>
					<div class="unit"><b><?php print _t("Related")." ".$va_item_types[$vn_occurrence_type_id]['name_singular'].((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></b><br/>
<?php
				foreach($va_occurrence_list as $vn_rel_occurrence_id => $va_info) {
					print "<div>".(($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"])." (".$va_info['relationship_typename'].")</div>";				
				}
				print "</div><!-- end unit -->";
			}
		}
		
		# --- entities
		$va_entities = $t_collection->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
		if(sizeof($va_entities) > 0){	
?>
			<div class="unit"><b><?php print _t("Related")." ".((sizeof($va_entities) > 1) ? _t("Entities") : _t("Entity")); ?></b><br/>
<?php
			foreach($va_entities as $va_entity) {
				print "<div>".caNavLink($this->request, $va_entity["displayname"], '', '', 'Browse', 'clearAndAddCriteria', array('facet' => 'entity_facet', 'id' => $va_entity['entity_id']))." (".$va_entity['relationship_typename'].")</div>\n";
			}
?>
			</div><!-- end unit -->
<?php
		}

											
		# --- alternate names
// 		if($vs_cat_source = $t_collection->get("ca_collections.cat_source")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.cat_source")."</b><br/> {$vs_cat_source}</div><!-- end unit -->";
// 		}
// 		if($vs_local_holdings = $t_collection->get("ca_collections.local_holdings")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.local_holdings")."</b><br/> {$vs_local_holdings}</div><!-- end unit -->";
// 		}
// 		if($vs_pub_distro = $t_collection->get("ca_collections.pub_distro")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.pub_distro")."</b><br/> {$vs_pub_distro}</div><!-- end unit -->";
// 		}					
// 		if($vs_corporate = $t_collection->get("ca_entities", array('restrictToTypes' => array('org')))){
// 			print "<div class='unit'><b>"._t('Corporate Names')."</b><br/> {$vs_corporate}</div><!-- end unit -->";
// 		}
// 
// 		if($vs_scope_contents = $t_collection->get("ca_collections.gen_physical_description")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.gen_physical_description")."</b><br/> {$vs_scope_contents}</div><!-- end unit -->";
// 		}		
// 		if($vs_generalNotes = $t_collection->get("ca_collections.generalNotes")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.generalNotes")."</b><br/> {$vs_generalNotes}</div><!-- end unit -->";
// 		}
// 		if($vs_provenance = $t_collection->get("ca_collections.provenance")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.provenance")."</b><br/> {$vs_provenance}</div><!-- end unit -->";
// 		}
// 		if($vs_creation_production_note = $t_collection->get("ca_collections.creation_production_note")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.creation_production_note")."</b><br/> {$vs_creation_production_note}</div><!-- end unit -->";
// 		}	
// 		if($vs_use_repro = $t_collection->get("ca_collections.use_repro")){
// 			print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.use_repro")."</b><br/> {$vs_use_repro}</div><!-- end unit -->";
// 		}	
// 		if($va_lcsh_names = $t_collection->get("ca_collections.lcsh_names", array("returnAsArray" => true))){
// 			$va_display = array();
// 			foreach($va_lcsh_names as $va_lcsh_name){
// 				$vs_term = $va_lcsh_name["lcsh_names"];
// 				$vn_pos1 = strpos($vs_term, "[");
// 				if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 					$va_display[] = $vs_display_term;
// 				}
// 			}
// 			if(sizeof($va_display)){
// 				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.lcsh_names")."</b><br/>";
// 				print join("<br/>", $va_display);
// 				print "</div><!-- end unit -->";
// 			}
// 		}
// 		if($va_lcsh_genre = $t_collection->get("ca_collections.lcsh_genre", array("returnAsArray" => true))){
// 			$va_display = array();
// 			foreach($va_lcsh_genre as $va_lcsh_genre_term){
// 				$vs_term = $va_lcsh_genre_term["lcsh_genre"];
// 				$vn_pos1 = strpos($vs_term, "[");
// 				if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 					$va_display[] = $vs_display_term;
// 				}
// 			}
// 			if(sizeof($va_display)){
// 				print "<div class='unit'><b>".$t_collection->getDisplayLabel("ca_collections.lcsh_genre")."</b><br/>";
// 				print join("<br/>", $va_display);
// 				print "</div><!-- end unit -->";
// 			}
// 		}
// 
// 		
// 		# --- places
// 		$va_places = $t_collection->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
// 		if(sizeof($va_places) > 0){
// 			print "<div class='unit'><b>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</b><br/>";
// 			foreach($va_places as $va_place_info){
// 				print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
// 			}
// 			print "</div><!-- end unit -->";
// 		}
		
		# --- if this is a collection record, only show related items if there isn't a series record
		if(($t_collection->get("type_id") != 132) || (sizeof($va_children_ids) == 0)){
			# --- output related object images as links
			$va_related_objects = $t_collection->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, 'sort' => 'ca_objects.generation_general'));
			if (sizeof($va_related_objects)) {
				print "<div id='relatedItems'>";
				print "<div id='relatedTitle'>".$t_collection->get("type_id", array("convertCodesToDisplayText" => true))." Items</div>";	
				foreach($va_related_objects as $vn_rel_id => $va_info){
					$t_rel_object = new ca_objects($va_info["object_id"]);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'medium'), null, array('return_with_access' => $va_access_values));
					if(!($vs_title = $t_rel_object->get("ca_objects.nonpreferred_labels.name", array("delimiter" => "<br/>")))){
						$vs_title = $t_rel_object->get('ca_objects.preferred_labels.name');
					}
					$va_rel_title = $vs_title." (".$t_rel_object->get("type_id", array("convertCodesToDisplayText" => true)).")";
					print "<div class='item'>";
					#if($va_reps['tags']['icon']){
						#print caNavLink($this->request, $va_reps['tags']['icon'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
					#}
					# --- only link to object if there is a media linked
					if(is_array($va_reps) && sizeof($va_reps)){
						print caNavLink($this->request, $va_rel_title, '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
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
					print "<div style='width:100%; height:1px;clear:both;'></div>";
					print "</div>";	
				}
				print "<div style='width:100%; height:1px;clear:both;'></div>";
				print "</div>";
			}	
		}
?>
	</div><!-- end bottomSection -->
</div><!-- end detailBody -->