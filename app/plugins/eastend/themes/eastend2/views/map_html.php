<?php
	$vo_result = $this->getVar('result');
	//print $vo_result->numHits();
?>

<div id="subnav">
	<form action="#">
<?php
		$va_criteria = $this->getVar('criteria');
		
		$va_facets = $this->getVar('available_facets');
		if(is_array($va_facets) && sizeof($va_facets)){
?>
			<span class="listhead"><?php print _t("Filter by"); ?></span>
			<ul>
<?php
			foreach($va_facets as $vs_facet_name => $va_facet_info) {
?>
				<li><a href="#" class="abFacetList" id="abFacetList<?php print $vs_facet_name; ?>" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetList<?php print $vs_facet_name; ?>").addClass("selected"); jQuery("#facetBox").load("<?php print caNavUrl($this->request, 'eastend', 'Map', 'getFacet', array('target' => 'ca_objects', 'facet' => $vs_facet_name, 'view' => 'simple_list')); ?>"); return false;'><?php print $va_facet_info['label_singular']; ?></a></li>				
<?php
			}
?>
				<li><a href="#" class="abFacetList selected" id="abFacetListAll" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetListAll").addClass("selected"); jQuery("#facetBox").html(" "); jQuery("#contentBox").load("<?php print caNavUrl($this->request, 'eastend', 'Map', 'clearAndAddCriteria'); ?>"); return false;'><?php print _t("view all"); ?></a></li>				
			</ul>
<?php
		}
?>	
		</form>
		
		<div id="facetBox"  style="height: 300px; overflow-y: auto;">
			
		</div>
</div><!--end subnav-->

<div id='contentBox' style='float:left; width:830px;'>
<?php
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
</div>