<?php /**
* 
*/
class Comments extends Model
{
	public $table = 'manif_comment';	


	public function findComments($req){


		foreach ($req as $k => $v) {
			
			$$k = $v;
		}

		if(isset($order)){

        	if ($order == "datedesc" || $order == '' || $order == '0')
            	$order=" date DESC ";
	        elseif ($order == "dateasc")
	            $order=" date ASC ";
	        elseif ($order == "noteasc")
	            $order=" note ASC ";
	        elseif ($order == "notedesc")
            	$order=" note DESC ";
        }
        else {
        	$order= " date DESC ";
        }

		if(isset($limit) && !empty($limit)){
			
			 $limit = (($page-1)*$limit).','.$limit;

		}
		else $limit = 165131654;
        
        if (isset($rch) && $rch != "0") {
        	if( trim( $rch != "" ) ) {
        		$rch =" ( U.login LIKE '%" . $rch . "%' OR X.content LIKE '%" . $rch . "%' )";
        	}           
        }

        $user_id = $this->session->user('user_id');
        

		$q = " SELECT C.*, U.user_id, U.login, U.avatar, V.id as voted, I.logo 
				FROM manif_comment as C
				LEFT JOIN users as U ON U.user_id = C.user_id
				LEFT JOIN manif_info as I ON I.manif_id = C.context_id
				LEFT JOIN manif_comment_voted as V ON (V.comment_id = C.id AND V.user_id = ".$user_id." )
				WHERE ";
				if(isset($comment_id)) {
							$q .= "
								C.id=".$comment_id." ";	
				} else {
							$q .= "
								C.context='".$context."'
								AND
								C.context_id=".$context_id." 
								AND 
								C.reply_to=0 ";
				}			
				if (isset($type) && $type != "all" && $type != "0" )
								$q .='
								AND C.type="'.$type.'"';
				if (isset($start) && $start!="0")
								$q .='
								AND C.id <= "'.$start.'"';
				if( isset($newer) && $newer!="0")
								$q .='
								AND C.id > "'.$newer.'"';
				$q.=" 
				ORDER BY ".$order."
				LIMIT ".$limit."
			";

		//debug($q);
		$res = $this->db->prepare($q);
		$res->execute();

		if($res->rowCount()>1)
			$coms = $res->fetch(PDO::FETCH_OBJ);
		else
			$coms = $res->fetchAll(PDO::FETCH_OBJ);

		$coms = $this->findReplies($coms);

		// debug($array);
		return $coms;
	}
	
	public function findCommentsWithoutJOIN($req){

		//$timestart=microtime(true);

		foreach ($req as $k => $v) {
			
			$$k = $v;
		}

		if(isset($order)){

        	if ($order == "datedesc" || $order == '' || $order == '0')
            	$order=" date DESC ";
	        elseif ($order == "dateasc")
	            $order=" date ASC ";
	        elseif ($order == "noteasc")
	            $order=" note ASC ";
	        elseif ($order == "notedesc")
            	$order=" note DESC ";
        }
        else {
        	$order= " date DESC ";
        }

		if(isset($limit) && !empty($limit)){

			if(!isset($page)) $page = 1;
			 $limit = (($page-1)*$limit).','.$limit;

		}
		else $limit = 165131654;
        
        if (isset($rch) && $rch != "0") {
        	if( trim( $rch != "" ) ) {
        		$rch =" ( U.login LIKE '%" . $rch . "%' OR X.content LIKE '%" . $rch . "%' )";
        	}           
        }

        $user_id = $this->session->user('user_id');

		$sql = " SELECT C.*
				FROM manif_comment as C
				
				WHERE ";
				if(isset($comment_id)) {
							$sql .= "
								C.id=".$comment_id." ";	
				} else {
							$sql .= "
								C.context='".$context."'
								AND
								C.context_id=".$context_id." 
								AND 
								C.reply_to=0 ";
				}			
				if (isset($type) && $type != "all" && $type != "0" )
								$sql .='
								AND C.type="'.$type.'"';
				if (isset($start) && $start!="0")
								$sql .='
								AND C.id <= "'.$start.'"';
				if( isset($newer) && $newer!="0")
								$sql .='
								AND C.id > "'.$newer.'"';
				$sql.="  
				ORDER BY ".$order."
				LIMIT ".$limit."
			";

		
		$res = $this->db->prepare($sql);
		$res->execute();
		$coms = $res->fetchAll(PDO::FETCH_OBJ);		

 		//$timeend=microtime(true);
		//$time=$timeend-$timestart;
		//debug('temps d\'execution sans les JOIN:'.$time);

		return $coms;
	}

	/*
	Associe les réponses aux commentaires
	@param array/objet of comments
	@return array of comments
	**/
	public function findReplies($comments){

		$array = array();



		if(is_object($comments)){

			if($comments->replies > 0){

				$q = "SELECT * FROM manif_comment WHERE reply_to = ".$comments->id." ORDER BY date ASC";
				$res = $this->db->prepare($q);
				$res->execute();
				if($res->rowCount() != 0) {

					$replies = $res->fetchAll(PDO::FETCH_OBJ);

					foreach ($replies as $reply) {

						$array[] = $reply;
							
						if($reply->replies > 0 ){
							
							$array[] = $this->findReplies($reply);	
						}									
						
					}
		
				}

				return $array;
			}
			else
				return $comments;

		}
		elseif(is_array($comments)) {

			foreach ($comments as $com) {
	
				$array[] = $com;

				if($com->replies > 0){				 
					
					$q = "SELECT C.* FROM manif_comment as C WHERE C.reply_to =".$com->id." ORDER BY C.date ASC";
					$res = $this->db->prepare($q);
					$res->execute();

					if($res->rowCount() != 0) {

						$replies = $res->fetchAll(PDO::FETCH_OBJ);

						$tab = array();

						foreach ($replies as $reply) {

							$tab[] = $reply;
								
							if($reply->replies > 0 ){
								
								$tab[] = $this->findReplies($reply);	
							}									
							
						}

						$array[] = $tab;
		
					}
				}
				
			}
		} 		

		

		return $array;

	}

	public function getComments($comments_id){

		$sql = 'SELECT * FROM manif_comment WHERE id = :comment_id';
		$pre = $this->db->prepare($sql);
		
		$array = array();

		if(is_array($comments_id)){

			

			foreach($comments_id as $comment_id){

				$pre->bindValue(':comment_id',$comments_id);
				$pre->execute();
				$res = $pre->fetch(PDO::FETCH_OBJ);
				$array[] = $res;

			}			

			
		}

		elseif(is_numeric($comments_id)){

			$pre->bindValue(':comment_id',$comments_id);
			$pre->execute();
			$res = $pre->fetch(PDO::FETCH_OBJ);
			$array[] = $res;			

			
		}

		return $array;

	}


	public function joinUserData($data){

		$data = $this->JOIN('users',array('user_id','login','avatar'),array('user_id'=>':user_id'),$data);
		$data = $this->JOIN('manif_comment_voted','id as voted',array('comment_id'=>':id','user_id'=>$this->session->user('user_id')),$data);
		$data = $this->JOIN('manif_info','logo as logoManif',array('manif_id'=>':context_id'),$data);

		
		//LEFT JOIN manif_info as I ON I.manif_id = C.context_id

		return $data;
	}

	public function totalComments($context,$context_id){

		$sql = "SELECT COUNT(id) as count FROM manif_comment WHERE context='$context' AND context_id=$context_id AND reply_to=0";
		$pre = $this->db->prepare($sql);
		$pre->execute();
		return $pre->fetchColumn();
	}

	public function userTotalComments($user_id){

		$sql = 'SELECT COUNT(id) as count FROM manif_comment WHERE user_id='.$user_id;
		$res = $this->db->prepare($sql);
		$res->execute();
		return $res->fetchColumn();
	}

	public function userTotalManifsComments($user_id,$manif_id){

		$sql = 'SELECT COUNT(id) as count FROM manif_comment WHERE user_id='.$user_id.' AND context="manif" AND context_id='.$manif_id;
		$res = $this->db->prepare($sql);
		$res->execute();
		return $res->fetchColumn();
	}

	public function alreadyVoted($id,$user_id){

		$sql = "SELECT COUNT(id) FROM manif_comment_voted WHERE comment_id=$id AND user_id=$user_id";
		$pre = $this->db->prepare($sql);
		$pre->execute();
		return (bool)$pre->fetchColumn();
	}

	public function haveVoted($data){

 		foreach ($data as $k => $v) {
	 			$tab[":$k"] = $v; //tableau des valeurs pour la fonction execute de PDO	 		
 		}
		$sql = "INSERT INTO manif_comment_voted SET user_id = :user_id , comment_id = :comment_id";
		$pre = $this->db->prepare($sql);
		$pre->execute($tab);			
		return true;
	}

	public function findUserComments($req){

		$sql = 'SELECT ';
 		if(isset($req['fields']))
			if(is_array($req['fields']))
 				$sql .= implode(', ',$req['fields']); 			
 			else
 				$sql .= $req['fields']; 			 		
 		else
 			$sql .= 'C.*, U.*'; 		

		$sql .= " FROM manif_comment as C
					JOIN users as U ON U.user_id=C.user_id
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
		$res = $this->db->prepare($sql);
		$res->execute();
		return $res->fetchAll(PDO::FETCH_OBJ);
	}

	public function threadUser($params){

		$thread = $this->getThreadUser($params); //get the order list
		$thread = $this->fillThreadUser($thread);	//fill the list

		return $thread;
	}

	public function getThreadUser($params){

		//limit
		if(isset( $params['limit']) && !empty($params['limit'])){
			if(!isset($params['page'])) $params['page'] = 1;
			$limit = (($params['page']-1)*$params['limit']).','.$params['limit'];
		}
		else $limit = 144;

		//request
		$sql = "SELECT 
					'joinProtest' as thread,
					id as id,
					date as date
				FROM 
					manif_participation
				WHERE 
					user_id = ".$params['context_id']."
				UNION
				SELECT 
					'manifNews' as thread,
					C.id as id,
					C.date as date
				FROM
					manif_comment as C
					LEFT JOIN manif_participation AS P ON P.user_id = ".$params['context_id']."
				WHERE
					C.context = 'manif' AND C.type='news' AND C.context_id = P.manif_id 
				ORDER BY date DESC
				LIMIT ".$limit." 

				";

		$pre = $this->db->prepare($sql);
		$pre->execute();
		$res = $pre->fetchAll(PDO::FETCH_OBJ);

		return $res;

	}

	public function fillThreadUser($list){

		$array = array();

		foreach ($list as $thread) {
			
			if($thread->thread == 'joinProtest'){
				
				$protester = $this->session->controller->Manifs->findProtesters(array(
																					'fields'=>array('U.user_id','U.login','P.manif_id','P.date','P.id'),
																					'conditions'=>array('P.id'=>$thread->id)
																				));

				$sql = "SELECT manif_id, nommanif, slug, logo FROM manif_descr WHERE manif_id=".$protester->manif_id;
				$pre = $this->db->prepare($sql);
				$pre->execute();
				$manif = $pre->fetch(PDO::FETCH_OBJ);
				
				$protester = (object) array_merge((array) $protester, (array) $manif);
				$protester->thread = $thread->thread;
				$array[] = $protester;

			}
			elseif($thread->thread == 'manifNews'){

				$com = $this->getComments($thread->id);
				$com = $this->joinUserData($com);
				$com = $com[0];
				$com->thread = $thread->thread;
				$array[] = $com;

				if($com->replies > 0){

					$replies = $this->findReplies($com);
					$replies = $this->joinUserData($replies);
					$array[] = $replies;

				}
			}
		}


		return $array;
	}





} ?>