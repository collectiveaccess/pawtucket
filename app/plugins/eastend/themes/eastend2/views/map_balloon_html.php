<?php
	$vo_result = $this->getVar('result');
	
	while($vo_result->nextHit()) {
		print $vo_result->get("ca_objects", array("template" => "<div style='width:325px;'><div style='float: left; margin-right: 5px; width: 84px;'><l>^ca_object_representations.media.icon</l></div><div style='float: right; width: 236px;'><l>^ca_objects.preferred_labels.name</l><br/><unit relativeTo='ca_entities' delimiter=', '><ifdef code='ca_entities.preferred_labels.name'><em><l>^ca_entities.preferred_labels.name</l></em></ifdef></unit><br/><unit relativeTo='ca_places'><ifdef code='ca_places.preferred_labels.name'>Location: <span style='text-style: italic; size: 9px'>^ca_places.preferred_labels.name</span><br/></ifdef></unit>^ca_objects.description</div></div><br style='clear: both'/><br/>"));
	}
?>