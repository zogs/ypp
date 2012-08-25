<?php 
class Manifs extends Model {

	public $table = 'manif_info';
	public $validates = array(

			'nommanif' => array(
				'rule'    => 'notEmpty',
				'message' => 'You can\'t protest for nothing , or can you ?'		
				),
			'description' => array(
				'rule' => 'notEmpty',
				'message' => "Please be a little more prolix"
				),
	);
	public $webroot_logo_directory = 'media/manif/logo';
	public $validates_files = array(
		'logo'=> array(
				'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
				'extentions_error'=> "Your logo must be an image file",
				'max_size'=>50000,
				'max_size_error'=>"Your image is too big",
				'ban_php_code'=>true			
				),
		);



	public function findManifs($req){


		$lang = $req['lang'];	

 		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){

			if(is_array($req['fields'])){
 				$sql .= implode(', ',$req['fields']);
 			}
 			else{
 				$sql .= $req['fields'];
 			}
 		}
 		else{
 			$sql .= '*';
 		}

 		$sql .= ' FROM manif_info as D 
 				JOIN manif_descr as MD ON MD.manif_id=D.id AND MD.lang="'.$lang.'"
 				LEFT JOIN users as U ON U.user_id=id_createur
 				LEFT JOIN config_category as C ON ( C.id = D.cat2 OR C.id = D.cat3)   
			    LEFT JOIN manif_tag as TM ON TM.manif_id = D.id
			    LEFT JOIN manif_tags as T ON TM.tag_id = T.tag_id
			    LEFT JOIN manif_admin as AD ON AD.manif_id=D.id AND AD.user_id ='.$req['user']['id'].'
			    LEFT JOIN association_admin as MA ON MA.user_id=U.user_id AND MA.creator=1
			    LEFT JOIN association as A ON MA.asso_id=A.asso_id	
		        LEFT JOIN world_country as WC ON WC.CC1=D.CC1
    			LEFT JOIN world_cities as City ON City.UNI=D.city AND City.LC=WC.LO		   
				LEFT JOIN (  
					      SELECT P.id, P.user_id, P.manif_id, P.date
					      FROM manif_participation as P
					      WHERE P.user_id ='. $req['user']['id'] .'
					      ) AS P ON P.manif_id = D.id
		     	';	
 		

 		$sql .= " WHERE MD.lang='".$lang."' AND "; 		 


 		//Construction des conditions de la requete sql
 		if(isset($req['conditions'])){

 			//Si les conditions sont pas un tableau , genre une requete personnelle
 			if(!is_array($req['conditions'])){
 				$sql .= $req['conditions']; 				
 			}
 			else {

 			$cond = array();
 			//On parse toutes les conditions du  tableau
	 			foreach ($req['conditions'] as $k => $v) {
	 				//On escape les valeurs
	 				if(!is_numeric($v)){ 
	 					$v = '"'.mysql_escape_string($v).'"';	 					
	 				}
	 				
	 				//On incremente le tableau avec les conditions
	 				$cond[] = "$k=$v";	 			
	 			}
	 			//Enfin on rassemble les conditions et on les ajoute à la requete sql
	 			$sql .= implode(' AND ',$cond);
 			}
 			
 		}


 		//Location
 		if(isset($req['location'])){
 			$sql .= ' AND ';
 			$cond = array();
 			foreach ($req['location'] as $k => $v) {

 				if($v!='' && $v!='NONE'){

	 				if(!is_numeric($v)){ 
	 					$v = '"'.mysql_escape_string($v).'"';	 					
	 				}
	 				$cond[] = "D.$k=$v ";
	 			}
 			}
 			$sql .= implode(' AND ',$cond);
 		}


 		//Category
 		if(isset($req['cat2']) && $req['cat2'] !='' && is_numeric($req['cat2'])) {

 			$sql .= " AND ( D.cat2=" . $req['cat2'] . " OR C.parent=" . $req['cat2'] . " ) ";
 		}
 		if(isset($req['cat3']) && $req['cat3'] !='' && is_numeric($req['cat3'])) {

 			$sql .= " AND ( D.cat3=" . $req['cat3'] . " OR C.parent=" . $req['cat3'] . " ) ";
 		}

 		//Si il y a des termes de recherche
 		if(isset($req['search']) && $req['search']!=''){

 			$rch = mysql_escape_string($req['search']);
 			 $sql .= " AND ( MD.nommanif LIKE '%" . $rch . "%' OR U.login LIKE '%" . $rch . "%' OR (T.tag LIKE '%" . $rch . "%' AND T.lang='" . $lang . "') ) ";

 		}


 		$sql .= " GROUP BY D.id";



 		//Ordre
 		if(isset($req['order'])){
 			
 			$odr = "numerus DESC";
 			if($req['order'] == 'new') $odr = "date_creation DESC";
 			if($req['order'] == 'old') $odr = "date_creation ASC";
 			if($req['order'] == 'big') $odr = "numerus DESC";
 			if($req['order'] == 'tiny') $odr = "numerus ASC";
 			$sql .= " ORDER BY ".$odr;
 		}

 		//Contruction de la LIMIT

 		if(isset($req['limit']) && $req['limit']!=''){

 			$sql .= ' LIMIT '.$req['limit'];

 		}

 		if(isset($req['end']) && $req['end'] != '') {

 			$sql .= ' '.$req['end'];
 		}
 		
 		// debug($sql);
		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);
 		
	}

	public function findManifsCount($params){
		
		$params['fields'] = ' COUNT('.$this->primaryKey.') as count FROM ( SELECT D.'.$this->primaryKey.' ';
		$params['limit'] = '';
		$params['end'] = ') as COUNT';
 		$res = $this->findFirstManif($params);
 		
 		if(!empty($res))
 			return $res->count;
 		else
 			return null;
 	}

 	public function findFirstManif($req){

 		return current($this->findManifs($req)); //current renvoi l'element courant , cest a dire le premier element dans ce cas là

 	}

 	public function findCategory($params){

 		extract($params);

 		$sql = "SELECT id," . $lang . " as name FROM config_category WHERE level > 0 ";
		if(isset($id)) {
			if($id != '' ) $sql .= " AND id='".$id."' ";
			else return false;
		} 
		
		if(isset($level) && $level != '') $sql .= " AND level='". $level. "'";
		if(isset($parent) && $parent !='') $sql .= " AND parent='" . $parent . "'";
 		$sql .= " ORDER BY '" . $lang . "'";
 		// debug($sql);
 		return $this->query($sql);
 	}

 	public function findKeywords($manif_id,$lang){

 		$sql = "SELECT * FROM manif_tag as tag LEFT JOIN manif_tags as tags ON tag.tag_id=tags.tag_id WHERE tag.manif_id=".$manif_id." AND tag.lang='".$lang."' ";

 		return $this->query($sql);

 	}

 	public function saveManif($form,$manif_id){


 		$this->saveManifInfo($form);
 		$this->saveManifDescription($form);
 		if(!empty($_FILES)){
 			$this->saveManifLogo($manif_id);
 		}

 	}

 	public function saveManifLogo($manif_id){

 		//Les vérifications sont faites dans model/validates

 		$tmp = $_FILES['inputlogo'];
 		$ext = $extention = substr(strrchr($tmp['name'], '.'),1);
 
 		$newname = 'm'.$manif_id.'.'.$ext;
 		$directory = 'media/manif/logo';
 		$destination = $directory.'/'.$newname;

 		
 		if(move_uploaded_file($tmp['tmp_name'], $destination)){
		
 			$manif = new StdClass();
 			$manif->id = $manif_id;
 			$manif->logo = $destination;

 			$this->table = 'manif_info';
 			$this->save($manif);

 		}
	

 	}

 	public function saveManifDescription($form){
 		
 		$manif = new StdClass();
 		$manif->nommanif = $form->nommanif;
 		$manif->accroche = substr($form->description,0,200).'...';
 		$manif->description = $form->description;
 		$manif->slug = str_replace(' ', '_', $form->nommanif);
 		$manif->valid = 1;
 		$manif->manif_id = $this->id;

 		$this->table = 'manif_descr';
 		$this->save($manif);

 	}

 	public function saveManifInfo($form){

 		$manif = new StdClass(); 
 		$fields = array('nommanif'=>'nom','CC1'=>'CC1','ADM1'=>'ADM1','ADM2'=>'ADM2','ADM3'=>'ADM3','ADM4'=>'ADM4','city'=>'city');			
 		foreach ($fields as $field => $sqlfield) {

 			if(isset($form->$field)){
 				$manif->$sqlfield = $form->$field;	
 			}	
 		}
 		$manif->cat2 = (isset($form->cat2) && $form->cat2 != '')? $form->cat2 : '';
 		$manif->cat3 = (isset($form->cat3) && $form->cat3 != '')? $form->cat3 : '';
 		$manif->id_createur = $this->session->user('user_id');
 		$manif->date_creation = unixToMySQL(time());

 		//debug($_FILES);

 		
 		$this->table = 'manif_info';
 		$this->save($manif);

 	}
 	//========================================================================================
 	public function findSlogan($manif_id, $lang = 'fr'){
 	//========================================================================================
 		$sql = 'SELECT content FROM manif_comment WHERE manif_id='.$manif_id.' AND type="slogan" AND lang="'.$lang.'" ORDER BY rand() LIMIT 1';

 		$res = $this->query($sql);
 		
 		if(count($res)!=0){

 			return $res[0];	 			
 		}
 		else {
 			return false;
 		}		

 	}

 	//========================================================================================
 	// Fonction findProtesters
 	// Renvoi un tableau des objets des users et participation 
 	// @params fields array()  [ les champs de users et manif_participation]
 	// @params conditions array()  [ les conditions appliqables]
 	// @params order string  ex: "field ASC" ou "field DESC"
 	// @params limit string  ex: "10", "1,10", "10,10"
 	public function findProtesters( $req ) {
 	//========================================================================================
 		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){

			if(is_array($req['fields'])){
 				$sql .= implode(', ',$req['fields']);
 			}
 			else{
 				$sql .= $req['fields'];
 			}
 		}
 		else{
 			$sql .= 'U.*, P.* ';
 		}


 		$sql .= " FROM users as U 
 					JOIN manif_participation as P ON P.user_id=U.user_id 
 					LEFT JOIN manif_info as M ON M.id = P.manif_id
 					LEFT JOIN manif_descr as D ON D.manif_id = P.manif_id AND D.lang='".$this->session->user('lang')."' 					
 					WHERE ";

 		 if(isset($req['conditions'])){
 			
 			if(!is_array($req['conditions']))
 				$sql .= $req['conditions']; 				
 			else {

 			$cond = array();
	 			foreach ($req['conditions'] as $k => $v) {
	 				if(!is_numeric($v)){ 
	 					$v = '"'.mysql_escape_string($v).'"';	 					
	 				}
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
	

} 
?>