<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />		
	<?php

/**
* INCLUDE CSS
* array Conf::$css
* array Controller $css_load
*/
	$css = array();
	if(isset(Conf::$css)) $css = array_merge(Conf::$css,$css);
	
	if(isset($css_load)){
		if(is_string($css_load)) $css_load = array($css_load);
		if(is_array($css_load)) $css = array_merge($css,$css_load);
	}
	foreach ($css as $name => $url) {
		if(strpos($url,'http')!==0) $url = Router::webroot($url);
		echo '<link rel="stylesheet" style="text/css" href="'.$url.'" />';		
	}


/**
 * INCLUDE JAVASCRIPT
 * array Conf::$js 
 * array Conf::$js_dependency
 * array Controller $js_load
 */
	$js = array();
	if(isset(Conf::$js_dependency))	$js = array_merge(Conf::$js_dependency,$js);
	
	if(isset($this->loadJS)){
		if(is_string($this->loadJS)) $this->loadJS = array($this->loadJS);
		if(is_array($this->loadJS)) $js = array_merge($js,$this->loadJS);
	}
	if(isset(Conf::$js_main)){
		if(is_string(Conf::$js_main)) $js[] = Conf::$js_main;
		if(is_array(Conf::$js_main)) $js = array_merge($js,$js_main);
	}
	foreach ($js as $name => $url) {
		
		if(strpos($url,'http')===0) $url = $url;
		else $url = Router::webroot($url);		
		echo '<script type="text/javascript" src="'.$url.'"></script>';
	}

	?>
	<title><?php echo isset($title_for_layout)?$title_for_layout : 'Ypp';?></title>
	
</head>
<body data-user_id="<?php echo $this->session->user('user_id'); ?>">


	<div class="navbar navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
      		<a class="brand" href="#">
	      	<img class="nav-logo" src="<?php echo Router::webroot('img/logo_yp.png'); ?>" />	  			
			</a>
			<form class ="navbar-search pull-left" action="<?php echo Router::url('manifs/index'); ?>" method="get">
			<input type ="text" class="search-query nav-search" name="rch" placeholder="Why You Protest?">
			</form>

			<ul class="nav">
				<li><a href="<?php echo Router::url('manifs/index');?>">Protests.</a></li>
				<li><a href="<?php echo Router::url('create');?>">Create.</a></li>

				
			</ul>
		
			

			<ul class="nav pull-right">
				<?php if ($this->session->user()): ?>
					<li><a href="<?php echo Router::url('users/thread');?>">
							<img class="nav-avatar" src="<?php echo Router::webroot($this->session->user('avatar')); ?>" />	
							<span class="nav-login"><?php echo $this->session->user('login'); ?></span>
					</a></li>
					<li class="dropdown">	
			
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Router::url('users/logout'); ?>">Déconnexion</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo Router::url('users/account'); ?>">Mon Compte</a></li>
							<li><a href="#">Mes manifs</a></li>							
						</ul>
					</li>
				<?php else: ?>

					<form class="loginForm" action="<?php echo Router::url('users/login'); ?>" method='post'>
						<input type="login" name="login" required="required" placeholder="Login or email" autofocus="autofocus" value="pumtchak"/>
						<input type="password" name="password" required="required" placeholder="Password" value="fatboy" />
						<input type="hidden" name="token" value="<?php echo $this->session->token();?>" />
						<input type="submit" value="OK" />
					</form>
					<li><a href="<?php echo Router::url('users/login');?>">Login</a></li>	
					<li><a href="<?php echo Router::url('users/register');?>" >Inscription</a></li>


				<?php endif ?>

			</ul>
		</div>
	  </div>
	</div>

	<div class="container mainContainer">	
			
		<?php echo $content_for_layout;?>
	</div>


	<div class="modal fade" id="myModal"></div>


</body>








<?php 
if($this->request->get('murl') && !$this->session->user()){

	$url = $this->request->get('murl');
	echo "<script type='text/javascript'>callModalBox('".$url."'); </script>";
} 
?>

 <script type="text/javascript">

 	/*===========================================================
 		Set security token
 	============================================================*/
 	var CSRF_TOKEN = '<?php echo $this->session->token(); ?>';

 	/*===========================================================
 		GOOGLE FONTS
 	============================================================*/
      WebFontConfig = {
        google: { families: [ 'Bangers','Squada One','Oswald:300,400,700' ] },      
        fontinactive: function(fontFamily, fontDescription) { /*alert('Font '+fontFamily+' is currently not available'); */}
      };

      (function() {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
      })();
</script>





</html>