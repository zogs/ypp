<?php
 class Model {

 	static $connections = array();
 	public $conf = 'default'; //Base de donnée à utiliser
 	public $table = false; //Table mysql à utiliser par le model
 	public $db = false; //Connexion courante à la base
 	public $primaryKey = 'id'; //Nom de la clef primaire de la table
 	public $id; //valeur de la clef courante
 	public $action;
 	public $errors = array(); // tableaux des erreurs à afficher



 	public function __construct() {

 		//Initialisation de la table sql du model
		if($this->table===false){
 			$this->table = strtolower(get_class($this));
 		}

 		//Initalisation de la base sql a utilsier
 		$conf = Conf::$databases[$this->conf];

 		//Creation de la connexion a la base de donnée
 		//Si la connexion existe déja, return true
 		if(isset($this->connections[$this->conf])){
 			$this->db = Model::$connections[$this->conf];
 			return true;	
 		} 
 		
 		//Cache
 		$this->cacheModel = new Cache(Conf::$cacheLocation,60*24*7);


 		//On essaye de se connecter a la base
 		try{
 			//Creation d'un objet PDO
 			$pdo = new PDO('mysql:host='.$conf['host'].';dbname='.$conf['database'].';',
 				$conf['login'],
 				$conf['password'],
 				array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::MYSQL_ATTR_INIT_COMMAND => $this->setTimeZone() ) //Important pour l'encode des carateres
 				);
 			$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING); //Important pour afficher les erreurs
 			Model::$connections[$this->conf] = $pdo; //Attribution de la connexion a une varaible static
 			$this->db = $pdo;	 //Attribution de la connexion a une varaible 

 			$this->db;

 			
 		}
 		//Si connexion echoue
 		catch (PDOException $e){
 			//En mode debug on affiche les erreurs
 			if(Conf::$debug >=1 ){
 				die($e->getMessage());
 			}
 			//En production on affiche une erreur simple
 			else{
 				die('Impossible de se connecter a la base de donnees');
 			}
 		}

 		
 	}

 	public function setTimeZone(){

		$now = new DateTime();
		$mins = $now->getOffset() / 60;

		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;

		$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

		return "SET time_zone='$offset'";
 	}

 	// ============================
 	// Permet de recuperer des champs de la base de donné à partir d'une requete
 	// @params $req array('conditions'=>array()) Tableaux des conditions de la requete

 	public function find($req)
 	{
 		//Si la table nest pas precisé on prend la table par default du model
 		if(!isset($req['table']))
 			$table = $this->table.' as '.get_class($this);
 		else
 			$table = $req['table'];

 		//requete
 		$sql = 'SELECT ';
 		
 		//champ à récuper
 		if(isset($req['fields']))
 			$sql .= $this->sqlFields($req['fields']);
 		else
 			$sql .= '*';

 		//depuis la table
 		$sql .= ' FROM '.$table.' ';

 		//conditions de la requete sql
 		if(isset($req['conditions']) && $req['conditions'] != ''){

 			$sql .= ' WHERE ';

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


 		//Contruction de l'ordre
 		if(isset($req['order'])){

 			$sql .= ' ORDER BY '.$req['order'];
 		}
 		//Contruction de la LIMIT
 		if(isset($req['limit'])){

 			$sql .= ' LIMIT '.$req['limit'];

 		}
 		
 		// debug($sql);
 		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);
 		
 	}


 	//=======================================
 	// Execute une requete sql
 	public function query($sql){

 		// debug($sql);
 		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);
 	}


 	//=========================================
 	// Recupere le premier element d'un requete 
 	// $param $req requete sql et conditions

 	public function findFirst($req){

 		return current($this->find($req)); //current renvoi l'element courant , cest a dire le premier element dans ce cas là

 	}

 	public function findCount($conditions){

 		$res = $this->findFirst( array(
				'fields'     => 'COUNT('.$this->primaryKey.') as count',
				'conditions' => $conditions
 			));

 		return $res->count;


 	}

 	public function delete($obj){

 		if(is_numeric($obj)){
	 		$sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey}=$id";
	 		$res = $this->db->query($sql);
			return $res;
		}
		elseif(is_object($obj)){

			if(isset($obj->table))
				$table = $obj->table;			
			else
				$table = $this->table;
			if(isset($obj->key))
				$key = $obj->key;
			else
				$key = $this->primaryKey;


			$sql = "DELETE FROM ".$table." WHERE ".$key."=".$obj->id;
			$res = $this->db->query($sql);
			return $res;
		}
 	}


 	/*===========================================================	        
 	Save informations into sql tables
 	@param $data = object of values to insert
 	@work effectue un insert ou un update si primaryKey est présente dans $data
 	@work pass a table in the object to specfify a table
 	@return true with $this->id as lastInsertId()
 	$return false with error in $this->error
 	============================================================*/
 	
 	public function save($data){

 		if(isset($data->table)){
 			$table = $data->table;
 			unset($data->table);
 		} 		
 		else
 			$table = $this->table;

 		if(isset($data->key)){
 			$primaryKey = $data->key;
 			unset($data->key);
 		}
 		else
 			$primaryKey = $this->primaryKey;
 	
 		$fields = array();
 		$tab = array();
 
 		//Pour chaque champ on cree les tableaux des valeurs 
 		foreach ($data as $k => $v) {
 			if($k!=$primaryKey){ //sauf si il s'agit de la clef primaire
 				if(!empty($v)) {	// si la valeur nest pas vide
		 			$fields[] = "$k=:$k"; //tableaux des valeurs en sql
		 			$tab[":$k"] = $v; //tableau des valeurs pour la fonction execute de PDO
	 			}
	 		}
	 		elseif(!empty($v)){ //Pour la clef primaire sauf si elle est vide
	 			$tab[":$k"] = $v; //on la rajoute a la liste des champs pour l'update
	 		}
 		}
 		// debug($data);
 		// debug($key);
 		//Si la clef existe on fait un update
 		if(isset($data->$primaryKey) && !empty($data->$primaryKey)){

 			$action = 'update';
 			$sql = 'UPDATE '.$table.' SET '.implode(',',$fields).' WHERE '.$primaryKey.'=:'.$primaryKey;
 			
 			$this->id = $data->$primaryKey; // retourne l'id de l'update


 		} else { //Sinon on fait un inset
 			$action = 'insert';
 			$sql = 'INSERT INTO '.$table.' SET '.implode(',',$fields);
 		}
 		

 		// debug($sql);
 		$pre = $this->db->prepare($sql); //prepare la requete
 		$pre->execute($tab); //execute la requete grace au tableaux des valeurs ( :name, :contenu, :date, ...)
 		
 		//Si c'est un insert on recupere l'id de l'insertion
 		if($action=='insert'){
 			$this->id = $this->db->lastInsertId();
 		}

 		//Set the action
 		$this->action = $action;

 		//Si pas d'error retourne true
 		if($pre->errorCode()==0){
 			return true;
 		}
 		else { 			
 			$this->error = $pre->errorInfo(); 			
 			return false;
 		} 
 		
 	}

 	public function increment($data){

 		if ( isset($data['table'])) {
 			$table = $data['table'];
 		}		
 		else {
 			$table = $this->table;
 		}

 		$field = $data['field'];
 		$id = $data['id'];

 		$sql = "UPDATE $this->table SET $field = $field+1 WHERE $this->primaryKey = $id";
 		$pre = $this->db->prepare($sql);
 		$pre->execute();

 		$sql ="SELECT $field FROM $this->table WHERE $this->primaryKey = $id";
 		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetch(PDO::FETCH_OBJ);
 	}

	public function validates($data, $rules = null, $field = null){


		$errors = array();

		//On recupere les regles de validation
		if(!$rules){
			$validates = $this->validates;
		}
		else {

			if($field==null)
				$validates = $this->validates[$rules];
			else
				$validates = array($field=>$this->validates[$rules][$field]);
		}
		
		//Vérifie les regles de validation pour chaque champs
		foreach ($validates  as $k => $v) { 
				
			//Si la donnée correspondant est manquante -> erreur				
			if(empty($data->$k)){

				//Si il y a plusiers regles
				if(isset($v['rules'])){
					$rules = $v['rules']; 						
					foreach ($rules as $r) {
						if($r['rule']=='optionnal') $optionnal=true;

					}
					//if not optionnal
					if(!isset($optionnal)){
						$rule = $rules[0];
						$errors[$k] = $rule['message'];
					}
					
				}
				//Si il y a qu'une regle
				if(isset($v['rule'])){
					
					//Si le champ est optionnel, sauter au prochain champ
					if($v['rule']=='optionnal') continue;
					
					$errors[$k] = $v['message'];
				}
				
			}
			else{
			
				//Si il y a plusiers regles
				if(isset($v['rules'])){
					$rules = $v['rules']; 						
				} 
				//Si il y a qu'une regle
				if(isset($v['rule'])){
					
					 $rules = array($v);
				}

				//Pour toutes les regles correspondante
				foreach ($rules as $rule) {
					

					if($rule['rule']=='notEmpty'){
						if(empty($data->$k)) $errors[$k] = $rule['message'];				
					}
					elseif($rule['rule']=='confirmPassword'){
						if($data->$k != $data->password) $errors[$k] = $rule['message'];
					}
					elseif($rule['rule']=='optionnal'){

					}
					elseif(!preg_match('/^'.$rule['rule'].'$/',$data->$k)){
						$errors[$k] = $rule['message'];
					}
				}				
			}
			//reset optionnal
			$optionnal=false;
		}



		//Vérifie les fichiers uploadé
		if(isset($_FILES)){ 				
		
		//Pour chaque fichier uploader
			foreach ($_FILES as $key => $file) {

				//Si le fichier n'est pas vide et quil n'y pas d'erreur d'envoi
				if($file['error'] == 'UPLOAD_ERR_OK'){
				
					$input = str_replace('input','',$key);

					//Si il y a des regles définies
					if($this->validates_files[$input]){

 					//Si il y a une limite de poids 
 					if($this->validates_files[$input]['max_size']) {

 						//Si le fichier est trop gros
 						if($file['size']>$this->validates_files[$input]['max_size']){

 							$errors[$input] = $this->validates_files[$input]['max_size_error'];
 						}
 					}
 					//Si il y a des extentions spécifiquement authorisées
						if($this->validates_files[$input]['extentions']){
							
							$extention = substr(strrchr($file['name'], '.'),1);
							$extentions = $this->validates_files[$input]['extentions'];

							if(!in_array($extention,$extentions)){
								$errors[$input] = $this->validates_files[$input]['extentions_error'];	 					
							} 
						}

						//If Prevent hidden php code
						if($this->validates_files[$input]['ban_php_code'] && $this->validates_files[$input]['ban_php_code'] == true){
							//Vérifie qu'il n'y a pas de code php caché dans l'image				 							
							if(strpos(file_get_contents($file['tmp_name']),'<?php')){

								die('Oh whats there ? Some php into a image file !! You funny jocker !');
							}	
						}
 					}
				}
			}
		}

		$this->errors = $errors;

		//Si une class Form est lié a ce model
		if(isset($this->Form)){
			$this->Form->setErrors($errors); //On lui envoi les erreurs
		}

		//Si pas d'erreur validates renvoi true
		if(empty($errors)){
			return true;
		}
		else {
			//debug($errors);
		}
				
		return false;
 			 			 		
 	}


 	/*	
	Renvoi le compte des primaryKey depuis un certain temps
	$params $conditions 
	@params $second : temps en seconde à deduire de à partir de maintenant
 	*/
 	public function countNew($params){

 		extract($params);

 		$sql = " SELECT COUNT( ".$this->primaryKey." ) as count ";
 		$sql .=" FROM ".$this->table;
 		$sql .=" WHERE ";
 		if(isset($conditions)){
 			if(!is_array($conditions)){
 				$sql .= $conditions; 				
 			}
 			else {
 			$cond = array();
	 			foreach ($conditions as $k => $v) {
	 				if(!is_numeric($v)){ 
	 					$v = '"'.mysql_escape_string($v).'"';	 					
	 				}
	 				$cond[] = "$k=$v";	 			
	 			}
	 			$sql .= implode(' AND ',$cond);
 			}
 			
 		}
 		$sql .=" AND ".$date_field." > DATE_SUB( CURRENT_TIMESTAMP , INTERVAL $second SECOND)";

		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetch(PDO::FETCH_OBJ);

 	}

 	public function sqlFields($fields){

 		$f = '';
		if($fields && !empty($fields))
			if(is_array($fields))
 				$f .= implode(', ',$fields); 			
 			elseif(is_string($fields))
 				$f .= $fields; 			 		
 		else
 			$f .= '*';
 		return $f; 	
 	}

 	public function sqlConditions($conditions){

 		$c='';
 		if($conditions && !empty($conditions)){
	 		if(is_array($conditions)){
	 			$cond = array();
	 			foreach ($conditions as $k => $v) {
	 				if(!is_numeric($v) && substr($v, 0, 1) != ':' )
	 					$v = '"'.mysql_escape_string($v).'"';
	 				$cond[] = "$k=$v";
	 			}
	 			$c .= implode(' AND ',$cond);
	 		}
	 		else
	 			$c .= $conditions;
	 	}
	 	else
	 		$c .= '1=1';
	 	return $c;
 	}

 	public function JOIN($table,$fields,$conds,$obj){

	 	$array = array();

	 	$fields = $this->sqlFields($fields);

		$sql = "SELECT ".$fields." FROM ".$table." WHERE ".$this->sqlConditions($conds);
		$pre = $this->db->prepare($sql);

		if(is_object($obj)){

			if(preg_match_all("/:([a-zA-Z\_\-]+)/",$sql,$matches)){
				unset($matches[0]);
				foreach ($matches as $key) {
		
					$pre->bindValue(":".$key[0],$obj->$key[0]);
				}
			}
			
			$pre -> execute();
			$res = $pre->fetch(PDO::FETCH_OBJ);

			if(!empty($res)){
				foreach ($res as $k=>$v) {
					
					$obj->$k = $v;

				}
			}			

			return $obj;

		}
		elseif( is_array($obj)){
			
			foreach ($obj as $row) {

				$array[] = $this->JOIN($table,$fields,$conds,$row);
				
			}

			return $array;
		}
		

	}

	public function joinUser($objects){

		if(!is_array($objects)) $objects = array($objects);

		foreach ($objects as $obj) {
			
			if(is_object($obj)){
				if(isset($obj->user_id)){

					$user = $this->findFirst(array('table'=>'users','fields'=>'*','conditions'=>array('user_id'=>$obj->user_id)));
					$user = new User($user);
					$obj->user = $user;
				}
			}
		}

		return $objects;		
	}

}


 	?>