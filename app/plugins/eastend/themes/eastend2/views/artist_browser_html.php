<div id="subnav">
	<form action="#">
<?php
		$va_facets = $this->getVar('available_facets');
		if(is_array($va_facets) && sizeof($va_facets)){
?>
			<div id="filterByNameContainer">
				<span class="listhead"><?php print _t("Search"); ?></span>
				<ul>
					<li><input type="text" name="filterByName" id="filterByName" value="" onfocus="this.value='';"/><a href="#" onclick="jQuery('.artistEntry').css('opacity', 1.0); jQuery('#filterByName').val(''); return false;"> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/eastend/x.png" title="clear search" width="15" height="15" alt="clear search" style="vertical-align:top; padding:3px 0px 0px 3px;" /></a></li>
				</ul>
			</div>
			<span class="listhead"><?php print _t("Filter by"); ?></span>
			<ul>
<?php
			foreach($va_facets as $vs_facet_name => $va_facet_info) {
				if($vs_facet_name == "onview"){
					continue;
				}
?>
				<li><a href="#" class="abFacetList" id="abFacetList<?php print $vs_facet_name; ?>" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetList<?php print $vs_facet_name; ?>").addClass("selected"); jQuery("#facetBox").load("<?php print caNavUrl($this->request, 'eastend', 'ArtistBrowser', 'getFacet', array('target' => 'ca_entities', 'facet' => $vs_facet_name, 'view' => 'simple_list')); ?>"); return false;'><?php print $va_facet_info['label_singular']; ?></a></li>				
<?php
			}
?>
				<li><a href="#" class="abFacetList" id="abFacetListOnView" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetListOnView").addClass("selected"); jQuery("#facetBox").html(" "); jQuery("#contentBox").load("<?php print caNavUrl($this->request, 'eastend', 'ArtistBrowser', 'clearAndAddCriteria', array('facet' => 'onview', 'id' => $this->getVar("on_view_yes_id"))); ?>"); return false;'><?php print _t("on view"); ?></a></li>				
				<li><a href="#" class="abFacetList selected" id="abFacetListAll" onclick='jQuery(".abFacetList").removeClass("selected"); jQuery("#abFacetListAll").addClass("selected"); jQuery("#facetBox").html(" "); jQuery("#contentBox").load("<?php print caNavUrl($this->request, 'eastend', 'ArtistBrowser', 'clearAndAddCriteria'); ?>"); return false;'><?php print _t("view all"); ?></a></li>				
			</ul>
<?php
		}
?>	
		</form>
		<script type="text/javascript">
			
			// This will break in jQuery 1.8
			jQuery.extend($.expr[':'], {
			  'containsi': function(elem, i, match, array)
			  {
				return (elem.textContent || elem.innerText || '').toLowerCase()
				.indexOf((match[3] || "").toLowerCase()) >= 0;
			  }
			});
			jQuery(document).ready(function() {
				//prevent form submit with enter key
				jQuery('#filterByName').bind("keypress", function(e) {
					if (e.keyCode == 13) {
						return false;
					}
				});
				
			$(window).scroll(function () {
				if ($(window).scrollTop() >= 170) {
					if(!jQuery('#filterByNameContainer').hasClass('filterByNameContainerFixed')){
						jQuery('#filterByNameContainer').addClass('filterByNameContainerFixed');
					}
				}else{
					if(jQuery('#filterByNameContainer').hasClass('filterByNameContainerFixed')){
						jQuery('#filterByNameContainer').removeClass('filterByNameContainerFixed');
					}
				}
			});
				
				var typingTimer;
				
				//jQuery('#filterByName').css('color', '#eeeeee');
				jQuery('#filterByName').bind('keyup', function(e) {
					if (!jQuery('#filterByName').val()) { jQuery(".artistEntry").css("opacity", 1.0); }
					typingTimer = setTimeout(function() {
						var t = jQuery('#filterByName').val();
						if (t.length > 0) {
							jQuery(".artistEntry").css("opacity", "0.3");
							if (jQuery(".artistEntry:containsi(" + t + ")").length) {
								jQuery(".artistEntry:containsi(" + t + ")").animate({ opacity: 1.0, duration: 750});
								jQuery.scrollTo(".artistEntry:containsi(" + t + ")", { duration: 750, offset: { top: (jQuery(window).height()/2) * -1, left: 0 }});
							}
						} else {
							jQuery(".artistEntry").css("opacity", "1.0");
						}
					}, 1500);
				});
				jQuery('#filterByName').bind('keydown', function(){
					clearTimeout(typingTimer);
					//jQuery('#filterByName').css('color', '#999999');
				});
				
				jQuery('#filterByName').bind('focus', function(){
					//jQuery('#filterByName').css('color', '#000000');
					if (jQuery('#filterByName').val() == 'Name') {
						jQuery('#filterByName').val('');
					}
				});
			});
		</script>
		<div id="facetBox">
			
		</div>
</div><!--end subnav-->

<div id='contentBox' style='float:left; width:830px;'></div>
	
<script type="text/javascript">
$(document).ready(function() {	
	//load a browse by type = individual to populate page on load
	jQuery("#contentBox").load("<?php print caNavUrl($this->request, 'eastend', 'ArtistBrowser', 'clearAndAddCriteria'); ?>");
});
</script>

