<?php
	global $wp, $wp_query, $wp_the_query, $wpdb;
	define('WP_USE_THEMES', false);
	require('./news/wp-config.php');
	require('./news/wp-blog-header.php');
	$wp->init(); $wp->parse_request(); $wp->query_posts();
	$wp->register_globals(); $wp->send_headers();

?>
<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Splash/splash_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 	$va_access_values = caGetUserAccessValues($this->request);
	$t_object = new ca_objects();
	
	$t_featured = new ca_sets();
	$t_featured->load(array('set_code' => $this->request->config->get('featured_set_name')));
	
	$set_items = $t_featured->getItems(array('thumbnailVersions' => Array("large", "tiny", "icon")));


#	$set_item_captions = array();
#	foreach ($set_items as $id => $set_item){
#		foreach ($set_item as $i_id => $item) {
#		$set_item_captions[] = $item['caption'];
#		}
#	}
	
#	$va_item_ids = $this->getVar('featured_content_slideshow_id_list');
#	$va_item_media = $t_object->getPrimaryMediaForIDs($va_item_ids, array("mediumlarge", "tiny"));
#	$va_item_labels = $t_object->getPreferredDisplayLabelsForIDs($va_item_ids);
	$t_featured = new ca_sets();
	$t_featured->load(array('set_code' => $this->request->config->get('spotlight_set_name')));
	 # Enforce access control on set
	if((sizeof($va_access_values) == 0) || (sizeof($va_access_values) && in_array($t_featured->get("access"), $va_access_values))){
		$va_set_id = $t_featured->get("set_id");
		$va_featured_ids = array_keys(is_array($va_tmp = $t_featured->getItemRowIDs(array('checkAccess' => array(1), 'shuffle' => 1))) ? $va_tmp : array());	// These are the object ids in the set
	}
	
 ?>
	<div id="splashBrowsePanel" class="browseSelectPanel" style="z-index:1000;">
		<a href="#" onclick="caUIBrowsePanel.hideBrowsePanel()" class="browseSelectPanelButton"></a>
		<div id="splashBrowsePanelContent">
		
		</div>
	</div>
	<script type="text/javascript">
		var caUIBrowsePanel = caUI.initBrowsePanel({ facetUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'getFacet'); ?>'});
	</script>
	
<div id="hpContainer">
	<ul id="navImgs"></ul>
	<div id="hpFeatured">
<?php
	foreach ($set_items as $vn_object_id => $set_item) {
		foreach ($set_item as $object_id => $item) {
			$item_id = $item['item_id'];
			$t_set_item = new ca_set_items($item_id);
	
			$vs_image_tag = $item['representation_tag_large'];
			$va_image_height = $item['representation_height_large'];
			#$va_caption = $item['caption'];
			$va_caption = $t_set_item->get("set_item_description");
			$vn_object_id = $item['row_id'];
			#$vn_height_padding = round((430-$va_image_height)/2);

			print "<div><img src='".$item['representation_url_large']."' rel='".$item['representation_url_icon']."' name='".$this->request->getBaseUrlPath()."/index.php/Detail/Object/Show/object_id/".$vn_object_id."'/>";
			print "<span class='featuredCaption'><p class='slideTitle'>".$va_caption."</p></span></div>"; 
		}	
	}	
?>
	</div>
		

</div>	<!-- end hpContainer-->
<div style='width:100%; height:20px; clear:both'></div>
<div id="spotLightItems">
	<div id="spotOne">
		<div id="spotOneTitle"><?php print caNavLink($this->request, 'Special Projects', '', 'simpleGallery', 'Show', 'Index'); ?></div>
			<div id="spotOneContent">
<?php
	$t_set = new ca_sets();
	$va_special_projects = caExtractValuesByUserLocale($t_set->getSets(array('table' => 'ca_objects', 'checkAccess' => $va_access_values, 'setType' => 'special_projects')));
 			
		$i = 0;
		foreach ($va_special_projects as $vn_set_id => $va_special_project) {
			print "<div class='section'>";
			#print "<div class='spotIcon'>".caNavLink($this->request, '<img src="'.$this->request->getThemeUrlPath().'/graphics/spotIcon.jpg" border="0">', '', 'simpleGallery', 'Show', 'displaySet', array('set_id_id' => $vn_set_id))."</div>";
			print "<H1>".caNavLink($this->request, $va_special_project["name"], '', 'simpleGallery', 'Show', 'displaySet', array('set_id' => $vn_set_id))."</H1>";
			#print "<div style='width:100%; height:1px; clear:both;'></div>";
			print "</div>";
			$i++;
			if($i == 3){
				break;
			}
		}
		print "<div class='spotOneLink'>".caNavLink($this->request, 'More >', '', 'simpleGallery', 'Show', 'Index')."</div>";
if($xxx){
  $args=array(
  'orderby' =>'parent',
  'order' =>'asc',
  'post_type' =>'page',
  'category_name' => 'front-page',

   );
   $page_query = new WP_Query($args); ?>
 
<?php while ($page_query->have_posts()) : $page_query->the_post(); ?>
   <div class="section">
    <h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
    <?php echo get_the_post_thumbnail($id, array(265,200)); ?>
    <?php the_excerpt(); ?>
        <div class='spotOneLink'><a href="<?php the_permalink();?>">See More ></a></div>
    </div>
<?php 
	endwhile; 
}
?>
		</div>
	</div>
	<div id="spotTwo">
		<div id="spotTwoTitle">Latest News</div>
		<div id="spotTwoContent">
		
	<?php rewind_posts(); ?>
<?php
	# Last Wordpress Post
	$posts = query_posts('numberposts=1');
	while (have_posts()) : the_post(); 
		foreach($posts as $post) : setup_postdata($post);
?>
			<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php the_excerpt(); ?>
<?php		
		endforeach; 
	endwhile;
	print "<div id='spotTwoLink'><a href='http://archive.appalshop.org/news'>See More ></a></div>";
?>	
		
		</div>		
	</div>
	<div id="spotThree">
		<div id="spotThreeTitle">Spotlight</div>
<?php
		foreach ($va_featured_ids as $featured_id) {
			$t_object = new ca_objects($featured_id);
			print "<div class='spotWrapper'>";
			print "<div class='item'>".caNavLink($this->request, $t_object->get('ca_objects.preferred_labels.name'), '', 'Detail', 'Object', 'Show', array('object_id' => $featured_id))."</div>";
			print "<div class='spotIcon'>".caNavLink($this->request, '<img src="'.$this->request->getThemeUrlPath().'/graphics/spotIcon.jpg" border="0">', '', 'Detail', 'Object', 'Show', array('object_id' => $featured_id))."</div>";
			print "<div style='width:100%; height:1px; clear:both;'></div>";
			print "</div>";
		}
?>		
	</div>

</div>



<script type="text/javascript">
$(document).ready(function() {
$('#hpFeatured').cycle({ 
    fx:     'fade', 
    speed:  '1000', 
    timeout: '4000', 
    pager:  '#navImgs',
    pause:	true,     // true to enable "pause on hover" 
    pauseOnPagerHover: true,
     
    pagerAnchorBuilder: function(i, slide) { 
        return '<a href="#"><img src="'
        + jQuery(slide).children("img").attr('rel')
        + '" /></a>'; 
    } 
});
    jQuery('#hpFeatured img').click(function (){
      document.location.href = jQuery(this).attr('name');
    }).css('cursor', 'pointer');     
}); 
 

</script>
