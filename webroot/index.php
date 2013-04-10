<?php

error_reporting(E_ALL);
ini_set('display_errors','1');


//$debut = microtime(true);
define('DEBUG',2);
define('WEBROOT',dirname(__FILE__));
define('ROOT',dirname(WEBROOT));
define('DS',DIRECTORY_SEPARATOR);
define('CORE',ROOT.DS.'core');
define('BASE_URL',dirname(dirname($_SERVER['SCRIPT_NAME'])));

//usefull functions
include '../core/functions.php';

//Date time zone
date_default_timezone_set('Europe/Paris');

//Errors gestion
include '../core/errors.php';
function uncatchError($errno, $errstr, $errfile, $errline ) {
	echo 'uncatchError';
    new zErrorException($errno, $errstr, $errfile, $errline);
}
function uncatchException($exception){
	echo 'uncatchException';
	new zException($exception);
}
set_error_handler('uncatchError');
set_exception_handler('uncatchException');


//init autoloader
//page github https://github.com/jonathankowalski/autoload
include '../core/autoloader.php';
$loader = Autoloader::getInstance()
->addDirectory('../config')
->addDirectory('../controller')
->addDirectory('../core')
->addDirectory('../model')
->addEntireDirectory('../lib/SwiftMailer')
->addEntireDirectory('../lib/Zend/I18n');

//Librairy dependency
require '../lib/SwiftMailer/swift_required.php';//Swift mailer


//require_once 'Zend/I18n/Translator/Translator.php';
//Zend_I18n_Translate::setlocale('', '');
//$translate = new Zend\I18n\Translator\Translator();

//debug($translator);

//define routes for the router
new Routes();

//launch the dispacher
new Dispatcher();


?>



<?php
//echo 'Page généré en '.round(microtime(true) - $debut,5).' secondes';
?>

