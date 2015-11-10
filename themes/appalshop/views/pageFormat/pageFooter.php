		<div style="clear:both; height:1px;"><!-- empty --></div></div><!-- end pageArea -->
		</div><!-- end contentArea -->
		<div id="footer">
			
			<div id="mailingList">
				<div class='join'>
					<div class='joinWrapper'>
					<span style='padding-right:10px;'>Join Our Mailing List</span>
					<form class='mailForm' action="store-address.php" method="post">
						<input type="text" name="firstname" onclick="this.value='';" value="first name">
						<input type="text" name="lastname" onclick="this.value='';" value="last name">
						<input type="text" name="email" onclick="this.value='';" value="email address">
						<?php print '<div style="float:right"><input type="image" src="'.$this->request->getThemeUrlPath().'/graphics/greenarrow.jpg" border="0" alt="submit"/></div>';?>

					</form>
					</div>
				</div>
				<div class='footInfo'>Appalshop - 91 Madison Ave.- Whitesburg, KY 41858 - 606-633-0108 - 606-633-1009 (fax) - info@appalshop.org</div>
			</div>
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
