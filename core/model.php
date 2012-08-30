<?php
 class Model {

 	static $connections = array();
 	public $conf = 'default'; //Base de donnée à utiliser
 	public $table = false; //Table mysql à utiliser par le model
 	public $db = false; //Connexion courante à la base
 	public $primaryKey = 'id'; //Nom de la clef primaire de la table
 	public $id; //valeur de la clef courante
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
 		//Sinon
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

 		$sql .= ' FROM '.$this->table.' as '.get_class($this).' ';


 		//Construction des conditions de la requete sql
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

 	public function delete($id){

 		$sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey}=$id";
 		$res = $this->db->query($sql);
		return $res;
 	}

 	public function save($data){

 		$key = $this->primaryKey;
 		$fields = array();
 		$tab = array();
 
 		//Pour chaque champ on cree les tableaux des valeurs 
 		foreach ($data as $k => $v) {
 			if($k!=$this->primaryKey){ //sauf si il s'agit de la clef primaire
 				if(!empty($v)) {	// si la valeur nest pas vide
		 			$fields[] = "$k=:$k"; //tableaux des valeurs en sql
		 			$tab[":$k"] = $v; //tableau des valeurs pour la fonction execute de PDO
	 			}
	 		}
	 		elseif(!empty($v)){ //Pour la clef primaire sauf si elle est vide
	 			$tab[":$k"] = $v; //on la rajoute a la liste des champs pour l'update
	 		}
 		}

 		//Si la clef existe on fait un update
 		if(isset($data->$key) && !empty($data->$key)){

 			$action = 'update';
 			$sql = 'UPDATE '.$this->table.' SET '.implode(',',$fields).' WHERE '.$key.'=:'.$key;
 			
 			$this->id = $data->$key; // retourne l'id de l'update

 		} else { //Sinon on fait un inset
 			$action = 'insert';
 			$sql = 'INSERT INTO '.$this->table.' SET '.implode(',',$fields);
 		}
 		//debug($sql);
 		$pre = $this->db->prepare($sql); //prepare la requete
 		$pre->execute($tab); //execute la requete grace au tableaux des valeurs ( :name, :contenu, :date, ...)
 		
 		//Si c'est un insert on recupere l'id de l'insertion
 		if($action=='insert'){
 			$this->id = $this->db->lastInsertId();
 		}
 		
 		return true;
 
 		
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

 	public function validates($data, $rules = null){


 			$errors = array();

 			//On recupere les regles de validation
 			if(!$rules){
 				$validates = $this->validates;
 			}
 			else {
 				$validates = $this->validates[$rules];
 			}
 			
 			//Vérifie les regles de validation pour chaque champs
 			foreach ($validates  as $k => $v) { 				
 				if(!isset($data->$k)){
 					$errors[$k] = $v['message'].'haa';
 				}
 				else{

 					if($v['rule']=='notEmpty'){
 						if(empty($data->$k)) $errors[$k] = $v['message'];				
 					}
 					elseif($v['rule']=='confirmPassword'){
 						if($data->$k != $data->password) $errors[$k] = $v['message'];
 					}
 					elseif(!preg_match('/^'.$v['rule'].'$/',$data->$k)){
 						$errors[$k] = $v['message'].'euhh';
 					}
 				}

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

 			// debug($errors); 			
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
 			else
 				$f .= $fields; 			 		
 		else
 			$f .= '*';
 		return $f; 	
 	}

 	public function JOIN_INFO($table,$fields,$data,$key){

	 	$array = array();

	 	$fields = $this->sqlFields($fields);

		$sql = "SELECT ".$fields." FROM ".$table." WHERE ".$key." = :".$key;
		$pre = $this->db->prepare($sql);

		foreach ($data as $obj) {

			if(is_object($obj)){

				$pre -> bindValue(":".$key, $obj->$key);
				$pre -> execute();
				$res = $pre->fetch(PDO::FETCH_OBJ);
				foreach ($res as $k => $value) {
				
					$obj->$k = $value;
				}
				$array[] = $obj;
			}
			elseif(is_array($obj)){

				$obj = $this->JOIN_INFO($table,$fields,$obj,$key);
			}
		}

		return $array;

	}
}


 	?>