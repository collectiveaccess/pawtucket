<?php
/* ----------------------------------------------------------------------
 * list_html.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
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
	$t_item 				= $this->getVar('t_item');
	$vn_item_id		 		= $t_item->getPrimaryKey();
	$vs_table				= $t_item->tableName();
	$vs_pk					= $t_item->primaryKey();
	
	$va_access_values		= $this->getVar('access_values');
	

	$va_titles 				= $this->getVar('titles');
	$va_letters				= $this->getVar('letters');
	$vs_selected_letter		= $this->getVar('selected_letter');
?>
	<div id="caFindingAidsLetterBar">
<?php
	//
	// Output letter bar
	//
	$va_letter_list = array();
	foreach(range('a','z') as $vs_l) {
		$vs_letter_to_print = $vs_l;
		if ($vs_selected_letter == $vs_l) {
			$vs_letter_to_print = "<span class='selectedLetter'>{$vs_letter_to_print}</span>"; // Format currently selected letter differently
		} 
		$vs_letter_to_print = (isset($va_letters[$vs_l]) && ($va_letters[$vs_l] > 0)) ? "<span class='letter'>".caNavLink($this->request, $vs_letter_to_print, '', 'FindingAids', 'List', 'Index', array('table' => $vs_table, 'l' => $vs_l))."</span>" : "<span class='letter'>".$vs_letter_to_print."</span>";
		
		$va_letter_list[] = $vs_letter_to_print;
	}
	
	print join("   ", $va_letter_list);
?>
	</div>
	<div>
		<div id="caFindingAidsTitleSideBar">
<?php	
		//
		// Output side bar
		//
		foreach($va_titles as $vn_id => $vs_title) {
			if ($vn_id == $vn_item_id) {
				$vs_title = "<span class='selectedItem'>{$vs_title}</span>";
			}
			print "<p>".caNavLink($this->request, $vs_title, '', 'FindingAids', 'List', 'Index', array('table' => $vs_table, $vs_pk => $vn_id, 'l' => $vs_selected_letter));
		}
?>
		</div>
		<div id="caFindingAidsItemDetail">
<?php
		//
		// Output detail for currently "selected" record
		//
		// view is being included from main theme dir
		//
		include(__CA_THEMES_DIR__."/".__CA_THEME__."/views/Detail/".$t_item->tableName()."_detail_html.php");
?>
		</div>
	</div>