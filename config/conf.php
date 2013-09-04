<?php

class Conf {


	static $debug = 1;
	static $adminPrefix = 'banane';
	static $Website = 'YouProtest.net';
	static $pays = 'FR';
	static $lang = 'fr';
		
	//Database connexion
	static $databases = array(
			
		'localhost'  => array(
			'host'     => 'localhost',
			'database' => 'ypp',
			'login'    => 'root',
			'password' => ''
			),
		'ypp.zogs.org' =>array(
			'host'     => 'localhost',
			'database' => 'ypp',
			'login'    => 'protestboy',
			'password' => 'protestandresist'
			) 
		);

	static $languageCodes = array("aa" => "Afar","ab" => "Abkhazian","ae" => "Avestan","af" => "Afrikaans","ak" => "Akan","am" => "Amharic","an" => "Aragonese","ar" => "Arabic","as" => "Assamese","av" => "Avaric","ay" => "Aymara","az" => "Azerbaijani","ba" => "Bashkir","be" => "Belarusian","bg" => "Bulgarian","bh" => "Bihari","bi" => "Bislama","bm" => "Bambara","bn" => "Bengali","bo" => "Tibetan","br" => "Breton","bs" => "Bosnian","ca" => "Catalan","ce" => "Chechen","ch" => "Chamorro","co" => "Corsican","cr" => "Cree","cs" => "Czech","cu" => "Church Slavic","cv" => "Chuvash","cy" => "Welsh","da" => "Danish","de" => "German","dv" => "Divehi","dz" => "Dzongkha","ee" => "Ewe","el" => "Greek","en" => "English","eo" => "Esperanto","es" => "Spanish","et" => "Estonian","eu" => "Basque","fa" => "Persian","ff" => "Fulah","fi" => "Finnish","fj" => "Fijian","fo" => "Faroese","fr" => "French","fy" => "Western Frisian","ga" => "Irish","gd" => "Scottish Gaelic","gl" => "Galician","gn" => "Guarani","gu" => "Gujarati","gv" => "Manx","ha" => "Hausa","he" => "Hebrew","hi" => "Hindi","ho" => "Hiri Motu","hr" => "Croatian","ht" => "Haitian","hu" => "Hungarian","hy" => "Armenian","hz" => "Herero","ia" => "Interlingua (International Auxiliary Language Association)","id" => "Indonesian","ie" => "Interlingue","ig" => "Igbo","ii" => "Sichuan Yi","ik" => "Inupiaq","io" => "Ido","is" => "Icelandic","it" => "Italian","iu" => "Inuktitut","ja" => "Japanese","jv" => "Javanese","ka" => "Georgian","kg" => "Kongo","ki" => "Kikuyu","kj" => "Kwanyama","kk" => "Kazakh","kl" => "Kalaallisut","km" => "Khmer","kn" => "Kannada","ko" => "Korean","kr" => "Kanuri","ks" => "Kashmiri","ku" => "Kurdish","kv" => "Komi","kw" => "Cornish","ky" => "Kirghiz","la" => "Latin","lb" => "Luxembourgish","lg" => "Ganda","li" => "Limburgish","ln" => "Lingala","lo" => "Lao","lt" => "Lithuanian","lu" => "Luba-Katanga","lv" => "Latvian","mg" => "Malagasy","mh" => "Marshallese","mi" => "Maori","mk" => "Macedonian","ml" => "Malayalam","mn" => "Mongolian","mr" => "Marathi","ms" => "Malay","mt" => "Maltese","my" => "Burmese","na" => "Nauru","nb" => "Norwegian Bokmal","nd" => "North Ndebele","ne" => "Nepali","ng" => "Ndonga","nl" => "Dutch","nn" => "Norwegian Nynorsk","no" => "Norwegian","nr" => "South Ndebele","nv" => "Navajo","ny" => "Chichewa","oc" => "Occitan","oj" => "Ojibwa","om" => "Oromo","or" => "Oriya","os" => "Ossetian","pa" => "Panjabi","pi" => "Pali","pl" => "Polish","ps" => "Pashto","pt" => "Portuguese","qu" => "Quechua","rm" => "Raeto-Romance","rn" => "Kirundi","ro" => "Romanian","ru" => "Russian","rw" => "Kinyarwanda","sa" => "Sanskrit","sc" => "Sardinian","sd" => "Sindhi","se" => "Northern Sami","sg" => "Sango","si" => "Sinhala","sk" => "Slovak","sl" => "Slovenian","sm" => "Samoan","sn" => "Shona","so" => "Somali","sq" => "Albanian","sr" => "Serbian","ss" => "Swati","st" => "Southern Sotho","su" => "Sundanese","sv" => "Swedish","sw" => "Swahili","ta" => "Tamil","te" => "Telugu","tg" => "Tajik","th" => "Thai","ti" => "Tigrinya","tk" => "Turkmen","tl" => "Tagalog","tn" => "Tswana","to" => "Tonga","tr" => "Turkish","ts" => "Tsonga","tt" => "Tatar","tw" => "Twi","ty" => "Tahitian","ug" => "Uighur","uk" => "Ukrainian","ur" => "Urdu","uz" => "Uzbek","ve" => "Venda","vi" => "Vietnamese","vo" => "Volapuk","wa" => "Walloon","wo" => "Wolof","xh" => "Xhosa","yi" => "Yiddish","yo" => "Yoruba","za" => "Zhuang","zh" => "Chinese","zu" => "Zulu");
	static $languageAvailable = array('fr'=>'Francais','en'=>'English');
	static $languageDefault = 'fr';

	static $cacheLocation = 'D:/wamp/www/ypp/webroot/cache';
	static $cacheLocationDuration = 10080; //(60*24*7);

	static $css = array(						
						'bootstrap/css/bootstrap.css',
						'css/default.css',
						'css/style.css',
						'js/jquery/select2-2.1/select2.css'
						);

	static $js_main = 'js/main.js';
	static $js_dependency = array(	
									'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
									'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',
									'js/jquery/jquery.livequery.min.js',
									'bootstrap/js/bootstrap.js',
									'js/phpfunctions.js',
									'js/jquery/select2-2.1/select2.min.js',
									'js/jquery/jquery.expander.min.js'
								);


	static $reserved = array(
		'login'=>array(
			'array'=>array('admin','administrator','administrateur'),
			'errorMsg'=>'This login is reserved'
			),
		'email'=>array(
			'array'=>array(),
			'errorMsg'=>'This email is reserved')
		);


	static $stepNumerusForMailAuthor = array(100,500,1000,2000,3000,4000,5000,10000,20000,30000,40000,50000,60000,70000,80000,90000,100000,150000,300000,500000,1000000);

		/**
		 * getTransportSwiftMailer
		 * return instance of Swift_SmtpTransport
		 * set the method of sending mail of the application
		 * configure with smtp info 
		 * or choose php mail() function (not recommended)
		 */
	    public static function getTransportSwiftMailer(){

	    	//SMTP configuration
	    	$transport = Swift_SmtpTransport::newInstance()
	    		->setHost('smtp.u-bourgogne.fr')
	    		->setPort(25)
	    		->setUsername('si3804gu')
	    		->setPassword('f4tb0ysl/m');

			//PHP mail() function
			//$transport = Swift_SendmailTransport::newInstance(); 

	    	return $transport;
	    }

	    
	    public static function getHost(){

	    	if(isset($_SERVER['HTTP_HOST'])) return $_SERVER['HTTP_HOST'];
	    	
	    	return self::$websiteURL;
	    }

	    public static function getSiteUrl(){
	    	
	    	$host = self::getHost();
	    	
	    	return self::$websiteURLs[$host];
	    }

}



?>