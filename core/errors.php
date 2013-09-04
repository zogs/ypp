<?php 
class zHandlingErrors {

	public static function handler($Exception){

		if(DEBUG==0){
			$controller = new Controller();
			$controller->e404('un pti bout de code est surement mal écrit...','Oups une erreur !');
		}

		if(DEBUG==1){
			
		}

		if(DEBUG==2){
			
			self::display($Exception);
		}

		
	}

	private static function display($e){

		// echo '<pre>';
		// print_r($e);
		// echo '</pre>';
			
		$error = new stdClass();
		$error->msg = $e->getMessage();
		$error->code = $e->getCode();
		$error->line = $e->getLine();
		$error->file = $e->getFile();
		$error->context = $e->getTraceAsString();
		
		echo '<div style="position:relative;width:500px;z-index:100;">
				<p>
					<span style="color:grey;font-size:small">Erreur '.$error->code.'</span>
					<h3>'.$error->msg.'</h3>
					<br />
					<h4><span>'.$error->file.'</span>  <span style="color:blue">line '.$error->line.'</span></h4>
					<br />

				</p>

				<p></p>
				';
		echo '<pre>';
		print_r($error->context);
		echo '</pre>';

		echo '</div>';
		// $controller = new Controller();
		// $controller->exception($error);


	}
}

class zErrorException extends ErrorException {



	public function __construct($errno, $errstr, $errfile, $errline){
		

		$this->message = $errstr;
		$this->code = $errno;
		$this->line = $errline;
		$this->file = $errfile;

		zHandlingErrors::handler($this);
	}


}

class zException extends Exception {

	public function __construct($msg='Errors class default message' ,$code = 0, Exception $previous = NULL){

		$this->message = $msg;
		$this->code = $code;
		$this->previous = $previous;
		zHandlingErrors::handler($this);
			
	}

} 



?>