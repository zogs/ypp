<?php 
class ManifsModel extends Model {

	public $table = 'manif_info';
	public $primaryKey = 'manif_id';
	public $validates = array(

			'nommanif' => array(
				'rule'    => 'notEmpty',
				'message' => 'You can\'t protest for nothing , or can you ?'		
				),
			'description' => array(
				'rule' => 'notEmpty',
				'message' => "Could you be more... explicit?"
				),
			'lang' => array(
				'rule' => 'notEmpty',
				'message' => "Please select a language"
				),
			'logo' => array(
				'rule'=>'file',
				'params'=>array(
					'destination'=>'media/manif/logo',
					'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
					'extentions_error'=>"The logo is not a image file !",
					'max_size'=>100000,
					'max_size_error'=>"The logo is too big to be keep on our server...",
					'ban_php_code'=>true
					)
				),
			'rain'=>array(
				'rule'=>'radio','optional'=>'optional'),
			'hover'=>array(
				'rule'=>'radio','optional'=>'optional'),
			'signs'=>array(		
				'rule'=>'radio','optional'=>'optional'),
			'colored'=>array(
				'rule'=>'regex','regex'=>'(100|[0-9]{1,2})','message'=>'Must be a percentage','optional'=>'optional'),
			'bonhom'=>array(
				'rule'=>'regex','regex'=>'(^bonhom_[0-9]$)|()','message'=>'Invalid string for bonhom parameter','optional'=>'optional')

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



/*===========================================================	        
 Find all manifs that fit conditions
 @param fields array||string
 @param conditions array||string
 $param cat2, cat3 numeric
 $param order string(new,old,big,tiny)
 @param search string
 @param location array(CC1,ADM1,ADM2,ADM3,ADM4,city)
 @param limit string('10,100')
 @throws Some_Exception_Class If something interesting cannot happen
 @return array of objects
 ============================================================
 */


	public function findManifs($req){


		//User information		
		$user_id = Session::user()->getID();

		//Lang
		if(isset($req['lang']) && $req['lang']!='')
			$lang = $req['lang'];

		//Query construct
 		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){

			$sql .= $this->sqlFields($req['fields']);
 		}
 		else{
 			$sql .= ' * ';
 		}


 		$sql .= ' FROM manif_info as D 
 				JOIN manif_descr as MD ON MD.manif_id=D.manif_id
 				';
 				
	 			if((!empty($req['cat2']) && trim($req['cat2'])!='') || (!empty($req['cat3']) && trim($req['cat2'])!=''))
	 				$sql .= 'LEFT JOIN config_category as C ON ( C.id = D.cat2 OR C.id = D.cat3) ';

	 			if(!empty($req['search']))
	 				$sql .= 'LEFT JOIN manif_tag as TM ON TM.manif_id = D.manif_id LEFT JOIN manif_tags as T ON TM.tag_id = T.tag_id ';
			    
			    if(!empty($req['location']))
		        	$sql .= 'LEFT JOIN world_country as WC ON WC.CC1=D.CC1 LEFT JOIN world_cities as City ON City.UNI=D.city AND City.LC=WC.LO ';

		        if(isset($user_id)){
		        	$sql .= '
							 LEFT JOIN (  
							      SELECT P.id, P.user_id, P.manif_id as protest_in, P.date
							      FROM manif_participation as P
							      WHERE P.user_id ='. $user_id .'
							      ) AS P ON P.protest_in = D.manif_id
					     	';	
 				}

 		$sql .= ' WHERE 1=1 ';

 		//Language
 		if(!empty($lang)){
 			$sql .= ' AND MD.lang="'.$lang.'"'; 
 		}
 				 
 		//Construction des conditions de la requete sql
 		if(isset($req['conditions'])){
 			$sql .=' AND ';
 			$sql .= $this->sqlConditions($req['conditions']);
 		}

 		//Location 	
 		if(!empty($req['location'])){
 			foreach ($req['location'] as $key => $value) {
 				if(!empty($value) && trim($value)!=''){ 				 					
	 				$req['location']['D.'.$key] = $value;	 				
 				}
 				unset($req['location'][$key]);
 			}
 			$sql .=' AND ';
 			$sql .= $this->sqlConditions($req['location']);
 		}

 		//Category
 		if(isset($req['cat2']) && $req['cat2'] !='' && is_numeric($req['cat2'])) {
 			$sql .= " AND ( D.cat2=" . $req['cat2'] . " OR C.parent=" . $req['cat2'] . " ) ";
 		}
 		if(isset($req['cat3']) && $req['cat3'] !='' && is_numeric($req['cat3'])) {
 			$sql .= " AND D.cat3=" . $req['cat3'] . " ";
 		}

 		//Si il y a des termes de recherche
 		if(isset($req['search']) && $req['search']!=''){

 			$rch = mysql_escape_string($req['search']);
 			 $sql .= " AND ( MD.nommanif LIKE '%" . $rch . "%' OR (T.tag LIKE '%" . $rch . "%' AND T.lang='" . $lang . "') ) ";
 		}

 		$sql .= " GROUP BY D.manif_id";

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
 		
 		//debug($sql);
 		$results = $this->query($sql);
 		
 		if(count($results)==0) return array(new Manif());

 		$manifs = array();
 		foreach ($results as $manif) {
 			$manifs[] = new Manif($manif);
 		}

 		return $manifs;
	}

	public function findFirstManif($req){
		return current($this->findManifs($req));
	}

	public function findManifsUserIsAdmin($user_id, $lang = ''){
		
		$isadmin = $this->find(array('table'=>'manif_admin','conditions'=>array('user_id'=>$user_id)));
		$arr = array();
		foreach ($isadmin as $key => $m) {
			
			$manif = $this->findFirstManif(array('conditions'=>array('D.manif_id'=>$m->manif_id),'MD.lang'=>$lang));
			if($manif->exist()) $arr[] = $manif;
		}

		return $arr;
	}

	public function findManifByID($manif_id, $fields = array()){

		return current($this->findManifs(array('conditions'=>array('D.'.$this->primaryKey=>$manif_id),'fields'=>$fields)));
	}

	public function exist($manif_id){
		
		$m = $this->findManifByID($manif_id,array('D.'.$this->primaryKey,"D.lang",'D.administrator_id','P.id as participation_id'));
		
		if($m->exist())
			return $m;
		else
			return false;
	}

	public function findUserParticipations( $user_id ){

		$results = array();

		$sql = "SELECT * FROM manif_participation WHERE user_id = :user_id";

		$pre = $this->db->prepare($sql);
		$pre->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$pre->execute();

		return $pre->fetchAll(PDO::FETCH_OBJ);

	}

	public function JOIN_MANIF($objects,$fields = '*'){

		$unique = false;
		if(!is_array($objects)) {
			$objects = array($objects);
			$unique = true;
		}

		foreach ($objects as $obj) {
			
			if(is_object($obj)){
				if(isset($obj->manif_id)){

					$manif = $this->findManifByID($obj->manif_id,$fields);
					$obj->manif = $manif;
				}
			}
		}

		if(true === $unique)
			return $objects[0];
		else
			return $objects;		
	}

	public function JOIN_MANIF_LOGO($data){

		return $this->JOIN('manif_info','logo as logoManif',array('manif_id'=>':manif_id'),$data);
	}

	public function i18n_exist($manif_id,$lang){

		$sql = "SELECT id, lang FROM manif_descr WHERE manif_id=:manif_id AND lang=:lang";
		$val = array('manif_id'=>$manif_id,'lang'=>$lang);

		$res = $this->query($sql,$val);

		if(!empty($res))
			return $res[0];
		else
			return false;
		
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

 	public function admin_delete($manif_id){
 		$sql = 'UPDATE '.$this->table.' SET online=0 WHERE manif_id='.$id;
 		return $this->query($sql);
 	}

 	public function admin_totalProtest(){

 		$sql = 'SELECT count(*) as total FROM '.$this->table;
 		$res = $this->query($sql); 		
 		return $res[0]->total;
 	}
 	public function admin_totalOnlineProtest(){
 		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE online=1';
 		$res = $this->query($sql);
 		return $res[0]->total;
 	}
 	public function admin_totalOfflineProtest(){
 		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE online=0';
 		$res = $this->query($sql);
 		return $res[0]->total;
 	}
 	public function admin_totalModerateProtest(){
 		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE valid=0';
 		$res = $this->query($sql);
 		return $res[0]->total;
 	}


 	public function findCategory($params){

 		extract($params);
 		$values = array();
 		$sql = "SELECT id," . $lang . " as name FROM config_category WHERE 1=1 ";
		if(isset($id)) {
			if(!empty($id)){
				$sql .= " AND id=:id ";		
				$values['id'] = $id;
			}
			else return;			
		}
		
		if(!empty($level)) {
			$sql .= " AND level=:level ";
			$values['level'] = $level;
		}
		if(!empty($parent)) {
			$sql .= " AND parent=:parent ";
			$values['parent'] = $parent;
		}
 		$sql .= " ORDER BY :lang";
 		$values['lang'] = $lang;

 		return $this->query($sql,$values);
 	}

 	public function findKeywords($manif_id,$lang){

 		$sql = "SELECT * FROM manif_tag as tag LEFT JOIN manif_tags as tags ON tag.tag_id=tags.tag_id WHERE tag.manif_id=".$manif_id." AND tag.lang='".$lang."' ";

 		return $this->query($sql);

 	}
 	
 	
 	public function findSimilarProtests($manif_id, $lang){

 		$sql = 'SELECT * FROM manif_info as D 
 				JOIN manif_descr as MD ON MD.manif_id=D.manif_id
 				JOIN (SELECT manif_id, COUNT(tag_id) as num FROM manif_tag WHERE tag_id in
 						(SELECT tag_id FROM manif_tag WHERE manif_id='.$manif_id.')
 					AND manif_id<>'.$manif_id.' GROUP BY manif_id HAVING num > 0)
					as T
				ON D.manif_id = T.manif_id
				WHERE MD.lang="'.$lang.'" AND D.online=1';

		$results = $this->query($sql);

		if(!empty($results)){
			foreach ($results as $key => $result) {
				$results[$key] = new Manif($result);
			}
		}
		return $results;

 	}

 	public function findTranslates( $manif_id, $params = array() ){

 		$sql = "SELECT D.lang FROM manif_descr as D WHERE D.manif_id=:manif_id";
 		$val = array('manif_id'=>$manif_id);
 		return $this->query($sql,$val);


 	}	

 	public function saveManif($data,$manif_id){


 		$save_manif = $this->saveProtest_info($data);
 		$this->saveProtest_i18n($data,$save_manif,$data->lang);
 		$this->saveKeywords($data,$save_manif['id'],$data->lang);
 		$this->saveManifLogo($save_manif['id']);
 		$this->saveProtester(Session::user()->getID(),$save_manif['id']);
 		$this->saveAdmin(Session::user()->getID(),$save_manif['id']);

 		return $save_manif;

 	}

 	/**
 	 * save protest info
 	 * @param object $data : data from the formular
 	 * save or update if $data->manif_id	
 	 */
 	public function saveProtest_info($data){

 		$return = array();
 		$return['lang'] = $data->lang;

 		//Data to save
 		$m = new StdClass(); 
 		$m->table = 'manif_info';
 		$m->administrator_id = Session::user()->getID(); 		
 		$fields = array('nommanif'=>'nom','lang'=>'lang','CC1'=>'CC1','ADM1'=>'ADM1','ADM2'=>'ADM2','ADM3'=>'ADM3','ADM4'=>'ADM4','city'=>'city','cat2'=>'cat2','cat3'=>'cat3','comments'=>'comments','colored'=>'colored','rain'=>'rain','hover'=>'hover','signs'=>'signs','bonhoms'=>'bonhoms');			
 		foreach ($fields as $field => $sqlfield) {

 			if(isset($data->$field)){
 				$m->$sqlfield = $data->$field;	
 			}	
 		}

 		//if the protest exist
 		if(!empty($data->manif_id)){
 			$m->manif_id = $data->manif_id;
	 		$m->administrator_id = Session::user()->getID();

	 		$return['action'] = 'update';
 		}
 		else {
 			$m->date_creation = Date::MysqlNow();
 			$return['action'] = 'insert';
 		}
 		
 		if($id = $this->save($m)){

 			$return['id'] = $id; 			
 		}
 		else throw new zException("Error while saving protest :saveProtest()", 1);
 		
 		return $return;
 	}

 	/**
 	 * save traduction
 	 * @param object $data : data of the formular
 	 * @param numeric $manif_id : id of the protest
 	 * @param string $lang : lang of the data
 	 * if traduction already exist, an update is executed
 	 */
 	public function saveProtest_i18n($data, $manif, $lang){

 		//data to save
 		$m = new StdClass();
 		$m->table = 'manif_descr';
 		$m->key = 'id';
 		$m->manif_id = $manif['id']; 		
 		$m->nommanif = $data->nommanif;
 		$m->accroche = substr($data->description,0,200).'...';
 		$m->description = $data->description;
 		$m->slug = String::slugify($data->nommanif);
 		$m->lang = $lang; 	

 		//check if description exist
 		$trad_exist = $this->findFirst(array('table'=>'manif_descr','fields'=>'id','conditions'=>array('manif_id'=>$manif['id'],'lang'=>$lang)));

 		//if protest exist, save with the ID
 		if(!empty($trad_exist)){ 			
 			$m->id = $trad_exist->id;
 		}
 		else {

 			if($manif['action']=='update'){

 				Session::setFlash('A new translation have been added ! (<a href="'.Router::url("manifs/create/".$manif['id']."?lang=".$m->lang).'">'.Conf::$languageAvailable[$m->lang].'</a>)','success');
 				
 			}
 		}			

 		$this->save($m);

 	}


 	/**
 	 * save the logo 
 	 * @param numeric $manif_id 	 
 	 */
 	public function saveManifLogo($manif_id){

 		//Les vérifications sont faites dans model/validates
 		if(!empty($_FILES)){
 			
 			$newname = 'm'.$manif_id;
 			if($dest = $this->saveFile('logo',$newname)){

 				$sql = "UPDATE manif_info SET logo=:logo WHERE manif_id=:manif_id";
 				$values = array('logo'=>$dest,'manif_id'=>$manif_id);
 				$this->query($sql,$values);
 			}
 		}
	

 	}

  	/**
 	 * save the administrator
 	 * @param numeric $manif_id 	 
 	 * @param numeric $user_id
 	 * return true if admin is saved, false if already admin
 	 */	
 	public function saveAdmin($user_id,$manif_id){

 		$sql = "SELECT id FROM manif_admin WHERE manif_id=:manif_id AND user_id=:user_id";
 		$val = array('manif_id'=>$manif_id,'user_id'=>$user_id);
 		$check = $this->query($sql,$val);

 		if(empty($check[0])){

 			$sql = "INSERT INTO manif_admin VALUES ('',:manif_id,:user_id,'','')";
 			$res = $this->query($sql,$val);
 			return true;
 		}

 		return false;
 	}

 	/**
 	 * save keyword 
 	 * @param object $data : data of the formular
 	 * @param numeric $manif_id : id of the protest
 	 * @param string $lang : lang of the data
 	 * will find if a keyword exist an add reference to it
 	 * else will save new keyword and reference to it
 	 */
 	public function saveKeywords($data,$manif_id,$lang){

 		$words = preg_split("/[\s,;:-]+/", trim($data->keywords));

 		foreach ($words as $word) {
 			
 			if(trim($word)=='') continue;

 			$word = String::replaceAccents($word);

 			$check = $this->query("SELECT tag_id FROM manif_tags WHERE tag=:tag AND lang=:lang",array('tag'=>$word,'lang'=>$lang));

 			//if tag exist
 			if(!empty($check[0])){

 				$tagID = $check[0]->tag_id;
 				$check = $this->query("SELECT id from manif_tag WHERE manif_id=? AND tag_id=? AND lang=?",array($manif_id,$tagID,$lang));
 				//if this tag is not set for this protest
 				if(empty($check[0])){
 					//save it
 					$this->query("INSERT INTO manif_tag VALUES ('',?,?,?)",array($manif_id,$tagID,$lang));
 				}

 			}
 			//if tag not exist
 			else {
 				//insert the word
 				$this->query("INSERT INTO manif_tags VALUES ('',?,?)",array($word,$lang));
 				//get id
 				$tagID = $this->db->lastInsertId();
 				//insert the reference to protest
 				$this->query("INSERT INTO manif_tag VALUES ('',?,?,?)",array($manif_id,$tagID,$lang));
 			}

 		}
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
 	// @params fields array() les champs à récupérer de users U et manif_participation P
 	// @params conditions array() les conditions applicables
 	// @params order string  ex: "field ASC" ou "field DESC"
 	// @params limit string  ex: "10", "1,10", "10,10"
 	public function findProtesters( $req ) {
 	//========================================================================================
 		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){

			$sql .= $this->sqlFields($req['fields']);
 		}
 		else{
 			$sql .= 'U.*, P.* ';
 		}


 		$sql .= " FROM users as U 
 					JOIN manif_participation as P ON P.user_id=U.user_id 
 					LEFT JOIN manif_info as M ON M.manif_id = P.manif_id
 					LEFT JOIN manif_descr as D ON D.manif_id = P.manif_id AND D.lang='".Session::user()->getLang()."' 					
 					WHERE ";

 		 if(isset($req['conditions'])){
 			
 			$sql .= $this->sqlConditions($req['conditions']);
 			
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

 		if($pre->rowCount()>1){
 			return $pre->fetchAll(PDO::FETCH_OBJ);
 		}
 		else {
 			return $pre->fetch(PDO::FETCH_OBJ);
 		}

 		

 	}

 	public function removeProtesting($id){

 		$r = new stdClass();
 		$r->table = 'manif_participation';
 		$r->key = 'id';
 		$r->id = $id;

 		if($this->delete($r))
 			return true;
 		else 
 			return false;
 	}

 	public function saveProtester($user_id,$manif_id){

 		//secu
 		if(!is_numeric($user_id)&&$user_id!=0) return false;
 		if(!is_numeric($manif_id)&&$manif_id!=0) return false;

 		//check if user is already protesting
 		$check = $this->findFirst(array('table'=>'manif_participation','fields'=>array('id','user_id'),'conditions'=>array('user_id'=>$user_id,'manif_id'=>$manif_id)));
 		if(empty($check)){

 			$p = new stdClass();
 			$p->user_id = $user_id;
 			$p->manif_id = $manif_id;
 			$p->date = Date::MysqlNow();
 			$p->table = 'manif_participation';
 			$p->key = 'id';

 			if($id = $this->save($p)){

 				return $id;
 			}
 		}
 		else return 'already';

 	}

 	public function admin_moderate($manif_id){

 		$sql = 'UPDATE '.$this->table.' SET valid=0 WHERE manif_id='.$manif_id;

 		$this->query($sql);
 	}

 	public function saveNumerusStep($manif_id,$step,$user_id){

 		$s = new stdClass();
 		$s->table='manif_step';
 		$s->key = 'id';
 		$s->manif_id = $manif_id;
 		$s->user_id = $user_id;
 		$s->step = $step;
 		$s->date = Date::MysqlNow();

 		if($id = $this->save($s)){
 		
 			return $id;
 		}
 		else
 			return false;
 	}
	

}

class Manif extends Model{

	public $table = 'manif_info';
	public $primaryKey = 'manif_id';

	
	//default values
	public $manif_id         = 0;
	public $nommanif         = 'Create Protest';
	public $slug             = '';
	public $numerus          = 0;
	public $administrator_id = 0;
	public $description      = '';
	public $date_creation    = '';
	public $keywords         = '';
	public $comments         = '';
	public $logo             = '';
	public $cat2             = '';
	public $cat3             = '';
	public $CC1              = ''; 
	public $ADM1             = ''; 
	public $ADM2             = ''; 
	public $ADM3             = '';
	public $ADM4             = ''; 
	public $city             = '';
	public $colored          = 0;
	public $bonhoms          = '';
	public $rain             = 0;
	public $hover            = 0;
	public $signs            = 0;


	//Constructor
	public function __construct( $fields = array() ){

		if(!empty($fields)){

			foreach ($fields as $field => $value) {
				
				$this->$field = $value;
			}
		}		
		
		
	}

	//Getter
	public function getID(){
		return $this->manif_id;
	}
	public function getTitle(){
		return $this->nommanif;
	}
	public function getSlug(){
		return $this->slug;
	}
	public function getNumerus(){
		return $this->numerus;
	}
	public function getCreator(){
		
		if(empty($this->administrator_id) ||$this->administrator_id==0) return new User();
		$model = new UsersModel();
		$user = $model->findUserByID($this->administrator_id);		
		return $user;
	}
	public function getDescription(){
		return $this->description;
	}
	public function getCreateDate(){
		if(!empty($this->date_creation)) return $this->date_creation;
		return date('Y-m-d');
	}
	public function getLogo(){
		if(!empty($this->logo)) return $this->logo;
		else return '/img/logo_yp.png';
	}

	public function isUserProtesting(){
		if(!empty($this->participation_id) && $this->participation_id!=0)
			return true;
		return false;
	}

	public function isUserAdmin( $user_id ){
		
		if(!empty($this->administrator_id) && $this->administrator_id == $user_id )
			return true;
		return false;
	}

	public function getUniqueBonhom(){
		if(isset($this->bonhoms) && trim($this->bonhoms)!='') return $this->bonhoms;
		else return '';
	}

	public function onHoverColorBonhom(){
		if(isset($this->hover) && $this->hover==1) return 'true';
		return 'false';
	}

	public function perCentColorBonhom(){
		if(isset($this->colored) && is_numeric($this->colored)) return $this->colored;
		return 0;		
	}

	public function enableBonhomRaining(){
		if(isset($this->rain) && $this->rain==1) return 'true';
		return 'false';
	}

	public function displaySignsProtest(){
		if(isset($this->signs) && $this->signs==1) return 'true';
		return 'false';
	}

	public function userCanComment($type,$user_id){

		if($this->isUserAdmin( $user_id )) return true;
		if($this->comments=='close') return 'Comments are closed for now...';
		if($user_id===0) 	return 'You must login to comment';
		if($type=='anonym') return $this->anonCanComment();
		if($type=='pseudo') return $this->pseudoCanComment();
		if($type=='public') return $this->publicCanComment();

		return false;
	}
	private function anonCanComment(){
		return "Remember... anonym account can not comment..";
	}
	private function pseudoCanComment(){
		if($this->comments=='open') return true;
		else return 'Comments are limited to public account.';
	}
	private function publicCanComment(){
		return true;
	}

	//setter
	public function setCreatorID($user_id){
		$this->administrator_id = $user_id;
	}
	public function setCC1($CC1){
		$this->CC1 = $CC1;
	}
	public function setADM1($ADM1){
		$this->ADM1 = $ADM1;
	}
	public function setADM2($ADM2){
		$this->ADM2 = $ADM2;
	}
	public function setADM3($ADM3){
		$this->ADM3 = $ADM3;
	}
	public function setADM4($ADM4){
		$this->ADM4 = $ADM4;
	}
	public function setCity($city){
		$this->city = $city;
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

	public function exist(){
		if($this->manif_id!=0) return true;
		return false;
	}
	public function debugContent(){
		$r = '';
		$r .= $this->getTitle().'<br>';
		$r .= $this->getDescription().'<br>';
		return $r;
	}
	public function isOffline(){
		if($this->valid==0) return 'This protest have been moderated';
		if($this->online==0) return 'This protest is not online anymore';
		return false;
	}
} 
?>