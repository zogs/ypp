<?php

class ConfigModel extends Model {
	
	private $tableStat = 'config_statistics';

	public function incrementTotalComments(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalComments','id'=>1));
	}

	public function incrementTotalReplies(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalReplies','id'=>1));
	}

	public function incrementTotalCommentsOffline(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalComments_offline','id'=>1));
	}

	public function incrementTotalCommentsModerate(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalComments_moderate','id'=>1));
	}

	public function incrementTotalProtests(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtests','id'=>1));
	}

	public function incrementTotalProtestsOnline(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtests_online','id'=>1));
		$this->incrementTotalProtests();
	}

	public function incrementTotalProtestsOffline(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtests_offline','id'=>1));
	}

	public function incrementTotalProtestsModerate(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtests_moderate','id'=>1));
	}

	public function incrementTotalUsers(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers','id'=>1));
	}

	public function incrementTotalUsersOnline(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_online','id'=>1));
		$this->incrementTotalUsers();
	}

	public function incrementTotalUsersOffline(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_offline','id'=>1));
	}

	public function incrementTotalUsersModerate(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_moderate','id'=>1));
	}

	public function incrementTotalUsersAccountType( $account_type ){

		if($account_type=='public')
			$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_publicAccount','id'=>1));
		if($account_type=='pseudo')
			$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_pseudoAccount','id'=>1));
		if($account_type=='anonym')
			$this->increment(array('table'=>$this->tableStat,'field'=>'totalUsers_anonymAccount','id'=>1));
	}

	public function incrementTotalReports(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalReports','id'=>1));
	}

	public function incrementTotalReportsUntreated(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalReports_untreated','id'=>1));
		$this->incrementTotalReports();
	}

	public function incrementTotalReportsTreated(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalReports_treated','id'=>1));
	}

	public function incrementTotalProtesters(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtesters','id'=>1));
	}

	public function incrementTotalProtestersCanceled(){

		$this->increment(array('table'=>$this->tableStat,'field'=>'totalProtesters_canceled','id'=>1));
	}

	public function decrementTotalProtesters(){

		$this->decrement(array('table'=>$this->tableStat,'field'=>'totalProtesters','id'=>1));
	}

}