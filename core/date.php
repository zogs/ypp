<?php 
/**
 * GESTION DES DATES
 */
 class Date
 {
 	private $Session;

 	public function __construct($session)
 	{
 		$this->session = $session;
 	}

 	public function SQLNow(){

 		return unixToMySQL(time());
 	}

 	public function month($num){

 		$array = array('fr' => array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre')
 				);

 		return $array[$this->session->getLang()][$num - 1];

 	}
 } ?>