<?php /**
* 
*/
class WorldsModel extends Model
{
	private $cacheSystem = true;


 	//Find all states in the database or the cache version
 	//@param array $ADM ['CC1'=>,'ADM1'=>...]
 	public function findAllStates($ADM){

 		$states = array();

 		$CC1 = $ADM['CC1'];
 		$parent = $CC1; 		

 		//loop in each level
 		foreach ($ADM as $level => $value) {

 					
 			if($level=='CC1'){

				$results = $this->findCountry();											
			} 
			elseif($level=='city' && $value!=''){
				
				$results = $this->findCities($CC1,$ADM);	
			}
 			elseif($value!='') {
 				$results = $this->findStates(array('CC1'=>$CC1,'level'=>$level,'parent'=>$parent));
 										 				 		
			}
			else
				continue;

			$states[$level] = $results;				
			$parent = $value;
 		}

 		return $states; 		
 	}



 	private function writeCacheVersion($location, $content){
 		
 		//If cache system is off 
 		if($this->cacheSystem == false) return false;

 		//Serialize array into string
 		$content = serialize($content);

 		//Write file into cache location
 		$this->cacheModel->write($location,$content);

 	}

 	private function findCacheVersion($location){
 		
 		//If cache system is off 
 		if($this->cacheSystem == false) return false;

 		//If cache location existe return cache version
 		if($content = $this->cacheModel->read($location)){

 			return unserialize($content);
 		}
 		else //else return false;
 			return false;
 	}


 	/*
 	* find Country list
 	* param $CC1 country code
 	*/
	public function findCountry($CC1 = ''){

		$location = 'location/CC1/country_list';

		if($cache = $this->findCacheVersion($location)){
			return $cache;
		}

 		$countries = $this->find(array(
 			'table' => "world_country",
 			'fields'=>array('CC1 as code','FULLNAME as name'),
 			'conditions'=>$CC1,
 			'order'=>'FULLNAME'
 			));

 		$result = array(
 			'lvl'=>'CC1',
 			'parent'=>'list',
 			'country'=>'country',
 			'title'=>'Select a country',
 			'list'=>$countries
 			);

 		$this->writeCacheVersion($location,$result);

 		return $result;

 	}
 	//Renvoi les regions/states/departements/cantons suivant son parent
 	//$params array $data (
 	//				CC1 => code du pays
 	//				level => niveau de la region
 	//				parent => code de la region pere
 	public function findStates($data){
;
 		extract($data);

 		//cache location
 		$location = 'location/'.$level.'/'.$CC1.'/'.$level.'_parent_'.$parent;

 		//find cache version
 		if($cache = $this->findCacheVersion($location)){
 			return $cache; //if exist return cache version
 		}
 		
 		//state query
 		$sql = "SELECT WR.ADM_CODE as code, WR.FULLNAME as name 
 				FROM world_states as WR
 				JOIN world_country as WC ON WC.CC1=WR.CC1";

	 		if ($level == 'ADM1')
			    $sql .= "	WHERE WR.CC1='" . $CC1 . "' AND WR.DSG='" . $level . "' AND WR.LC=WC.LO";
			else
			    $sql .= "	WHERE WR.CC1='" . $CC1 . "' AND WR.ADM_PARENT='" . $parent . "' AND WR.DSG='" . $level . "' AND WR.LC=WC.LO";

		$sql .= " GROUP BY WR.FULLNAME ORDER BY WR.FULLNAME";

		 //debug($sql);
 		$states = $this->query($sql);

 		$result = array(
 			'lvl'=>$level,
 			'parent'=>$parent,
 			'country'=>$CC1,
 			'title'=>'Select a state',
 			'list'=>$states
 			);

 		//write cache version
 		$this->writeCacheVersion($location,$result);

 		return $result;

 	}
 	
 	//Renvoi les villes suivant la/les regions
 	//$params string $CC1
 	//$params array $ADM (
 	//				ADM1 => code region 1
 	//				ADM2 => code region 2
 	//				ADM3 => 
 	//				ADM..=>
 	public function findCities($CC1, $ADM){

 		//reformat ADM array 
	 	$ADM = $this->formatADMArray($ADM);

	 	//set location of cache file
	 	$location = 'location/city/'.$CC1.'/city_';
	 	foreach ($ADM as $k => $v) {
	 		$location .= $k.'_'.$v.'_';
	 	}
	 	$location = substr($location, 0, -1);

	 	//if cache file exist
	 	if($cache = $this->findCacheVersion($location)){

	 		return $cache; //return cache version
	 	}

	 	//City request
 		$sql = "SELECT C.UNI as code, C.FULLNAME as name
				FROM world_cities as C	
				JOIN world_country as WC ON C.CC1=WC.CC1				
				WHERE C.CC1='".$CC1."' ";


			$sql .= " AND (";
			$cond = array();
 			foreach ($ADM as $k => $v) {

				$parent = $v;
 				$v = '"'.mysql_escape_string($v).'"';	 						 		
 				$cond[] = "C.$k=$v";

	 				 				 				 			
 			}
 			$sql .= implode(' AND ',$cond);
 			$sql .= " ) ";
			
		$sql .=" AND ( C.LC=WC.LO OR C.LC='') ORDER BY C.FULLNAME";
		
		$cities = $this->query($sql);

		$result = array(
			'lvl'=>'city',
			'parent'=>$parent,
			'country'=>$CC1,
			'title'=>'Select a city',
			'list'=>$cities
			);

		//write cache version
		$this->writeCacheVersion($location,$result);

		return $result;
 	}


 	/**
 	* SuggestCities
 	* Find city from the autocompletion
 	* params array(CC1,limit,prefix)
 	*/
 	public function suggestCities($params){

 		

 			(isset($params['CC1']))? $CC1 = $params['CC1'] : $CC1 = Conf::$pays;
 			(isset($params['limit']))? $nbResult = $params['limit'] : $nbResult = 10;
 			if(isset($params['prefix'])) $queryString = $params['prefix'];
 			else return false;

 			$sql = "SELECT DISTINCT City.UNI as city_id, City.FULLNAME as name, City.CC1, City.ADM1, City.ADM2, City.ADM3, City.ADM4 
 								FROM world_cities as City
								LEFT JOIN world_country as Pays ON Pays.CC1=City.CC1
								WHERE City.CC1='" . $CC1 . "' AND (City.LC=Pays.LO OR City.LC='') AND City.FULLNAME LIKE '" . $queryString . "%' LIMIT " . $nbResult;

			$cities = $this->query($sql);

			$array=array();
			foreach ($cities as $city) {
				
				if($city->ADM4=='') unset($city->ADM4);
				if($city->ADM3=='') unset($city->ADM3);
				if($city->ADM2=='') unset($city->ADM2);
				if($city->ADM1=='') unset($city->ADM1);

				$city = $this->JOIN_GEO($city);
				$array[] = $city;
			}
						
			return $array;

 		
 	}

 	public function formatADMArray($array){

 		//Delete first row
 		array_shift($array);

 		$i = 1;
 		$new = array();
 		//Create new array with ADM1,ADM2,ADM3... levels
 		foreach ($array as $level => $value) {

 			if($level!='city' && $value!='') {
 			$new['ADM'.$i] = $value;
 			}
 			$i++; 			
 		}

 		return $new;
 	}

 	/**
 	*	JOIN_GEO
 	* join geo informations to an object
 	* param obj OR array(objs)
 	* obj should have ( $obj->CC1, $obj->ADM1, $obj->AMD2, ...)
	*/
 	public function JOIN_GEO($object){
 		
 		//if object set new array
 		if(is_object($object)) $objs = array($object);

 		else if(is_array($object)) $objs = $object;

 		else return false;

		$res = array();
		foreach ($objs as $obj) {
				
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

		 		$res[] = $obj;
		}
		
 		if(is_object($object)) return $res[0];

 		if(is_array($object)) return $res;

 	}

} ?>