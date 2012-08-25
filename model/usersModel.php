<?php 
class Users extends Model{

	public $primaryKey = 'user_id';

	public $validates = array(
		'register' => array(
			'login' => array(
				'rule'    => 'notEmpty',
				'message' => 'Vous devez choisir un pseudo'		
				),
			'mail' => array(
				'rule' => '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})',
				'message' => "L'adresse email n'est pas valide"
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
		'account_info' => array(
			'login' => array(
				'rule'    => 'notEmpty',
				'message' => 'Vous devez choisir un pseudo'		
				),
			'mail' => array(
				'rule' => '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})',
				'message' => "L'adresse email n'est pas valide"
				)
		),
		'account_mdp' => array(
			'oldpassword' => array(
				'rule' => 'notEmpty',
				'message' => "Votre mot de passe doit contenir entre 5 et 20 caracteres"
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
		'account_delete' => array(
			'password' => array(
				'rule' => 'notEmpty',
				'message' => "Enter your password"
				)
		),
		'account_avatar'=>array(),
	);

	public $validates_files = array(
		'avatar'=>array(
			'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
			'extentions_error'=>'Your avatar is not an image file',
			'max_size'=>50000,
			'max_size_error'=>'Your image is too big',
			'ban_php_code'=>true
			)
	);


	public function saveUser($data,$user_id){


		$data->user_id = $user_id;
		$data->modify = '';

		if($this->save($data))
			return true;
		else
			return false;

	}

	public function saveUserAvatar($user_id){

 		//Les vérifications sont faites dans model/validates

 		$tmp = $_FILES['inputavatar'];
 		$ext = $extention = substr(strrchr($tmp['name'], '.'),1);
 
 		$newname = 'u'.$user_id.'.'.$ext;
 		$directory = 'media/user/avatar';
 		$destination = $directory.'/'.$newname;

 		
 		if(move_uploaded_file($tmp['tmp_name'], $destination)){
		
 			$user = new StdClass();
 			$user->user_id = $user_id;
 			$user->avatar = $destination;

 			$this->table = 'users';
 			if($this->save($user)){
 				return true;
 			}
 		}
 		else return false;

	}

	public function findUsers($req){

		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){
			if(is_array($req['fields']))
 				$sql .= implode(', ',$req['fields']);
 			else
 				$sql .= $req['fields'];
 		}
 		else $sql .= 'U.*, P.* ';


 		$sql .= " FROM users									
 					WHERE ";


 		 if(isset($req['conditions'])){ 			
 			if(!is_array($req['conditions']))
 				$sql .= $req['conditions']; 				
 			else {
	 			$cond = array();
		 			foreach ($req['conditions'] as $k => $v) {
		 				if(!is_numeric($v))
		 					$v = '"'.mysql_escape_string($v).'"';	 							 				
		 				$cond[] = "$k=$v";	 			
		 			}
		 			$sql .= implode(' AND ',$cond);
 			}
 			
 		}

 		if(isset($req['order'])){
 			if($req['order'] == 'random') $sql .= ' ORDER BY rand()';
 			else $sql .= ' ORDER BY '.$req['order'];
 		}

 		if(isset($req['limit'])){
			$sql .= ' LIMIT '.$req['limit'];
 		}

 		  // debug($sql);

 		$pre = $this->db->prepare($sql);
 		$pre->execute();

 		return $pre->fetchAll(PDO::FETCH_OBJ);
	}

	public function findParticipations( $fields, $user_ids ){

		$results = array();

		$sql = "SELECT $fields FROM manif_participation as P 
				LEFT JOIN manif_info as M ON M.id = P.manif_id
				LEFT JOIN manif_descr as D ON D.manif_id = P.manif_id AND D.lang='".$this->session->user('lang')."'
				WHERE user_id = :user_id";

		foreach ($user_ids as $user_id) {

			$pre = $this->db->prepare($sql);
			$pre->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$pre->execute();

			return $pre->fetchAll(PDO::FETCH_OBJ);
			# code...
		}


	}

	public function findUserThread($user_id){


		$sql=" SELECT P.id,
	                 'PROTEST' AS TYPE,
	                 P.date,	                 
	                 D.nommanif as relatedName,
	                 D.manif_id as relatedID,
	                 D.slug     as relatedSlug,
	                 I.logo     as relatedLogo

        		FROM manif_participation as P
        		LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".$this->session->user('lang')."'	
        		LEFT JOIN manif_info as I ON I.id=P.manif_id
           		WHERE P.user_id = $user_id
      		UNION
        --   	SELECT `id`,
	       --           'COMMENT' AS TYPE,
	       --           `date`
        --     	FROM `manif_comment`
	       --     	WHERE user_id = $user_id AND reply_to=0
      		-- UNION
       		SELECT C.id,
                	'NEWS' AS TYPE,
                  	C.date,
                  	D.nommanif as relatedName,
                  	D.manif_id as relatedID,
                  	D.slug     as relatedSlug,
                  	I.logo     as relatedLogo
              	FROM manif_comment AS C
              	LEFT JOIN manif_participation AS P ON P.user_id = $user_id
              	LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".$this->session->user('lang')."'
              	LEFT JOIN manif_info as I ON I.id=P.manif_id	
             	WHERE C.manif_id = P.manif_id AND C.type='news'
			ORDER BY date DESC
			";


		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);

	}

	
}
 ?>