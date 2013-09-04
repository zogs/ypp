<?php 
class ManifsController extends Controller{


	public $CookieRch;
	public $logo_default = 'img/bonhom/bonhom_1.gif';	

	public function __construct( $request = null ) {

		parent::__construct($request);

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
			"fields"=>array('D.manif_id','D.numerus','D.date_creation','D.logo','D.cat2','D.cat3','MD.nommanif','MD.description','MD.slug','P.id as participation_id'),
			"conditions"=>array('D.valid'=>1,'D.online'=>1),
			"cat2"=>$this->CookieRch->read('cat2'),
			"cat3"=>$this->CookieRch->read('cat3'),
			"order"=>$this->CookieRch->read('order'),
			"search"=>$this->CookieRch->read('rch'),
			"lang"=>$this->getLang(),
			"location"=>array(	'CC1'=>$this->CookieRch->read('CC1'),
								'ADM1'=>$this->CookieRch->read('ADM1'),
								'ADM2'=>$this->CookieRch->read('ADM2'),
								'ADM3'=>$this->CookieRch->read('ADM3'),
								'ADM4'=>$this->CookieRch->read('ADM4'),
								'city'=>$this->CookieRch->read('city')),
			'limit'=>(($this->request->page-1)*$perPage).','.$perPage
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
		'lang'     =>$this->getLang(),
		'level'    =>2
		));	

		$d['Ccat2'] = $this->Manifs->findCategory(array('id'=>$this->CookieRch->read('cat2'),'lang'=>$this->getLang() ));

		$d['cat3'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLang(),
		'level'    =>3
		));
		$d['Ccat3'] = $this->Manifs->findCategory(array('id'=>$this->CookieRch->read('cat3'),'lang'=>$this->getLang() ));		


		$d['order'] = array(
							'new' => '+ recentes',
							'big'=> '+ grosses',
							'old' => '+ anciennes',
							'tiny' => '+ petites'
							);						

		$this->set($d);
		
	}


	/**
	 * Add meta data to the list of protests ( as date, month, years, numerus)
	 */
	private function metadata($list){

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
	public function view($id,$slug = ''){

		$this->loadModel('Manifs');
		$this->loadModel('Users');
		$this->loadModel('Worlds');
		

		$params = array(
			'fields' => array('D.*','MD.nommanif','MD.slug','P.id as participation_id','MD.description'),
			'conditions' => array('D.manif_id'=>$id),
			"user"=>array('statut'=>$this->session->user()->getRole(),"id"=>$this->session->user()->getID()),
			"lang"=>$this->getLang(),
			"pays"=>$this->getCountryCode()
			);


		$manif = $this->Manifs->findFirstManif($params);		
		$manif = $this->Worlds->JOIN_GEO($manif);


		//if not exist redirect 404
		if(empty($manif)){
			$this->e404('Manif introuvable');
		}
		if($manif->isOffline()){
			$this->e404($manif->isOffline());
		}

		//reload correct url if slug is wrong
		if($slug!=$manif->slug){
			//$this->redirect("manifs/view/id:$id/slug:".$d['manif']->slug,301);
		}		

		
		//get creator 
		$manif->creator = $this->Users->findFirstUser(array('conditions'=>array('user_id'=>$manif->administrator_id)));		
		$manif->creator = $this->Worlds->JOIN_GEO($manif->creator);
		if(empty($manif)) throw new zException("Creator of the protest does not exist anymore", 1);
		

		//get manif translations
		$d['translationAvailable'] = $this->Manifs->find(array('table'=>'manif_descr','conditions'=>array('manif_id'=>$id),'fields'=>'lang'));
		
		//get protest of the same creator
		$d['sameCreatorProtests'] = $this->Manifs->findManifs(array('conditions'=>array('administrator_id'=>$manif->creator->getID()),'lang'=>$this->getLang()));
		//get random protest
		$d['randomProtests'] = $this->Manifs->findManifs(array('order'=>'RAND()','limit'=>'0,5','lang'=>$this->getLang()));
		//get similar protest
		$d['similarProtests'] = $this->Manifs->findSimilarProtests($manif->getID(),$this->getLang());


		$d['manif'] = $manif;
		$this->set($d);
	}

	public function getCategory(){


		$this->loadModel('Manifs');
		$this->layout = 'none';

		$d['category'] = $this->Manifs->findCategory(array(
			'lang'=>$this->getLang(),
			'level'=>$this->request->get('level'),
			'parent'=>$this->request->get('parent')
			));

		$this->set($d);
	}


	public function create($manif_id = null,$slug = null){

		if(isset($manif_id) && !is_numeric($manif_id)) throw new Exception("manif_id should be numeric", 1);
		if(isset($slug) && !is_string($slug)) throw new Exception("slug should be string", 1);
		
		if(empty($manif_id) && !$this->request->post()){

			$this->createProtest();
		}

		if(is_numeric($manif_id)){

			$this->saveProtest($manif_id,$slug);
		}

	}

	private function createProtest(){

		$this->loadModel('Manifs');
		$this->loadModel('Worlds');

		$this->session->setFlash("All you need to <strong>create</strong> a protest is here !","info");
		$this->session->setFlash("Make sure your <strong><a href=".Router::url('manifs/index').">idea</a></strong> is not already existing !",'warning');

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
		'lang'     =>$this->getLang(),
		'level'    =>2
		));	
		$d['cats3'] = $this->Manifs->findCategory(array(
		'lang'     =>$this->getLang(),
		'level'    =>3
		));	

		$manif = new Manif();
		$manif->setCreatorID($this->session->user()->getID());
		$manif->setCC1($this->CookieRch->read('CC1')); 
		$manif->setADM1($this->CookieRch->read('ADM1'));
		$manif->setADM2($this->CookieRch->read('ADM2'));
		$manif->setADM3($this->CookieRch->read('ADM3'));
		$manif->setADM4($this->CookieRch->read('ADM4'));
		$manif->setcity($this->CookieRch->read('city'));

		$d['manif'] = $manif;
		$d['submit_text'] = 'Start Protest';
		$this->set($d);
	}

	private function saveProtest($manif_id,$slug){

		$this->loadModel('Manifs');
		$this->loadModel('Worlds');

		$d = array();

		if($this->session->user()->islog()){

			//lang to render by default
			$lang = $this->getLang();
			
			//if not new protest
			if(!empty($manif_id)){

				//if protest exist
				if($manif = $this->Manifs->exist($manif_id)){
					
					if(!$manif->isUserAdmin( $this->session->user()->getID() )) {

						$this->session->setFlash("You are not admin of this protest...",'danger');
						$this->e404('So, who are you ?');
					}

					//if translate dont exist
					if(!$this->Manifs->i18n_exist($manif_id,$lang)){

						//redirect to origin lang
						$this->session->setFlash("This protest is not yet translated in ".Conf::$languageAvailable[$lang].".","warning");
						$this->session->setFlash("If you want to translate, modify this form and save it in ".Conf::$languageAvailable[$lang],"info");
						$this->redirect('manifs/create/'.$manif_id.'?lang='.$manif->lang);
					}

				}
				//protest dont exist return 404
				else {
					$this->session->setFlash("This protest does not exist","info");
					$this->e404('hum... nothing here. ');													
				}

			}
			
			
			//if form data is send
			if($this->request->data){

				$data = $this->request->data;
 		 		
				//validates data
				if($this->Manifs->validates($data)){
			
					//save data
					$saved_manif = $this->Manifs->saveManif($data,$manif_id);

					if($saved_manif['action']=='insert') {

						$this->session->setFlash('Protest have been <strong>save</strong> ! <a href="'.Router::url('manifs/view/'.$saved_manif['id'].'/'.$slug.'?lang='.$data->lang).'">See it in action !</a>','success');
						
						//Statistics
						$this->loadModel('Config');
						$this->Config->incrementTotalProtestsOnline();
					}
					else {

						$this->session->setFlash('Protest have been <strong>updated</strong> ! <a href="'.Router::url('manifs/view/'.$saved_manif['id'].'/'.$slug.'?lang='.$data->lang).'">See it in action !</a>','success');
						
					}
					
					//redirect to create form
					$this->redirect('manifs/create/'.$saved_manif['id'].'/?lang='.$saved_manif['lang']);
				}
				else {
					$this->session->setFlash('Please review the formulaire','error');
					
				}							
			}
				
				

			//============================================================================================
			//Récupération donnée
			$params = array(
			'fields' => array('D.*','MD.nommanif','MD.slug','P.id','MD.description'),
			'conditions' => array('D.manif_id'=>$manif_id,'online'=>1),			
			"lang"=>$lang
			);
			$manif = $this->Manifs->findFirstManif($params);

			//Translation available
			$d['translates'] = $this->Manifs->findTranslates($manif->getID());

			//Category
			$d['cats2'] = $this->Manifs->findCategory(array(
			'lang'     =>$lang,
			'level'    =>2
			));	
			$d['cats3'] = $this->Manifs->findCategory(array(
			'lang'     =>$lang,
			'level'    =>3,
			'parent' =>$manif->cat2
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
			$d['keywords'] = $this->Manifs->findKeywords($manif_id,$lang);				
			$keywords = '';
			foreach ($d['keywords'] as $v) {
				$keywords .= $v->tag.' ';					
			}
			$manif->keywords = $keywords;
			//=============================================================================================			

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

		if($this->request->get('manif_id')){			

			$d = $this->removeProtester($this->request->get('manif_id'),$this->session->user()->getID());
			
		}
		else $d['error'] = 'Missing data in manifs/removeUser url ';

		$this->set($d);

	}


	private function removeProtester($manif_id,$user_id){

		$this->loadModel('Manifs');

		$participation = $this->Manifs->findProtesters(array(
			'fields'=>array('P.id','U.login','U.user_id','bonhom'),
			'conditions'=>array('P.manif_id'=>$manif_id,'P.user_id'=>$user_id))
		);

		//if user is really protesting
		if(!empty($participation)){

			//remove protester
			if($this->Manifs->removeProtesting($participation->id)){

				$d['login'] = $participation->login;
				$d['bonhom'] = $participation->bonhom;
				$d['user_id'] = $participation->user_id;
				$d['success'] = 'remove';

				//decrement manif numerus
				$this->Manifs->decrement(array('field'=>'numerus','id'=>$manif_id));

				//Global statistic
				$this->loadModel('Config');
				$this->Config->decrementTotalProtesters();
				$this->Config->incrementTotalProtestersCanceled();
			}
			else {
				$d['error'] = 'Error canceling protesting '.$participation->login;
			}
		}
		else $d['error'] = 'not protesting';

		return $d;

	}


	public function addUser(){

		$this->view = 'json';
		$this->layout = 'none';

		//if POST data are correct
		if($manif_id = $this->request->get('manif_id')){
		
			//call to addProtester
			$d = $this->addProtester($manif_id,$this->session->user()->getID());				


		}
		else $d['error'] = 'Missing data in manifs/addUser url';

		$this->set($d);
	}

	private function addProtester($manif_id,$user_id){
		
		$this->loadModel('Manifs');
		
		//secu
 		if(!is_numeric($user_id)&&$user_id!=0) return false;
 		if(!is_numeric($manif_id)&&$manif_id!=0) return false;

 		//save
		$result = $this->Manifs->saveProtester( $user_id, $manif_id );
		
		//if the protester have been saved
		if(is_numeric($result)){

			//increment manif numerus
			$this->Manifs->increment(array('field'=>'numerus','id'=>$manif_id));

			//return user login and bonhom
			$user = $this->Manifs->findFirst(array(
			'table'=>'users',
			'fields'=>'bonhom,login',
			'conditions'=>array('user_id'=>$user_id))
			);
			
			$d['login'] = $user->login;
			$d['bonhom'] = $user->bonhom;
			$d['success'] = "added";

			//save a numerus stage
			$this->setNumerusStep($manif_id,$user_id);

			//Global statistic
			$this->loadModel('Config');
			$this->Config->incrementTotalProtesters();

		}
		//user is already in
		elseif($result=='already'){
			$d['error'] = "You are already in";			
		}
	

		return $d;
	}


	private function setNumerusStep($manif_id,$user_id){

		$step = 0;

		//get numerus
		$m = $this->Manifs->findFirst(array('conditions'=>array('manif_id'=>$manif_id),'fields'=>array('manif_id','numerus')));

		//for <= 100
		if($m->numerus <= 100){
			$i=0;
			while($i<=100){				
				if($m->numerus==$i) $step = $id;
				$i = $i+10;
			}
		}
		//for <= 1000
		if($m->numerus <= 1000){
			$i=0;
			while($i<=1000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+100;
			}
		}
		//for <= 10 000
		if($m->numerus <= 10000){
			$i=0;
			while($i<=10000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+500;
			}
		}
		//for <= 50000
		if($m->numerus <= 50000){
			$i=0;
			while($i<=50000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+1000;
			}
		}
		//for <= 100000
		if($m->numerus <= 100000){
			$i=0;
			while($i<=100000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+2000;
			}
		}
		//for <= 500000
		if($m->numerus <= 500000){
			$i=0;
			while($i<=500000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+10000;
			}
		}
		if($m->numerus > 500000){
			$i=0;
			while($i>500000){				
				if($m->numerus==$i) $step = $id;
				$i = $i+50000;
			}
		}

		if(!empty($step)){
			$this->saveNumerusStep($manif_id,$step,$user_id);	
		}
	}

	private function saveNumerusStep($manif_id,$step,$user_id){

		$this->view='none';
		$this->layout='none';
		$this->loadModel('Manifs');

		//save step in database
		$this->Manifs->saveNumerusStep($manif_id,$step,$user_id);

		//send mail
		if(in_array($step,Conf::$stepNumerusForMailAuthor)){

			$this->sendStepMail($manif_id,$step);
		}
	}

	private function sendStepMail($manif_id,$step){

		$this->loadModel('Manifs');
		$this->loadModel('Users');

		//instacnce the mailer
		$mailer = Swift_Mailer::newInstance(Conf::getTransportSwiftMailer());

		//find all the admin for this protest
		$admins = $this->Users->find(array('table'=>'manif_admin','conditions'=>array('manif_id'=>$manif_id),'fields'=>array('user_id')));
		//foreach admin
		foreach ($admins as $a) {
		
			//find user
			$u = $this->Users->findFirstUser(array('conditions'=>array('user_id'=>$a->user_id),'fields'=>array('user_id','login','email','lang')));
			//find protest on user lang
			$m = $this->Manifs->findFirstManif(array('conditions'=>array('D.manif_id'=>$manif_id),'lang'=>$u->lang,'fields'=>array('D.manif_id','MD.slug','MD.nommanif','MD.logo','MD.lang')));
			//set link to protest
			$link2Protest = "http://localhost/ypp/manifs/view/".$manif_id.'/'.$m->getSlug().'?lang='.$u->lang;

			//set subject of the mail
			$subject = $step.' manifestants pour '.$m->getTitle().' !';
			if($step>=1000) $subject.='!';
			if($step>=10000) $subject.='!';
			if($step>=100000) $subject.='!';

			//set body
			$body = file_get_contents('../view/email/newStepProtest.html');
	        $body = preg_replace("~{site}~i", Conf::$Website, $body);
	        $body = preg_replace("~{user}~i", $u->getLogin(), $body);
	        $body = preg_replace("~{link2Protest}~i", $link2Protest, $body);
	        $body = preg_replace("~{title}~i",$subject, $body);
	        $body = preg_replace("~{manif}~i",$m->getTitle(), $body);

	       //set message 
	        $message = Swift_Message::newInstance()
	        	->setSubject($subject)
	        	->setFrom('noreply@'.Conf::$Website,Conf::$Website)
	        	->setTo($u->email,$u->login)
	        	->setBody($body,'text/html','uft-8')
	        	->addPart($subject.' see your protest at this url : '.$link2Protest,'text/plain');

	        //if mail sended return true
	        if(!$mailer->send($message, $failures)){
	        	echo 'Erreur lors de lenvoi du mail';
	        	debug($failures);
	        }
	        else{
	        	return true;
	        }

		}

	}

	public function statistics($manif_id){

		$this->loadModel('Manifs');		
		$this->layout='none';

		$stats = $this->Manifs->find(array('table'=>'manif_step','fields'=>array('date','step'),'conditions'=>array('manif_id'=>$manif_id)));

		$js='[';
		foreach ($stats as $key => $value) {
			$stats[$key] = array($value->date,$value->step);
			$js .= "['$value->date',$value->step],";
		}
		$js.=']';

		$this->set(array('stats'=>$stats,'js'=>$js));
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
					"user"=>array('statut'=>$this->session->user()->getRole(),"id"=>$this->session->user()->getID()),
					'lang'=>$lang,
					'limit'=>1
				));
				$d['slogan'] = $nommanif;
			}
		}

		$this->set($d);
	}


} ?>