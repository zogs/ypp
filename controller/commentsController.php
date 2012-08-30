<?php 
/**
 * 

	Controller des commentaires

 */
 class CommentsController extends Controller
 {
 	public $layout = 'none';
 	public $table = 'manif_comment';

 	public function view($manif_id , $comment_id = null){


 		$this->loadModel('Comments');
 		
 		$perPage = 10;
		$params = array(	
						
			"pays"       =>$this->session->getPays(),
			"lang"       =>$this->session->getLang(),
			"comment_id" =>$comment_id,
			"manif_id"   =>$manif_id,
			'limit'      =>$perPage
			);

		$array_session = ($this->session->user())? get_object_vars($this->session->user('obj')) : get_object_vars($this->session->noUserLogged() );
		$array_get     = get_object_vars($this->request->get);
		$params        = array_merge($array_session,$array_get,$params);			

		$d['coms']     = $this->Comments->findComments($params);
		$d['total']    = $this->Comments->totalComments($manif_id);

		$d['count']    = count($d['coms']);	
		$d['manif_id'] = $manif_id;		
		$d['remain']   = $d['total'] - ($perPage*$this->request->get->page);
		$d['nbpage']   = ceil($d['total']/$perPage);

		$this->set($d);
			

 	}

 	public function tcheck($manif_id,$second){

 		$this->loadModel('Comments');
 		$this->layout = 'none';

 		$params = array(
				"conditions" => array("manif_id"=>$manif_id,"reply_to"=>0),
				"date_field" => 'date',
				"second"     => $second
 			);

 		$d['count'] = $this->Comments->countNew($params);

 		$this->set($d);



 	}

 	public function add(){

		$this->loadModel('Comments');
 		$this->layout = 'none';



 		if($this->session->isLogged()){

 			//New Objet
 			$newComment = new stdClass();
 			//Values
 			$newComment->user_id = $this->session->user('user_id');
 			$newComment->lang = $this->session->getLang();
 			$newComment->manif_id = $this->request->data->manif_id;
 			$newComment->type = $this->request->data->type;
 			//Content treatment
 			//If there is a media content
 			if(isset($this->request->data->media) && $this->request->data->media !=''){

 				$content = str_replace($this->request->data->media_url, '', $content);
 				$content = $this->request->data->content.html_entity_decode($this->request->data->media);
 				
 			}
 			else
 				$content = $this->request->data->content;
 			//Replace
 			$content = str_replace(array("\\n","\\r"),array("<br />",""),$content);
 			//If NEWS , ifthere is a title then this is a News
 			if(isset($this->request->data->head) && $this->request->data->head!=''){
 				$newComment->head = $this->request->data->head;
 				$newComment->type = 'news';
 			} 			
 			$newComment->content = $content;
 			$newComment->media = '';
 			//Saving
 			$this->Comments->save($newComment);
 			$id = $this->Comments->id;
 			$d['id'] = $id;
 		}
 		else {

 			$d['fail'] = "Please Login";
 		} 		 		

 		$this->set($d);
 	}


 	public function reply(){

 		$this->loadModel('Comments');
 		$this->view = 'comments/view';
 		$this->layout = 'none';

 		if( $this->request->post('reply_to') ){

 			if($this->session->isLogged()){

 			//On incremente le nombre de réponse du commentaire
 			$this->Comments->increment(array('field'=>'replies','id'=>$this->request->data->reply_to));

 			//On rajoute les params nécessaires
 			$this->request->data->user_id = $this->session->user('user_id');
 			$this->request->data->lang = $this->session->getLang();

 			//On sauvegarde la reponse dans la base
 			$this->Comments->save($this->request->data);

 			//Récupération de l'id du commentaire
 			$comment_id = $this->request->data->reply_to;

 			//Enfin , on renvoi la vue du commentaire
 			$this->view($this->request->data->manif_id,$comment_id);

	 		}
	 		else {

	 			$d['fail'] = 'You should log in first..';
	 			$this->set($d);
	 		}
 		}
 		else {

 			$d['fail'] = '[Error] Post Data missing [Action] Post a reply ';
 			$this->set($d);
 		}
 	

 	}

 	public function vote($id){

 		$this->loadModel('Comments');
 		$this->layout = 'none'; 		

 		if(is_numeric($id)){

	 		if($this->session->isLogged()){

	 			$user_id = $this->session->read('user')->user_id;

	 			if(!$this->Comments->alreadyVoted($id,$user_id)){

		 			$note = $this->Comments->increment(array(
							"table" =>"manif_comment",
							"field" =>"note",
							"id"    =>$id
		 				));
		 			$this->Comments->haveVoted(array(
							"user_id"    =>$user_id,
							"comment_id" =>$id
		 				));

		 			if(is_numeric($note->note)) $d['note'] = $note->note;
		 			else $d['erreur'] = 'Note not exist for this comment';

		 			
	 			}
	 			else $d['erreur'] = "You have already voted for this comment";
	 		}

 		}
 		else $d['erreur'] = 'Error id is not numeric';


 		$this->set($d);
 	}


 	public function preview(){

 		$this->layout = 'none';
 		$vars = array();


		if($this->request->get() ){

			if($this->request->get('url')) {


				$url = trim($this->request->get('url'));								
				$pattern = '/^http\:\/\/[a-zA-Z0-9\-\.\_]+\.[a-zA-Z]{2,4}(\/\S*)?$/';				
				if(!preg_match($pattern,$url)) exit('not url');

				$url_domain = getUrlDomain($url);
				$image_extension = array('jpg','jpeg','gif','png');
				$extension = pathinfo($url, PATHINFO_EXTENSION);
				$default_thumbnail = 'http://localhost/ypp/img/sign.png';

				if(in_array($extension,$image_extension)){

					$type             = 'img';
					$title            = $url;
					$description      = 'Image from '.getUrlDomain($url,true);
					$thumbnails       = array();
					$thumbnails[]     = $url;
					$thumbnails_first = $url;
					$contentURL       = $url;			

				
				}
				elseif($url_domain =='youtube') {

							$video_id                      = getYTid($url);
							
							require_once 'Zend/loader.php';
							
							Zend_Loader::loadClass('Zend_Gdata_Youtube');
							
							$yt                            = new Zend_Gdata_Youtube();
							$yt->setMajorProtocolVersion(2);
							$videoEntry                    = $yt->getVideoEntry($video_id);
							$title                         = $videoEntry->getVideoTitle();
							$description                   = $videoEntry->getVideoDescription();					
							$ytthumbnails                  = $videoEntry->getVideoThumbnails();
							$contentURL                    = $videoEntry->getFlashPlayerUrl();
							$t                             = array();
							foreach ($ytthumbnails as $key => $value) {
							
							
							$thumbnails[]                  = $value['url'];
							}			
							$thumbnails_first              = $thumbnails[0];
							$embed_code                    = make_valid_youtube_embed_code($video_id);
							$type                          = 'video';
				}
				elseif( $url_domain == 'dailymotion') {

							$video_id = getDMid($url);
							
							$html = file_get_contents_curl($url);

							if($html){

								$doc                           = new DOMDocument();
								@$doc->loadHTML($html);
								
								$metas                         = $doc->getElementsByTagName('meta');
								for( $i                        = 0; $i < $metas->length; $i++){
								$meta                          = $metas->item($i);
								if($meta->getAttribute('name') =='title')
								$title                         = $meta->getAttribute('content');
								if($meta->getAttribute('name') =='description')
								$description                   = $meta->getAttribute('content');
								}
								
								$thumbails                     = array();
								$thumbnails[]                  = 'http://www.dailymotion.com/thumbnail/video/'.$video_id;
								$thumbnails_first              = $thumbnails[0];
								$embed_code                    = make_valid_dailymotion_embed_code($video_id);
								$contentURL                    = "http://www.dailymotion.com/swf/".$video_id;
								$type = 'video';

							}
							else $type = '404';											

				}
				elseif( $url_domain == 'vimeo') {
					
							$video_id         = getVIMEOid($url);
							$video_info       = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$video_id.".php"));
							
							$type             = 'video';
							$title            = $video_info[0]['title'];
							$description      = $video_info[0]['description'];
							$thumbnails       = array();
							$thumbnails[]     = $video_info[0]['thumbnail_small'];
							$thumbnails[]     = $video_info[0]['thumbnail_medium'];
							$thumbnails[]     = $video_info[0]['thumbnail_large'];
							$thumbnails_first = $thumbnails[0];
							$embed_code       = make_valid_vimeo_embed_code($video_id);
							$contentURL       = "http://vimeo.com/moogaloop.swf?clip_id=".$video_id."&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1&amp;autoplay=0&amp;loop=0";

				}
				else {

							$html = file_get_contents_curl($url);



							if($html){
								$doc = new DOMDocument();
								@$doc->loadHTML($html);
								$nodes      = $doc->getElementsByTagName('title');
								$title      = $nodes->item(0)->nodeValue;
								$metas      = $doc->getElementsByTagName('meta');
								$contentURL = $url;	

								for ($i = 0; $i < $metas->length; $i++)
								{
								    $meta = $metas->item($i);			    
								    if($meta->getAttribute('name') == 'description')
								        $description = $meta->getAttribute('content');
								}

								$image_regex = '/<img[^>]*'.'src=[\"|\'](.*)[\"|\']/Ui';
								preg_match_all($image_regex, $html, $images, PREG_PATTERN_ORDER);
								$images = $images[1];
								if(!empty($images)){

									foreach ($images as $key => $value) {
										
										//we get only images with absolute url
										if(strpos($value,'http://')===0){
											$thumbnails[] = $value;											
										}
										/*
										else {										
											$thumbnails[] = pathinfo($url,PATHINFO_DIRNAME).$value;											
										}
										*/
									}
									
									if(!empty($thumbnails)){
										$thumbnails_first = $thumbnails[0];
									}
								}
								else {

									$thumbnails = array($default_thumbnail);
									$thumbnails_first = $default_thumbnail;
								}
								$type = 'url';
							}
							else $type = '404';							
					}

			}
			

			$vars['type']             = (isset($type))? $type : '';
			$vars['url']              = (isset($url))? $url : '';
			$vars['title']            = (isset($title))? $title : '';
			$vars['description']      = (isset($description))? $description : '';
			$vars['thumbnails']       = (isset($thumbnails))? $thumbnails : '';
			$vars['thumbnails_first'] = (isset($thumbnails_first))? $thumbnails_first : '';
			$vars['video_id']         = (isset($video_id))? $video_id : '';
			$vars['embed_code']       = (isset($embed_code))? $embed_code : '';
			$vars['contentURL']       = (isset($contentURL))? $contentURL : '';

			//debug($vars);

			$this->set($vars);
		}

 	}






 } ?>