<?php 
class Session {

	function __construct($controller){

		$this->controller = $controller;

		if(!isset($_SESSION)){
			session_start();

			if(!isset($_SESSION['token'])){
				$this->setToken();
			}

			if(!isset($_SESSION['user'])){
				$user = new stdClass();
				$user->user_id = 0;
				$user->avatar = 'img/logo_yp.png';
				$_SESSION['user'] = $user;
			}
			//$_SESSION['flash'] = array();
			
		}
		
	}

	public function setToken(){

		if(!isset($_SESSION['token'])){
			$_SESSION['token'] = md5(time()*rand(111,777));	
		}
	}

	public function token(){

		return $this->read('token');
	}

	public function setFlash($message, $type = 'success'){

		$flash = array('message'=>$message,'type'=>$type);

		if(isset($_SESSION['flash'])){
			array_push($_SESSION['flash'],$flash);			
		}
		else {
			$_SESSION['flash'] = array($flash);
		}
		
	}

	public function flash(){

		if(isset($_SESSION['flash'])){
			$html='';
			foreach($_SESSION['flash'] as $v){

				if(isset($v['message'])){
					$html .= '<div class="alert alert-'.$v['type'].'">
								<button class="close" data-dismiss="alert">×</button>
								<p>'.$v['message'].'</p>
							</div>';				
				}
			}

			$_SESSION['flash'] = array();
			return $html;
		}
	}

	public function write($key,$value){
		$_SESSION[$key] = $value;
	}


	public function read($key = null){


		if($key){

			if(isset($_SESSION[$key])){
				return $_SESSION[$key];		
			}			
			else{

				return false;			}
		}
		else{
			return $_SESSION;
		}
	}

	public function role(){
		return isset($_SESSION['user']->statut); 

	}

	public function isLogged(){
		if($this->user('user_id')!=0)
			return true;		
	}

	public function allow($statuts){

		if(in_array($this->user('statut'),$statuts))
			return true;
		else
			$this->controller->e404('Vous n\'avez pas les droits pour voir cette page');

	}

	public function noUserLogged(){

		$params = new stdClass();
		$params->user_id = 0;
		return $params;
	}

	public function user($key = null){
		
		if($this->read('user')){

			if($key){

				if($key=='obj'){
					return $this->read('user');
				}

				if(isset($this->read('user')->$key)){
					return $this->read('user')->$key;
				}
				else 
					return false;				
			}	

			else {
				return $this->isLogged();
			}
		}
		else 
		{

			if( $key == 'user_id' )
				return 0;
			else			
				return false;
			if( $key == 'statut' )
				return 'visitor';
			else
				return false;
		}
	}

	public function user_id(){

		if( $this->read('user') ){
			return $this->user('user_id');
		}
		else return 0;
	}
	public function getLang(){
		if(isset($this->read('user')->lang))
			return $this->read('user')->lang;		
		else 
			return Conf::$lang;		
	}

	public function getPays(){
		if(isset($this->read('user')->pays))
			return $this->read('user')->pays;		
		else 
			return Conf::$pays;
	}
}

?>