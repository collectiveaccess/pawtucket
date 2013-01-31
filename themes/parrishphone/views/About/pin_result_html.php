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
 	
		
		print '<div class="listItems">';
		
			print "<div class='item'><div class='thumb'><img src='".$this->request->getThemeUrlPath()."/graphics/blu-pointer.png' height='30' width='35' border='0'></div><!-- end thumb -->";
			
			print count($va_ids)." items at this location<br/>";
			print caNavLink($this->request, 'Click to view', '', '', 'About', 'listview', array('pin_ids' => $vs_string_ids), array('data-transition' => 'slide') );

			print "<div style='clear:left;'><!--empty --></div>";
			print "</div>";
			
			
		print "\n</div>\n";
	  
?>
