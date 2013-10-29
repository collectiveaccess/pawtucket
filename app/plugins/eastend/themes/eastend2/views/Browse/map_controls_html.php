<?php
	$vo_result = $this->getVar('result');
	$va_facets = $this->getVar('facets');
	$va_criteria = $this->getVar('criteria');
	array_shift($va_criteria);
	$va_criteria = array_shift($va_criteria);
?>
	<div>
<?php
		print "<strong><em>Showing ".(($vs_criteria = array_pop($va_criteria)) ? $vs_criteria : "everything")."</em></strong>";
		
		if(!is_array($va_facets) || !sizeof($va_facets)){
?>
	<div style="float: right;"><a href="#" class="abFacetList selected" id="abFacetListAll" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetListAll").addClass("selected"); jQuery("#facetBox").html(" "); jQuery("#contentBox").load("<?php print caNavUrl($this->request, 'eastend', 'Map', 'clearAndAddCriteria'); ?>"); return false;'><?php print _t("view all"); ?></a></div>
<?php
		}
?>
	</div>
<?php
	$o_viz = new Visualizer('ca_objects');
	$o_viz->addData($vo_result);
	print $o_viz->render('map');
?>