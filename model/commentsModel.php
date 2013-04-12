<?php /**
* 
*/
class CommentsModel extends Model
{
	public $table = 'comments';	
	public $table_vote = 'comments_voted';


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

        $user_id = Session::user()->getID();
        

		$q = " SELECT C.*, U.user_id, U.login, U.avatar, V.id as voted 
				FROM $this->table as C
				LEFT JOIN users as U ON U.user_id = C.user_id
				LEFT JOIN $this->table_vote as V ON (V.comment_id = C.id AND V.user_id = ".$user_id." )
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
				if( isset($newest) && $newest!="0")
								$q .='
								AND C.id > "'.$newest.'"';
				if(isset($lang) && !empty($lang))
								$q .=' AND lang="'.$lang.'" ';

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

		$val = array();

		if(isset($order)){

        	if ($order == "datedesc" || $order == '' || $order == '0')
            	$order="date DESC";
	        elseif ($order == "dateasc")
	            $order="date ASC";
	        elseif ($order == "noteasc")
	            $order="note ASC";
	        elseif ($order == "notedesc")
            	$order="note DESC";
        }
        else {
        	$order = "date DESC";
        }

		if(!empty($limit) && is_numeric($limit)){

			if(!isset($page) || empty($page)) $page = 1;
				$limit = (($page-1)*$limit).','.$limit;
		}
		else $limit = 165131654;
       


        if (isset($rch) && $rch != "0") {
        	if( trim( $rch != "" ) ) {
        		$rch =" ( U.login LIKE '%" . $rch . "%' OR X.content LIKE '%" . $rch . "%' )";
        		$rch =" ( U.login LIKE '%:rch%' OR X.content LIKE '%:rch%' )";
        	}           
        }

        $user_id = Session::user()->getID();

		$sql = " SELECT C.*
				FROM $this->table as C
				
				WHERE ";
				if(isset($comment_id) && is_numeric($comment_id)) {
							$sql .= " C.id=$comment_id ";	
							
				} 
				elseif(isset($reply_to) && is_numeric($reply_to)){
							$sql .= " C.reply_to=$reply_to ";							
				}
				else {
							$sql .= " C.context=:context AND C.context_id=:context_id AND C.reply_to=0 ";
							$val['context'] = $context;
							$val['context_id'] = $context_id;
				}			
				if (isset($type) && $type != "all" && $type != "0" ){

							$sql .=' AND C.type=:type ';
							$val['type'] = $type;

				}
				if (!empty($start) && is_numeric($start)){

							$sql .=' AND C.id <= '.$start.' ';			
				}
				if( !empty($newest) && is_numeric($newest)){

							$sql .=' AND C.id > '.$newest.' ';
					
				}
				if(isset($lang) && !empty($lang)!=''){

							$sql .= ' AND lang=:lang ';
							$val['lang'] = $lang;
					
				}
				$sql.=" ORDER BY ".$order." LIMIT ".$limit." ";


		$res = $this->query($sql,$val);		
		
		//foreach comment make an object	
		$comments = array();
		foreach ($res as $com) {
			
			$com = new Comment($com);
			$com = $this->JOIN_CONTEXT($com);
			$comments[] = $com;

		}

		//foreach comment join replies if exist
		$comments = $this->joinReplies($comments);
		$comments = $this->JOIN_COM_DATA($comments);

 		//$timeend=microtime(true);
		//$time=$timeend-$timestart;
		//debug('temps d\'execution sans les JOIN:'.$time);
		//debug($comments);
		return $comments;
	}



	/*
	Associe les réponses aux commentaires
	@param array/objet of comments
	@return array of comments
	**/
	public function joinReplies($comments){

		//put in a array if its not an array
		if(is_object($comments)) $comments = array($comments);
		
		//loop the array of comments
		foreach ($comments as $comment) {
			
			if($comment->haveReplies()){

				//this will find all replies in Comment object
				$replies = $this->findCommentsWithoutJOIN(array('reply_to'=>$comment->id,'order'=>'dateasc'));								
				$comment->replies = $replies;
							
			}			
		}

		return $comments;
	}


	public function getComments($comments_id){
	
		if(is_array($comments_id)){

			foreach($comments_id as $key => $comment_id){

				$comments_id[$key] = $this->findCommentsWithoutJOIN(array('comment_id'=>$comment_id));				
			}					
			return $comments_id;
		}
		elseif(is_numeric($comments_id)){

			$res = $this->findCommentsWithoutJOIN(array('comment_id'=>$comments_id));
			return $res[0];				
		}

	}

	public function saveComment($com){

		$c = new stdClass();
		$c = $com;

		
		$c->content = str_replace(array("\\n","\\r"),array("<br />",""),$c->content); 

		if(!empty($c->media) && !empty($c->media_url)){
			$c->content = str_replace($c->media_url,'',$c->content);
			$c->media = $c->media;
			$c->media = html_entity_decode($c->media,ENT_NOQUOTES|'ENT_XHTML', 'UTF-8' );
		}

		if(!empty($title)){
			$c->title = $c->title;
			$c->news = 1;
		}

		if(!empty($c->reply_to) && is_numeric($c->reply_to)){

			$this->increment(array('field'=>'replies','id'=>$c->reply_to));
		}

		if($id = $this->save($c)){

			return $id;
		}
		else
			return false;
	}

	public function admin_moderate($id){

		//secu
		if(!Session::user()->isLog() || !Session::user()->isSuperAdmin()) $this->redirect('users/login');
		$sql = 'UPDATE '.$this->table.' SET valid=0 WHERE id='.$id;
		$this->query($sql);
	}
	public function admin_delete($id){
		$sql = 'UPDATE '.$this->table.' SET online=0 WHERE id='.$id;
		$this->query($sql);
	}
	public function admin_totalComments(){
		$sql = 'SELECT count(*) as total FROM '.$this->table;
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalOnlineComments(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE online=1';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalOfflineComments(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE online=0';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalModerateComments(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE valid=0';
		$res = $this->query($sql);
		return $res[0]->total;
	}

	public function JOIN_COM_DATA($data){

		$data = $this->JOIN_USER($data);
		$data = $this->JOIN_USER_VOTE($data);

		return $data;
	}

	public function JOIN_USER_VOTE($data){

		return $this->JOIN($this->table_vote,'id as voted',array('comment_id'=>':id','user_id'=>Session::user()->getID()),$data);
	}

	public function JOIN_CONTEXT($com){
		
		if($com->context=='manif') return $this->JOIN('manif_descr','manif_id as context_id,nommanif as contextTitle,logo as contextLogo,slug as contextSlug,lang as contextLang',array('manif_id'=>':context_id','lang'=>':lang'),$com);
		return $com;
	}

	public function totalComments($context,$context_id){

		$sql = "SELECT COUNT(id) as count FROM $this->table WHERE context='$context' AND context_id=$context_id AND reply_to=0";		
		return $this->query($sql);
	}

	public function userTotalComments($user_id){

		$sql = "SELECT COUNT(id) as count FROM $this->table WHERE user_id=$user_id";
		return $this->query($sql);
	}

	public function userTotalManifsComments($user_id,$manif_id){

		$sql = "SELECT COUNT(id) as count FROM $this->table WHERE user_id=$user_id AND context='manif' AND context_id=$manif_id";
		return $this->query($sql);
	}

	public function alreadyVoted($id,$user_id){

		$sql = "SELECT COUNT(id) FROM $this->table_vote WHERE comment_id=$id AND user_id=$user_id";
		return (bool) $this->query($sql);		
	}

	public function haveVoted($data){

 		foreach ($data as $k => $v) {
	 			$tab[":$k"] = $v; //tableau des valeurs pour la fonction execute de PDO	 		
 		}
		$sql = "INSERT INTO $this->table_vote SET user_id = :user_id , comment_id = :comment_id";

		if($this->query($sql,$tab))			
			return true;
		return false;
	}

	public function findUserComments($req = array()){

		$sql = 'SELECT ';
 		if(isset($req['fields']))
 			$sql .= $this->sqlFields($req['fields']);
 		else
 			$sql .= ' * ';		

		$sql .= " FROM $this->table as C
					JOIN users as U ON U.user_id=C.user_id
				  	WHERE ";

		if(isset($req['conditions'])){ 			
 			
 			$sql .= $this->sqlConditions($req['conditions'])	;
 			
 		}

		if(isset($req['order'])){
 			if($req['order'] == 'random') $sql .= ' ORDER BY rand()';
 			else $sql .= ' ORDER BY '.$req['order'];
 		}

 		if(isset($req['limit'])){
			$sql .= ' LIMIT '.$req['limit'];
 		}

 		// debug($sql);
		return $this->query($sql);
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
					'joinProtest' as type,
					id as id,
					date as date
				FROM 
					manif_participation
				WHERE 
					user_id = ".$params['context_id']."

				UNION
				SELECT
					'manifStep' as type,
					S.id as id,
					S.date as date
				FROM
					manif_step as S
					LEFT JOIN manif_participation AS P ON P.manif_id = S.manif_id
					
				WHERE
					P.user_id = ".$params['context_id']."

				UNION
				SELECT 
					'manifNews' as type,
					C.id as id,
					C.date as date
				FROM
					comments as C
					LEFT JOIN manif_participation AS P ON P.user_id = ".$params['context_id']."
				WHERE
					C.context = 'manif' AND C.news=1 AND C.context_id = P.manif_id AND C.lang ='".$params['lang']."'
				ORDER BY date DESC
				LIMIT ".$limit." 

				";
		
		$res = $this->query($sql);

		return $res;



	}






} 


class Comment {

	public $id = 0;

	public function __construct($params){

		foreach ($params as $key => $param) {
			
			$this->$key = $param;
		}
	}

	public function getID(){

		return $this->id;
	}

	public function exist(){

		if($this->id!=0) return true;
		return false;
	}

	public function haveReplies(){

		return (!empty($this->replies))? true : false;
	}

	public function userHaveVoted(){

		return (isset($this->voted)&&is_numeric($this->voted))? true : false;
	}

	public function isModerate( $msg = false){

		if($this->note<-10) {
			$msg = 'Ce commentaire a reçu trop de vote négatif...';
		}
		if($this->valid==0) {		
			$msg = 'Ce commentaire a été modéré.';
		}
		if($this->online==0) {
			$msg = 'Ce commentaire a été modéré et va être supprimé.';
		}
 
		return $msg;
	}

	public function isNews(){
		if(isset($this->news) && $this->news==1) return true;
		return false;
	}

	public function contextTitle(){
		if(isset($this->contextTitle)) return $this->contextTitle;
		return 'No title for this context';
	}

	public function contextLogo(){
		if(isset($this->contextLogo)) return $this->contextLogo;
		if(isset($this->avatar)) return $this->avatar;
		return '/img/logo_yp.png';
	}

	public function debugContent(){
		$r = '';
		if(!empty($this->title)) $r .= $this->title.'<br>';
		if(!empty($this->content)) $r .= $this->content.'<br>';
		if(!empty($this->media)) $r .= $this->media.'<br>';
		return $r;
	}

}?>