<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Detail/ca_objects_detail_html.php : 
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
	$t_object = 						$this->getVar('t_item');
	$vn_object_id = 					$t_object->get('object_id');
	$vs_title = 						$this->getVar('label');
	$vs_alternate_title = $t_object->get("ca_objects.nonpreferred_labels.name", array("delimiter" => "<br/>"));
	if($vs_alternate_title){
		$vs_title = $vs_alternate_title;
	}
	
	$va_access_values = 				$this->getVar('access_values');
	$t_rep = 							$this->getVar('t_primary_rep');
	$vn_num_reps = 						$t_object->getRepresentationCount(array("return_with_access" => $va_access_values));
	$vs_display_version =				$this->getVar('primary_rep_display_version');
	$va_display_options =				$this->getVar('primary_rep_display_options');

?>	
	<div id="detailBody">
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_objects', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}else{
					print "&lsaquo; "._t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				if ($this->getVar('next_id') > 0) {
					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('next_id')), array('id' => 'next'));
				}else{
					print _t("Next")." &rsaquo;";
				}
			}
?>
		</div><!-- end nav -->
	<div id="detailWrapper">	
		<div id="rightCol">
<?php
		if ($t_rep && $t_rep->getPrimaryKey()) {
?>
			<div id="objDetailImage">
<?php
			if($va_display_options['no_overlay']){
				print $t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'));
			}else{
				$va_opts = array('display' => 'detail', 'object_id' => $vn_object_id, 'containerID' => 'cont');
				print "<div id='cont'>".$t_rep->getRepresentationViewerHTMLBundle($this->request, $va_opts)."</div>";
			}
?>
			</div><!-- end objDetailImage -->
<?php
		}

if (!$this->request->config->get('dont_allow_registration_and_login')) {
		$va_tags = $this->getVar("tags_array");
		$va_comments = $this->getVar("comments");
		# --- user data --- comments - ranking - tagging
?>			
		<div id="objUserData">
<?php
			if($this->getVar("ranking") || (is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){
?>
				<div class="divide" style="margin:12px 0px 10px 0px;"><!-- empty --></div>
<?php			
			}
			if($this->getVar("ranking")){
?>
				<h2 id="ranking"><?php print _t("Average User Ranking"); ?> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/user_ranking_<?php print $this->getVar("ranking"); ?>.gif" width="104" height="15" border="0" style="margin-left: 20px;"></h2>
<?php
			}
			if(is_array($va_tags) && sizeof($va_tags) > 0){
				$va_tag_links = array();
				foreach($va_tags as $vs_tag){
					$va_tag_links[] = caNavLink($this->request, $vs_tag, '', '', 'Search', 'Index', array('search' => $vs_tag));
				}
?>
				<h2><?php print _t("Tags"); ?></h2>
				<div id="tags">
					<?php print implode($va_tag_links, ", "); ?>
				</div>
<?php
			}
			if(is_array($va_comments) && (sizeof($va_comments) > 0)){
?>
				<h2><div id="numComments">(<?php print sizeof($va_comments)." ".((sizeof($va_comments) > 1) ? _t("comments") : _t("comment")); ?>)</div><?php print _t("User Comments"); ?></h2>
<?php
				foreach($va_comments as $va_comment){
					if($va_comment["media1"]){
?>
						<div class="commentImage" id="commentMedia<?php print $va_comment["comment_id"]; ?>">
							<?php print $va_comment["media1"]["tiny"]["TAG"]; ?>							
						</div><!-- end commentImage -->
<?php
						TooltipManager::add(
							"#commentMedia".$va_comment["comment_id"], $va_comment["media1"]["large_preview"]["TAG"]
						);
					}
					if($va_comment["comment"]){
?>					
					<div class="comment">
						<?php print $va_comment["comment"]; ?>
					</div>
<?php
					}
?>					
					<div class="byLine">
						<?php print $va_comment["author"].", ".$va_comment["date"]; ?>
					</div>
<?php
				}
			}else{
				if(!$vs_tags && !$this->getVar("ranking")){
					$vs_login_message = _t("Login/register to be the first to rank, tag and comment on this object!");
				}
			}

		if($this->request->isLoggedIn()){
?>

			<div class="divide" style="margin:0px 0px 10px 0px;"><!-- empty --></div>
			<h2><?php print _t("Add your rank, tags and comment"); ?></h2>
			<form method="post" action="<?php print caNavUrl($this->request, 'Detail', 'Object', 'saveCommentRanking', array('object_id' => $vn_object_id)); ?>" name="comment" enctype='multipart/form-data'>
				<div class="formLabel">Rank
					<select name="rank">
						<option value="">-</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
				<div class="formLabel"><?php print _t("Tags (separated by commas)"); ?></div>
				<input type="text" name="tags">
				<div class="formLabel"><?php print _t("Media"); ?></div>
				<input type="file" name="media1">
				<div class="formLabel"><?php print _t("Comment"); ?></div>
				<textarea name="comment" rows="5"></textarea>
				<br><a href="#" name="commentSubmit" onclick="document.forms.comment.submit(); return false;"><?php print _t("Save"); ?></a>
			</form>
<?php
		}else{
			if (!$this->request->config->get('dont_allow_registration_and_login')) {
				if($this->getVar("ranking") || (is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){
					print "<p>".caNavLink($this->request, (($vs_login_message) ? $vs_login_message : _t("Please login/register to rank, tag and comment on this item.")), "", "", "LoginReg", "form", array('site_last_page' => 'ObjectDetail', 'object_id' => $vn_object_id))."</p>";
				}
			}
		}
?>		
		</div><!-- end objUserData-->
<?php
	}
?>
		</div><!-- end rightCol -->		
		<div id="leftCol">
			<h1><?php print $vs_title." (".unicode_ucfirst($this->getVar('typename')).')'; ?></h1>
			<div id="leftSide">
<?php
			# --- identifier
			if($t_object->get('idno')){
				print "<div class='unit'><b>"._t("Identifier")."</b><br/> ".$t_object->get('idno')."</div><!-- end unit -->";
			}
			if($vs_altID = $t_object->get("ca_objects.altID")){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.altID")."</b><br/> {$vs_altID}</div><!-- end unit -->";
			}				
			if ($t_object->get("ca_objects.type_id") == 21) {	
				if($vs_av_value = $t_object->get("ca_objects.av_date.av_dates_value")){
					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.av_date")."</b><br/> {$vs_av_value}";
					if($t_object->get("ca_objects.av_date.date_approximate1", array('convertCodesToDisplayText' => true)) == "Yes"){
						print " (approximate)";
					}
					print "</div><!-- end unit -->";
				}
			}else{	
				if($vs_value = $t_object->get("ca_objects.date.dates_value")){
					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.date")."</b><br/> {$vs_value}</div><!-- end unit -->";
				}
			}			
			if($vs_creator = $t_object->get("ca_entities", array('restrictToRelationshipTypes' => array('artist', 'co_producer', 'composer', 'director', 'illustrator', 'performer', 'photographer', 'producer', 'writer')))){
				print "<div class='unit'><b>"._t('Creator')."</b><br/> {$vs_creator}</div><!-- end unit -->";
			}			
			if($va_contributors = $t_object->get("ca_entities", array('returnAsArray' => true, 'restrictToRelationshipTypes' => array('actor', 'animator', 'audio_engineer', 'author', 'broadcast_engineer', 'camera_assistant', 'camera_operator', 'cinematographer', 'composer', 'contributing_artist', 'editor', 'engineer', 'filmmaker', 'interviewee', 'interviewer', 'musician', 'narrator', 'performer', 'recording_engineer', 'sound_mixer', 'subject', 'writer')))){
				print "<div class='unit'><b>"._t('Contributor(s)')."</b><br/>";
				$va_contributor_display = array();
				foreach($va_contributors as $va_contributor){
					print $va_contributor["displayname"]." (".$va_contributor["relationship_typename"].")<br/>"; 
				}
				print "</div><!-- end unit -->";
			}
			if($vs_media_type = $t_object->get("ca_objects.media_type" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.media_type")."</b><br/> {$vs_media_type}</div><!-- end unit -->";
			}
			if($vs_av_format = $t_object->get("ca_objects.av_format_Hierachical" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.av_format_Hierachical")."</b><br/> {$vs_av_format}</div><!-- end unit -->";
			}
			
			#if($t_object->get("ca_objects.duration")){
			#	$vs_value = $t_object->get("ca_objects.duration" , array('convertCodesToDisplayText' => true, 'template' => 'Run time: ^runTime'));
			#	if ($t_object->get("ca_objects.duration.approximate") == 841) {
			#		$va_approximate = "(Approximate)";
			#	}
			#	print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.duration")."</b><br/> {$vs_value} {$va_approximate}</div><!-- end unit -->";
			#}
			#if($vs_av_digital_format = $t_object->get("ca_objects.av_digital_format" , array('convertCodesToDisplayText' => true))){
			#	print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.av_digital_format")."</b><br/> {$vs_av_digital_format}</div><!-- end unit -->";
			#}
			if($vs_ph_digital_format = $t_object->get("ca_objects.ph_digital_format" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.ph_digital_format")."</b><br/> {$vs_ph_digital_format}</div><!-- end unit -->";
			}						
			if($vs_photo_format = $t_object->get("ca_objects.photo_format" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.photo_format")."</b><br/> {$vs_photo_format}</div><!-- end unit -->";
			}
			if($vs_paper_format = $t_object->get("ca_objects.paper_format" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.paper_format")."</b><br/> {$vs_paper_format}</div><!-- end unit -->";
			}
			if($vs_erec_format = $t_object->get("ca_objects.erec_format" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.erec_format")."</b><br/> {$vs_erec_format}</div><!-- end unit -->";
			}	
			if($vs_generation_element = $t_object->get("ca_objects.generation_element" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.generation_element")."</b><br/> {$vs_generation_element}</div><!-- end unit -->";
			}
			if($vs_generation_general = $t_object->get("ca_objects.generation_general" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.generation_general")."</b><br/> {$vs_generation_general}</div><!-- end unit -->";
			}
			if($vs_born_digital = $t_object->get("ca_objects.born_digital" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.born_digital")."</b><br/> {$vs_born_digital}</div><!-- end unit -->";
			}
			if($vs_container = $t_object->get("ca_objects.container" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.container")."</b><br/> {$vs_container}</div><!-- end unit -->";
			}
			if($vs_color = $t_object->get("ca_objects.color" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.color")."</b><br/> {$vs_color}</div><!-- end unit -->";
			}
			if($vs_sound = $t_object->get("ca_objects.av_sound" , array('convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.av_sound")."</b><br/> {$vs_sound}</div><!-- end unit -->";
			}
?>
			</div>
			<div id="rightSide">
<?php
			#if($vs_hierarchy = $t_object->get("ca_objects.hierarchy_location")){
			#	print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.hierarchy_location")."</b><br/> {$vs_hierarchy}</div><!-- end unit -->";
			#}
			if($vs_description = $t_object->get("ca_objects.description_w_type", array('convertCodesToDisplayText' => true, 'template' => '^description'))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.description_w_type")."</b><br/> {$vs_description}</div><!-- end unit -->";
			}
			# --- vocabulary terms
			$va_terms = $t_object->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><h2>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</h2>";
				foreach($va_terms as $va_term_info){
					print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</div>";
				}
				print "</div><!-- end unit -->";
			}
			if($vs_source = $t_object->get("ca_objects.source", array('convertCodesToDisplayText' => true, 'template' => '^description'))){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.source")."</b><br/> {$vs_source}</div><!-- end unit -->";
			}
			if($t_object->get("ca_objects.been_preserved")){
				print "<div class='unit'><b>Preservation History</b><br/>";
				if($t_object->get("ca_objects.action")){
					print $t_object->get("ca_objects.action")."; ";
				}
				if($t_object->get("ca_objects.preservation_date")){
					print $t_object->get("ca_objects.preservation_date")."; ";
				}
				if($t_object->get("ca_objects.sponsor")){
					print $t_object->get("ca_objects.sponsor").".";
				}
				print "</div>";
			}
			if($vs_access_restrictions = $t_object->get("ca_objects.access_restrictions")){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.access_restrictions")."</b><br/> {$vs_access_restrictions}</div><!-- end unit -->";
			}
			if($vs_user_restrictions = $t_object->get("ca_objects.user_restrictions")){
				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.user_restrictions")."</b><br/> {$vs_user_restrictions}</div><!-- end unit -->";
			}	
			if($va_links = $t_object->get('externalLink', array('returnAsArray' => true, 'template' => '<a href="^url_entry" target="_blank">^url_source</a>'))){
				print "<div class='unit'><b>"._t("External Link")."</b>: ";
				$va_link_display = array();
				foreach($va_links as $va_link){
					$va_link_display[] = "<a href='".$va_link["url_entry"]."' target='_blank'>".$va_link["url_source"]."</a>";
				}
				print join(", ", $va_link_display)."</div>";
			}
			# --- occurrences
			$va_occurrences = $t_object->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
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
						<div class="unit"><h2><?php print _t("Related")." ".$va_item_types[$vn_occurrence_type_id]['name_singular'].((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></h2>
<?php
					foreach($va_occurrence_list as $vn_rel_occurrence_id => $va_info) {
						print "<div>".(($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"])." (".$va_info['relationship_typename'].")</div>";
					}
					print "</div><!-- end unit -->";
				}
			}
			# --- collections
			$va_collections = $t_object->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])."</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- places
			$va_places = $t_object->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			
			if(sizeof($va_places) > 0){
				print "<div class='unit'><h2>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</h2>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
		if($t_object->get('coverage')){
			print "<div class='unit'><b>"._t("Coverage")."</b><br/>".$t_object->get('coverage')."</div><!-- end unit -->";
		}
		if($t_object->get('georeference')){
			$o_map = new GeographicMap(250, 250, 'map');
			$o_map->mapFrom($t_object, 'georeference');
			print "<div class='unit'>".$o_map->render('HTML')."</div>";
		}			
			
			
			
// 			
// 			if($va_lcsh_names = $t_object->get("ca_objects.lcsh_names", array("returnAsArray" => true))){
// 				$va_display = array();
// 				foreach($va_lcsh_names as $va_lcsh_name){
// 					$vs_term = $va_lcsh_name["lcsh_names"];
// 					$vn_pos1 = strpos($vs_term, "[");
// 					if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 						$va_display[] = $vs_display_term;
// 					}
// 				}
// 				if(sizeof($va_display)){
// 					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.lcsh_names")."</b><br/>";
// 					print join("<br/>", $va_display);
// 					print "</div><!-- end unit -->";
// 				}
// 			}
// 			if($va_lcsh_topical = $t_object->get("ca_objects.lcsh_topical", array("returnAsArray" => true))){
// 				$va_display = array();
// 				foreach($va_lcsh_topical as $va_lcsh_topical_term){
// 					$vs_term = $va_lcsh_topical_term["lcsh_topical"];
// 					$vn_pos1 = strpos($vs_term, "[");
// 					if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 						$va_display[] = $vs_display_term;
// 					}
// 				}
// 				if(sizeof($va_display)){
// 					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.lcsh_topical")."</b><br/>";
// 					print join("<br/>", $va_display);
// 					print "</div><!-- end unit -->";
// 				}
// 			}
// 			if($va_lcsh_geo = $t_object->get("ca_objects.lcsh_geo", array("returnAsArray" => true))){
// 				$va_display = array();
// 				foreach($va_lcsh_geo as $va_lcsh_geo_term){
// 					$vs_term = $va_lcsh_geo_term["lcsh_geo"];
// 					$vn_pos1 = strpos($vs_term, "[");
// 					if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 						$va_display[] = $vs_display_term;
// 					}
// 				}
// 				if(sizeof($va_display)){
// 					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.lcsh_geo")."</b><br/>";
// 					print join("<br/>", $va_display);
// 					print "</div><!-- end unit -->";
// 				}
// 			}
// 			if($va_lcsh_genre = $t_object->get("ca_objects.lcsh_genre", array("returnAsArray" => true))){
// 				$va_display = array();
// 				foreach($va_lcsh_genre as $va_lcsh_genre_term){
// 					$vs_term = $va_lcsh_genre_term["lcsh_genre"];
// 					$vn_pos1 = strpos($vs_term, "[");
// 					if($vs_display_term = trim(substr($vs_term, 0, $vn_pos1))){
// 						$va_display[] = $vs_display_term;
// 					}
// 				}
// 				if(sizeof($va_display)){
// 					print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.lcsh_genre")."</b><br/>";
// 					print join("<br/>", $va_display);
// 					print "</div><!-- end unit -->";
// 				}
// 			}
// 			if($vs_coverage = $t_object->get("ca_objects.coverage")){
// 				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.coverage")."</b><br/> {$vs_coverage}</div><!-- end unit -->";
// 			}
// 			if($vs_rights = $t_object->get("ca_objects.rights")){
// 				print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.rights")."</b><br/> {$vs_rights}</div><!-- end unit -->";
// 			}			
?>			
	</div><!-- end rightSide-->
</div><!-- end leftCol-->
</div><!-- end detailWrapper-->	
	<div id='sideBar'>
<?php	
	$item_reps = $t_object->getRepresentations(array("preview170"), null, array('checkAccess' => $va_access_values));
	if (sizeof($item_reps) > 1) {
?>	
		<div id='more'>
			<div id='moreTitle'>More images</div>
			<div class='moreContent'>
<?php
			
			foreach (array_slice($item_reps, 1) as $item_rep) {
			$va_item_rep = $item_rep['tags']['preview170'];
			
			$vn_rep_id = $item_rep['representation_id'];
				print "<a href='#' onclick='caMediaPanel.showPanel(\"".$this->request->getBaseUrlPath()."/index.php/Detail/Object/GetRepresentationInfo/object_id/".$vn_object_id."/representation_id/".$vn_rep_id."\"); return false;'>".$va_item_rep."</a>";
			}
?>			
			</div>
		</div>		
<?php
	}
			# --- output related object images as links
			$va_related_objects = $t_object->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if (sizeof($va_related_objects)) {
			print "<div id='relatedItems'>";
			print "<div id='relatedTitle'>Related Items</div>";	
				foreach($va_related_objects as $vn_rel_id => $va_info){
					$t_rel_object = new ca_objects($va_info["object_id"]);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'small'), null, array('return_with_access' => $va_access_values));
					$va_rel_title = $t_rel_object->get('ca_objects.preferred_labels.name');
					print "<div class='item'>".caNavLink($this->request, $va_rel_title, '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
					print "<div style='width:100%; height:1px;clear:both;'></div>";
					print "</div>";
					
					// set view vars for tooltip
					$this->setVar('tooltip_representation', $va_reps['tags']['small']);
					$this->setVar('tooltip_title', $va_info['label']);
					$this->setVar('tooltip_idno', $va_info["idno"]);
					TooltipManager::add(
						".icon".$va_info["object_id"], $this->render('../Results/ca_objects_result_tooltip_html.php')
					);	
				}
				print "<div style='width:100%; height:1px;clear:both;'></div>";
				print "</div>";
			}
			
?>			
		
	
	</div>	<!-- end sideBar -->

	</div><!-- end detailBody -->
<?php
	require_once(__CA_LIB_DIR__.'/core/Parsers/COinS.php');
	
	print COinS::getTags($t_object);
	# -- metatags for facebook sharing
	MetaTagManager::addMeta('og:title', $vs_title);
	if($t_rep && $t_rep->getPrimaryKey() && $vs_media_url = $t_rep->getMediaUrl('media', 'thumbnail')){
		MetaTagManager::addMeta('og:image', $vs_media_url);
		MetaTagManager::addLink('image_src', $vs_media_url);
	}
	if($vs_description_text){
		MetaTagManager::addMeta('og:description', $vs_description_text);
	}
	
?>
