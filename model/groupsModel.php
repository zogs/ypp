<?php 
class Groups extends Model {


	public $primaryKey = 'group_id';

	public $validates = array(
			'create' => array(
				'name' => array(
					'rule'    => 'notEmpty',
					'message' => 'Must not be empty'		
					),
				'mail' => array(
					'rule' => '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})',
					'message' => "Email address is not correct"
					),
				'password' => array(
					'rule' => '.{5,20}',
					'message' => "Votre mot de passe doit etre entre 5 et 20 caracteres"
					),
				'confirm' => array(
					'rule' => 'confirmPassword',
					'message' => "Vos mots de passe ne sont pas identiques"
					)
			),
			'change' => array(
				'name' => array(
					'rule'    => 'notEmpty',
					'message' => 'Must not be empty'		
					),
				'mail' => array(
					'rule' => '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})',
					'message' => "Email address is not correct"
					)
				)
		);

	public $validates_files = array(
		'logo'=>array(
			'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
			'extentions_error'=>'Your logo is not an image file',
			'max_size'=>50000,
			'max_size_error'=>'Your image is too big',
			'ban_php_code'=>true
			),
		'banner'=>array(
			'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
			'extentions_error'=>'Your banner is not an image file',
			'max_size'=>50000,
			'max_size_error'=>'Your image is too big',
			'ban_php_code'=>true
			)
	);


	public function findGroups($params){

		$sql = "SELECT ";

		if(isset($params['fields'])){

			$sql .= $this->sqlFields($params['fields']);
 		}
 		else $sql .='*';

 		$sql .="
 				FROM groups as A
 				JOIN users as U ON U.user_id=A.user_id
 				";
 		if(isset($params['category']))
			$sql .= ' LEFT JOIN config_category as C ON ( C.id = A.cat2 OR C.id = A.cat3)';
		if(isset($params['location'])){
			$sql .= ' 	LEFT JOIN world_country as WC ON WC.CC1=A.pays
						LEFT JOIN world_cities as City ON City.UNI=A.ville AND City.LC=WC.LO';

		}
		
		$sql .="
 				WHERE 
 				";

 		if(isset($params['group_id'])){

 			$sql .= 'A.group_id = '.$params['group_id'];
 		}

 		if(isset($params['conditions'])){

 			$sql .= $this->sqlConditions($params['conditions']);

 		}

 		if(isset($params['location'])){

 			$sql .= $this->sqlConditions($params['location']);

 		}

 		if(isset($params['category'])){

	 		if(isset($params['category']['cat2']) && $params['category']['cat2'] !='' && is_numeric($params['category']['cat2'])) {

	 			$sql .= " AND ( A.cat2=" . $params['category']['cat2'] . " OR C.parent=" . $params['category']['cat2'] . " ) ";
	 		}
	 		if(isset($params['category']['cat3']) && $params['category']['cat3'] !='' && is_numeric($params['category']['cat3'])) {

	 			$sql .= " AND ( A.cat3=" . $params['category']['cat3'] . " OR C.parent=" . $params['category']['cat3'] . " ) ";
	 		}

 		}

 		if(isset($params['search']) && $params['search']!=''){

 			$rch = $params['search'];
 			$sql .= " AND ( A.name LIKE '%" . $rch . "%' OR A.purpose LIKE '%" . $rch . "%' OR A.description LIKE '%".$rch."%' ) ";

 		}


 		$sql .= " GROUP BY A.group_id";

 		
 		if(isset($params['order'])){
 			
 			$odr = "date DESC";
 			if($params['order'] == 'new') $odr = "date DESC";
 			if($params['order'] == 'old') $odr = "date ASC";
 			if($params['order'] == 'big') $odr = "number_bonhom DESC";
 			if($params['order'] == 'tiny') $odr = "number_bonhom ASC";
 			$sql .= " ORDER BY ".$odr;
 		}

 		if(isset($params['limit']) && $params['limit']!=''){

 			$sql .= ' LIMIT '.$params['limit'];

 		}

 		if(isset($params['end']) && $params['end'] != '') {

 			$sql .= ' '.$params['end'];
 		}

 		// debug($sql);
		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);
 		
	}

	
	public function saveGroup($group){

		if($this->save($group)) return true;
		else return false;

	}
	
	public function saveGroupFiles($group_id, $slug){

 		//Les vérifications sont faites dans model/validates

 		if($_FILES['logo']){
	 		$tmp = $_FILES['logo'];
	 		$ext = substr(strrchr($tmp['name'], '.'),1);
	 
	 		$newname = 'logo_gid'.$group_id.'_'.$slug.'.'.$ext;
	 		$directory = 'media/group/logo';
	 		$destination = $directory.'/'.$newname;

	 		
	 		if(move_uploaded_file($tmp['tmp_name'], $destination)){
			
	 			$group = new StdClass();
	 			$group->group_id = $group_id;
	 			$group->logo = $destination;
	 			$this->save($group);

	 		}
	 	}

 		if($_FILES['banner']){
	 		$tmp = $_FILES['banner'];
	 		$ext = substr(strrchr($tmp['name'], '.'),1);
	 
	 		$newname = 'banner_gid'.$group_id.'_'.$slug.'.'.$ext;
	 		$directory = 'media/group/banner';
	 		$destination = $directory.'/'.$newname;

	 		
	 		if(move_uploaded_file($tmp['tmp_name'], $destination)){
			
	 			$group = new StdClass();
	 			$group->group_id = $group_id;
	 			$group->banner = $destination;
	 			$this->save($group);

	 		}
	 	}	
 	}

 	public function isAdmin( $user , $group_id ){

 		if(is_numeric($user)){

 			$admin = $this->findFirst(array(
 				'table'=>'groups_admin',
 				'fields'=>'*',
 				'conditions'=>array('user_id'=>$user,'group_id'=>$group_id)
 				));
 			return $admin;
 		}

 	}
}
?>