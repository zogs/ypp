<?php 
class ReportController extends Controller{

	public function report($context,$context_id){

		//secu
		//user have to be logged
		if(!$this->session->user()->isLog()) $this->redirect('users/login');

		//set reporter id				
		$reporter_id = $this->session->user()->getID();

		//on submit
		$post = $this->request->post();		
		if(!empty($post) && !empty($post->about)){

			$this->loadModel('Alerts');

			//find context
			if($context=='comment'){
				$this->loadModel('Comments');
				$comment          = $this->Comments->findCommentsWithoutJOIN(array('comment_id'=>$context_id));
				$comment          = $comment[0];				
				if(!$comment->exist()) $this->e404('This comment does not exist');
				$post->author_id  = $comment->user_id;
				$post->lang       = $comment->lang;
				$post->context    = 'comments';
				$post->context_id = $comment->getID();
			}
			elseif($context=='protest'){
				$this->loadModel('Manifs');
				$manif = $this->Manifs->findFirstManif(array('conditions'=>array('D.manif_id'=>$context_id),'lang'=>$this->getLang()));				
				if(!$manif->exist()) $this->e404('This protest does not exist');
				$post->author_id  = $manif->administrator_id;
				$post->lang       = $this->getLang();
				$post->context    = 'protest';
				$post->context_id = $manif->getID();
			}
			else throw new zException("context is not valid", 1);


			if($res = $this->Alerts->saveAlert($post,$reporter_id)){

				if(is_numeric($res)){	

					$this->session->setFlash('<strong>Your report have been saved !</strong> We will study it as soon as possible. Please be patient we are a small team...');

					//increement Statistics
					$this->loadModel('Config');
					$this->Config->incrementTotalReportsUntreated();

				}
				elseif($res=='reporter_have_already_report'){
					$this->session->setFlash('<strong>You have aleady reported this content</strong>','info');

				}
			}
			else {
				$this->session->setFlash('Sorry an error occured when saving... Please retry.','danger');

			}
			
		}

	}

	public function admin_index($lang = ''){

		$this->loadModel('Alerts');

		if(empty($lang)) $lang = $this->getLang();

		$reports = $this->Alerts->findUntreatedAlerts($lang);
		foreach ($reports as $key => $report) {
				
				$reports[$key] = $this->Alerts->joinAuthorAndReporter($report);
				$reports[$key] = $this->Alerts->joinContext($report);
		}
		

		return $reports;

	}

	public function admin_clear($id){

		//secu
		if(!$this->session->user()->isLog() || !$this->session->user()->isSuperAdmin()) $this->redirect('users/login');

		$this->loadModel('Alerts');
		
		$this->Alerts->clearSimilarReports($id,$this->session->user()->getID());

		$this->redirect('admin/super/reports');
	}

	public function admin_moderate($id){

		//secu
		if(!$this->session->user()->isLog() || !$this->session->user()->isSuperAdmin()) $this->redirect('users/login');

		$this->loadModel('Alerts');
		$this->loadModel('Config');

		//moderate
		$report = $this->Alerts->findFirst(array('conditions'=>array('id'=>$id)));
		if($report->context=='comments'){
			$this->loadModel('Comments');
			$this->Comments->admin_moderate($report->context_id);
			$this->Config->incrementTotalCommentsModerate();

		}
		elseif($report->context=='protest'){
			$this->loadModel('Manifs');
			$this->Manifs->admin_moderate($report->context_id);
			$this->Config->incrementTotalProtestsModerate();
		}

		//avert author
		$this->loadModel('Users');
		$this->Users->admin_avertUser($report->author_id);

		//clear reports
		$this->Alerts->clearSimilarReports($id,$this->session->user()->getID());

		$this->redirect('admin/super/reports');
	}

	public function admin_deleteContent($id){

		$this->loadModel('Alerts');
		$this->loadModel('Comments');

		$report = $this->Alerts->findFirst(array('conditions'=>array('id'=>$id)));

		if($report->context=='comments'){
			$this->loadModel('Comments');
			$this->Comments->admin_delete($report->context_id);
			$this->Config->incrementTotalCommentsOffline();

		}
		elseif($report->context=='protest'){
			$this->loadModel('Manifs');
			$this->Manifs->admin_delete($report->context_id);
			$this->Config->incrementTotalProtestsOffline();
		}

		$this->loadModel('Users');
		$this->Users->admin_avertUser($report->author_id);

		$this->Alerts->clearSimilarReports($id,$this->session->user()->getID());

		$this->redirect('admin/super/reports');
	}

	public function admin_warnReporters($id){

		//secu
		if(!$this->session->user()->isLog() || !$this->session->user()->isSuperAdmin()) $this->redirect('users/login');

		//get reporter id
		$this->loadModel('Alerts');
		$report = $this->Alerts->findFirst(array('conditions'=>array('id'=>$id)));
		//get similar reports
		$reports = $this->Alerts->find(array('conditions'=>array('context'=>$report->context,'context_id'=>$report->context_id),'fields'=>'reporter_id'));
		//avert authors
		$this->loadModel('Users');
		foreach ($reports as $key => $r) {
			
			$this->Users->admin_avertUser($r->reporter_id);
		}
		//clear reports
		$this->Alerts->clearSimilarReports($id,$this->session->user()->getID());

		$this->redirect('admin/super/reports');
	}

	public function admin_warnReporter($id){

		//secu
		if(!$this->session->user()->isLog() || !$this->session->user()->isSuperAdmin()) $this->redirect('users/login');

		//get reporter id
		$this->loadModel('Alerts');
		$report = $this->Alerts->findFirst(array('conditions'=>array('id'=>$id)));
		//avert author
		$this->loadModel('Users');			
		$this->Users->admin_avertUser($report->reporter_id);
		
		//clear reports
		$this->Alerts->clearSimilarReports($id,$this->session->user()->getID());

		$this->redirect('admin/super/reports');
	}

} ?>