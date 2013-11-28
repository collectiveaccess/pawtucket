<?php
	$va_participate_images = $this->getVar("participate_images");
	
	if(is_array($va_participate_images) && (sizeof($va_participate_images) > 0)){
		print "<div id='participateRightCol'><div id='participateSlideShow'>";
		foreach($va_participate_images as $vn_image_id => $va_info){
			print "<div><div class='participateImg".(($va_participate_images["vaga_class"] ? " ".$va_participate_images["vaga_class"] : ""))."'>".$va_info["image"]."</div>";

			if($va_info["caption"]){
				print "<span class='captionSmall obl'>";
				if($va_info["vaga_class"]){
					print "<a href='http://www.vagarights.com' target='_blank'>";
					$vn_vaga_disclaimer_output = 1;
				}
				print $va_info["title"];
				if($va_info["title"] && $va_info["caption"]){
					print "<br/>";
				}
				print $va_info["caption"];
				if($va_info["vaga_class"]){
					print "</a>";
				}
				print "</span>";
			}
			print "</div>";
		}
		print "</div><!-- participateSlideShow --></div><!-- end participateSlideShow -->";
	}
	
	if($vn_vaga_disclaimer_output){
		TooltipManager::add(
			".vagaDisclaimer", "<div style='width:250px;'>Reproduction of this image, including downloading, is prohibited without written authorization from VAGA, 350 Fifth Avenue, Suite 2820, New York, NY 10118. Tel: 212-736-6666; Fax: 212-736-6767; e-mail:info@vagarights.com; web: <a href='www.vagarights.com' target='_blank'>www.vagarights.com</a></div>"
		);
	}
?>

<h1>Participate</h1>
<div class="textContent">
	<div>
		East End Stories illustrates the dynamic history of artists who have lived and worked on the East End of Long Island since the 1820s. The site includes biographical information, art historical narratives, photographs, maps, and much more.
	</div>
	<div>
		YOU can become a part of the story! The Parrish is currently soliciting contributions of oral histories, photographs, audio, video, home movies, and print ephemera related to artists who have lived or worked in the region.
	</div>
	<div>
<?php
	print "<b>".caNavLink($this->request, _t("Click here to see user contributed content"), "", "", "Search", "Index", array("search" => "ca_objects.source_id:".$this->getVar("user_contributed_source_id")." or ca_objects.source_id:".$this->getVar("user_contributed_other_source_id")))."</b>";
?>
	</div>
	<div>
		DATABASE ARTISTS: Submit photographs or a video tour of your East End studio. Submit biographical information, links, memories, and information about yourself or other artists.
	</div>
	<div>
		ARTISTS' ESTATES and FAMILIES: Submit photographs, home movies, and biographical information.
	</div>
	<div>
		COMMUNITY MEMBERS: Submit memories and anecdotes about artists in your community.
	</div>
	<div>
<?php
	if($this->request->isLoggedIn()){
		print "<b>".caNavLink($this->request, _t("Click here to submit your story."), "", "Contribute", "Form", "Edit")."</b>";
	}else{
		print "<b>".caNavLink($this->request, _t("Please login/register to share your story and upload media."), "", "", "LoginReg", "form")."</b>";
	}
?>
	</div>
	<div>
		East End Stories reflects the Parrish Art Museum’s ongoing research on the art and artists of the East End of Long Island.  Site content is updated regularly.  Artists may submit a resume to be considered for inclusion.  Contact us at 631-283-2118 ext. 121, or email <a href="mailto:wingfieldc@parrishart.org">wingfieldc@parrishart.org</a> for more information.
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#participateSlideShow').cycle({
		fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
		speed:  500,
		timeout: 3000
	});
});
</script>