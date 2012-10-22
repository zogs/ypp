<div class="formulaire">
	
	<?php echo $this->session->flash(); ?>


	<div class="form-block">
		<form class="form-yp" action="<?php echo Router::url('users/login'); ?>" method='post'>
		
				
				<?php echo $this->Form->input('login','Identifiant',array('required'=>'required','placeholder'=>'Pseudo ou E-mail','icon'=>'icon-user')); ?>
				<?php echo $this->Form->input('password','Mot de passe',array('type'=>'password','required'=>'required','placeholder'=>'Mot de passe','icon'=>'icon-lock')); ?>		

				<input type="submit" class="btn btn-large btn-inverse" value="Se connecter"/>		    			
		</form>	
	</div>
</div>
