<?php 


?>

<div class="error_page">	
	<div class="sign">
		<div class="oups"><table><tr><td><?php echo $oups ?></td></tr></table></div>
		<img src="<?php echo Router::webroot('/img/sign.png'); ?>"
	</div>
	<div class="bubble">
		<?php echo $message; ?>		
	</div>
</div>