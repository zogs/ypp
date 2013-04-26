<?php 
class UsersModel extends Model{

	public $primaryKey = 'user_id';

	public $validates = array(
		'register' => array(
			'login' => array(
				'rule'    => 'notEmpty',
				'message' => 'Vous devez choisir un pseudo'		
				),
			'email' => array(
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
				),
			'prenom' => array(
				'rule'=> 'optionnal',
				),
			'nom' => array(
				'rule' => 'optionnal',
				),
			'age' => array(
				'rules'=> array(
							array(
								'rule'=> '19[0-9]{1}[0-9]{1}',
								'message'=> "Between 1900 and 1999..."
							),
							array(
								'rule'=>'optionnal',
								'message'=>''
							)
						)
				)
		),
		'account_info' => array(
			'login' => array(
				'rules'=> array(
							array(
								'rule' => 'notEmpty',
								'message' => 'Your login is empty'
									),
							array('rule' => '.{5,20}',
								'message' => 'Login between 5 and 20 caracters'
								)
							)
				),
			'email' => array(
				'rule' => '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})',
				'message' => "L'adresse email n'est pas valide"
				)
		),
		'account_profil' => array(
			
		),
		'recovery_mdp' => array(
			'password' => array(
				'rule' => '.{5,20}',
				'message' => "Votre mot de passe doit etre entre 5 et 20 caracteres"
				),
			'confirm' => array(
				'rule' => 'confirmPassword',
				'message' => "Vos mots de passe ne sont pas identiques"
				)
		),
		'account_password' => array(
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
			
	public function saveUser($user,$user_id = null){

		//unset action value
		if(isset($user->action)) unset($user->action);
		//set user_id for updating
		if(isset($user_id)) $user->user_id = $user_id;
		//if there is nothing to save
		$tab = (array) $user;
		if( (count($tab)==0 ) || (count($tab)==1 && isset($tab['user_id'])))
			return true;

		//if new user , check unique value
		if(!isset($user->user_id)){

			//check if login already exist
			$check = $this->findFirst(array(
											'fields'=>'user_id',
											'conditions'=>array('login'=>$user->login)
											));
			if(!empty($check)){
				Session::setFlash("Sorry this login is in use.","error");
				return false;
			}

			//check if mail already in use
			$checkmail = $this->findFirst(array(
												'fields'=>'mail',
												'conditions'=>array('mail'=>$user->mail)
												));
			if(!empty($checkmail)){
				Session::setFlash("This email is already in use. Please try to recovery your password","error");
				return false;
			}
		}		

		if($this->save($user))
			return true;
		else
			return false;

	}

	public function saveUserAvatar($user_id){

 		//Les vérifications sont faites dans model/validates

 		$tmp = $_FILES['avatar'];
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

	public function updateLastConnexion($user_id){
		$sql = 'UPDATE '.$this->table.' SET last_visite = NOW() WHERE user_id='.$user_id;
		$this->query($sql);
	}


	public function admin_avertUser($user_id){

		//secu
		if(!Session::user()->isLog() || !Session::user()->isSuperAdmin()) $this->redirect('users/login');
		$this->increment(array('field'=>'avert','id'=>$user_id));
	}

	public function admin_totalUsers(){
		$sql = 'SELECT count(*) as total FROM '.$this->table;
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalTodayRegistration(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE date >= CURDATE()';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalWeekRegistration(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE Date > NOW() - INTERVAL 1 WEEK';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalMonthRegistration(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE Date > NOW() - INTERVAL 1 MONTH';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalTodayConnexion(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE last_visite > NOW() - INTERVAL 1 DAY';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalWeekConnexion(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE last_visite > NOW() - INTERVAL 1 WEEK';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalMonthConnexion(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE last_visite > NOW() - INTERVAL 1 MONTH';
		$res = $this->query($sql);
		return $res[0]->total;
	}

	public function findUsers($req){

		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){
			if(is_array($req['fields']))
 				$sql .= implode(', ',$req['fields']);
 			else
 				$sql .= $req['fields'];
 		}
 		else $sql .= '*';


 		$sql .= " FROM users									
 					WHERE ";


 		 if(isset($req['conditions'])){ 			
 			if(!is_array($req['conditions']))
 				$sql .= $req['conditions']; 				
 			else {
	 			$sql .= $this->sqlConditions($req['conditions']);
 			}
 			
 		}

 		if(isset($req['order'])){
 			if($req['order'] == 'random') $sql .= ' ORDER BY rand()';
 			else $sql .= ' ORDER BY '.$req['order'];
 		}

 		if(isset($req['limit'])){
			$sql .= ' LIMIT '.$req['limit'];
 		}

 		$results = $this->query($sql); 		

 		$users = array();

 		//if no result , set a empty user
 		 if(empty($results)) $users[] = new User();	

 		//contruct array of users 
 		foreach ($results as $user) {
 	
 			$user = new User($user);
 			$user = $this->JOIN('groups',array('group_id','logo as avatar','slug'),array('user_id'=>$user->getID()),$user); 		
 			$users[] = $user;
 		}
 		 
 		return $users;
	}

	public function findFirstUser($req){

		return current($this->findUsers($req));
	}

	public function findUserByID($user_id, $fields = '*'){

		return current($this->findUsers(array('fields'=>$fields,'conditions'=>array($this->primaryKey=>$user_id))));
	}

	public function findParticipations( $fields, $user_ids ){

		$results = array();

		$sql = "SELECT $fields FROM manif_participation as P 
				LEFT JOIN manif_info as M ON M.manif_id = P.manif_id
				LEFT JOIN manif_descr as D ON D.manif_id = P.manif_id AND D.lang='".Session::user()->getLang()."'
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
	                 D.name as relatedName,
	                 D.manif_id as relatedID,
	                 D.slug     as relatedSlug,
	                 I.logo     as relatedLogo

        		FROM manif_participation as P
        		LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".Session::user()->getLang()."'	
        		LEFT JOIN manif_info as I ON I.manif_id=P.manif_id
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
                  	D.name as relatedName,
                  	D.manif_id as relatedID,
                  	D.slug     as relatedSlug,
                  	I.logo     as relatedLogo
              	FROM manif_comment AS C
              	LEFT JOIN manif_participation AS P ON P.user_id = $user_id
              	LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".Session::user()->getLang()."'
              	LEFT JOIN manif_info as I ON I.manif_id=P.manif_id	
             	WHERE C.context_id = P.manif_id AND C.context='manif' AND C.type='news'
			ORDER BY date DESC
			";


		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);

	}

	
}


class User {


	public $user_id = 0;
	public $login   = 'Unknow';
	public $status  = 'visitor';
	public $avatar  = 'img/logo_yp.png';
	public $bonhom  = '';
	public $account = 'visitor';

	public function __construct( $fields = array() ){

		foreach ($fields as $field => $value) {
			
			$this->$field = $value;
		}
	}

	public function getID(){

		return $this->user_id;
	}

	public function getLogin(){
		
		if($this->account=='anonym') return 'anonym_'.$this->user_id;
		else return $this->login;
	}

	public function getLinkedLogin(){

		if($this->account=='anonym') return 'anonym_'.$this->user_id;
		if(Session::user()->isLog() && $this->user_id==Session::user()->getID()) return '<a class="userLink currentUser" href="'.Router::url('users/view/'.$this->user_id).'" >You</a>';
		return '<a class="userLink" href="'.Router::url('users/view/'.$this->user_id).'" >'.$this->login.'</a>';
	}

	public function getAvatar(){

		if(isset($this->avatar)&&!empty($this->avatar)) return $this->avatar;
		else return 'img/bonhom/'.$this->bonhom.'.gif';
	}

	public function getBonhom(){

		return $this->bonhom;
	}

	public function getRole(){
		return $this->account;
	}

	public function getFullName(){

		return $this->prenom.' '.$this->nom;
	}

	public function getAge(){
		if(!empty($this->age)) return date('Y') - $this->age;
	}

	public function isLog(){
		if($this->user_id!==0) return true;
	}

	public function setLang($lang){
		$this->lang = $lang;
	}

	public function getLang(){
		if(!empty($this->lang)) return $this->lang;
		return false;		
	}

	public function getFullLocateArray(){
		$loc = array();
		if(!empty($this->CC1)) $loc['CC1'] = $this->CC1;
		if(!empty($this->ADM1)) $loc['ADM1'] = $this->ADM1;
		if(!empty($this->ADM2)) $loc['ADM2'] = $this->ADM2;
		if(!empty($this->ADM3)) $loc['ADM3'] = $this->ADM3;
		if(!empty($this->ADM4)) $loc['ADM4'] = $this->ADM4;

		return $loc;
	}

	public function getFullLocateString(){
		$arr = $this->getFullLocateArray();
		$arr = array_values($arr);
		if(!empty($arr)) return implode(', ',$arr);
		return '';
	}

	public function isSuperAdmin(){

		if($this->status=='admin') return true;
		else return false;
	}

	public function exist(){

		if($this->user_id==0) return false;
		return true;
	}


}
 ?>