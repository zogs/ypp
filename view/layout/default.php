<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<link rel="stylesheet" style="text/css" href="<?php echo Router::webroot('bootstrap/css/bootstrap.css'); ?>" />
	<link rel="stylesheet" style="text/css" href="<?php echo Router::webroot('css/style.css'); ?>" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.livequery.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('bootstrap/js/bootstrap.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/phpfunctions.js');?>"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/select2-2.1/select2.min.js') ?>"></script>
	<link rel="stylesheet" style="text/css" href="<?php echo Router::webroot('js/jquery/select2-2.1/select2.css');?>" />
	<script type="text/javascript" src="<?php echo Router::webroot('js/jquery/jquery.expander.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo Router::webroot('js/main.js'); ?>"></script>
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
				<?php if ($this->session->user('login')): ?>
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
						<input type="login" name="login" required="required" placeholder="Login or email" autofocus="autofocus"/>
						<input type="password" name="password" required="required" placeholder="Password" />
						<input type="submit" value="OK" />
					</form>
					<li><a class="callModal" href='<?php echo Router::url('users/register');?>' >Inscription</a></li>	

				<?php endif ?>

			</ul>
		</div>
	  </div>
	</div>

	<div class="container" style="margin-top:42px;">			
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