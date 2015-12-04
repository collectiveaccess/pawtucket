<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php print MetaTagManager::getHTML(); ?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	<link href='http://fonts.googleapis.com/css?family=Muli:300,400,300italic,400italic|Raleway:400,700,600,500,800,900,300,200,100|Crimson+Text:400,700italic,700,600italic,600,400italic' rel='stylesheet' type='text/css' />
	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/sets.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/bookmarks.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/videojs/video-js.css" type="text/css" media="screen" />
 	<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="<?php print $this->request->getThemeUrlPath(true); ?>/css/iestyles.css" />
	<![endif]-->

	<!--[if (!IE)|(gte IE 8)]><!-->
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/viewer-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain.css" media="screen" rel="stylesheet" type="text/css" />
	<!--<![endif]-->
	<!--[if lte IE 7]>
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/viewer.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/plain.css" media="screen" rel="stylesheet" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-tileviewer/jquery.tileviewer.css" type="text/css" media="screen" />
<?php
	print AssetLoadManager::getLoadHTML($this->request);
?>
	<script type="text/javascript">
		 //jQuery(document).ready(function() {
		//	jQuery('#quickSearch').searchlight('<?php print $this->request->getBaseUrlPath(); ?>/index.php/Search/lookup', {showIcons: false, searchDelay: 100, minimumCharacters: 3, limitPerCategory: 3});
		//});
		// initialize CA Utils
			var caUIUtils = caUI.initUtils();
	</script>
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-507388-26', 'auto');
  ga('send', 'pageview');

</script>

</head>
<body>
		<div id="topBar">
<?php
			$vb_client_services = (bool)$this->request->config->get('enable_client_services');
			if (!$this->request->config->get('dont_allow_registration_and_login')) {
				if($this->request->isLoggedIn()){
					$o_client_services_config = caGetClientServicesConfiguration();
					if ($vb_client_services && (bool)$o_client_services_config->get('enable_user_communication')) {
						//
						// Unread client communications
						//
						$t_comm = new ca_commerce_communications();
						$va_unread_messages = $t_comm->getMessages(array('unreadOnly' => true, 'user_id' => $this->request->getUserID()));
						
						$va_message_set_ids = array();
						foreach($va_unread_messages as $vn_transaction_id => $va_messages) {
							$va_message_set_ids[] = $va_messages[0]['set_id'];
						}
						
					}
					
					if(!$this->request->config->get('disable_my_collections')){
						# --- get all sets for user
						$t_set = new ca_sets();
						$va_sets = caExtractValuesByUserLocale($t_set->getSets(array('table' => 'ca_objects', 'user_id' => $this->request->getUserID())));
						if(is_array($va_sets) && (sizeof($va_sets) > 1)){
							print "<div id='lightboxLink'>
										<a href='#' onclick='$(\"#lightboxList\").toggle(0, function(){
																								if($(\"#lightboxLink\").hasClass(\"lightboxLinkActive\")) {
																									$(\"#lightboxLink\").removeClass(\"lightboxLinkActive\");
																								} else {
																									$(\"#lightboxLink\").addClass(\"lightboxLinkActive\");
																								}
																								});')>Lightbox</a>";
							if(is_array($va_message_set_ids) && sizeof($va_message_set_ids)){
								print " <img src='".$this->request->getThemeUrlPath()."/graphics/icons/envelope.gif' border='0'>";
							}
							print "<div id='lightboxList'><b>"._t("your lightboxes").":</b><br/>";
							foreach($va_sets as $va_set){
								print caNavLink($this->request, ((strlen($va_set["name"]) > 30) ? substr($va_set["name"], 0, 30)."..." : $va_set["name"]), "", "", "Sets", "Index", array("set_id" => $va_set["set_id"]));
								if($vb_client_services && is_array($va_message_set_ids) && in_array($va_set["set_id"], $va_message_set_ids)){
									print " <img src='".$this->request->getThemeUrlPath()."/graphics/icons/envelope.gif' border='0'>";
								}
								print "<br/>";
							}
							print "</div>";
							print "</div>";
						}else{
							print caNavLink($this->request, _t("Lightbox"), "", "", "Sets", "Index");
							if($vb_client_services && is_array($va_message_set_ids) && sizeof($va_message_set_ids)){
								print " <img src='".$this->request->getThemeUrlPath()."/graphics/icons/envelope.gif' border='0'>";
							}
						}
					}
					
					if ($vb_client_services && (bool)$o_client_services_config->get('enable_my_account')) {
						$t_order = new ca_commerce_orders();
						if ($vn_num_open_orders = sizeof($va_orders = $t_order->getOrders(array('user_id' => $this->request->getUserID(), 'order_status' => array('OPEN', 'SUBMITTED', 'IN_PROCESSING', 'REOPENED'))))) {
							print "<span style='color: #cc0000; font-weight: bold;'>".caNavLink($this->request, _t("My Account (%1)", $vn_num_open_orders), "", "", "Account", "Index")."</span>";
						} else {
							print caNavLink($this->request, _t("My Account"), "", "", "Account", "Index");
						}
							
					}				
					
					if($this->request->config->get('enable_bookmarks')){
						print caNavLink($this->request, _t("My Bookmarks"), "", "", "Bookmarks", "Index");
					}
					print caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout");
				}else{
					print caNavLink($this->request, _t("Login/Register"), "", "", "LoginReg", "form");
				}
			}
			
			# Locale selection
			global $g_ui_locale;
			$vs_base_url = $this->request->getRequestUrl();
			$vs_base_url = ((substr($vs_base_url, 0, 1) == '/') ? $vs_base_url : '/'.$vs_base_url);
			$vs_base_url = str_replace("/lang/[A-Za-z_]+", "", $vs_base_url);
			
			if (is_array($va_ui_locales = $this->request->config->getList('ui_locales')) && (sizeof($va_ui_locales) > 1)) {
				print caFormTag($this->request, $this->request->getAction(), 'caLocaleSelectorForm', null, 'get', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true));
			
				$va_locale_options = array();
				foreach($va_ui_locales as $vs_locale) {
					$va_parts = explode('_', $vs_locale);
					$vs_lang_name = Zend_Locale::getTranslation(strtolower($va_parts[0]), 'language', strtolower($va_parts[0]));
					$va_locale_options[$vs_lang_name] = $vs_locale;
				}
				print caHTMLSelect('lang', $va_locale_options, array('id' => 'caLocaleSelectorSelect', 'onchange' => 'window.location = \''.caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), array('lang' => '')).'\' + jQuery(\'#caLocaleSelectorSelect\').val();'), array('value' => $g_ui_locale, 'dontConvertAttributeQuotesToEntities' => true));
				print "</form>\n";
			
			}
?>
		</div><!-- end topbar -->
		<div id="pageArea">
			<div id="header">
			
			<div class="logo">
			<a href="http://www.archives.nysed.gov"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/NYSA_Logo.jpg" alt="New York State Archives" border="0" /></a>		
			</div>			
				<div id="logotext"><a href="http://www.archives.nysed.gov"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/NYSA_HeaderType.png" alt="New York State Archives" /></a></div>	
				
			<div class="right-box">
			<div class="social">
			<ul>
				<li>
					<a href="https://www.facebook.com/nysarchives"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/Facebook_icon.png" width="27px" height="27px" alt="" /></a>
				</li>
				<li>
					<a href="https://twitter.com/nysarchives"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/twitter_icon.png" width="27px" height="27px" alt="" /></a>
				</li>
				<li>
					<a href="https://www.youtube.com/user/nysarchives"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/YouTubeIcon.png" width="27px" height="27px" alt="" /></a>
				</li>
			</ul>
			</div>
			<div class="search-box">
				<form method="get" action="http://srv52.nysed.gov/search">
					<input type="text" id="search" name ="q" value="" />
					<input class="submit" type="submit" name="btnG" value=""/> 
					<input type="hidden" name="site" value="Drupal_CA_XTF"/>
					<input type="hidden" name="client" value="drupal_ca_xtf"/>
					<input type="hidden" name="proxystylesheet" value="drupal_ca_xtf"/>
					<input type="hidden" name="output" value="xml_no_dtd"/>
				</form>
			</div>
		</div>
			
			
			</div><!-- end header -->
<?php
	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
			<div id="nav">
				<ul>
				<li class="list-item-one"><a href="http://digitalcollections.archives.nysed.gov/">DIGITAL COLLECTIONS</a></li>
				<li><a href="http://www.archives.nysed.gov/education/index.shtml">EDUCATION</a></li>
				<li><a href="http://www.archives.nysed.gov/grants">GRANTS &amp; AWARDS</a></li>
				<li><a href="http://www.archives.nysed.gov/records/index.shtml">MANAGING RECORDS</a></li>
				<li><a href="http://www.archives.nysed.gov/research/index.shtml">RESEARCH</a></li>
				<li><a href="http://www.archives.nysed.gov/workshops">WORKSHOPS</a></li>
			</ul>
			</div><!-- end nav -->
<?php
	$vs_header = "archive";
	if($this->request->getController() == "Browse"){
		switch($this->request->session->getVar('pawtucket2_browse_target')){
			case "ca_occurrences":
				$vs_header = "edu";
			break;
			# ------------
			case "ca_objects";
				$vs_header = "archive";
			break;
			# ------------
		}
	}
	if($this->request->getController() == "Search"){
		switch($this->request->session->getVar('pawtucket2_search_target')){
			case "ca_occurrences":
				$vs_header = "edu";
			break;
			# ------------
			case "ca_objects";
				$vs_header = "archive";
			break;
			# ------------
		}
	}
	if(($this->request->getController() == "Occurrence") || ($this->request->getController() == "Download")){
		$vs_header = "edu";
	}
	
	if($this->request->getController() == "Object"){
		$vs_header = "archive";
	}
	
	if(($this->request->getController() == "About") && ($this->request->getAction() == "Education")){
		$vs_header = "edu";
	}
	
	if($vs_header == "edu"){
?>
			<div id="TopicBar" class="edu">
			<h1>Education</h1>
				<div id="objectSearch">
				<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"></a>
						<input type="text" name="search" value="SEARCH LESSONS" onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off" size="100"/>
						<input class="submit" type="submit" name="s" value="" />
						<input type="hidden" name="target" value="ca_occurrences" />
				</form></div>
				</div>
				

			</div><!-- end colored "Ditigal Collections" bar -->
			<div id="menuBar"> 
			<?php				
				#print caNavLink($this->request, _t("Advanced Search"), "", "", "AdvancedSearch", "Index", array("target" => "ca_occurrences"));
				print " ".caNavLink($this->request, _t("About"), "", "", "About", "Education");
				print caNavLink($this->request, _t("Browse Documents/Lessons"), "", "", "Browse", "Index", array("target" => "ca_occurrences"));
				print " ".caNavLink($this->request, _t("Copyright"), "", "", "About", "Copyright");
?>
			</div>
<?php
	}else{
?>
			<div id="TopicBar" class="archive">
			<h1>Digital Collections</h1>
				<div id="objectSearch">
				<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"></a>
						<input type="text" name="search" value="SEARCH DIGITAL COLLECTIONS" onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off" size="100"/>
						<input class="submit" type="submit" name="s" value="" />
						<input type="hidden" name="target" value="ca_objects" />
				</form></div>
				</div>
				

			</div><!-- end colored "Ditigal Collections" bar -->
			<div id="menuBar">
			<?php				
				print " ".caNavLink($this->request, _t("Browse the Collection"), "", "", "Browse", "Index", array("target" => "ca_objects"));
				#print " ".caNavLink($this->request, _t("Advanced Search"), "", "", "AdvancedSearch", "Index", array("target" => "ca_objects"));
				#print " ".caNavLink($this->request, _t("About"), "", "", "About", "DigitalCollection");
				print " ".caNavLink($this->request, _t("Copyright"), "", "", "About", "Copyright");
?>
				<a href="/index.php/Browse/Index/target/ca_occurrences">LEARNING ACTIVITIES</a>
			</div>
<?php
	
	}
?>