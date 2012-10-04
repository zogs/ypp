<?php /**
* 
*/
class Worlds extends Model
{
	

	public function findCountry($CC1 = ''){

		$this->table = "world_country";

 		return $this->find(array(
 			'fields'=>array('FULLNAME as name','CC1 as code'),
 			'conditions'=>$CC1,
 			'order'=>'FULLNAME'
 			));


 	}

 	public function findAllStates($ADM){

 		$states = array();

 		foreach ($ADM as $key => $value) {
			if($key=='CC1'){
				$CC1 = $value;
				$states[$key] = $this->findCountry();
			}
			elseif($key=='ADM1') {

				
				$states[$key] = $this->findStates(array('CC1'=>$CC1,'ADM'=>$key,'ADM_PARENT'=>'useless'));
 				
 			}
 			elseif($key=='city' && $value != 0){

 				$states['city'] = $this->findCities($CC1,$this->formatADMArray($ADM) );

 			} 
 			else {
 						 				
	 				$states[$key] = $this->findStates(array('CC1'=>$CC1,'ADM'=>$key,'ADM_PARENT'=>$parent));
 				 			
			}

			$parent = $value;

 		}

 		return $states; 	

 		
 	}

 	//Renvoi les regions/states/departements/cantons suivant son parent
 	//$params array $data (
 	//				CC1 => code du pays
 	//				ADM => niveau de la region
 	//				ADM_PARENT => code de la region pere
 	public function findStates($data){

 		extract($data);

 		$sql = "SELECT WR.FULLNAME as name, WR.ADM_CODE as code
 				FROM world_states as WR
 				JOIN world_country as WC ON WC.CC1=WR.CC1";


 		if ($ADM == 'ADM1')
		    $sql .= "	WHERE WR.CC1='" . $CC1 . "' AND WR.DSG='" . $ADM . "' AND WR.LC=WC.LO";
		else
		    $sql .= "	WHERE WR.CC1='" . $CC1 . "' AND WR.ADM_PARENT='" . $ADM_PARENT . "' AND WR.DSG='" . $ADM . "' AND WR.LC=WC.LO";


		$sql .= " GROUP BY WR.FULLNAME ORDER BY WR.FULLNAME";

		 //debug($sql);
 		return $this->query($sql);

 	}
 	
 	//Renvoi les villes suivant la/les regions
 	//$params string $CC1
 	//$params array $ADM (
 	//				ADM1 => code region 1
 	//				ADM2 => code region 2
 	//				ADM3 => 
 	//				ADM..=>
 	public function findCities($CC1, $ADM){

	 	extract($ADM);
 		$sql = "SELECT C.FULLNAME as name, C.UNI as code
				FROM world_cities as C	
				JOIN world_country as WC ON C.CC1=WC.CC1				
				WHERE C.CC1='".$CC1."' ";


			$sql .= " AND (";
			$cond = array();
 			foreach ($ADM as $k => $v) {
 				if(isset($v) && $v != ''){
	 				$v = '"'.mysql_escape_string($v).'"';	 						 		
	 				$cond[] = "C.$k=$v";
	 			}	 				 				 			
 			}
 			$sql .= implode(' AND ',$cond);
 			$sql .= " ) ";
			
		$sql .=" AND ( C.LC=WC.LO OR C.LC='') ORDER BY C.FULLNAME";
		//debug($sql);
		return $this->query($sql);
 	}

 	public function formatADMArray($array){

 		//Delete first row
 		array_shift($array);

 		$i = 1;
 		$new = array();
 		//Create new array with ADM1,ADM2,ADM3... keys
 		foreach ($array as $key => $value) {

 			if($key!='city') {
 			$new['ADM'.$i] = $value;
 			}
 			$i++; 			
 		}

 		return $new;
 	}

 	public function JOIN_GEO($obj){
 		
 		

 		if(isset($obj->ADM1)){

 			$obj = $this->JOIN('world_states','FULLNAMEND as ADM1',array(
																				'CC1'=>$obj->CC1,
																				'DSG'=>'ADM1',
																				'ADM_CODE'=>$obj->ADM1,
																				'NT'=>'N'

																			),$obj
							);
 		}

 		if(isset($obj->ADM2)){

 			$obj = $this->JOIN('world_states','FULLNAMEND as ADM2',array(
																				'CC1'=>$obj->CC1,
																				'DSG'=>'ADM2',
																				'ADM_CODE'=>$obj->ADM2,
																				'NT'=>'N'

																			),$obj
							);
 		}

 		if(isset($obj->ADM3)){

 			$obj = $this->JOIN('world_states','FULLNAMEND as ADM2',array(
																				'CC1'=>$obj->CC1,
																				'DSG'=>'ADM3',
																				'ADM_CODE'=>$obj->ADM3,
																				'NT'=>'N'

																			),$obj
							);
 		}

 		if(isset($obj->ADM4)){

 			$obj = $this->JOIN('world_states','FULLNAMEND as ADM4',array(
																				'CC1'=>$obj->CC1,
																				'DSG'=>'ADM4',
																				'ADM_CODE'=>$obj->ADM4,
																				'NT'=>'N'

																			),$obj
							);
 		}

 		if(isset($obj->city) && $obj->city!=0 && !empty($obj->city)){

 			$obj = $this->JOIN('world_cities','FULLNAMEND as city',array('UNI'=>$obj->city),$obj);
 		}
 		if(isset($obj->CC1)){

 			$obj = $this->JOIN('world_country','FULLNAME as CC1',array('CC1'=>$obj->CC1),$obj);
 		}

 		return $obj;
 	}

} ?>