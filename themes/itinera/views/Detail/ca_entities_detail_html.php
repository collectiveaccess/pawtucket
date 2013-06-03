<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_entities_detail_html.php : 
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
	$t_entity 			= $this->getVar('t_item');
	$vn_entity_id 		= $t_entity->getPrimaryKey();
	
	$vs_title 			= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');

if (!$this->request->isAjax()) {		
?>
	<div id="detailBody">
<?php
#		<div id="pageNav">
#
#			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_entities', _t("Back"), ''))) {
#				if ($this->getVar('previous_id')) {
#					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Entity', 'Show', array('entity_id' => $this->getVar('previous_id')), array('id' => 'previous'));
#				}else{
#					print "&lsaquo; "._t("Previous");
#				}
#				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
#				if ($this->getVar('next_id') > 0) {
#					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Entity', 'Show', array('entity_id' => $this->getVar('next_id')), array('id' => 'next'));
#				}else{
#					print _t("Next")." &rsaquo;";
#				}
#			}
#
#		</div><!-- end nav -->
?>		

		<div id="leftCol">		
<?php
			if((!$this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks')){
?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
<?php
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_entity_id, 'tablename' => 'ca_entities'));
				}else{
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_entity_id, 'tablename' => 'ca_entities'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			# --- identifier
			if($va_entity_image = $t_entity->get('ca_entities.agentMedia', array('version' => 'small'))){
				print $va_entity_image;
			}
			print "<div class='artistName'>".$vs_title."</div>";
			print "<div class='affiliations'>Travel Route</div>";
			print "<div class='artistInfo'>";
			print "<div class='unit'><b>Biography</b></div>";
			
			$va_entity_dates = $t_entity->get('ca_entities.agentLifeDateSet', array('returnAsArray' => true, 'convertCodesToDisplayText' => true));
			$vs_entity_birthplace = $t_entity->get('ca_places', array('restrictToRelationshipTypes' => array('birthplace')));
			
			if(is_array($va_entity_dates) || $vs_entity_birthplace) {
				print "<div class='unit'>";
				print "<div><b>Born</b></div>";
			
				if(is_array($va_entity_dates)) {
					foreach ($va_entity_dates as $entity_date) {
						if ($entity_date['agentLifeDateType'] == "Birth dates") {
							print $entity_date['agentLifeDisplayDate'];
						}
					}
				}
				print "<div>{$vs_entity_birthplace}</div>";
				print "</div><!-- end Born -->"; 
			}
			
			$vs_entity_deathplace = $t_entity->get('ca_places', array('restrictToRelationshipTypes' => array('deathplace')));
			if(is_array($va_entity_dates) || $vs_entity_deathplace) {
				print "<div class='unit'>";
				print "<div><b>Died</b></div>";
				foreach ($va_entity_dates as $entity_date) {
					if ($entity_date['agentLifeDateType'] == "Death dates") {
						print $entity_date['agentLifeDisplayDate'];
					}
				}
				print "<div>{$vs_entity_deathplace}</div>";
				print "</div><!-- end Died -->"; 
			}
			
			if ($va_life_roles = $t_entity->get('ca_entities.agentLifeRoleSet.agentLifeRoleType', array('convertCodesToDisplayText' => true, 'delimiter' => '<br/>'))) {
				print "<div class='unit'><div><b>Life Roles</b></div>";
				print $va_life_roles;
				print "</div>";
			}

			print "</div><!-- end artistInfo-->";
			# --- attributes
			$va_attributes = $this->request->config->get('ca_entities_detail_display_attributes');
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_entity->get("ca_entities.{$vs_attribute_code}")){
						print "<div class='unit'><b>".$t_entity->getDisplayLabel("ca_entities.{$vs_attribute_code}").":</b> {$vs_value}</div><!-- end unit -->";
					}
				}
			}
			# --- description
			if($this->request->config->get('ca_entities_description_attribute')){
				if($vs_description_text = $t_entity->get("ca_entities.".$this->request->config->get('ca_entities_description_attribute'))){
					print "<div class='unit'><div id='description'><b>".$t_entity->getDisplayLabel('ca_entities.'.$this->request->config->get('ca_entities_description_attribute')).":</b> {$vs_description_text}</div></div><!-- end unit -->";				
?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#description').expander({
								slicePoint: 300,
								expandText: '<?php print _t('[more]'); ?>',
								userCollapse: false
							});
						});
					</script>
<?php
				}
			}

			# --- occurrences
			$va_occurrences = $t_entity->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
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
			$va_collections = $t_entity->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>";
					print (($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])." (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- vocabulary terms
			$va_terms = $t_entity->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><h2>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</h2>";
				foreach($va_terms as $va_term_info){
					print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</div>";
				}
				print "</div><!-- end unit -->";
			}			

?>
	</div><!-- end leftCol -->
	<div id="rightCol">
	<div class='header'>	
		<div><?php print $vs_title; ?></div>
		<div>Interpersonal Relations over Grand Tour</div>
		<div>(interstitial date range here)</div>
	</div>
		<div id="resultBox">
<?php
}
	require_once(__CA_LIB_DIR__."/ca/Search/TourStopSearch.php");
	$o_viz = new Visualizer('ca_entities');
	
	
	
	$o_viz->addData($t_entity);
	print $o_viz->render('network');
	
	// set parameters for paging controls view
	$this->setVar('other_paging_parameters', array(
		'entity_id' => $vn_entity_id
	));
	//print $this->render('related_objects_grid.php');
	
if (!$this->request->isAjax()) {
?>
		</div><!-- end resultBox -->
	</div><!-- end rightCol -->

</div><!-- end detailBody -->
	<div id='entityFooter'>
		<div><i>Source: Ford, Brinsley and John Ingamells. _A Dictionary of British and Irish Travellers in Italy, 1701-1800_. New Haven: Yale University Press, 1997.</i></div>
		<div><i>Discovered by: The Itinera Team</i></div>
	</div>
<?php
}
?>