<?php 
class ManifsController extends Controller{


	public $CookieRch;
	public $logo_default = 'img/bonhom/bonhom_1.gif';

	public function __construct( $request = null ) {

		parent::__construct($request);

		$this->CookieRch = new Cookie('Rch',60*60*24*30,true);
	}

	/**
	 * index
	 */
	public function index(){

		$this->loadModel('Manifs');
		$this->loadModel('Worlds');
		
		//If no country is chosen, set one by default 
		if(!$this->CookieRch->read('CC1')){
				
			$this->CookieRch->write(array('CC1'=>$this->getCountryCode()) );
		}


		//If search params are send
		if( $this->request->get() ) {
			
			//Write cookie of seach params
			$this->CookieRch->write(array('rch'=>$this->request->get('rch'),												
												'CC1'=>$this->request->get('CC1'),
												'ADM1'=>$this->request->get('ADM1'),
												'ADM2'=>$this->request->get('ADM2'),
												'ADM3'=>$this->request->get('ADM3'),
												'ADM4'=>$this->request->get('ADM4'),
												'city'=>$this->request->get('city'),
												'cat2'=>$this->request->get('cat2'),
												'cat3'=>$this->request->get('cat3'),
												'order'=>$this->request->get('order')
												));			
		}
		
		//get protests
		$perPage = 10;
		$params = array(
			"fields"=>array('D.manif_id as id','D.numerus','D.date_creation','D.logo','D.cat2','D.cat3','MD.nommanif','MD.description','MD.slug','P.id as pid','AD.user_id as isadmin'),
			"conditions"=>array('D.online'=>1),
			"cat2"=>$this->CookieRch->read('cat2'),
			"cat3"=>$this->CookieRch->read('cat3'),
			"order"=>$this->CookieRch->read('order'),
			"search"=>$this->CookieRch->read('rch'),
			"user"=>array('statut'=>$this->session->user('statut'),"id"=>$this->session->user_id()),
			"lang"=>$this->session->getLang(),
			"location"=>array(	'CC1'=>$this->CookieRch->read('CC1'),
								'ADM1'=>$this->CookieRch->read('ADM1'),
								'ADM2'=>$this->CookieRch->read('ADM2'),
								'ADM3'=>$this->CookieRch->read('ADM3'),
								'ADM4'=>$this->CookieRch->read('ADM4'),
								'city'=>$this->CookieRch->read('city')),
			'limit'=>(($this->request->get->page-1)*$perPage).','.$perPage
			);


		$list        = $this->Manifs->findManifs($params);	
		$metas       = $this->metadata($list);	
		$d['manifs'] = $metas;
		
		$d['total']  = $this->Manifs->findManifsCount($params);		
		$d['nbpage'] = ceil($d['total']/$perPage);
		
		//get geographical 
		$d['states'] = $this->Worlds->findAllStates(array(
												'CC1'=>$this->CookieRch->read('CC1'),
												'ADM1'=>$this->CookieRch->read('ADM1'),
												'ADM2'=>$this->CookieRch->read('ADM2'),
												'ADM3'=>$this->CookieRch->read('ADM3'),
												'ADM4'=>$this->CookieRch->read('ADM4'),
												'city'=>$this->CookieRch->read('city')
												));

		//get category
		$d['cat2'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLanguage(),
		'level'    =>2
		));	
		$d['Ccat2'] = $this->Manifs->findCategory(array('id'=>$this->CookieRch->read('cat2'),'lang'=>$this->getLanguage() ));

		$d['cat3'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLanguage(),
		'level'    =>3
		));
		$d['Ccat3'] = $this->Manifs->findCategory(array('id'=>$this->CookieRch->read('cat3'),'lang'=>$this->getLanguage() ));		

		$d['order'] = array(
							array('name'=>'News','code'=>'new'),
							array('name'=>'Biggest','code'=>'big'),
							array('name'=>'Oldies', 'code'=>'old'),
							array('name'=>'Deserted', 'code'=>'tiny')
							);


		$this->set($d);

	}


	/**
	 * Add meta data to the list of protests ( as date, month, years, numerus)
	 */
	public function metadata($list){

		$manifs = array();

		foreach ($list as $key => $manif) {			
			$date = date_parse($manif->date_creation);

			if($manif->logo=='') $manif->logo = $this->logo_default;

			$array = array();
			$array['metas'] = array(
							'year' => $date['year'],
							'month' => $date['month'],
							'day' => $date['day'],
							'numerus'=>$manif->numerus
							);
			$array['manif'] = $manif;
			
			$manifs[] = $array;
		}
		
		return $manifs;

	}
	public function view($id,$slug){

		$this->loadModel('Manifs');

		$params = array(
			'fields' => array('D.manif_id as id','D.numerus','D.logo','D.date_creation','MD.nommanif','MD.slug','U.login as user','P.id as pid','MD.description','AD.user_id as isadmin'),
			'conditions' => array('D.manif_id'=>$id,'online'=>1),
			"user"=>array('statut'=>$this->session->user('statut'),"id"=>$this->session->user_id()),
			"lang"=>$this->session->getLang(),
			"pays"=>$this->getCountryCode()
			);



		$d['manif'] = $this->Manifs->findFirstManif($params);
		$d['manif']->context = 'manif';
		$d['manif']->context_id = $id;

		if(empty($d['manif'])){
			$this->e404('Manif introuvable');
		}

		if($slug!=$d['manif']->slug){
			$this->redirect("manifs/view/id:$id/slug:".$d['manif']->slug,301);
		}

		$this->set($d);
	}

	public function getCategory(){


		$this->loadModel('Manifs');
		$this->layout = 'none';

		$d['category'] = $this->Manifs->findCategory(array(
			'lang'=>$this->getLanguage(),
			'level'=>$this->request->post('level'),
			'parent'=>$this->request->post('parent')
			));

		$this->set($d);
	}


	public function create($manif_id = null,$slug = null){

		if(isset($manif_id) && $manif_id!='' && $manif_id!=0 && is_numeric($manif_id) ){

			$this->modif($manif_id,$slug);

		}
		else {
			$this->createnew();
		}
	}

	public function createnew(){

		$this->loadModel('Manifs');
		$this->loadModel('Worlds');


		$this->session->setFlash("Help yourself ! But before you should check the existing <a href=".Router::url('manifs/index').">protests</a>",'warning');

		//get geographical 
		$d['states'] = $this->Worlds->findAllStates(array(
			'CC1'=>$this->CookieRch->read('CC1'),
			'ADM1'=>$this->CookieRch->read('ADM1'),
			'ADM2'=>$this->CookieRch->read('ADM2'),
			'ADM3'=>$this->CookieRch->read('ADM3'),
			'ADM4'=>$this->CookieRch->read('ADM4'),
			'city'=>$this->CookieRch->read('city')
			));
		//get category
		$d['cats2'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLanguage(),
		'level'    =>2
		));	
		$d['cats3'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLanguage(),
		'level'    =>3
		));	


		$manif->nommanif = '';
		$manif->slug = '';
		$manif->id = '';
		$manif->description = '';
		$manif->keywords = '';
		$manif->cat2 = '';
		$manif->cat3 = '';
		$manif->CC1 = $this->CookieRch->read('CC1'); 
		$manif->ADM1 = $this->CookieRch->read('ADM1'); 
		$manif->ADM2 = $this->CookieRch->read('ADM2'); 
		$manif->ADM3 = $this->CookieRch->read('ADM3');
		$manif->ADM4 = $this->CookieRch->read('ADM4'); 
		$manif->city = $this->CookieRch->read('city');
		$d['manif'] = $manif;
		$d['submit_text'] = 'Start Protest';
		$this->set($d);
	}

	public function modif($manif_id,$slug){

		$this->loadModel('Manifs');
		$this->loadModel('Worlds');

		$d = array();

		if($this->session->user()){

		//Récupération donnée
			$params = array(
			'fields' => array('D.manif_id','D.numerus','MD.nommanif','MD.slug','P.id','MD.description','AD.user_id as isadmin','D.cat2','D.cat3','D.logo','D.CC1','D.ADM1','D.ADM2','D.ADM3','D.ADM4','D.city'),
			'conditions' => array('D.manif_id'=>$manif_id,'online'=>1),
			"user"=>array('statut'=>$this->session->user('statut'),"id"=>$this->session->user_id()),
			"lang"=>$this->session->getLang(),
			"pays"=>$this->getCountryCode()
			);
			$manif = $this->Manifs->findFirstManif($params);

			if(!empty($manif)){

				//Category
					$d['cats2'] = $this->Manifs->findCategory(array(
					'lang'     =>$this->getLanguage(),
					'level'    =>2
					));	
					$d['cats3'] = $this->Manifs->findCategory(array(
					'lang'     =>$this->getLanguage(),
					'level'    =>3
					));	
				//Localisation
					$d['states'] = $this->Worlds->findAllStates(array(
					'CC1'=>$manif->CC1,
					'ADM1'=>$manif->ADM1,
					'ADM2'=>$manif->ADM2,
					'ADM3'=>$manif->ADM3,
					'ADM4'=>$manif->ADM4,
					'city'=>$manif->city
					));
				//Keywords
					$d['keywords'] = $this->Manifs->findKeywords($manif_id,$this->getLanguage());
					$keywords = '';
					foreach ($d['keywords'] as $v) {
						$keywords .= $v->tag.' ';					
					}
					$manif->keywords = $keywords;


				//Update des données
					
					if(isset($manif->isadmin)){


						if($this->request->data){
						
							if($this->Manifs->validates($this->request->data)){

								$this->Manifs->saveManif($this->request->data,$manif_id);

								$this->session->setFlash('Protest have been <strong>updated</strong> ! See it <a href="'.Router::url('manifs/view/'.$manif_id.'/'.$slug).'">there</a>','success');

							}
							else {

								$this->session->setFlash('Please review the formulaire','error');
							}

						}
					}
					else {

						$this->session->setFlash("You are not admin of this protest...",'danger');
						$this->e404('So, who are you ?');
					}

			}
			else {
				$this->session->setFlash("That thing don't exist",'error');
				$this->e404('What are you looking for ?');
			}

		}
		else {

			$this->session->setFlash('Sorry but you need to be connected here <a class="callModal" href="'.Router::url('users/register').'">Inscription here</a>','danger');
		}

		$d['manif'] = $manif;
		$d['submit_text'] = 'Save Protest';
		$this->set($d);
		
	}



	public function removeUser(){

		$this->view = 'json';
		$this->layout = 'none';

		if($this->request->post('manif_id') && $this->request->post('user_id')){

			if( $this->session->user_id() == $this->request->post('user_id') ){

				$d = $this->removeProtester(
					$this->request->post('manif_id'),
					$this->session->user_id()
					);
			}
			else $d['error'] = 'User is not connected';			
		}
		else $d['error'] = 'Missing data in manifs/removeUser url ';

		$this->set($d);

	}


	private function removeProtester($manif_id,$user_id){

		$this->loadModel('Manifs');
		$this->Manifs->table = "manif_participation";
		$this->Manifs->primaryKey = "id";

		$participation = $this->Manifs->findProtesters(array(
			'fields'=>array('P.id','U.login','U.user_id'),
			'conditions'=>array('P.manif_id'=>$manif_id,'P.user_id'=>$user_id))
		);

		if(!empty($participation)){

			$del        = new stdClass();
			$del->table = T_MANIF_PROTESTERS;
			$del->key   = K_MANIF_PROTESTERS;
			$del->id    = $participation->id;

			if($this->Manifs->delete($del)){

				$d['success'] = 'Cancel Ok';
			}
			else {
				$d['error'] = 'Error canceling protesting '.$participation->login;
			}

		}
		else $d['error'] = 'User not protest';

		return $d;

	}


	public function addUser(){

		$this->view = 'json';
		$this->layout = 'none';

		//if POST data are correct
		if($this->request->post('manif_id') && $this->request->post('user_id')){

			//if user is logged and the sender of data
			if( $this->session->user_id() == $this->request->post('user_id') ){

				//call to addProtester
				$d = $this->addProtester(
					$this->request->post('manif_id'),
					$this->session->user_id()
					);
			}
			else $d['error'] = 'User is not connected';			
		}
		else $d['error'] = 'Missing data in manifs/addUser url';

		$this->set($d);
	}

	private function addProtester($manif_id,$user_id){
		
		$this->loadModel('Manifs');
		$this->Manifs->table = "manif_participation";
		$this->Manifs->primaryKey = "id";
				
		$user = $this->Manifs->findFirst(array(
			'fields'=>'id',
			'conditions'=>array('manif_id'=>$manif_id,'user_id'=>$user_id))
		);
		
		if(empty($user)){

			$protester = new stdClass();
			$protester->manif_id = $manif_id;
			$protester->user_id = $user_id;
			$protester->date = Date::SQLNow();
			$user = $this->Manifs->save( $protester );
			
			if($user){
				$this->Manifs->table = "users";
				$user = $this->Manifs->findFirst(array(
				'fields'=>'bonhom,login',
				'conditions'=>array('user_id'=>$user_id))
				);
				
				$d['login'] = $user->login;
				$d['bonhom'] = $user->bonhom;
				$d['success'] = "You protest ! ";
			}
			else {
				$d['error'] = "SQL insertion failed ";
				//debug($this->Manifs->error);
			}
		}
		else {
			$d['error'] = "You are already in";
		}

		return $d;
	}

	
	//========================================================================================
	public function getProtester(){
	//========================================================================================
		$this->loadModel('Manifs');
		$this->layout = 'none';
		$this->view = 'json';

		$manif_id = $this->request->get('mid');
		$lang     = $this->request->get('l');
		$bonhom   = $this->request->get('b');

		if(isset($manif_id) && is_numeric($manif_id)){

			if($bonhom=='slogan'){

				$slogan = $this->Manifs->findSlogan($manif_id,$lang);
				
				if($slogan)
					$return = $slogan->content;				

			}
			else {
				$return = $this->Manifs->findProtesters(array(
														'fields'=>'U.login',
														'conditions'=>array('P.manif_id'=>$manif_id,'U.bonhom'=>$bonhom),
														'order'=>'random',
														'limit'=>1
													));	
			}

			if( empty($return)) { 

				$return = $this->Manifs->findProtesters(array(
														'fields'=>'U.login',
														'conditions'=>array('P.manif_id'=>$manif_id),
														'order'=>'random',
														'limit'=>1
													));	
			}

		} else exit();

		$d['user'] = $return;
		
		$this->set($d);

	}
	//========================================================================================
	public function getProtesterInfo(){
	//========================================================================================

		$this->loadModel('Manifs');
		$this->loadModel('Comments');
		$this->layout = 'none';
		$this->view = 'json';

		$manif_id = $this->request->get('mid');
		$lang     = $this->request->get('l');
		$login   = $this->request->get('u');

		if(isset($manif_id) && is_numeric($manif_id)){

			if(preg_match('/^bonhom_/', $login)){
				
				$user = $this->Manifs->findProtesters(array(
													'fields' => array('U.login','U.user_id','U.avatar','P.date'),
													'conditions' => array('P.manif_id'=>$manif_id),
													'order'=>'random',
													'limit'=>1
													));
			}
			else {

				$user = $this->Manifs->findProtesters(array(
													'fields' => array('U.login','U.user_id','U.avatar','P.date'),
													'conditions' => array('P.manif_id'=>$manif_id,'U.login'=>$login),
													'limit'=>1
														));
			}

			if(empty($user)){
				$user = $this->Manifs->findProtesters(array(
													'fields' => array('U.login','U.user_id','U.avatar','P.date'),
													'conditions' => array('P.manif_id'=>$manif_id),
													'order'=>'random',
													'limit'=>1
													));

				if(empty($user)) exit(); // there is no protesters at all !
			}
			
			$user->comment = $this->Comments->findUserComments(array(
				'fields'=>array('C.id','C.content','C.date','C.note','C.lang'),
				'conditions'=>array('C.user_id'=>$user->user_id,'C.context'=>'manif','C.context_id'=>$manif_id,'C.type'=>'com','C.reply_to'=>0,'C.online'=>1),
				'order'=>'note DESC',
				'limit'=>1));

			$numComments = $this->Comments->userTotalComments($user->user_id);

			$user->infoline = "since ".$user->date." - ".$numComments." comments";
			

			
		}  else exit();
		

		$d['user'] = $user;
		$this->set($d);

	}

	//========================================================================================
	public function getSlogan(){
	//========================================================================================

		$this->loadModel('Manifs');
		$this->loadModel('Comments');
		$this->layout = 'none';
		$this->view = 'json';

		$manif_id = $this->request->get('mid');
		$lang     = $this->request->get('l');
		
		if(isset($manif_id) && is_numeric($manif_id)){


			$slogan = $this->Comments->findUserComments(array(
				'fields'=>'C.content as slogan, U.login, U.user_id',
				'conditions'=>array('C.type'=>'slogan','C.manif_id'=>$manif_id,'C.lang'=>$lang,'online'=>1),
				'order'=>'random',
				'limit'=>1
				));
				
			if($slogan)
				$d['slogan'] = $slogan;	
			else {
				$nommanif = $this->Manifs->findManifs(array(
					'fields'=>'MD.nommanif as slogan',
					'conditions'=>array('D.manif_id'=>$manif_id,'MD.lang'=>$lang),
					"user"=>array('statut'=>$this->session->user('statut'),"id"=>$this->session->user_id()),
					'lang'=>$lang,
					'limit'=>1
				));
				$d['slogan'] = $nommanif;
			}
		}

		$this->set($d);
	}


} ?>