<?php 
class alertsModel extends Model{

	public $table = 'alerts';

	public function clearSimilarReports($id, $mod_id){

		//Get report
		$report = $this->findFirst(array('conditions'=>array('id'=>$id)));
		//GET all similar report
		$reports = $this->find(array('conditions'=>array('context'=>$report->context,'context_id'=>$report->context_id),'fields'=>array('id')));
		//Set as reported
		foreach ($reports as $key => $r) {
			
			$this->clearReport($r->id,$mod_id);
		}

		return true;
	}

	public function clearReport($id,$mod_id){

		$sql = 'UPDATE '.$this->table.' SET treated=1, moderator_id=:moderator_id WHERE id=:id';
		$tab = array('moderator_id'=>$mod_id,'id'=>$id);
		$this->query($sql,$tab);

		//increment statistics
		$this->loadModel('Config');
		$this->Config->incrementTotalReportsTreated();
	}

	public function saveAlert($data,$reporter_id){

		//if reporter have reported this before
		if($this->ifReporterAlreadyAlert($data->context,$data->context_id,$reporter_id)) return 'reporter_have_already_report';

		$a = new stdClass();
		$a->reporter_id = $reporter_id;
		$a->context     = $data->context;
		$a->context_id  = $data->context_id;
		$a->author_id   = $data->author_id;		
		$a->about       = $data->about;
		$a->description = $data->description;
		$a->lang        = $data->lang;
		$a->date        = Date::MysqlNow();

		if($id = $this->save($a)){

			return $id;
		}
		else return false;
	}

	public function ifReporterAlreadyAlert($context,$id,$reporter_id){

		$r = $this->findFirst(array('table'=>$this->table,'conditions'=>array('context'=>$context,'context_id'=>$id,'reporter_id'=>$reporter_id)));

		if(empty($r)) return false;
		else return true;
	}

	public function findUntreatedAlerts($lang){

		$sql= 'SELECT *,count(*) as recurrence FROM '.$this->table.' WHERE lang=:lang AND treated=0 GROUP BY context,context_id ORDER BY recurrence DESC LIMIT 10;';
		$val = array('lang'=>$lang);
		$res = $this->query($sql,$val);

		return $res;
	}

	public function joinAuthorAndReporter($report){

		$this->loadModel('Users');

		$report->reporter = $this->Users->findFirstUser(array('conditions'=>array('user_id'=>$report->reporter_id)));
		$report->author = $this->Users->findFirstUser(array('conditions'=>array('user_id'=>$report->author_id)));

		return $report;
	}

	public function joinContext($report){
			
		if($report->context=='comments'){

			$this->loadModel('Comments');
			$report->content = $this->Comments->getComments($report->context_id);
		}

		if($report->context=='protest'){
			$this->loadModel('Manifs');
			$report->content = $this->Manifs->findFirstManif(array('lang'=>$report->lang,'conditions'=>array('D.manif_id'=>$report->context_id)));
		}					

		return $report;
	}

	public function admin_totalAlerts(){
		$sql = 'SELECT count(*) as total FROM '.$this->table;
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalUntreatedAlerts(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE treated=0';
		$res = $this->query($sql);
		return $res[0]->total;
	}
	public function admin_totalTreatedAlerts(){
		$sql = 'SELECT count(*) as total FROM '.$this->table.' WHERE treated=1';
		$res = $this->query($sql);
		return $res[0]->total;
	}
}

class Alert {

	public function __construct($fields = array()){

		foreach ($fields as $field => $value) {
			
			$this->$field = $value;
		}
	}
} ?>