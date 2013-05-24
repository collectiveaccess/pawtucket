<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Splash/splash_html.php : 
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
	include_once(__CA_LIB_DIR__."/ca/Search/EntitySearch.php");
 
	$t_object = new ca_objects();
	
	$va_item_ids = $this->getVar('featured_content_slideshow_id_list');
	$va_item_media = $t_object->getPrimaryMediaForIDs($va_item_ids, array("mediumlarge"), array('checkAccess' => array(1)));
	$va_item_labels = $t_object->getPreferredDisplayLabelsForIDs($va_item_ids);


	$o_entity_search = new EntitySearch();
	$qr_entities = $o_entity_search->search("tour_yn:957", array('sort' => 'ca_entities.preferred_labels.surname', 'sort_direction' => 'asc'));
?>
	<div id='artistList'>
<?php	
	while($qr_entities->nextHit()) {
		print "<div class='artist'>".caNavLink($this->request, $qr_entities->get('ca_entities.preferred_labels.displayname'), '', 'Detail', 'Entity', 'Show', array('entity_id' => $qr_entities->get('ca_entities.entity_id')))."</div>";
	}
?>	
	</div>
		<div id="hpText">
<?php
		print $this->render('Splash/splash_intro_text_html.php');
?> 

		</div>			
