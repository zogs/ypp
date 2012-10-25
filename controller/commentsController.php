<?php 
/**
 * 

	Controller des commentaires

 */
 class CommentsController extends Controller
 {
 	public $layout = 'none';
 	public $table = 'manif_comment';


 	/*=======================================
 	Show the whole comment system
 	@param $context ( manif, group, user ...)
 	@param $context_id int
 	========================================*/
 	public function show( $context, $context_id ){

 	
 		$d['context'] = $context;
 		$d['context_id'] = $context_id;

 		if($d['context'] == 'manif'){

 			$d['isadmin'] = false;
 			$d['commentsAllow'] = true;

 		}
 		elseif($d['context'] == 'group'){

 			$d['isadmin'] = true;
 			$d['commentsAllow'] = false;

 		}
 		elseif($d['context'] == 'user'){

	 		$d['isadmin'] = false;
 			$d['commentsAllow'] = false;
 		}
 		elseif($d['context'] == 'comment'){

 			$d['isadmin'] = false;
 			$d['commentsAllow'] = false;
 		}


 		// $d['flash'] = array('message'=>'You could spread a News to all your Protest(s) by filling the Title form',
 		// 					'type'=>'warning');


 		$this->set($d);
 		$this->view = 'comments/show';
 		$this->render();

 	}


 	/*========================================
 	List all the comment depending of the context
 	@param $context (manif, group user...)
 	@param $context_id int
 	=========================================*/
 	public function index($context, $context_id){


		$context         = (strlen($context)<=10)? $context : exit('wrong url context parameter');
		$context_id      = (is_numeric($context_id))? $context_id : exit('wrong url id parameter');
		
		$d['context']    = $context;
		$d['context_id'] = $context_id;


 		$this->loadModel('Comments');
 		$this->loadModel('Manifs');
 		$this->view = 'comments/index';
			
			
 		$perPage = 3;
		$params = array(	
									
			"context"    =>$context,
			"context_id" =>$context_id,
			'limit'      =>$perPage,
			"pays"       =>$this->session->getPays(),
			"lang"       =>$this->session->getLang()
			);

		if(isset($this->request->get)){

			$array_get     = get_object_vars($this->request->get);
			$params        = array_merge($array_get,$params);					
		}

		
		if($context=='manif' ||$context=='group'){

			//$d['coms']     = $this->Comments->findComments($params);
			$coms       = $this->Comments->findCommentsWithoutJOIN($params);
			$coms       = $this->Comments->findReplies($coms);
			$coms       = $this->Comments->joinUserData($coms);
			$d['coms']  = $coms;
			$d['total'] = $this->Comments->totalComments($context,$context_id);
				
		}
		elseif($context=='comment'){


			$com_id          = $context_id;
			$com             = $this->Comments->getComments($com_id);
			$com             = $this->Comments->findReplies($com);		
			$com             = $this->Comments->joinUserData($com);	
			
			$d['context']    = $com[0]->context;
			$d['context_id'] = $com[0]->context_id;
			$d['coms']       = $com;

		}
		elseif($context=='user'){

			$d['coms'] = $this->Comments->threadUser($params);

		}



		$this->set($d);

		$this->render();

 	}


 	/*====================================
	Show a unique comment
	@param $comment_id int
 	=====================================*/
 	public function view($comment_id){

		$this->layout    = 'default';
		$this->loadModel('Comments');
		$this->view      = 'comments/view';
		
		$com = $this->Comments->getComments($comment_id);
		$com = $com[0];

		if($com->context == 'manif'){

			$this->loadModel('Manifs');
			$manif = $this->Manifs->findFirstManif(array(
													'fields'=>array('MD.nommanif','D.manif_id','MD.slug'),
													'conditions'=>array('D.manif_id'=>$com->context_id)));			
			$link = Router::url('manifs/view/'.$manif->manif_id.'/'.$manif->slug);
			$name = $manif->nommanif;

		}
			
		$d['context_name'] = $name;	
		$d['context_link'] = $link;
		$d['comment_id'] = $comment_id;

 		$this->set($d);

		
 	}


 	/*========================================
 	Check for new comments
 	@param $context_id
 	$param $context
 	@second time in second since
 	========================================*/
 	public function tcheck($context,$context_id,$second){

 		$this->loadModel('Comments');
 		$this->layout = 'none';

 		$params = array(
				"conditions" => array("context"=>$context,"context_id"=>$context_id,"reply_to"=>0),
				"date_field" => 'date',
				"second"     => $second
 			);

 		$d['count'] = $this->Comments->countNew($params);

 		$this->set($d);



 	}



 	/*================================
 	Add a new comment
 	================================*/
 	public function add(){

		$this->loadModel('Comments');
 		$this->layout = 'none';


 		//if there is a user logged in
 		if($this->session->isLogged()){

 			//create new comment object
 			$newComment = new stdClass();
 			$newComment->context = $this->request->data->context;
 			$newComment->user_id = $this->session->user('user_id');
 			$newComment->lang = $this->session->getLang();
 			$newComment->context_id = $this->request->data->context_id;
 			$newComment->type = $this->request->data->type;

 			//If there is a media content
 			if(isset($this->request->data->media) && $this->request->data->media !=''){

 				$content = str_replace($this->request->data->media_url, '', $content); //suppress url
 				$content = $this->request->data->content.html_entity_decode($this->request->data->media); //add media to content
 				
 			}
 			else
 				$content = $this->request->data->content;
 			$newComment->content = $content;
 			$newComment->media = '';


 			//Replace line jump by <br>
 			$content = str_replace(array("\\n","\\r"),array("<br />",""),$content);

 			//if there is a title then this is a NEWS
 			if(isset($this->request->data->title) && $this->request->data->title!=''){
 				$newComment->title = $this->request->data->title;
 				$newComment->type = 'news';
 			} 	
	
 			//Save
 			$this->Comments->save($newComment);
 			$id = $this->Comments->id;
 			$d['id'] = $id;
 			//return id of the new comment
 		}
 		else {

 			$d['fail'] = "Please Login";
 		} 		 		

 		$this->set($d);
 	}


 	public function reply(){

 		$this->loadModel('Comments');
 		$this->view = 'comments/index';
 		$this->layout = 'none';

 		if($this->request->post('content')!=''){

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
	 			$this->index($this->request->data->context,$this->request->data->context_id,$comment_id);

		 		}
		 		else {

		 			$d['fail'] = 'You should log in first..';
		 			$this->set($d);
		 		}
	 		}
	 		else {

	 			$d['fail'] = '[Error] Reply_to is missing [Action] Post a reply ';
	 			$this->set($d);
	 		}
	 	}
	 	else {
	 		$d['fail'] = 'This is empty you know...';
	 		$this->set($d);
	 	}
 	

 	}

 	public function vote($id){

 		$this->loadModel('Comments');
 		$this->layout = 'none'; 
 		$this->view ='json';		

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


		if($this->request->post() ){

			if($this->request->post('url')) {


				$url = trim($this->request->post('url'));								
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
										if(strpos($value,'http://')===0 || strpos($value,'https://')===0 || strpos($value,'www')===0 ){
											$thumbnails[] = $value;											
										}
										
										else {	
											

											$thumbnails[] = getUrlDomain($url,true,true).$value;											
										}
										
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