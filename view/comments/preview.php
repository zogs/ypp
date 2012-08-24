<?php 

if($type!='404'):

	$html ='<div class="previewMedia">';
		$html .='<div class="previewMedia-img">';
		if(count($thumbnails)>1):
			$html .='<div class="previewMedia-totalImage">
						Choose a thumbnail<br />
						<a href="" id="prev_thumb">Previous</a> Total '.count($thumbnails).' images <a href="" id="next_thumb">Next</a>
					</div>';
		endif;

		foreach ($thumbnails as $key) {
			if($key == $thumbnails_first)
			$html .='<img src="'.$key.'" alt="" class="previewMedia-thumbnail" data-type="'.$type.'" data-comid="01" data-url="'.$contentURL.'">';
			else
			$html .='<img src="'.$key.'" alt="" class="previewMedia-thumbnail hide" data-type="'.$type.'" data-comid="01" data-url="'.$contentURL.'">';
		}
		$html .='</div>';

		$html .='<div class="previewMedia-info">';
			$html .='<div class="previewMedia-close"><strong>x</strong></div>';
			$html .='<div class="previewMedia-title"><a href="'.$url.'" target="_blank" title="'.$title.'">'.$title.'</a></div>';
			$html .='<div class="previewMedia-desc">'.$description.'</div>';
		$html .='</div>';
	$html .='</div>';
else :
	$html = '';
endif;


$html = utf8_decode($html); // json_encode ne fonctionne qu'avec des donnees utf8
$html = htmlentities($html); // json_encode ne fonctionne pas avec les balises html

$array = array(
	"type" => $type,
	"content" => $html 
	);



echo json_encode($array);



?>