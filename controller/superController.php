<?php 
class SuperController extends Controller {

	public function admin_board(){

		$this->loadModel('Users');
		$this->loadModel('Manifs');
		$this->loadModel('Comments');
		$this->loadModel('Alerts');

		//Users
		$d['totalUsers'] = $this->Users->admin_totalUsers();
		$d['totalTodayConnexion'] = $this->Users->admin_totalTodayConnexion();
		$d['totalWeekConnexion'] = $this->Users->admin_totalWeekConnexion();
		$d['totalMonthConnexion'] = $this->Users->admin_totalMonthConnexion();
		

		//Registration
		$d['totalTodayRegistration'] = $this->Users->admin_totalTodayRegistration();
		$d['totalWeekRegistration'] = $this->Users->admin_totalWeekRegistration();
		$d['totalMonthRegistration'] = $this->Users->admin_totalMonthRegistration();

		//Protests
		$d['totalProtests'] = $this->Manifs->admin_totalProtest();
		$d['totalOnlineProtests'] = $this->Manifs->admin_totalOnlineProtest();
		$d['totalOfflineProtests'] = $this->Manifs->admin_totalOfflineProtest();
		$d['totalModerateProtests'] = $this->Manifs->admin_totalModerateProtest();

		//Comments
		$d['totalComments'] = $this->Comments->admin_totalComments();
		$d['totalOnlineComments'] = $this->Comments->admin_totalOnlineComments();
		$d['totalOfflineComments'] = $this->Comments->admin_totalOfflineComments();
		$d['totalModerateComments'] = $this->Comments->admin_totalModerateComments();
		
		//Reports
		$d['totalReports'] = $this->Alerts->admin_totalAlerts();
		$d['totalUntreatedReports'] = $this->Alerts->admin_totalUntreatedAlerts();
		$d['totalTreatedReports'] = $this->Alerts->admin_totalTreatedAlerts();

		$d['title'] = 'Administration';

		$this->set($d);
	}

	public function admin_reports(){


	}
} ?>