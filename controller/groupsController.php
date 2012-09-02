<?php
class GroupsController extends Controller{
	
	public function index(){

	}
	public function view($asso_id){

		$this->loadModel('Groups');
		$this->loadModel('Worlds');

		if(is_numeric($asso_id)){


			$params = array(
							'fields'=>'A.asso_id as id, A.name,A.address,A.mail,A.website,A.logo,A.official_number,A.purpose,A.description,A.lang,A.banner',
							'asso_id'=>$asso_id);

			$groups = $this->Groups->findGroups($params);
			$d['group'] = $groups[0];
			$d['group']->context = 'group';
			$d['group']->context_id = $d['group']->id;
			$d['group']->isadmin ='';

		} 
		else 
			exit('wrong id param in url');
		
		
		$this->set($d);



	}
}
?>