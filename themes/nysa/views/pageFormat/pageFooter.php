		<div style="clear:both; height:0px;"><!-- empty --></div></div><!-- end pageArea -->
		<div id="footer">
			<div class="left-box">
			<a href="#"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/footer-logo.jpg" width="113px" height="24px" alt="New York State Education Department" /></a>
			<p>
				The New York State Archives is part of the Office of Cultural Education, an office 
				of the New York State Education Department.
			</p>
		</div>
		<ul>
			<li class="list-item-one">
				<ul>
					<li><a href="#">About Us</a></li>
					<li><a href="#">News</a></li>
					<li><a href="#">Directions</a></li>
					<li><a href="#">Contact Us</a></li>
					<li><a href="#">Site Map</a></li>
				</ul>
			</li>
			<li class="list-item-two">
				<ul>
					<li><a href="#">New York Archives Magazine</a></li>
					<li><a href="#">Archives Partnership Trust</a></li>
					<li><a href="#">New York State Historical Records</a></li>
					<li class="advisory-board"><a href="#">Advisory Board</a></li>
					<li><a href="#">Online Catalog</a></li>
				</ul>
			</li>
			<li>
				<ul>
					<li><a href="#">Office of Cultural Education</a></li>
					<li><a href="#">New York State Education Department</a></li>
					<li><a href="#">New York State</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Terms of Use</a></li>
				</ul>
			</li>
		</ul>
		
			<p class="bottom-paragraph"><?php print $this->request->config->get('page_footer_text'); ?> </p>
		</div><!-- end footer -->
<?php
print TooltipManager::getLoadHTML();
?>
	<div id="caMediaPanel"> 
		<div id="caMediaPanelContentArea">
		
		</div>
	</div>
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
				exposeBackgroundOpacity: 0.8,							/* opacity of background color masking out page content; 1.0 is opaque */
				panelTransitionSpeed: 400, 									/* time it takes the panel to fade in/out in milliseconds */
				allowMobileSafariZooming: true,
				mobileSafariViewportTagID: '_msafari_viewport',
				closeButtonSelector: '.close'					/* anything with the CSS classname "close" will trigger the panel to close */
			});
		}
	});
	</script>
	</body>
</html>
