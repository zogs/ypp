<?php 
class UsersController extends Controller{


	public $primaryKey = 'user_id'; //Nom de la clef primaire de la table

	public function login(){

		$this->layout = 'default';
		$this->loadModel('Users');

		if($this->request->data){		
			
			$data = $this->request->data;
			$login = $data->login;

			if(strpos($login,'@'))
				$field = 'email';
			else
				$field = 'login';
			
			$user = $this->Users->findFirst(array(
				'fields'=> 'user_id,login,bonhom,avatar,hash,salt,status,pays,lang',
				'conditions' => array($field=>$login))
			);

			$user = $this->Users->JOIN('groups',array('group_id','logo as avatar','slug'),array('user_id'=>$user->user_id),$user);
			
			if(!empty($user)){

				if($user->hash == md5($user->salt.$data->password)){

					unset( $user->hash);
					unset( $user->salt);
					unset($_SESSION['user']);
					unset($_SESSION['token']);

					$this->user = $user;

					$this->session->write('user', $user);
					$this->session->setToken();				
					$this->session->setFlash('Vous êtes maintenant connecté');

					$loc = $_SERVER['HTTP_REFERER'];
					if(strpos($loc,'users/login')&&strpos($loc,'users/validate')){
				
						//$this->redirect('users/thread');
					}				
					//else
						//$this->reload(); 
									
	

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

	}

	public function logout(){
		
		unset($_SESSION['user']);
		unset($_SESSION['token']);
		$this->session->setFlash('Vous êtes maintenant déconnecté','info');	
		$this->reload();


	}	

	/*===========================================================	        
	Register
	@param object of a user
	============================================================*/
	public function register( $data = null){

		$this->loadModel('Users');
		$this->layout = 'none';

		$d = array();		

		
		if(isset($data) && is_object($data)){
			$data = $data;
		}
		elseif($this->request->data){
			$data = $this->request->data;
		}


		if($data){
			if($this->Users->validates($data,'register')){

				//user object
				$user = new stdClass();
				$user = $data;
				$user->salt = randomString(10);
				$user->hash = md5($user->salt.$data->password);
				$user->codeactiv = randomString(25);
				$user->password = '';
				$user->confirm = '';
				$user->lang = Conf::$lang;
				$user->date = unixToMySQL(time());

								
				//Sauvegarde
				if($this->Users->save($user)) {

					$user_id = $this->Users->id;

					if(isset($user->status) && $user->status!='group')
						$this->session->setFlash("<strong>Welcome</strong>","success");


					if($this->sendValidateMail(array('dest'=>$user->email,'user'=>$user->login,'codeactiv' =>$user->codeactiv,'user_id'=>$user_id)))
					{
						$d['Success'] = true;						
						$this->session->setFlash("Un email <strong>a été envoyé</strong> à votre boite email. Pour confirmer votre incription, <strong>veuillez cliquer sur le lien</strong> présent dans cette email", "info");
						$this->session->setFlash("Il est possible que ce email soit placé parmis les <strong>indésirables ou spam</strong> , pensez à vérifier !", "error");
					}
					else {
						$d['Success'] = false;
						$this->session->setFlash("Il y a eu une erreur lors de l'envoi de l'email de validation", "error");
					}
				}
				else {

					$d['Success'] = false;
					$this->session->setFlash("Il y a eu une erreur lors de l'enregistrement dans la base", "error");
				}
			}
			else {				
				$this->session->setFlash("Veuillez vérifier vos informations",'error');
			}
			//debug($this->request->data);
		}
		$this->set($d);
	}



	/*===========================================================	        
	Validate
	Validate the email of the user	
	============================================================*/
	public function validate(){

		$this->loadModel('Users');
		$this->view = 'users/login';

		if($this->request->get('c') && $this->request->get('u') ) {

			$get       = $this->request->get;
			$user_id   = base64_decode(urldecode($get->u));			
			$code_url = base64_decode(urldecode($get->c));

			$user = $this->Users->findFirst(array(
				'fields'=>array('login','codeactiv'),
				'conditions'=>array('user_id'=>$user_id)
				));


			if(!empty($user)){

				if($user->codeactiv == $code_url) {
					$data =  new stdClass();
					$data->user_id = $user_id;
					$data->valid = 1;
					$this->Users->save($data);

					$this->session->setFlash('<strong>Bravo</strong> '.$user->login.' ! Tu as validé ton inscription','success');
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

public function account($action = null){    	

    	$this->loadModel('Users');
    	//$this->layout = 'none';

    	/*======================
			If user is logged
		========================*/
    	if($this->session->user('user_id'))
    	{

    		//if user is a group ,redirect to group page
    		if($this->session->user('status')=='group'){

    			$this->redirect('groups/account/'.$this->session->user('group_id').'/'.$this->session->user('slug'));
    		}


	    	$user_id = $this->session->user('user_id');
	    	
	    	/*======================
				If POST DATA are sended
			========================*/
	    	if($this->request->data) {							    		


	    		/*====================
	    			MODIFY LOGIN
	    		====================*/
	    		if($this->request->post('action')=='login'){

	    			if($this->Users->validates($this->request->data,'account_login')){

	    				$user = $this->Users->findFirst(array('fields'=>'login','conditions'=>array('login'=>$this->request->post('login'))));

	    				if(empty($user->login)){

	    					if($this->Users->saveUser($this->request->data,$user_id)){

								$this->session->setFlash("Your login have been changed !","success");

								//get and update session
								$user = $this->session->user('obj');
		    					$user->login = $this->request->data->login;
		    					$this->session->write('user', $user);
							}
							else{
								$this->session->setFlash("Your login have not been changed, please retry","error");
							}
	    				}
	    				else {
	    					$this->session->setFlash("This login is already in use","error");
	    				}
	    			}
	    		}
	    		else {
	    			if($this->request->post('login')) unset($_POST['login']);
	    		}

	    		/*====================
	    			MODIFY EMAIL
	    		====================*/
	    		if($this->request->post('action')=='email'){



	    			if($this->Users->validates($this->request->data,'account_email')){

	    				$user = $this->Users->findFirst(array('fields'=>'email','conditions'=>array('email'=>$this->request->post('email'))));
	    				
	    				if(empty($user->email)){

	    					if($this->Users->saveUser($this->request->data,$user_id)){

								$this->session->setFlash("Your email have been changed !","success");
							}
							else{
								$this->session->setFlash("Your email have not been changed, please retry","error");
							}
	    				}
	    				else {
	    					$this->session->setFlash("This email is already in use","error");
	    				}
	    			}	    			
	    		}
	    		else {
	    			if($this->request->post('email')) unset($_POST['email']);
	    		}


	    		/*====================
					MODIFY INFO
	    		=====================*/
	    		if($this->request->post('action') == 'profil'){

	    			if($this->Users->validates($this->request->data,'account_profil')){


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
	    		if($this->request->post('action') == 'avatar'){

	    			if($this->Users->validates($this->request->data,'account_avatar')){

	    				if($this->Users->saveUserAvatar($user_id)){

	    					$this->session->setFlash('Your avatar have been changed ! ', 'success');

	    					//get the new avatar and set it in the session
	    					$new = $this->Users->findFirst(array('fields'=>'avatar','conditions'=>array('user_id'=>$user_id)));
	    					$user = $this->session->user('obj');
	    					$user->avatar = $new->avatar;
	    					$this->session->write('user', $user);
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
	    		if($this->request->post('action') == 'password')
	    		{
	    			
	    			if($this->Users->validates($this->request->data,'account_password')){

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
	    		if($this->request->post('action') == 'delete'){

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

		    //get account info
	    	$user = $this->Users->findFirst(array(
					'conditions' => array('user_id'=>$user_id))
				);	    	    	
	    	// /!\ request->data used by Form class
	    	$this->request->data = $user;

	    	$d['user'] = $user;

	    	//action
	    	if(!isset($action)) $action = 'profil';
	    	$d['action'] = $action;

	    	$this->set($d);
	    }
	    else {

	    	$this->redirect('/');	    	
	    }

    }




	public function recovery(){

		$this->loadModel('Users');

		$action='';
		
		//if user past the link we mailed him
		if($this->request->get('c') && $this->request->get('u') ){

			
			//find that user 
			$user_id = base64_decode(urldecode($this->request->get('u')));
			$user = $this->Users->findFirst(array(
				'fields'=>array('user_id','salt'),
				'conditions'=>array('user_id'=>$user_id)));
			
			//check the recovery code
			$code = base64_decode(urldecode($this->request->get('c')));
			$hash = md5($code.$user->salt);
			$user = $this->Users->findFirst(array(
				'table'=>T_USER_RECOVERY,
				'fields'=>'user_id',
				'conditions'=>'user_id='.$user_id.' AND code="'.$hash.'" AND date_limit >= "'.unixToMySQL(time()).'"'));

			//if this is good
			if(!empty($user)){

				//show password form
				$this->session->setFlash('Enter your new password','success');
				$action = 'show_form_password';

			}
			else {
				//else the link isnot good anymmore
				$this->session->setFlash('Your link is not valid anymore. Please ask for a new password reset.','error');
				$action = 'show_form_email';
				
			}

			$d['code'] = $code;
			$d['user_id'] = $user_id;

		}

		//if user enter a new password
		if($this->request->post('password') && $this->request->post('confirm') && $this->request->post('code') && $this->request->post('user')){


			$data    = $this->request->post();
			
			//find that user
			$user_id = $data->user;
			$user = $this->Users->findFirst(array(
				'fields'=>array('user_id','salt'),
				'conditions'=>array('user_id'=>$user_id)));

			//check the recovery code
			$code = md5($data->code.$user->salt);
			$user = $this->Users->findFirst(array(
				'table'=>T_USER_RECOVERY,
				'fields'=>'user_id',
				'conditions'=>'user_id='.$user_id.' AND code="'.$code.'" AND date_limit >= "'.unixToMySQL(time()).'"'));

			//if the code is good
			if(!empty($user)){

				unset($data->code);
				unset($data->user);
				
				//validates the password
				if($this->Users->validates($data,'recovery_mdp')){

					//save new password
					$new = new stdClass();
					$new->salt = randomString(10);
					$new->hash = md5($new->salt.$data->password);
					$new->user_id = $user->user_id;
					if($this->Users->save($new)){

						//find the recovery data 
						$rec = $this->Users->findFirst(array(
							'table'=>T_USER_RECOVERY,
							'fields'=>array('id'),
							'conditions'=>array('user_id'=>$user_id,'code'=>$code)));

						//supress recovery data
						$del = new stdClass();
						$del->table = T_USER_RECOVERY;
						$del->key = K_USER_RECOVERY;
						$del->id = $rec->id;
						$this->Users->delete($del);

						//redirect to connexion page
						$this->session->setFlash("Your password have been changed !","success");
						$this->redirect('users/login');
					}
					else {
						$action = 'show_form_password';
						$this->session->setFlash("Error while saving. Please retry","error");
					}
				}
				else {
					$action = 'show_form_password';
					$this->session->setFlash("Please review your data","error");
				}

			}
			else
			{
				$action = 'show_form_email';
				$this->session->setFlash("Error. Please ask for a new password reset.","error");
			}

			$d['code'] = $code;
			$d['user_id'] = $user_id;



		}

		//If user enter his email address
		if( $this->request->post('email') ) {

			$action = 'show_form_email';

			//check his email
			$user = $this->Users->findFirst(array(
				'fields'=>array('user_id','email','login','salt'),
				'conditions'=>array('email'=>$this->request->post('email')),				
			));

			if(!empty($user)){

				//check if existant recovery data
				$recov = $this->Users->find(array(
					'table'=>T_USER_RECOVERY,
					'fields'=>array('id'),
					'conditions'=>array('user_id'=>$user->user_id)
					));

				//if exist, delete it
				if(!empty($recov)){

					$del = new stdClass();
					$del->table = T_USER_RECOVERY;
					$del->key = K_USER_RECOVERY;
					$del->id = $recov[0]->id;
					$this->Users->delete($del);
				}

				//create new recovery data
				$code = randomString(100);

				$rec = new stdClass();				
				$rec->user_id = $user->user_id;
				$rec->code = md5($code.$user->salt);
				$rec->date_limit = unixToMySQL(time() + (2 * 24 * 60 * 60));
				$rec->table = T_USER_RECOVERY;
				$rec->key = K_USER_RECOVERY;

				//save it
				if($this->Users->save($rec)){

					//send email to user
					if($this->sendRecoveryMail(array('dest'=>$user->email,'user'=>$user->login,'code' =>$code,'user_id'=>$user->user_id))){

						$this->session->setFlash('An email have been send to you.','success');
						$this->session->setFlash("Please tcheck the spam box if you can't find it.","warning");

					}
					else{
						$this->session->setFlash('Error while sending the email. users/recovery','error');
						
					}
				}
				else{
					$this->session->setFlash('Error while saving data. users/recovery','error');
					
				}
			}
			else {
				$this->session->setFlash('This email is not in our database','error');
			}


		}

		$d['action'] = $action;
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


	public function sendRecoveryMail($data)
    {
    	extract($data);

		$lien = "http://localhost/ypp/users/recovery?c=".urlencode(base64_encode($code))."&u=".urlencode(base64_encode($user_id));

        //Création d'une instance de swift transport (SMTP)
        $transport = Swift_SmtpTransport::newInstance()
          ->setHost('smtp.manifeste.info')
          ->setPort(25)
          ->setUsername('admin@manifeste.info')
          ->setPassword('XSgvEPbG');

        //Création d'une instance de swift mailer
        $mailer = Swift_Mailer::newInstance($transport);
       
        //Récupère le template et remplace les variables
        $body = file_get_contents('../view/email/recoveryPassword.html');
        $body = preg_replace("~{site}~i", Conf::$Website, $body);
        $body = preg_replace("~{user}~i", $user, $body);
        $body = preg_replace("~{lien}~i", $lien, $body);

        //Création du mail
        $message = Swift_Message::newInstance()
          ->setSubject("Change ton mot de passe")
          ->setFrom('noreply@'.Conf::$Website, Conf::$Website)
          ->setTo($dest, $user)
          ->setBody($body, 'text/html', 'utf-8')
          ->addPart("Hey {$user}, copy this link ".$lien." in your browser to change your password. Don't stop the Protest.", 'text/plain');
       
        //Envoi du message et affichage des erreurs éventuelles
        if (!$mailer->send($message, $failures))
        {
            echo "Erreur lors de l'envoi du email à :";
            print_r($failures);
        }
        else return true;
    }

	public function sendValidateMail($data)
    {
    	extract($data);

		$lien = "http://localhost/ypp/users/validate?c=".urlencode(base64_encode($codeactiv))."&u=".urlencode(base64_encode($user_id));

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
        $body = file_get_contents('../view/email/validateAccount.html');
        $body = preg_replace("~{site}~i", Conf::$Website, $body);
        $body = preg_replace("~{user}~i", $user, $body);
        $body = preg_replace("~{lien}~i", $lien, $body);

        //Création du mail
        $message = Swift_Message::newInstance()
          ->setSubject("Validation de l'inscription à ".Conf::$Website)
          ->setFrom('noreply@'.Conf::$Website, Conf::$Website)
          ->setTo($dest, $user)
          ->setBody($body, 'text/html', 'utf-8')
          ->addPart("Hey {$user}, copy this link ".$lien." in your browser. Welcome on the Protest.", 'text/plain');
       
        //Envoi du message et affichage des erreurs éventuelles
        if (!$mailer->send($message, $failures))
        {
            echo "Erreur lors de l'envoi du email à :";
            print_r($failures);
        }
        else return true;
    }


    


    public function index(){

    	if($this->session->user()){
    		$this->thread();
    	}
    	else {
    		$this->redirect('users/login');
    	}
    	
    }

    /**
    *    
	*	User Thread
	*
    */ 
    public function thread(){

    	$this->view = 'users/thread';
    	$this->loadModel('Users');
    	$this->loadModel('Manifs');
    	$this->loadModel('Comments');

    	//if user is logged
    	if($this->session->user()){

    		//if user is numeric
    		if(is_numeric($this->session->user('user_id'))){

    			//if user is a group, redirect to group page
    			if($this->session->user('status')=='group'){

    				$group = $this->Users->findFirst(array(
    					'table'=>'groups',
    					'fields'=>'group_id as id, slug',
    					'conditions'=>array('user_id'=>$this->session->user('user_id'))
    				));

    				$this->redirect('groups/view/'.$group->id.'/'.$group->slug);
    			} 
    				
    			//set $user_id
    			$user_id = $this->session->user('user_id');

    			//User
    			$d['user'] = $this->Users->findUsers(array('fields'=>'user_id,login,avatar,bonhom', 'conditions'=>array('user_id'=>$user_id)));
    			$d['user'] = $d['user'][0];
    			//$d['user']->context = 'userThread';
    			//$d['user']->context_id = $user_id;
    			//Participations
    			$d['protests'] = $this->Users->findParticipations('P.id,P.manif_id,M.logo,D.nommanif',array($user_id));

	    		/*	
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
    						'fields'=>array('P.id as pid','U.user_id','U.login','P.date','M.logo','M.manif_id','D.name','D.slug'),
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
				*/

    			

    		}
    	}
    	else {
    		$this->redirect('/');
    	}


    	$this->set($d);

    }  

}

 ?>