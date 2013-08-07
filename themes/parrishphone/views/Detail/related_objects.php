<?php
	$qr_hits = $this->getVar('browse_results');
	$vn_itemc = 0;
	if($qr_hits->numHits() > 0){

			print "<div class='listItems' data-role='collapsible' data-inset='false'>";
		if (!$this->request->isAjax()) {	
			print "<h2>"._t("Related Objects")."</h2><!-- end collapseListHeading -->";
		}

			while(($vn_itemc < 4) && ($qr_hits->nextHit())) {
				$vn_object_id = $qr_hits->get('object_id');
				$va_labels = $qr_hits->getDisplayLabels();
				$vs_caption = "";
				foreach($va_labels as $vs_label){
					$vs_caption .= $vs_label;
				}
				# --- get the height of the image so can calculate padding needed to center vertically
				print "<div class='item'><div class='thumb'>".caNavLink($this->request, $qr_hits->getMediaTag('ca_object_representations.media', 'icon'), '', 'Detail', 'Object', 'Show', array('object_id' => $qr_hits->get('ca_objects.object_id')))."</div>";
				
				// Get thumbnail caption
				$this->setVar('object_id', $vn_object_id);
				$this->setVar('caption_title', $vs_caption);
				$this->setVar('caption_idno', $qr_hits->get('idno'));
				
				print $this->render('../Results/ca_objects_result_caption_html.php');
				print "<div style='clear:left;'><!--empty --></div></div><!-- end item -->\n";
				$vn_itemc++;
			}
			print $this->render('paging_controls_html.php');
		print "</div>";
	}
?>