<?php

class Controller {

	public $request;	
	public $layout      = 'default';
	public $view        = 'default';
	private $rendered   = false;
	private $vars       = array();

	function __construct($request=null){

		$this->session = new Session($this);	
		$this->Form = new Form($this);
		$this->Date = new Date($this->session);
		

		if($request){
			$this->request = $request; //ON stocke la request dans l'instance
			require ROOT.DS.'config'.DS.'hook.php'; //Systeme de hook pour changer le layer en fonction du prefixe
		}

		
	}


	// Permet de rendre le resultat du controller
	// @params view : la page a rendre

	public function render($view){
		
		//Si la page a deja ete rendu on stop la function
		if($this->rendered) return false;

		//On extrait les variables de la méthode appelée
		extract($this->vars);

		
		//Si la vue par default
		if( $this->view == 'default') {
			//Si la page a rendre commence par un '/ '
			if(strpos($view,'/')===0){
				$view = ROOT.DS.'view'.$view.'.php'; //On rend la page demandé
			} else {
				$view = ROOT.DS.'view'.DS.$this->request->controller.DS.$view.'.php'; //Sinon on utlise le systeme MVC
			}
		}
		else { //Sinon une vue personnalisée est appelée
			$view = ROOT.DS.'view'.DS.$this->view.'.php';
		}

		
		//Recuperation des données du viewer
		ob_start();
		require $view;
		$content_for_layout = ob_get_clean();
		require ROOT.DS.'view'.DS.'layout'.DS.$this->layout.'.php';


		//La page a été rendu
		$this->rendered = true;
	
	}

	//Permet de créer une variable du controller
	//@params $key : nom de la variable
	//@params $value : valeur de la variable

	public function set($key,$value=null){
		if(is_array($key)) {
			$this->vars += $key;
		}
		else {
			$this->vars[$key] = $value;
		}
	}


	//Permet de charger un model
	public function loadModel($name){
 
		$file = ROOT.DS.'model'.DS.$name.'Model.php';
		require_once($file);
		if(!isset($this->$name)) {
			$this->$name = new $name();

			if(isset($this->Form)){
				$this->$name->Form = $this->Form;	
			}

			if(isset($this->session)){
				$this->$name->session = $this->session;
			}
		}
		else {
			//echo 'Model '.$name.' deja chargé';
		}
		
	}


	public function e404($message, $oups = 'Oups'){

		header("HTTP/1.0 404 Not Found"); 
		$this->set('message',$message);
		$this->set('oups',$oups);
		$this->render('/errors/404');
		
		die();
		
	}


	//Permet d'appeler un controller depuis une vue
	// @params 
	// $controller prefixe du Controller .ex: users
	// $action action à appeler
	public function request($controller,$action){

		$controller .= 'Controller';
		require_once ROOT.DS.'controller'.DS.$controller.'.php';
		$c = new $controller;
		return $c->$action();
	}

	public function redirect($url,$code = null){

		if($code == 301) {
			header("HTTP/1.1 301 Moved Permanently");
		}
		header("Location: ".Router::url($url));
	}

	public function getCountryCode(){


		if($this->session->getPays()){
			return $this->session->getPays();
		}
		else if($this->CookieRch->read('CC1')){
			return $this->CookieRch->read('CC1');
		}
		else {
			return Conf::$pays;
		}
	}

	public function getLanguage(){


		if($this->session->getLang()){
			return $this->session->getLang();
		}
		else if($this->CookieRch->read('lang')){
			return $this->CookieRch->read('lang');
		}
		else {
			return Conf::$lang;
		}
	}


}
?>