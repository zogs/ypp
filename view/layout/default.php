<!DOCTYPE html>
<html lang="<?php echo $this->getLang();?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />		
	<?php $this->loadCSS();?>
	<?php $this->loadJS();?>

	<title><?php echo isset($title_for_layout)?$title_for_layout : 'Ypp';?></title>
	
</head>
<body data-user_id="<?php echo Session::user()->getID(); ?>">


	<div class="navbar navbar-fixed-top navbar-inverse navbar-yp">
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
		
			<?php //debug($this);?>

			<ul class="nav pull-right">

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="flag flag-<?php echo $this->getFlagLang();?>"></i><b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php 
						foreach (Conf::$languageAvailable as $lang => $name) {
							
							echo '<a href="?lang='.$lang.'" ><i class="flag flag-'.$this->getFlagLang($lang).'"></i>'.$name.'</a>';
						}

						?>
					</ul>
				</li>

				
				<?php if (Session::user()->isLog()): ?>
					<li><a href="<?php echo Router::url('users/thread');?>">
							<img class="nav-avatar" src="<?php echo Router::webroot(Session::user()->getAvatar()); ?>" />	
							<span class="nav-login"><?php echo Session::user()->getLogin(); ?></span>
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
						<input type="hidden" name="token" value="<?php echo Session::token();?>" />
						<input type="submit" value="OK" />
					</form>
					<li><a href="<?php echo Router::url('users/login');?>">Login</a></li>	
					<li><a href="<?php echo Router::url('users/register');?>" >Inscription</a></li>


				<?php endif ?>

				
			</ul>
		</div>
	  </div>
	</div>

	<div class="container-fluid mainContainer">	
			
		<?php echo $content_for_layout;?>
	</div>

	<footer id="footer">
		<div id="foo-one">
			<div class="container">
				<div id="contact">
					<form action="">
						<textarea name="contact" id="contact" placeholder="Got an idea ? Have a question ? Want a sandwitch ? Let us know !"></textarea>
						<input type="submit" value="envoyer">
					</form>
				</div>
				<div class="footer-list">
					<h4>GENERAL</h4>
					<ul>
						<li><a href="<?php echo Router::url('/');?>">Home</a></li>
						<li><a href="<?php echo Router::url('manifs/index');?>">Protests</a></li>						
						<li><a href="<?php echo Router::url('termsofuse');?>">Terms of use</a></li>
						<li><a href="<?php echo Router::url('aboutus');?>">About us</a></li>
					</ul>
				</div>
				<div class="footer-list">
					<h4>ACTION</h4>
					<ul>
						<li><a href="<?php echo Router::url('manifs/create');?>">Create</a></li>
						<li><a href="<?php echo Router::url('share');?>">Share</a></li>
						<li><a href="<?php echo Router::url('report');?>">Report</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="foo-sign">
			<div id="text-sign"></div>
			<img src="<?php echo Router::webroot('img/sign-welcome.png');?>" alt="">
		</div>
		<div id="foo-two">
			<div class="container">	
				<div class="footer-menu">
					<a href="<?php echo Router::url('/');?>">Home</a>
					<a href="<?php echo Router::url('manifs/index');?>">Protests</a>						
					<a href="<?php echo Router::url('termsofuse');?>">Terms of use</a>
					<a href="<?php echo Router::url('termsofuse');?>">Privacy</a>
					<a href="<?php echo Router::url('aboutus');?>">About us</a>
				</div>			
				<div class="footer-brand">YouProtestProject © 2013 <img src="<?php echo Router::webroot('img/logo_yp.png');?>"></div>
			</div>
		</div>
	</footer>


</body>



 <script type="text/javascript">


 	$(document).ready(function(){


 		var timeout;
 		var opened = false;
 		$("#footer").hover(
			function () {

				timeout = setTimeout(function(){ 

					if(opened==false){
						$('#foo-two').animate({top:'-='+$('#foo-two').height()}, 150, 'linear');
						$('#foo-one').delay(50).animate({top:'-='+$('#foo-one').height()}, 150, 'linear');
						$('#foo-sign').delay(50).animate({top:'-='+$('#foo-sign').height()}, 250, 'linear',function(){ opened = true; });
					}
				},300);
			},
			function () {

				clearTimeout(timeout);
				if(opened==true){
					
					$('#foo-two').animate({top:'+='+$('#foo-two').height()}, 100);
					$('#foo-one').animate({top:'+='+$('#foo-one').height()}, 100);
					$('#foo-sign').animate({top:'+='+$('#foo-sign').height()}, 150, 'linear',function(){ opened=false });
				}
			}
			);

 	});
 	/*===========================================================
 		Set security token
 	============================================================*/
 	var CSRF_TOKEN = '<?php echo Session::token(); ?>';


 	/*===========================================================
 		Language of the page
 	============================================================*/
 	var Lang = '<?php echo $this->getLang();?>';


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