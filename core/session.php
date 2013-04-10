<?php 
class Session {

	private static $controller;

	public static function init($controller){

		self::$controller = $controller;

		if(!isset($_SESSION)){
			session_start();

			if(!isset($_SESSION['token'])){
				self::setToken();
			}

			if(!isset($_SESSION['user'])){

				$user = new User();				
				self::write('user',$user);
			}	
			//destroy session if user in not from User class
			elseif(!$_SESSION['user'] instanceof User){

				if ( isset( $_COOKIE[session_name()] ) ){
					setcookie( session_name(), '', time()-3600, '/' );
				}
				$_SESSION = array();
				session_destroy();
			}	
			
		}
		
	}

	public static function setToken(){

		if(!isset($_SESSION['token'])){
			$_SESSION['token'] = md5(time()*rand(111,777));	
		}
	}

	public static function token(){

		return self::read('token');
	}

	public static function setFlash($message, $type = 'success', $duration = 0){

		$flash = array('message'=>$message,'type'=>$type,'duration'=>$duration);

		if(isset($_SESSION['flash'])){
			array_push($_SESSION['flash'],$flash);			
		}
		else {
			$_SESSION['flash'] = array($flash);
		}
		
	}

	public static function flash(){

		if(isset($_SESSION['flash'])){
			$html='';
			foreach($_SESSION['flash'] as $v){

				if(isset($v['message'])){
					$html .= '<div class="alert alert-'.$v['type'].' alert-hide-'.$v['duration'].'s">
								<button class="close" data-dismiss="alert">×</button>
								<p>'.$v['message'].'</p>
								<div class="alert-progress alert-progress-'.$v['duration'].'s"></div>
							</div>';				
				}
			}

			$_SESSION['flash'] = array();
			return $html;
		}
	}

	public static function write($key,$value){
		$_SESSION[$key] = $value;
	}


	public static function read($key = null){

		if($key){

			if(!empty($_SESSION[$key])){
				return $_SESSION[$key];		
			}			
			else{

				return false;			}
		}
		else{
			return $_SESSION;
		}
	}

	public static function role(){
		return isset($_SESSION['user']->statut); 

	}

	public static function isLogged(){
		if(self::user('user_id')!=0)
			return true;		
	}

	public static function allow($statuts){

		if(in_array(self::user('statut'),$statuts))
			return true;
		else
			self::$controller->e404('Vous n\'avez pas les droits pour voir cette page');

	}

	public static function noUserLogged(){

		$params = new stdClass();
		$params->user_id = 0;
		return $params;
	}

	// public function user($key = null){

	// 	if(self::read('user')){

	// 		if($key){

	// 			if($key=='obj'){
	// 				return self::read('user');
	// 			}

	// 			$val = trim(self::read('user')->$key);

	// 			if(!empty($val)){

	// 				return $val;
	// 			}
	// 			else 
	// 				return false;				
	// 		}	

	// 		else {
	// 			return self::isLogged();
	// 		}
	// 	}
	// 	else 
	// 	{

	// 		if( $key == 'user_id' )
	// 			return 0;
	// 		else			
	// 			return false;
	// 		if( $key == 'statut' )
	// 			return 'visitor';
	// 		else
	// 			return false;
	// 	}
	// }

	public static function user(){
		
		return self::read('user');
	}

	public static function user_id(){

		if( self::read('user') ){
			return self::user('user_id');
		}
		else return 0;
	}
	public static function getLang(){
		if(isset(self::read('user')->lang) && self::read('user')->lang!='')
			return self::read('user')->lang;		
		else 
			return Conf::$languageDefault;		
	}

	public static function getPays(){
		if(isset(self::read('user')->pays))
			return self::read('user')->pays;		
		else 
			return Conf::$pays;
	}
	

    
}

?>