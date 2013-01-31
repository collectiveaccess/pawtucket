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

$vs_pin_ids = $this->request->getParameter("pin_ids", pString);
$va_pin_ids = explode(',+', $vs_pin_ids);

$t_object = new ca_objects();

$qr_result = $t_object->makeSearchResult('ca_objects', $va_pin_ids);
 
		print '<div class="listItems">';
		
		while($qr_result->nextHit()) {	
	
			print "<div class='item'><div class='thumb'>".$qr_result->getMediaTag('ca_object_representations.media', 'icon')."</div><!-- end thumb -->";
			
			print caNavLink($this->request, $qr_result->get("ca_objects.preferred_labels"), '', 'Detail', 'Object', 'Show', array('object_id' => $qr_result->get("ca_objects.object_id")));;

			print "<div style='clear:left;'><!--empty --></div>";
			print "</div>";
			
		}
			
		print "\n</div>\n";
	
?>
