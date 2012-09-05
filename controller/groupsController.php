<?php
class GroupsController extends Controller{
	
	public function index(){

	}
	public function view($asso_id){

		$this->loadModel('Groups');
		$this->loadModel('Worlds');

		if(is_numeric($asso_id)){


			$params = array(
							'fields'=>'A.asso_id as id, A.name,A.address,A.mail,A.website,A.logo,A.official_number,A.purpose,A.motsclefs,A.description,A.lang,A.banner,A.CC1,A.ADM1,A.ADM2,A.ADM3,A.ADM4,A.city',
							'asso_id'=>$asso_id);

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
}
?>