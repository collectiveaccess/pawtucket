<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_occurrences_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
	$t_occurrence 			= $this->getVar('t_item');
	$vn_occurrence_id 	= $t_occurrence->getPrimaryKey();
	
	$vs_title 					= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');



	$t_collection 			= $this->getVar('t_item');
	$vn_collection_id 		= $t_collection->getPrimaryKey();
	
	$vs_title 					= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');
?>
	<div id="detailBody" class='work'>
		<div id="pageHeader">
		<div id="pageNav">
			
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_occurrences', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print "<div style='float:left'>".caNavLink($this->request, "&lsaquo; ", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
				}else{
					print "";
				}
				#print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				if ($this->getVar('next_id') > 0) {
					print "<div style='float:right'>".caNavLink($this->request, "&rsaquo;", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
				}else{
					print "";
				}
			}
?>
		</div><!-- end nav -->
		<h1><?php print unicode_ucfirst($t_occurrence->getTypeName()).': '.$vs_title; ?></h1>
		</div>
<?php
		$va_primary_objects = $t_collection->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, "restrictToRelationshipTypes" => array("primary")));
		if(is_array($va_primary_objects) && sizeof($va_primary_objects)){
			foreach($va_primary_objects as $va_primary_object){
				$t_rel_object = new ca_objects($va_primary_object["object_id"]);
				if ($t_primary_rep = $t_rel_object->getPrimaryRepresentationInstance()) {
 					if (!sizeof($va_access_values) || in_array($t_primary_rep->get('access'), $va_access_values)) { 		// check rep access
						$va_rep_display_info = caGetMediaDisplayInfo('occ_detail', $t_primary_rep->getMediaInfo('media', 'INPUT', 'MIMETYPE'));
						
						if($va_display_options['no_overlay']){
							print $t_primary_rep->getMediaTag('media', $va_rep_display_info['display_version'], $va_rep_display_info);
						}else{
							$va_opts = array('display' => 'occ_detail', 'object_id' => $t_rel_object->get('object_id'), 'containerID' => 'cont');
							print "<div id='colImage'><div id='cont'>".$t_primary_rep->getRepresentationViewerHTMLBundle($this->request, $va_opts)."</div></div>";
						}
					}
				}
				break;
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
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}else{
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			# --- identifier
			if($t_occurrence->get('idno')){
				print "<div class='unit'><b>"._t("Identifier")."</b>: ".$t_occurrence->get('idno')."</div><!-- end unit -->";
			}
			if($vs_date = $t_occurrence->get("ca_occurrences.dateProduced")){
				print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.dateProduced")."</b><br/> {$vs_date}</div><!-- end unit -->";
			}
			if($t_occurrence->get('color')){
				print "<div class='unit'><b>"._t("Color")."</b>: ".$t_occurrence->get('color', array('convertCodesToDisplayText' => true))."</div><!-- end unit -->";
			}
			if($t_occurrence->get('sound')){
				print "<div class='unit'><b>"._t("Sound")."</b>: ".$t_occurrence->get('sound', array('convertCodesToDisplayText' => true))."</div><!-- end unit -->";
			}
			if($t_occurrence->get("ca_occurrences.duration")){
				$vs_value = $t_occurrence->get("ca_occurrences.duration", array('convertCodesToDisplayText' => true, 'template' => 'Run time: ^runTime'));
				if ($t_occurrence->get("ca_occurrences.duration.approximate") == 841) {
					$va_approximate = "(Approximate)";
				}
				print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.duration")."</b><br/> {$vs_value} {$va_approximate}</div><!-- end unit -->";
			}
			if($vs_creator = $t_occurrence->get("ca_entities", array('restrictToRelationshipTypes' => array('artist', 'co_producer', 'composer', 'director', 'illustrator', 'performer', 'photographer', 'producer', 'writer')))){
				print "<div class='unit'><b>"._t('Creator(s)')."</b><br/> {$vs_creator}</div><!-- end unit -->";
			}
?>
	</div><!-- end leftCol -->
			
	<div id="bottomSection">
<?php											
		if($vs_desc = $t_occurrence->get("ca_occurrences.work_description_w_type")){
			print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.work_description_w_type")."</b><br/> {$vs_desc}</div><!-- end unit -->";
		}
					
		if($va_contributors = $t_occurrence->get("ca_entities", array('returnAsArray' => true, 'restrictToRelationshipTypes' => array('actor', 'animator', 'audio_engineer', 'author', 'broadcast_engineer', 'camera_assistant', 'camera_operator', 'cinematographer', 'composer', 'contributing_artist', 'editor', 'engineer', 'filmmaker', 'interviewee', 'interviewer', 'musician', 'narrator', 'performer', 'recording_engineer', 'sound_mixer', 'subject', 'writer')))){
			print "<div class='unit'><b>"._t('Contributor(s)')."</b><br/>";
			$va_contributor_display = array();
			foreach($va_contributors as $va_contributor){
				print $va_contributor["displayname"]." (".$va_contributor["relationship_typename"].")<br/>"; 
			}
			print "</div><!-- end unit -->";
		}		
		if($vs_sponsor = $t_occurrence->get("ca_entities", array('restrictToRelationshipTypes' => array('sponsor'), 'delimiter' => ', '))){
			print "<div class='unit'><b>"._t('sponsor(s)')."</b><br/> {$vs_sponsor}</div><!-- end unit -->";
		}
		# --- vocabulary terms
		$va_terms = $t_occurrence->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
		if(sizeof($va_terms) > 0){
			print "<div class='unit'><b>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</b><br/>";
			foreach($va_terms as $va_term_info){
				print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['name_singular']))."</div>";
			}
			print "</div><!-- end unit -->";
		}
				
		if($va_links = $t_occurrence->get('externalLink', array('returnAsArray' => true, 'template' => '<a href="^url_entry" target="_blank">^url_source</a>'))){
			print "<div class='unit'><b>"._t("External Link")."</b>: ";
			$va_link_display = array();
			foreach($va_links as $va_link){
				$va_link_display[] = "<a href='".$va_link["url_entry"]."' target='_blank'>".$va_link["url_source"]."</a>";
			}
			print join(", ", $va_link_display)."</div>";
		}
			
			# --- occurrences
			$va_occurrences = $t_occurrence->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
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
			# --- collections
			$va_collections = $t_occurrence->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><b>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</b><br/>";
				foreach($va_collections as $va_collection_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label']);
					print " (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
		if($va_lcsh_genre = $t_occurrence->get("ca_occurrences.lcsh_genre", array("returnAsArray" => true))){
			$va_display = array();
			foreach($va_lcsh_genre as $va_lcsh_genre_term){
				$vs_term = $va_lcsh_genre_term["lcsh_genre"];
				$vn_pos1 = strpos($vs_term, "[");
				if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
					$va_display[] = $vs_display_term;
				}
			}
			if(sizeof($va_display)){
				print "<div class='unit'><b>".$t_occurrence->getDisplayLabel("ca_occurrences.lcsh_genre")."</b><br/>";
				print join("<br/>", $va_display);
				print "</div><!-- end unit -->";
			}
		}
			# --- places
			$va_places = $t_occurrence->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_places) > 0){
				print "<div class='unit'><b>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</b><br/>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
		if($t_occurrence->get('coverage')){
			print "<div class='unit'><b>"._t("Coverage")."</b><br/>".$t_occurrence->get('coverage')."</div><!-- end unit -->";
		}
		if($t_occurrence->get('georeference')){
			$o_map = new GeographicMap(500, 300, 'map');
			$o_map->mapFrom($t_occurrence, 'georeference');
			print "<div class='unit'>".$o_map->render('HTML')."</div>";
		}



		
			
			# --- output related object images as links
			$va_related_objects = $t_occurrence->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, 'excludeRelationshipTypes' => array("primary")));
			if (sizeof($va_related_objects)) {
				print "<div id='relatedItems'>";
				print "<div id='relatedTitle'>Related Items</div>";	
				foreach($va_related_objects as $vn_rel_id => $va_info){
					$t_rel_object = new ca_objects($va_info["object_id"]);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'medium'), null, array('return_with_access' => $va_access_values));
					if(!($vs_title = $t_rel_object->get("ca_objects.nonpreferred_labels.name", array("delimiter" => "<br/>")))){
						$vs_title = $t_rel_object->get('ca_objects.preferred_labels.name');
					}
					$va_rel_title = $vs_title." (".$t_rel_object->get("type_id", array("convertCodesToDisplayText" => true)).")";
					print "<div class='item'>";
					if($va_reps['tags']['icon']){
						print caNavLink($this->request, $va_reps['tags']['icon'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
					}
					print caNavLink($this->request, $va_rel_title, '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
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
					print "<div style='width:100%; height:1px;clear:both;'></div>";
					print "</div>";	
				}
				print "<div style='width:100%; height:1px;clear:both;'></div>";
				print "</div>";
			}				
?>
	</div><!-- end rightCol -->
</div><!-- end detailBody -->