		<div style="clear:both;"><!-- empty --></div></div><!-- end pageArea -->
<?php
	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
			<div id="nav" data-role="footer" data-position="fixed" data-tap-toggle="false">
				<div class='homeNav'><?php print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/home.png' height='28' width='28' border='0'>", '', '', '', ''); ?></div>			
<?php				
				if ((($this->request->getController() == "About") && ($this->request->getAction() == "map")) || (($this->request->getController() == "Search") && ($this->request->getAction() == "Index"))) {
?>
				<div class='helpNav'><a href='#' onclick='clickroute()'><img src='<?php print $this->request->getThemeUrlPath()?>/graphics/crosshairs1.png' height='28' width='28' border='0'></a></div>
<?php				
				}
?>
				<div class='helpNav'><?php print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/help.png' height='28' width='28' border='0'>", '', '', '', ''); ?></div>
				
				<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/spacer.gif' border='0' width='17' height='16'></a><input type="text" name="search" value="<?php print ($vs_search) ? $vs_search : ''; ?>" autocomplete="off" size="100"/>
				</form></div>
				<!--<a href="#" onclick='$("#navMenu").slideToggle(250); return false;'><?php print _t("Menu"); ?>&darr;</a>-->
			<div style="clear:both;"><!-- empty --></div>
			</div><!-- end nav -->
		<div id='helpDiv'>
			This feature requires you to enable Location Services.  Please adjust your browser settings and try again.
			<div id='helpDivClose'><a href='#'>Close</a></div>
		</div>	
	</div><!-- end pageWidth -->
<?php
print TooltipManager::getLoadHTML();
?>
	<div id="caMediaPanel"> 
		<div id="caMediaPanelContentArea">
		
		</div>
	</div>
	<script type="text/javascript">
		$(function(){  // $(document).ready shorthand
		  
		$("#helpDivClose").click(function(event) {
        event.stopPropagation();
        
        $("#helpDiv").fadeOut();
    })
		  
		});
	</script>

	<script type="text/javascript">
	/*
		Set up the "caMediaPanel" panel that will be triggered by links in object detail
		Note that the actual <div>'s implementing the panel are located here in views/pageFormat/pageFooter.php
	*/
	var caMediaPanel;
	jQuery(document).ready(function() {
		if (caUI.initPanel) {
			caMediaPanel = caUI.initPanel({ 
				panelID: 'caMediaPanel',										/* DOM ID of the <div> enclosing the panel */
				panelContentID: 'caMediaPanelContentArea',		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: '#000000',						/* color (in hex notation) of background masking out page content; include the leading '#' in the color spec */
				exposeBackgroundOpacity: 0.7,							/* opacity of background color masking out page content; 1.0 is opaque */
				panelTransitionSpeed: 200, 									/* time it takes the panel to fade in/out in milliseconds */
				allowMobileSafariZooming: true,
				mobileSafariViewportTagID: '_msafari_viewport',
				mobileSafariInitialZoom: .43,
				mobileSafariMinZoom: .43,
				mobileSafariMaxZoom: 1.0,
				mobileSafariDeviceWidth: 740,
				mobileSafariDeviceHeight: 640,
				mobileSafariUserScaleable: true
			});
		}
	});
	</script>
	</body>
</html>
