<?php 


/**
* 
*/
class WorldController extends Controller
{
	
	public function getCountry(){

		$this->loadModel('Worlds');
		return $this->Worlds->findCountry();
		
	}


	public function getStates(){

 		$this->loadModel('Worlds');
 		$this->layout = 'none';
 		 
 		$ADM = $this->request->get('ADM'); 		
 		$ADM1 = '';
 		$ADM2 = '';
 		$ADM3 = '';
 		$ADM4 = '';
 		$ADM_PARENT = '';

 		if($ADM == 'CC1') $ADM = 'ADM1';
 		elseif($ADM == 'ADM1') $ADM = 'ADM2';
 		elseif($ADM == 'ADM2') $ADM = 'ADM3';
 		elseif($ADM == 'ADM3') $ADM = 'ADM4';
 		elseif($ADM == 'ADM4') $ADM = 'city';

 		

 		//Recuperation des parametres
 		if($this->request->get('CC1')){ 			
 			$CC1 = $this->request->get('CC1');
 		} 		
 		else { 
 			$CC1 = $this->getCountryCode();
 		}
 		if($this->request->get('ADM1'))	{
 			$ADM1 = $this->request->get('ADM1');
 			$ADM_PARENT = $this->request->get('ADM1');		
 		}		
 		if($this->request->get('ADM2'))	{
 			$ADM2 = $this->request->get('ADM2');
 			$ADM_PARENT = $this->request->get('ADM2');
 		}
 		if($this->request->get('ADM3'))	{
 			$ADM3 = $this->request->get('ADM3');
			$ADM_PARENT = $this->request->get('ADM3');
 		}		
 		if($this->request->get('ADM4'))	{
 			$ADM4 = $this->request->get('ADM4');
 			$ADM_PARENT = $this->request->get('ADM4');
 		}	

 		//debug('CC1:'.$CC1. ' ADM:'.$ADM.' ADM_PARENT:'.$ADM_PARENT);
		
 		//Find the states in the database , using the GET data 		
 		$d['states'] = $this->Worlds->findStates(array(
										 			'CC1'=>$CC1,
										 			'ADM_PARENT'=>$ADM_PARENT,
										 			'ADM'=>$ADM
 			));
 		
 		//If there is no states, we look for a city
 		if(empty($d['states'])) {

 			$ADM = 'city';
 			$d['states'] = $this->Worlds->findCities($CC1,array(									 				
									 				'ADM1'=>$ADM1,
									 				'ADM2'=>$ADM2,
									 				'ADM3'=>$ADM3,
									 				'ADM4'=>$ADM4
			));
 		}
		
 		//If there is no city at all we are done
 		if(empty($d['states'])) {

 			$d['states'] = false;
 		}


		//Instanciation de la variable du niveau d'ADM
 		$d['ADM'] = $ADM;
 		
 		//debug($d);
 		$this->set($d);

 	}
} 

?>