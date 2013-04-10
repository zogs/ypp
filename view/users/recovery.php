<div class="formulaire">
	
	<?php echo Session::flash(); ?>

	<?php if($action=='' || $action=='show_form_email'): ?>

	<form class="form-yp" action="<?php echo Router::url('users/recovery'); ?>" method="POST">
		<div class="form-block">
			<?php echo $this->Form->input('email','Entrer votre adresse email',array('required'=>'required','icon'=>'icon-envelope')) ;?>
			<?php echo $this->Form->input('token','hidden',array('value'=>Session::token())) ;?>
			<input type="submit" value="Envoyer" />
		</div>
	</form>	

	<?php endif;?>


	<?php if($action=='show_form_password') : ?>

	<form class="form-yp" action="<?php echo Router::url('users/recovery'); ?>" method="POST">

		<div class="form-block">
			
			<?php echo $this->Form->input('code','hidden',array('value'=>$code)) ;?>
			<?php echo $this->Form->input('user','hidden',array('value'=>$user_id)) ;?>	
			<?php echo $this->Form->input('password','New password',array('type'=>'password','icon'=>'icon-lock')) ;?>
			<?php echo $this->Form->input('confirm','Confirm password',array('type'=>'password','icon'=>'icon-lock')) ;?>
			<?php echo $this->Form->input('token','hidden',array('value'=>Session::token())) ;?>
			<input type="submit" value="Envoyer" />
		</div>	

	</form>

<?php endif ;?>
	
</div>