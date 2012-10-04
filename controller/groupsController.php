<?php
class GroupsController extends Controller{
	

	public function __construct( $request = null ) {

		parent::__construct($request);

		$this->CookieRch = new Cookie('Rch',60*60*24*30,true);
	}


	public function index(){

	}
	public function view($group_id){

		$this->loadModel('Groups');
		$this->loadModel('Worlds');

		if(is_numeric($group_id)){


			$params = array(
							'fields'=>'A.group_id as id, A.name,A.address,A.mail,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city,A.cat2,A.cat3',
							'group_id'=>$group_id);

			$groups = $this->Groups->findGroups($params);
			$groups= $groups[0];
			$groups->context = 'group';
			$groups->context_id = $groups->id;
			$groups->isadmin ='';

			$groups = $this->Worlds->JOIN_GEO($groups);



			$d['group'] = $groups;

		} 
		else 
			exit('wrong id param in url');
		
		
		$this->set($d);



	}

	public function account( $action = null, $group_id = null, $slug = null){

		if($action=='new' || $action == null){

			$this->account_new();
		}

		if($action=='modify'){

			$this->account_modify( $group_id, $slug );
		}


	}

	public function account_modify( $group_id, $slug ){

		$d = array();
		$this->loadModel('Groups');
		$this->loadModel('Worlds');
		$this->loadModel('Manifs');

		
		//Get data
		$group = $this->Groups->findGroups(array(
										'fields'=>'A.group_id as id, A.name,A.address,A.mail,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city,A.cat2,A.cat3',
										'group_id'=>$group_id
										));
		$group = $group[0];
		//Get Geo
		$d['states'] = $this->Worlds->findAllStates(array(
		'CC1'=>$group->CC1,
		'ADM1'=>$group->ADM1,
		'ADM2'=>$group->ADM2,
		'ADM3'=>$group->ADM3,
		'ADM4'=>$group->ADM4,
		'city'=>$group->city
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

		$d['group'] = $group;
		$d['submit_tx'] = 'Save';

		$this->set($d);
	}

	public function account_new(){


		$this->loadModel('Groups');
		$this->loadModel('Users');
		$this->loadModel('Worlds');
		$this->loadModel('Manifs');


		if($this->request->data){

			if($this->Groups->validates($this->request->data,'register')){

				$data = $this->request->data;

				$group = new stdClass();
				$group->name = $data->name;
				$group->slug = slugify($data->name);
				$group->mail = $data->mail;							
				$group->lang = Conf::$lang;
				$group->cat2 = (isset($data->cat2) && !empty($data->cat2))? $data->cat2 : '';
				$group->cat3 = (isset($data->cat3) && !empty($data->cat3))? $data->cat3 : '';
				$group->CC1  = (isset($data->CC1) && !empty($data->CC1))? $data->CC1 : '';
				$group->ADM1 = (isset($data->ADM1) && !empty($data->ADM1))? $data->ADM1 : '';
				$group->ADM2 = (isset($data->ADM2) && !empty($data->ADM2))? $data->ADM2 : '';
				$group->ADM3 = (isset($data->ADM3) && !empty($data->ADM3))? $data->ADM3 : '';
				$group->ADM4 = (isset($data->ADM4) && !empty($data->ADM4))? $data->ADM4 : '';
				$group->city = (isset($data->city) && !empty($data->city))? $data->city : '';
				
				//Save in the group database
				if($this->Groups->saveGroup($group)){

					$this->session->setFlash('Your group have succefully been created');
					$group_id = $this->Groups->id;
				}
				else{
					$this->session->setFlash('Oups... Error while saving group !','error');
				}

				$user           = new stdClass();
				$user->login    = slugify($data->name);
				$user->mail     = $data->mail;
				$user->password = $data->password;
				$user->confirm  = $data->confirm;
				$user->bonhom   = 'bonhom_2';	
				$user->lang     = Conf::$lang;
				$user->status   = 'group';	

				//Save in the user database
				$this->request('users','register',array('user'=>$user));

				//Get data
				$group = $this->Groups->findGroups(array(
												'fields'=>'A.group_id as id, A.name,A.address,A.mail,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city,A.cat2,A.cat3',
												'group_id'=>$group_id
												));
				$group = $group[0];
				//Get Geo
				$d['states'] = $this->Worlds->findAllStates(array(
				'CC1'=>$group->CC1,
				'ADM1'=>$group->ADM1,
				'ADM2'=>$group->ADM2,
				'ADM3'=>$group->ADM3,
				'ADM4'=>$group->ADM4,
				'city'=>$group->city
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

				$d['group'] = $group;
				$d['submit_tx'] = 'Save';

				
			}
			else {
				$this->session->setFlash('Please check your information','error');
			}
		}
		else {

			$this->session->setFlash("As a group, you can create protests, spread news, schedule events ! ",'warning');
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

			$group = new stdClass();
			$group->cat2 = '';
			$group->cat3 = '';
			$group->CC1 = $this->CookieRch->read('CC1'); 
			$group->ADM1 = $this->CookieRch->read('ADM1'); 
			$group->ADM2 = $this->CookieRch->read('ADM2'); 
			$group->ADM3 = $this->CookieRch->read('ADM3');
			$group->ADM4 = $this->CookieRch->read('ADM4'); 
			$group->city = $this->CookieRch->read('city');
			$d['group'] = $group;
			$d['submit_tx'] = 'Sign in';	
		}


		$this->set($d);



	}
}
?>