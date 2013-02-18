<?php
/* ----------------------------------------------------------------------
 * themes/default/views/Results/ca_objects_results_thumbnail_html.php :
 * 		thumbnail search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2009 Whirl-i-Gig
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

		$va_ids = $this->getVar('ids');
		$vs_string_ids = implode(", ", $va_ids);
 	
 		$va_id_count = count($va_ids);
 		
 	if ($va_id_count > 1) {	
		
		print '<div class="listItems">';
		
			print "<div class='item'><div class='thumb'>".caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/blu-pointer.png' height='30' width='35' border='0'>", '', '', 'About', 'listview', array('pin_ids' => $vs_string_ids), array('data-transition' => 'slide'))."</div><!-- end thumb -->";
			
			print $va_id_count." items at this location<br/>";
			print caNavLink($this->request, 'Click to view', '', '', 'About', 'listview', array('pin_ids' => $vs_string_ids), array('data-transition' => 'slide'));

			print "<div style='clear:left;'><!--empty --></div>";
			print "</div>";
			
			
		print "\n</div>\n";
	
	} else {
		$pin_id = $va_ids[0];
		$t_object = new ca_objects($pin_id);
		print '<div class="listItems">';	
	
			print "<div class='item' style='padding:7px 0px 7px 0px;'><div class='thumb'>".caNavLink($this->request, $t_object->getMediaTag('ca_object_representations.media', 'icon'), '', 'Detail', 'Object', 'Show', array('object_id' => $pin_id), array('data-transition' => 'slide'))."</div><!-- end thumb -->";
			
			print caNavLink($this->request, $t_object->get("ca_objects.preferred_labels"), '', 'Detail', 'Object', 'Show', array('object_id' => $pin_id), array('data-transition' => 'slide'));

			print "<div style='clear:left;'><!--empty --></div>";
			print "</div>";
			
		print "\n</div>\n";
	
	}
	  
?>
