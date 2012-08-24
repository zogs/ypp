<div id="account">

<?php echo $this->session->flash();?>
	<div class="account-block" id="myaccount">
		<form class="form-yp" id="form-account" autocomplete="off" action="<?php echo Router::url('users/account'); ?>" method="post" enctype="multipart/form-data">
			<?php echo $this->Form->input('modify','hidden',array('value'=>'informations')); ?>
			<?php echo $this->Form->input('login','Login',array('icon'=>'icon-user','required'=>'required')); ?>
			<?php echo $this->Form->input('mail','Email',array('icon'=>'icon-envelope','type'=>'email','required'=>'required')); ?>			
			<?php echo $this->Form->input('prenom','Prenom',array('icon'=>'icon-user','placeholder'=>'Prenom')); ?>
			<?php echo $this->Form->input('nom','Nom',array('icon'=>'icon-user','placeholder'=>'Nom')); ?>

			<div class="control-group">
				<div class="controls">
					<?php 
						$dir = "img/bonhom/";
						$extension = ".gif";
						$files = glob($dir.'*'.$extension);

						for ($i=0; $i<count($files); $i++){

							$path = $files[$i];
							$name = str_replace($dir,'',$path);
							$name = str_replace($extension,'',$name);
							
							echo '<label class="label_bonhom">';
							echo '<img src="'.Router::webroot($path).'" alt="'.$name.'">';
							echo '<input type="radio" name="bonhom" value="'.$name.'"';
							if($name==$user->bonhom) echo "checked='checked' ";
							echo ' >';
							echo '</label>';
						}
					?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="pays">Pays</label>
				<div class="controls">
					<i class='icon-form icon-home'></i>
					<?php $options = $this->request('world','getCountry'); ?>
					<?php echo $this->Form->generateSelect('pays','',$options,'code','name',$user->pays); ?>
				</div>
			</div>	
			<input class="btn btn-large btn-inverse" type="submit" value="Save Informations" />	
		</form>
	</div>

	<div class="account-block" id="changeavatar">
		<div class='avatar'>
			<img src="<?php echo Router::webroot($user->avatar); ?>" />
		</div>
		<form class="form-yp" action="<?php echo Router::url('users/account'); ?>" method="post" enctype="multipart/form-data">
			<?php echo $this->Form->input('modify','hidden',array('value'=>'avatar')); ?>
			<?php echo $this->Form->input('avatar','Avatar',array('icon'=>'icon-bullhorn','type'=>'file')); ?>
			<input type="submit" class="btn btn-large btn-inverse" value="Change avatar" />
		</form>
	</div>


	<div class="account-block" id="changepassword">
		<form class="form-yp" autocomplete="off" action="<?php echo Router::url('users/account'); ?>" method="post">
			<?php echo $this->Form->input('modify','hidden',array('value'=>'password')); ?>
			<?php echo $this->Form->input('oldpassword','Old password',array('type'=>'password','icon'=>'icon-lock')); ?>
			<?php echo $this->Form->input('password','New password',array('type'=>'password','icon'=>'icon-lock')); ?>
			<?php echo $this->Form->input('confirm','Confirm password',array('type'=>'password','icon'=>'icon-lock')); ?>
			<input class="btn btn-large btn-inverse" type="submit" value="Change Password" />	
		</form>
	</div>

	<div class="account-block" id="delete">
			<form class="form-yp" autocomplete="off" action="<?php echo Router::url('users/account'); ?>" method="post">
				<?php echo $this->Form->input('modify','hidden',array('value'=>'delete')); ?>
				<?php echo $this->Form->input('password','Do you want to delete your account ?', array('type'=>'password','icon'=>'icon-lock','placeholder'=>'Type your password and delete')); ?>
				<input class="btn btn-large btn-inverse" type="submit" value="Delete" />
			</form>	
	</div>
	</div>
</div>




