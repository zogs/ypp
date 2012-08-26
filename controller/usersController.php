<?php 
class UsersController extends Controller{


	public $primaryKey = 'user_id'; //Nom de la clef primaire de la table

	public function login(){

		$this->layout = 'none';
		$this->loadModel('Users');

		if($this->request->data){		
			
			$data = $this->request->data;
			$login = $data->login;

			if(strpos($login,'@'))
				$field = 'mail';
			else
				$field = 'login';
			
			$user = $this->Users->findFirst(array(
				'fields'=> 'user_id,login,avatar,hash,salt,statut,pays,lang',
				'conditions' => array($field=>$login))
			);
			
			if(!empty($user)){

				if($user->hash == md5($user->salt.$data->password)){

					unset( $user->hash);
					unset( $user->salt);
					$this->user = $user;

					$this->session->write('user', $user);				
					$this->session->setFlash('Vous êtes maintenant connecté');	
				}
				else {
					$this->session->setFlash('Mauvais mot de passe','error');
				}						
			}
			else {
				$this->session->setFlash("Le ".$field." <strong>".$data->login."</strong> n'existe pas dans la base de donnée",'error');
			}
			$data->password = "";				

		}

		/*  
			Validation de l'inscription
			===========================
		*/
		elseif($this->request->get('code') && $this->request->get('uid') && $this->request->get('login')) {

			$get       = $this->request->get;
			$login     = $get->login;
			$user_id   = $get->uid;			
			$codeactiv = $get->code;

			$user = $this->Users->findFirst(array(
				'fields'=>'codeactiv',
				'conditions'=>array('login'=>$login,'user_id'=>$user_id)
				));


			if(!empty($user)){

				if($user->codeactiv == $codeactiv) {
					$data =  new stdClass();
					$data->user_id = $user_id;
					$data->valid = 1;
					$this->Users->save($data);

					$this->session->setFlash('<strong>Bravo</strong> '.$login.' ! Tu as validé ton inscription','success');
					$this->session->setFlash('Tu peux te <strong>connecter</strong> dés maintenant!','info');

				}
				else {
					$this->session->setFlash("Une erreur inconnue est intervenue pendant l'activation",'error');
				}
			}
			else {				
				$this->session->setFlash("Pas trouvé dans la bdd",'error');
			}

		}

	}

	public function logout(){
		
		unset($_SESSION['user']);
		$this->session->setFlash('Vous êtes maintenant déconnecté','info');
		$this->redirect('/');


	}	

	public function register(){

		$this->loadModel('Users');
		$this->layout = 'none';
		$d = array();		

		if($this->request->data){
			if($this->Users->validates($this->request->data,'register')){

				//Password hash & salt
				$this->request->data->salt = randomString(10);
				$this->request->data->hash = md5($this->request->data->salt.$this->request->data->password);
				$this->request->data->codeactiv = randomString(25);
				$this->request->data->password = '';
				$this->request->data->confirm = '';
				//Lang
				$this->request->data->lang = Conf::$lang;
				//Date d'inscription
				$this->request->data->date = unixToMySQL(time());
				//Sauvegarde				
				$this->Users->save($this->request->data);

				$user_id = $this->Users->id;														
				$successMail = $this->sendValidateMail(array(
										'dest'      =>$this->request->data->mail,
										'user'      =>$this->request->data->login,
										'codeactiv' =>$this->request->data->codeactiv,
										'user_id'   =>$user_id										
										));

				if($successMail === true) {
					$d['Success'] = true;
					$this->session->setFlash("Votre inscription <strong>a bien été enregistré</strong>","success");
					$this->session->setFlash("Un email <strong>a été envoyé</strong> à votre boite mail. Pour confirmer votre incription, <strong>veuillez cliquer sur le lien</strong> présent dans cette email", "info");
					$this->session->setFlash("Il est possible que ce mail soit placé parmis les <strong>indésirables ou spam</strong> , pensez à vérifier !", "error");
				}
				else {
					$d['Success'] = false;
					$this->session->setFlash("Il y a eu une erreur lors de l'envoi de l'email de validation", "error");
				}
			}
			else {				
				$this->session->setFlash("Veuillez vérifier vos informations",'error');
			}
			//debug($this->request->data);
		}
		$this->set($d);
	}

	public function check(){

		$this->loadModel('Users');
		$this->layout = 'none';
		$d['result'] ='';

		if($this->request->data){
			$data = $this->request->data;
			$type = $data->type;
			$value = $data->value;			
			$user = $this->Users->findFirst(array(
				'fields'=> $type,
				'conditions' => array($type=>$value))
			);
			if(!empty($user)) $d['result'] = 'exist';
		}
	
		$this->set($d);	
	}

	public function sendValidateMail($data)
    {
    	extract($data);

		$lien = "http://localhost/ypp/?murl=".urlencode("/ypp/users/login?code=".$codeactiv."&uid=".$user_id."&login=".$user);

        //Création d'une instance de swift transport (SMTP)
        $transport = Swift_SmtpTransport::newInstance()
          ->setHost('smtp.manifeste.info')
          ->setPort(25)
          ->setUsername('admin@manifeste.info')
          ->setPassword('XSgvEPbG');

        //Création d'une instance de swift mailer
        $mailer = Swift_Mailer::newInstance($transport);
       
        //Genère un mot de passe aléatoire
        $pass = randomString(10);
       
        //Récupère le template et remplace les variables
        $body = file_get_contents('../view/mail/validateAccount.html');
        $body = preg_replace("~{site}~i", Conf::$Website, $body);
        $body = preg_replace("~{user}~i", $user, $body);
        $body = preg_replace("~{lien}~i", $lien, $body);

        //Création du mail
        $message = Swift_Message::newInstance()
          ->setSubject("Validation de l'inscription à ".Conf::$Website)
          ->setFrom('noreply@'.Conf::$Website, Conf::$Website)
          ->setTo($dest, $user)
          ->setBody($body, 'text/html', 'utf-8')
          ->addPart("Bonjour {$user}, voici votre nouveau mot de passe: {$pass}", 'text/plain');
       
        //Envoi du message et affichage des erreurs éventuelles
        if (!$mailer->send($message, $failures))
        {
            echo "Erreur lors de l'envoi du mail à :";
            print_r($failures);
        }
        else return true;
    }


    public function account(){    	

    	$this->loadModel('Users');
    	//$this->layout = 'none';


    	if($this->session->user('user_id'))
    	{
	    	$user_id = $this->session->user('user_id');
	    	
	    	/*======================
				Requete POST 
			========================*/
	    	if($this->request->data) {							    		


	    		/*====================
					MODIFY INFO
	    		=====================*/
	    		if($this->request->post('modify') == 'informations'){

	    			if($this->Users->validates($this->request->data,'account_info')){

	    				if($this->Users->saveUser($this->request->data,$user_id)){

	    					$this->session->setFlash('Your profil have been saved ! ','success');
	    				}
	    				else {
	    					$this->session->setFlash("Sorry but something goes wrong please retry",'error');
	    				}
			    		
		    		}
		    		else 
		    			$this->session->setFlash('Please review your informations','error');
		    	
	    		}


	    		/*===================
	    		 *   MODIFY AVATAR
	    		===================== */
	    		if($this->request->post('modify') == 'avatar'){

	    			if($this->Users->validates($this->request->data,'account_avatar')){

	    				if($this->Users->saveUserAvatar($user_id)){

	    					$this->session->setFlash('Your avatar have been changed ! ', 'success');
	    				}
	    				else
	    					$this->session->setFlash('Sorry something goes wrond.. Please retry', 'error');
	    			}	    			
	    			else
	    				$this->session->setFlash('Please review your file','error');
	    		}
	    		/*====================
					MODIFY PASSWORD
	    		=====================*/
	    		if($this->request->post('modify') == 'password')
	    		{
	    			
	    			if($this->Users->validates($this->request->data,'account_mdp')){

		    			if($this->request->post('password') == $this->request->post('confirm')){

		    				$db = $this->Users->findFirst(array(
		    					'fields' => 'user_id,salt,hash',
		    					'conditions'=> array('user_id'=>$user_id)
		    					));

		    				if($db->hash == md5($db->salt.$this->request->post('oldpassword'))){

		    					$data = new stdClass();
		    					$data->hash = md5($db->salt.$this->request->post('oldpassword'));
		    					$data->user_id = $user_id;
		    					
		    					$this->Users->save($data);
		    					$this->session->setFlash('Your password has been changed !');

		    				}
		    				else $this->session->setFlash('Your old password is not good','error');
		    			}
		    			else $this->session->setFlash('Your new passwords are not the same','error');
		    		}
		    		else 
		    			$this->session->setFlash('Please review your informations','error');
	    		}

	    		/*====================
					MODIFY DELETE
	    		=====================*/
	    		if($this->request->post('modify') == 'delete'){

	    			if($this->Users->validates($this->request->data,'account_delete')){

	    				$db = $this->Users->findFirst(array(
	    					'fields'=>'hash,salt',
	    					'conditions'=>array('user_id'=>$user_id)
	    					));
	    				
	    				if($db->hash == md5($db->salt.$this->request->post('password'))){

	    					
	    					$this->Users->delete($user_id);
	    					unset($_SESSION['user']);
	    					$user_id = 0;
	    					$this->session->setFlash('Your account has been delete... <strong>Wait ? Why did you do that ??</strong>');

	    				}
	    				else
	    					$this->session->setFlash('Your password is not good','error');

	    			}
	    			else
	    				$this->session->setFlash('Please review your password','error');

	    		}
	    			    			   
		    	
		    }

	    	$user = $this->Users->findFirst(array(
					'fields'=> 'user_id,login,avatar,mail,prenom,nom,statut,bonhom,pays,lang',
					'conditions' => array('user_id'=>$user_id))
				);
	    	    	

	    	$this->request->data = $user;

	    	$var['user'] = $user;

	    	$this->set($var);
	    }
	    else {
	    	$this->e404('You are not logged');
	    }

    }



    /**
        
			User Thread

        */ 
    public function thread(){


    	$this->loadModel('Users');
    	$this->loadModel('Manifs');
    	$this->loadModel('Comments');


    	if($this->session->user()){

    		
    		if(is_numeric($this->session->user('user_id'))){


    			$user_id = $this->session->user('user_id');

    			//User
    			$d['user'] = $this->Users->findUsers(array('fields'=>'user_id,login,avatar,bonhom', 'conditions'=>array('user_id'=>$user_id)));
    			//Participations
    			$d['protests'] = $this->Users->findParticipations('P.id,P.manif_id,M.logo,D.nommanif',array($user_id));


    			//Timeline
    			$timing = $this->Users->findUserThread($user_id);
    			$thread = array();
    			//Fill the timeline
    			foreach($timing as $t){

					$a          = array();
					$a['TYPE']  = $t->TYPE;
					$a['DATE']  = $t->date;
					$a['RNAME'] = $t->relatedName;
					$a['RID']   = $t->relatedID;
					$a['RSLUG'] = $t->relatedSlug;
					$a['RLOGO'] = $t->relatedLogo;

    				if( $t->TYPE == 'PROTEST'){

    					$a['OBJ'] = $this->Manifs->findProtesters(array(
    						'fields'=>array('P.id as pid','U.user_id','U.login','P.date','M.logo','M.id as manif_id','D.nommanif','D.slug'),
    						'conditions'=>array('P.id'=>$t->id)
    						));
    				}
    				elseif( $t->TYPE == 'COMMENT'){

    					$a['OBJ'] = $this->Comments->findComments(array(
    						'fields'=>array('*'),
    						'comment_id'=> $t->id
    						));

    				}
    				elseif( $t->TYPE == 'NEWS'){

    					$a['OBJ'] = $this->Comments->findComments(array(
    						'fields'=>array('*'),
    						'comment_id'=> $t->id
    						));
    				}

    				$thread[] = $a;
    			}


    			

    		}
    	}

    	$d['thread'] = $thread;
    	$this->set($d);

    }  

}

 ?>