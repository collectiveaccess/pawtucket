<?php
	AssetLoadManager::register('superfish');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php print MetaTagManager::getHTML(); ?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/sets.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/bookmarks.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/videojs/video-js.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-autocomplete/jquery.autocomplete.css" type="text/css" media="screen" />
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
		 jQuery(document).ready(function() {
			jQuery('#quickSearch').searchlight('<?php print $this->request->getBaseUrlPath(); ?>/index.php/Search/lookup', {showIcons: false, searchDelay: 100, minimumCharacters: 3, limitPerCategory: 3});
		});
		// initialize CA Utils
			var caUIUtils = caUI.initUtils();
	</script>
	


</head>
<body class='pawtucket'>
<?php include_once("analytics.php") ?>

		<div id="topBar">
		<a href='http://www.appalshop.org'>Back to Appalshop website ></a>
		</div><!-- end topbar -->
		<div id="pageArea">
			<div id="header">
<?php
				print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/spacer.gif' width='300' height='80' border='0'>", "", "", "", "");
		print "<div id='headerLinks'>";	
?>
			<ul class='sf-menu'>
				<li><a href='#'>About</a>
					<ul>
						<li><a href='http://archive.appalshop.org/news/?page_id=7'>History of Appalshop</a></li>
						<li><a href='http://archive.appalshop.org/news/?page_id=75'>The Archive</a></li>
						<li><a href='http://archive.appalshop.org/news/?page_id=9'>Supporters</a></li>
						<li><a href='http://archive.appalshop.org/news/?page_id=11'>Staff</a></li>
					</ul>
				</li>
				<li><a href='http://archive.appalshop.org/news'>News</a></li>
				<li><a href='http://archive.appalshop.org/news/?page_id=13'>Services</a></li>
				<li><a href='http://archive.appalshop.org/news/?page_id=15'>Support</a></li>
				<li><a href='http://archive.appalshop.org/news/?page_id=17'>Contact</a></li>
			</ul>
			<a href='https://www.facebook.com/Appalshop?fref=ts'><img src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/f_logo.jpg' border='0' style='margin:0px 5px 0px 8px;'></a>
			<a href='https://twitter.com/AppalArchive'><img src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/imgres.jpg' border='0'></a>
		</div>

			</div><!-- end header -->
<?php
	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
			<div id="nav">
<?php				
				print caNavLink($this->request, _t("Browse The Database"), '', '', 'Browse', 'clearCriteria')." ";
				print caNavLink($this->request, _t("Collections"), '', 'FindingAids', 'List', 'Index')." ";
				print caNavLink($this->request, _t("Special Projects"), '', 'simpleGallery', 'Show', 'Index');
?>			
				<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" style="position: absolute; z-index:1500; margin: 2px 0px 0px 140px;" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src='<?php print $this->request->getThemeUrlPath(); ?>/graphics/searchglass.gif' width='13' height='13' border='0'></a>
						<input type="text" name="search" id="quickSearch"  autocomplete="off" size="100"  placeholder="Search"/>
				</form></div>
				<a href='https://npo1.networkforgood.org/Donate/Donate.aspx?npoSubscriptionId=10058' target='_blank' class='donateLink'>Donate</a>
			</div><!-- end nav -->
			<div id='contentArea'>
<script>

	$(document).ready(function(){
		$("ul.sf-menu").superfish();
	});

</script>