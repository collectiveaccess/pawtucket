		<div style="clear:both;"><!-- empty --></div></div><!-- end pageArea -->
<?php
	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
			<div id="nav" data-role="footer" data-position="fixed" >
				<div class='homeNav'><?php print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/home.png' height='28' width='28' border='0'>", '', '', '', ''); ?></div>			
<?php				
				if ((($this->request->getController() == "About") && ($this->request->getAction() == "map")) || (($this->request->getController() == "Search") && ($this->request->getAction() == "Index"))) {
?>
				<div class='helpNav'><a href='#' onclick='clickroute()'><img src='<?php print $this->request->getThemeUrlPath()?>/graphics/crosshairs1.png' height='28' width='28' border='0'></a></div>
<?php				
				}
				
				if (($this->request->getController() == "About") && ($this->request->getAction() == "map") || ($this->request->getController() != "About")) {
?>
				<div class='helpNav'><a href="#" onclick="$('#helpDiv').slideDown(250)"><?php print "<img src='".$this->request->getThemeUrlPath()."/graphics/help.png' height='28' width='28' border='0'>"; ?></a></div>
<?php
				}
?>				
				<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/spacer.gif' border='0' width='17' height='16'></a><input type="text" name="search" value="<?php print ($vs_search) ? $vs_search : ''; ?>" autocomplete="off" size="100"/>
				</form></div>
				<!--<a href="#" onclick='$("#navMenu").slideToggle(250); return false;'><?php print _t("Menu"); ?>&darr;</a>-->
			<div style="clear:both;"><!-- empty --></div>
			</div><!-- end nav -->
<?php
	if ($this->request->getController() == "Splash") {
		$vs_message = "To navigate the archive, select one of the available menu options";
	} else if ($this->request->getController() == "Browse") {
		$vs_message = "To browse the archive, choose a topic below";
	} else if ($this->request->getController() == "Search") {
		 $vs_message = "You may view your search results as a map";
	} else if ($this->request->getController() == "Detail") {
		 $vs_message = "To view related information about this item, expand the collapsible content at the bottom of this page";
	} else {
		 $vs_message = "This feature requires you to enable Location Services.  Please adjust your browser settings and try again.";	
	}
?>			
		<div id='helpDiv'>
			<?php print $vs_message; ?>
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
	<script>

function smart_scroll(el, offset) { 


offset = offset || 0; // manual correction, if other elem (eg. a header above) should also be visible

var air         = 15; // above+below space so element is not tucked to the screen edge

var el_height   = $(el).height()+ 2 * air + offset;
var el_pos      = $(el).offset();
var el_pos_top  = el_pos.top - air - offset;

var vport_height = $(window).height();
var win_top      = $(window).scrollTop();

//  alert("el_pos_top:"+el_pos_top+"  el_height:"+el_height+"win_top:"+win_top+"  vport_height:"+vport_height);

var hidden = (el_pos_top + el_height) - (win_top + vport_height);

if ( hidden > 0 ) // element not fully visible
    {
    var scroll;

    if(el_height > vport_height) scroll = el_pos_top;       // larger than viewport - scroll to top
    else                         scroll = win_top + hidden; // smaller than vieport - scroll minimally but fully into view
	 
    $('html, body').animate({ scrollTop: (scroll) }, 500); 
    }

}




// when using "on"  'expand' seems to fire before it's actually expanded. 
// el_height will then be height of closed collaps. won't work.

$('div.ui-collapsible').live('expand', function(e){ 
	e.stopPropagation();
	smart_scroll(e.target);
	event.preventDefault(); 
});

$('div.ui-collapsible').live('expand', function(e){
	e.stopPropagation(); 
  smart_scroll( e.target, $('ui-collapsible-heading').height() + 20 ); 
  event.preventDefault();
});


$(".zoomLink").bind("click", function(){
        $("[data-position='fixed']").fixedtoolbar('toggle');
});
function zoomImg() {
	$('#zoomImage').show();
    var maxwidth = $(window).width();
    var maxheight = $(window).height();
    var ratio = 0;
	var e1 = document.getElementById("largeImg");
	var imgw = $("#largeImg").width();
	var imgh = $("#largeImg").height();
	if (imgw > maxwidth) {
		ratio = maxwidth / imgw;
    	e1.style.width = maxwidth + "px";
    	$('#largeImg').css("height", imgh * ratio);
    	imgh = imgh * ratio;
    	imgw = imgw * ratio;
    	
    } 
	if (imgh > maxheight) {
		ratio = maxheight / imgh;
    	e1.style.height = maxheight + "px";
    	$('#largeImg').css("width", imgw * ratio);
    	imgw = imgw * ratio;
    }     

}
$(window).bind('orientationchange', function (e) { 
    setTimeout(function () {
        // Get height of div
        var div   = $('#zoomImage'),
            width = $(window).width();
			ratio = 1.4; // THIS IS A HACK - NEED TO GET ACTUAL RATIO
        // Set the height of the div
        div.css({ height: Math.ceil(width / ratio) });
    }, 500);
});

$("input").blur(function() {
    $("[data-role=footer]").show();
});

$("input").focus(function() {
    $("[data-role=footer]").css("bottom", "0");
});

</script>

<!--$(document).bind('swiperight', function (event) {
    history.back($('body'), { transition: "slide"});
    event.preventDefault();
});-->
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
