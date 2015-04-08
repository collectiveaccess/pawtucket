<?php
/* ----------------------------------------------------------------------
 * /views/nysa/ca_occ_pdf_html.php 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
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
	$va_occ_info = $this->getVar("occ_info");
	$va_occ_info2 = $this->getVar("occ_info2");
	$va_related_objects_info = $this->getVar("related_objects_info");
	$vs_title = $this->getVar("title");
?>
<HTML>
	<HEAD>
		<style type="text/css">
			<!--
			body, p, div{font-size: 14px; font-weight:500; line-height: 20px; color: rgb(46, 34, 31); font-family: Helvetica, sans-serif;}
			.pageHeader { background-color: #FFFFFF; margin: 0px; padding: 0px; width: 100%; height: 133px;}
			.divide{ clear:both; border-top:1px solid #828282; }
			.unit { color: #000; margin: 0px 0px 15px 0px; }
			.headerText { color: #000; margin: 0px 0px 10px 20px; }
			H1 {margin-top:10px; font-family: 'Crimson Text',†serif; font-weight: normal; font-size: 24px; line-height: 30px; color: rgb(0, 103, 148);}
			H2 {margin-top:10px; font-family: 'Crimson Text',†serif; font-weight: normal; font-size: 20px; line-height: 20px; color: rgb(0, 103, 148);}
			#footer {
			  position: fixed;
			  bottom: -15px;
			  left: 0px;
			  right: 0px;
			  height: 15px;
			  text-align: left;
			  border-top: 2px solid gray;
			}
			.image{max-height:500px; page-break-before: always;}
			.pageBreak{page-break-before: always;}
			HR{ border:1px solid #DEDEDE;}
			-->
		</style>
	</HEAD>
	<BODY>
		<div id='footer'>
			<b>New York State Archives</b> - http://www.archives.nysed.gov
		</div>

<?php
		print "<H1>".$vs_title."</H1>";
		if(is_array($va_occ_info) && sizeof($va_occ_info)){
			foreach($va_occ_info as $vs_attribute => $vs_info){
				print "<div class='unit'>".$vs_info."</div>";
			}
		}
		if(is_array($va_related_objects_info) && sizeof($va_related_objects_info)){
			$count = 1;
			foreach($va_related_objects_info as $vn_relation_id => $va_object_info){
				$vs_document_number = "";
				if(sizeof($va_related_objects_info) > 1){
					$vs_document_number = "<div><strong>Document ".$count++."</strong></div>";
				}
				$i = 1;
				foreach($va_object_info["reps"] as $vs_media_tag){
					print "<div class='unit image'>";
					if (($i == 1) && $vs_document_number) {
						print $vs_document_number;
					}
					$i++;
					
					print $vs_media_tag."</div>";
				}
				if($va_object_info["object_caption_info"]){
					print "<div class='unit'>".$va_object_info["object_caption_info"]."</div>";
				}
				if(is_array($va_object_info["md"]) && sizeof($va_object_info["md"])){
					if(is_array($va_occ_info2) && sizeof($va_occ_info2)){
						$vs_break_class = "pageBreak";
						foreach($va_object_info["md"] as $vs_attribute => $vs_info){
							if($vs_break_class){
								print "<div class='".$vs_break_class."'></div>";
							}
							print $vs_info."<br/><br/>";
							$vs_break_class = "";
						}
					}
				}
			}
		}
		foreach($va_occ_info2 as $vs_attribute => $vs_info){
			print "<div class='unit'>".$vs_info."</div>";
		}
?>
        <script type="text/php">

        if ( isset($pdf) ) {
			$w = $pdf->get_width();
			$h = $pdf->get_height();
			
			$font = Font_Metrics::get_font("helvetica", "bold");
			$pdf->page_text($w - 100, $h - 28, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 10, array(0,0,0));
        }
        </script>
	</BODY>
</HTML>