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

		if(isset($group_id)){

			if(is_numeric($group_id)){


				$params = array(
								'fields'=>'A.group_id as id, A.name,A.address,A.email,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city,A.cat2,A.cat3',
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
				exit('Error: (id must be numeric) {groups,view} ');
		}
		else
			exit('Error: (id must exist) {groups,view}');
		
		$this->set($d);



	}


	public function account( $group_id = null, $slug = null){

		if($this->request->data){

			return $this->account_save( $this->request->data);
		}

		if(!isset($group_id)){

			return $this->account_view();
		}

		if(is_numeric($group_id)){			

			return $this->account_view($group_id,$slug);
		}
		else exit('Error: id must be numeric {groupsController, account');


	}

	public function account_view( $group_id = null, $slug = null ){		

		$d = array();
		$this->loadModel('Groups');
		$this->loadModel('Worlds');
		$this->loadModel('Manifs');

		//if specific group		
		if(isset($group_id)){

			if(is_numeric($group_id)){


				//check if user is admin of the group
				$admin = $this->Groups->isAdmin(Session::user()->getID(),$group_id);
				if(!$admin) {
					Session::setFlash("You are not admin of this group","warning");
					$this->redirect('/');
				}					

				//Get group data
				$group = $this->Groups->findGroups(array(
												'fields'=>'A.group_id, A.user_id, A.name,A.address,A.email,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city,A.cat2,A.cat3',
												'group_id'=>$group_id
												));
				if(isset($group[0])){
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
					'lang'     =>$this->getLang(),
					'level'    =>2
					));	
					$d['cats3'] = $this->Manifs->findCategory(array(
					'lang'     =>$this->getLang(),
					'level'    =>3
					));	

					$d['action'] = 'change';
					$d['group'] = unescape($group);
					$d['submit_tx'] = 'Save';

					Session::setFlash("You are admin of ".$group->name,"success");
				}
				else{
					Session::setFlash('This group does not exist','error');
					return $this->account_view();
				}
			}
			else {
				debug('group_id is not numeric');
				die();
			}
		}
		//else set default data for new group
		else {


			Session::setFlash("As a group, you can create protests, spread news, schedule events ! ",'warning');
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

			$group              = new stdClass();
			$group->name        = '';
			$group->email        = '';
			$group->cat2        = '';
			$group->cat3        = '';
			$group->CC1         = $this->CookieRch->read('CC1'); 
			$group->ADM1        = $this->CookieRch->read('ADM1'); 
			$group->ADM2        = $this->CookieRch->read('ADM2'); 
			$group->ADM3        = $this->CookieRch->read('ADM3');
			$group->ADM4        = $this->CookieRch->read('ADM4'); 
			$group->city        = $this->CookieRch->read('city');
			$group->description = '';
			$group->group_id    = '';
			$group->user_id     = '';
			$group->logo        = '';
			$group->banner      = '';

			$d['action'] = 'create';
			$d['group']      = $group;
			$d['submit_tx']  = 'Sign in';	
		}			

		$this->set($d);
	}
 	

	public function account_save( $data ){


		$this->loadModel('Groups');
		$this->loadModel('Users');
		$this->loadModel('Worlds');
		$this->loadModel('Manifs');


		if($data){


			//Create new OR modify existing group
			if(isset($data->group_id) && is_numeric($data->group_id))
				$action = 'change';
			else
				$action = 'create';

				

			//Validates form with appropriate rules
			if($this->Groups->validates($data,$action)){

				//make slug
				$slug = slugify($data->name);

				//Save user data first				
				$user           = new stdClass();
				$user->login    = $slug;
				$user->email     = $data->email;
					if($action == 'create'){
				$user->password = $data->password;
				$user->confirm  = $data->confirm;
					}
				$user->bonhom   = 'bonhom_2';	
				$user->lang     = Conf::$lang;
				$user->status   = 'group';	
				//if the group already exist, set user_id for update
				if(is_numeric($data->user_id))
					$user->user_id = $data->user_id;

				//Save in the user database
				$this->Users->save($user);
				//Get back the user_id
				$user_id = $this->Users->id;


				//Group to save
				//If the group exist, set group_id for udpate , else set logo by default
				$group = new stdClass();				
				if(is_numeric($data->group_id)) {
					$group->group_id = $data->group_id;						
				}	
				else {
					$group->logo = 'img/logo_yp.png';
				}		
				$group->name    = $data->name;
				$group->slug    = $slug;
				$group->email   = $data->email;							
				$group->lang    = Conf::$lang;
				$group->user_id = $user_id;
				$group->cat2    = (isset($data->cat2) && !empty($data->cat2))? $data->cat2 : '';
				$group->cat3    = (isset($data->cat3) && !empty($data->cat3))? $data->cat3 : '';
				$group->CC1     = (isset($data->CC1) && !empty($data->CC1))? $data->CC1 : '';
				$group->ADM1    = (isset($data->ADM1) && !empty($data->ADM1))? $data->ADM1 : '';
				$group->ADM2    = (isset($data->ADM2) && !empty($data->ADM2))? $data->ADM2 : '';
				$group->ADM3    = (isset($data->ADM3) && !empty($data->ADM3))? $data->ADM3 : '';
				$group->ADM4    = (isset($data->ADM4) && !empty($data->ADM4))? $data->ADM4 : '';
				$group->city    = (isset($data->city) && !empty($data->city))? $data->city : '';
				
				//Save in the group database				
				if($this->Groups->saveGroup($group)){

					if($this->Groups->action=='insert')
						Session::setFlash('Your group have been <strong>succefully created</strong>');
					elseif($this->Groups->action=='update')
						Session::setFlash('<strong>Update successful</strong>');

					$group_id = $this->Groups->id;

				}
				else{
					Session::setFlash('Oups... error while saving group !','error');
				}
				

				
				//If there is image files
				if(!empty($_FILES)){
	 				$this->Groups->saveGroupFiles($group_id, $slug);
	 			}

				return $this->account_view($group_id);

				
			}
			else {
				Session::setFlash('Please check your information','error');
			}
		}
		else {

			$this->account_view();
		}		


	}
}
?>