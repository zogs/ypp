<?php

class Request{

	public $url; //url appelé par l'utilisateur
	public $page = 1; //Pagination
	public $prefix =false; //Prefixes
	public $data = false; //Donnees de formulaire

	//Permet de récupérer la requete url demandé
	function __construct() {
		
		$this->url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
		//$this->url = str_replace(BASE_URL."/", "", $_SERVER['REQUEST_URI']); //Recuperation du PATH_INFO 
		

		//Récuperation des données GET dans un objet
		if(!empty($_GET)){

			$this->get = new stdClass();
			foreach ($_GET as $k => $v) {
				if($k!='_')
					$this->get->$k = $v;
			}						

		}
		
		if(!isset($this->get->page) || $this->get->page <=0 ){
			 $this->page = 1;
		}
		else {
			$this->page = round($this->get->page);
		}


		//Récuperation des données POST dans un objet
		if(!empty($_POST)){
			$this->data = new stdClass();
			foreach ($_POST as $k => $v) {

				$this->data->$k = $v;
			}

		}

	}

	//Renvoi le parametre GET demandé ou renvoi false
	public function get($param = null){	

		if($param){	
			if(isset($this->get->$param) && $this->get->$param != ''){
				return $this->get->$param;
			}
			else return false;
		}
		else {
			if(!empty($_GET)){
				return $this->get;
			} 
			else {
				return false;
			}
		} 
	}

	public function post($param = null){

		if($param){
			if(isset($this->data->$param)){
				return $this->data->$param;
			}
			else return false;
		}
		else {
			if(!empty($_POST)){
				return $this->data;
			}
			else {
				return false;
			}
		}
	}

	public function parse_url($_url = null){
    
	    try{
			if($_url===null) $_url = $this->url;

			$parsed = parse_url($_url);

			$arr = array();
			$arr['protocol'] = $parsed['scheme'].'://';
			$arr['www'] = '';
			if(strpos($parsed['host'],'www.')===0){
				$arr['www'] = 'www.';
				$parsed['host'] = str_replace('www.','',$parsed['host']);
			}
			$host = explode('.',$parsed['host']);
			$ln = count($host);
			$arr['extension'] = $host[$ln-1];
			$arr['domain'] = $host[$ln-2];
			$arr['subdomain'] = ($ln>2)? $host[$ln-3].'.' : '';
			$arr['path'] = $parsed['path'];
			$arr['all'] = $arr['protocol'].$arr['www'].$arr['subdomain'].$arr['domain'].'.'.$arr['extension'].$arr['path'];

			return $arr;
			}
	    catch(Exception $e){
	    	return false;
	    }
	}

	public function get_client_language($availableLanguages){
     
	    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	     
		    $langs=explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		     
		    //start going through each one
		    foreach ($langs as $value){
		     
			    $choice=substr($value,0,2);
			    if(in_array($choice, $availableLanguages)){
			    	return $choice;
			     
			    }
		     
		    }
	    }
	    return false;
    }
}