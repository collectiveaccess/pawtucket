<?php
	$vn_next_id = $this->getVar("next_id");
	$vn_previous_id = $this->getVar("previous_id");
	$t_object = $this->getVar("object");
	$vs_vaga_class = "";
	if($t_object->get("object_status") == 349){
		$vs_vaga_class = "vagaDisclaimer";
	}
	
?>
	<div id="caMediaOverlayContent">
		<div class="caMediaOverlayControlsSlideshow">
			<div class='close'><a href="#" onclick="caMediaPanel.hidePanel(); return false;" title="close">&nbsp;&nbsp;&nbsp;</a></div>
<?php
			# title
			if($t_object->getLabelForDisplay()){
				print $t_object->getLabelForDisplay();
			}
			# date
			if($t_object->get("creation_date") && !(strstr($t_object->getLabelForDisplay(), $t_object->get("creation_date")))){
				print ", ".$t_object->get("creation_date");
			}
			# creator
			$va_creator = $t_object->get("ca_entities", array("restrictToRelationshipTypes" => array("maker", "artist"), "returnAsArray" => 1, 'checkAccess' => $va_access_values, 'sort' => 'surname'));
			if(sizeof($va_creator) > 0){	
				foreach($va_creator as $va_entity) {
					$vs_creator = $va_entity["label"];
				}
				print ", ".$vs_creator;
			}
			if($t_object->get("medium")){
				print ", ".$t_object->get("medium");
			}
			print "<br/>";
			if($t_object->get("caption")){
				print "<div class='objectInfoSlideshow'>";
				if($vs_vaga_class){
					print "<a href='http://www.vagarights.com' target='_blank'>";
				}
				print $t_object->get("caption");
				if($vs_vaga_class){
					print "</a>";
				}
				print "</div>";
			}
?>
		</div>
		<div class='caMediaOverlayMediaContainer'>
			<table border="0" align="center"><tr>
				<td valign="middle" align="center" class="nextPrevCell">
<?php
					if($vn_previous_id){
						print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'eastend', 'ObjectSlideshow', 'Index', array('object_id' => $vn_previous_id))."\"); return false;' ><img src='".$this->request->getThemeUrlPath()."/graphics/eastend/previousSlideshow.png' title='previous' alt='previous' /></a>";
					}
?>
				</td>
				<td valign="middle" align="center" class="mediaCell">
<?php
					if($t_object->get("object_status") == 348){
						# --- VAGA ARS do not show image
						print "<div id='imgPlaceholderDetail'>".caNavLink($this->request, "Image not available for view", "", "Detail", "Object", "Show", array("object_id" => $this->getVar("object_id")))."</div>";
					}else{
						print caNavLink($this->request, $this->getVar("image"), "", "Detail", "Object", "Show", array("object_id" => $this->getVar("object_id")));
					}
?>
				</td>
				<td valign="middle" align="center" class="nextPrevCell">
<?php
					if($vn_next_id){
						print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'eastend', 'ObjectSlideshow', 'Index', array('object_id' => $vn_next_id))."\"); return false;' ><img src='".$this->request->getThemeUrlPath()."/graphics/eastend/nextSlideshow.png' title='next' alt='next' /></a>";
					}
?>
				</td>
			</tr></table>
		</div><!-- end caMediaOverlayMediaContainer -->
	</div><!-- end caMediaOverlayContent -->