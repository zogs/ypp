<!DOCTYPE html>
<html lang="<?php echo $this->getLang();?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />		
	<?php $this->loadCSS();?>
	<?php $this->loadJS();?>

	<title><?php echo isset($title_for_layout)?$title_for_layout : 'Ypp';?></title>
	
</head>
<body data-user_id="<?php echo Session::user()->getID(); ?>">


	<div class="navbar navbar-fixed-top navbar-inverse">
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
		<div id="foo-one"></div>
		<div id="foo-sign"><img src="<?php echo Router::webroot('img/sign.png');?>" alt=""></div>
		<div id="foo-two">
			<div class="container foo-content">				
				<div class="fright">YouProtest</div>
			</div>
		</div>
	</footer>

	<style>
		#footer { position:fixed; bottom:-120px; height:150px; z-index:20;width:100%; background-color: rgba(0,0,0,0.1);}
		#foo-one { position:absolute; top:30px; height:120px;  z-index:-20; width:100%; background-color: rgba(0,0,0,0.5);}
		#foo-two { position:absolute; top:30px; z-index:-10; width:100%; background-color: rgba(0,0,0,1);}
		#foo-sign {position:absolute; top:30px; height: 250px; left:30%; z-index:-15;}
		#foo-sign img {height:200px;}

		.foo-content{ padding:10px 0;}
	</style>

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
      // WebFontConfig = {
      //   google: { families: [ 'Bangers','Squada One','Oswald:300,400,700' ] },      
      //   fontinactive: function(fontFamily, fontDescription) { /*alert('Font '+fontFamily+' is currently not available'); */}
      // };

      // (function() {
      //   var wf = document.createElement('script');
      //   wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      //       '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      //   wf.type = 'text/javascript';
      //   wf.async = 'true';
      //   var s = document.getElementsByTagName('script')[0];
      //   s.parentNode.insertBefore(wf, s);
      // })();
</script>





</html>