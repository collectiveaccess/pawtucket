<?php
	$t_rep = 					$this->getVar('t_primary_rep');
	$vn_representation_id = 	$t_rep->getPrimaryKey();
	
				if ($this->request->isLoggedIn() && $this->request->user->canDoAction('can_replicate_media') && $t_rep && $t_rep->getPrimaryKey()) {
					if(is_array($va_available_targets = $t_rep->getAvailableMediaReplicationTargets('media', 'original')) && sizeof($va_available_targets)) {
						$vs_target_list = $t_rep->getAvailableMediaReplicationTargetsAsHTMLFormElement('target', 'media', 'original');
						print "<div class='caRepresentationMediaReplicationTargetList' id='caRepresentationMediaReplicationControls{$vn_representation_id}'>\n";
						print _t('Send media to %1', $vs_target_list);
						print "<a href='#' onclick='jQuery(\"#caRepresentationMediaReplicationLoadIcon{$vn_representation_id}\").css(\"display\", \"inline\"); jQuery(\"#caRepresentationMediaReplicationControls{$vn_representation_id}\").load(\"".caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'StartMediaReplication')."/representation_id/{$vn_representation_id}/target/\" + jQuery(\"#caRepresentationMediaReplicationControls{$vn_representation_id} select[name=target]\").val()); return false;'>"._t('Start &rsaquo;')."</a>";
	
						print "<div id='caRepresentationMediaReplicationLoadIcon{$vn_representation_id}' class='caRepresentationMediaReplicationLoadIcon'>".caBusyIndicatorIcon($this->request)."</div>";
						print "</div>\n";
					}
				}
				
				if (is_array($va_replications = $t_rep->getUsedMediaReplicationTargets('media', 'original')) && sizeof($va_replications)) {
					print "<table class='caRepresentationMediaReplicationStatusTable'>\n<tr><th>"._t('Copied to')."</th><th>"._t('Status')."</th></tr>\n";
					foreach($va_replications as $vs_target => $va_target_info) {
						$va_status = $t_rep->getReplicationStatus('media', $vs_target);
						print "<tr><td>".(($vs_url = $t_rep->getReplicatedMediaUrl('media', $vs_target)) ? "<a href='{$vs_url}' target='_ext'>{$va_target_info['name']}</a>" : $va_target_info['name'])." (<em>{$va_target_info['type']}</em>)</td><td>{$va_status['status']}</td></tr>\n";

					}
					print "</table>\n";
				}
?>