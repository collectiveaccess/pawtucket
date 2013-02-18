<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php print MetaTagManager::getHTML(); ?>
	
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery.mobile-1.2.0/jquery.mobile-1.2.0.css" type="text/css" media="screen" />	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/iphone.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts.css" rel="stylesheet" type="text/css" />	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/sets.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/videojs/video-js.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-jplayer/jplayer.blue.monday.css" type="text/css" media="screen" />





<?php 
	print JavascriptLoadManager::getLoadHTML($this->request->getBaseUrlPath());
?>
	<script>

	$(document).on("mobileinit", function(){
  		$.mobile.defaultPageTransition = 'slide';
  		$.mobile.ajaxEnabled = false;
  		$.mobile.touchOverflowEnabled = true;
	});
	</script>
	
    <script src="<?php print $this->request->getBaseUrlPath(); ?>/js/ca/markerclusterer.js" type="text/javascript"></script>
    <script src="<?php print $this->request->getBaseUrlPath(); ?>/js/ca/geolocationmarker.js" type="text/javascript"></script>
    <script src="<?php print $this->request->getBaseUrlPath(); ?>/js/ca/infobox.js" type="text/javascript"></script>
    <script src="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery.mobile-1.2.0/jquery.mobile-1.2.0.js" type="text/javascript"></script>
    
	<meta name="viewport" id="_msafari_viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<link rel="apple-touch-icon" href="images/myiphone_ico.png"/>
	<script type="text/javascript">
		window.addEventListener('load', setOrientation, false);  
		window.addEventListener('orientationchange', setOrientation, false);
		function setOrientation() {  
			var orient = Math.abs(window.orientation) === 90 ? 'landscape' : 'portrait';  
			var cl = document.body.className;  
			cl = cl.replace(/portrait|landscape/, orient);  
 			document.body.className = cl; 
 			window.scrollTo(0, pageYOffset);
		}
	</script>
</head>
<body onload="setTimeout(function() { window.scrollTo(0, 1) }, 100);" class="portrait">
		<div id="pageWidth" data-role="page">

			<div id="header" data-role="header" data-position="fixed" data-fullscreen="true">
<?php
				print "<h1>".caNavLink($this->request, _t('Parrish East End Stories'), "", "", "", "")."</h1>";
				#print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/".$this->request->config->get('header_img')."' border='0'>", "", "", "", "");
?>				
			</div><!-- end header -->
<?php
		if (($this->request->getController() == "About") && ($this->request->getAction() == "map"))	{	
?>			
			<div id="pageArea" data-role="content" style='margin-top:0px;'>
<?php
		} else {
?>
			<div id="pageArea" data-role="content">
<?php
		}
?>					
			<div id="navMenu">
<?php
				print "<div>".caNavLink($this->request, _t("Home"), "", "", "", "")."</div>";
				print "<div>".caNavLink($this->request, _t("Browse"), "", "", "Browse", "clearCriteria")."</div>";
				print "<div>".caNavLink($this->request, _t("Gallery"), "", "simpleGallery", "Show", "Index")."</div>";
				print "<div>".caNavLink($this->request, _t("About"), "", "", "About", "Index")."</div>";
				if (!$this->request->config->get('dont_allow_registration_and_login')) {
					if($this->request->isLoggedIn()){
						print "<div>".caNavLink($this->request, _t("My Collections"), "", "", "Sets", "Index")."</div>";
						print "<div>".caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout")."</div>";
					}else{
						print "<div>".caNavLink($this->request, _t("Login/Register"), "", "", "LoginReg", "form")."</div>";
					}
				}
				# Locale selection
				global $g_ui_locale;
				if (is_array($va_ui_locales = $this->request->config->getList('ui_locales')) && (sizeof($va_ui_locales) > 1)) {
					foreach($va_ui_locales as $vs_locale) {
						if($g_ui_locale != $vs_locale){
							$va_parts = explode('_', $vs_locale);
							$vs_lang_name = Zend_Locale::getTranslation(strtolower($va_parts[0]), 'language', strtolower($va_parts[0]));
							print "<div>".caNavLink($this->request, $vs_lang_name, "", "", $this->request->getController(), $this->request->getAction(), array("lang" => $vs_locale))."</div>";
						}
					}
				
				}
?>
				<div><a href="#" onclick='$("#navMenu").slideUp(250); return false;'><?php print _t("Close Menu"); ?></a></div>
			</div><!-- end navMenu -->


