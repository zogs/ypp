<div class="register">

	<div class="intro">
		<div class="charly">
			<img src="<?php echo Router::webroot('img/sign.png');?>" alt="Welcome" />
			<strong>REGISTER</strong>
		</div>
	</div>

	<?php echo $this->session->flash();?>

	<form id="form_register" autocomplete="on" action="<?php echo Router::url('users/register'); ?>" method="post" <?php echo (isset($Success))? 'class="hide"':''; ?>>	

	<div class="form-block">
		<div class="form-title">
			<span class="number">1</span><span class="text">choose <strong>account</strong> type</span>
		</div>

		<div class="form-fields">

			<div class="account_type">
				<input type="radio" name="account_type" id="public_type" value="public" <?php echo (isset($data)&&$data->account_type=="public")? 'checked="checked"':'';?> >
				<label for="public_type">
				<strong>Public</strong>
				<p>participate and create protest. Can comment all protest.</p>
				</label>
			</div>
			<div class="account_type">
				<input type="radio" name="account_type" id="pseudo_type" value="pseudo" <?php echo (isset($data)&&$data->account_type=="pseudo")? 'checked="checked"':'';?> >
				<label for="pseudo_type">				
				<strong>Pseudonym</strong>
				<p>can protest and comment open protest</p>
				</label>
			</div>
			
			<div class="account_type">
				<input type="radio" name="account_type" id="anonym_type" value="anonym" <?php echo (isset($data)&&$data->account_type=="anonym")? 'checked="checked"':'';?> >
				<label name="account_type" for="anonym_type">				
				<strong>ANONYM</strong>
				<p>can protest but can't comment</p>
				</label>
			</div>
			
		</div>
	</div>

	<div class="form-block">
		<div class="form-title">
			<span class="number">2</span><span class="text">choose a <strong>protester</strong></span>			
		</div>

		<div class="form-fields">
			<div class="bonhom_pickup">
			<?php 
							$dir = "img/bonhom/";
							$extension = ".gif";
							$files = glob($dir.'*'.$extension);

							for ($i=0; $i<count($files); $i++){

								$path = $files[$i];
								$name = str_replace($dir,'',$path);
								$name = str_replace($extension,'',$name);
								
								echo '<div class="bonhom_pick">';
								echo '<input type="radio" name="bonhom" id="'.$name.'" value="'.$name.'" ';
								echo (isset($data)&&$data->bonhom==$name)? ' checked="checked" ' : '';
								echo ' >';
								echo '<label class="label_bonhom" for="'.$name.'">';
								echo '<img src="'.Router::webroot($path).'" alt="'.$name.'">';												
								echo '</label>';
								echo '</div>';
							}
						?>
			</div>
		</div>

	</div>

	<div class="form-block">
		<div class="form-title">
			<span class="number">3</span><span class="text">fill your <strong>information</strong> </span>
		</div>

		<div class="form-fields">

			<div class="account_info">
				<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>	
				<?php echo $this->Form->input('login','',array('icon'=>'icon-user','required'=>'required','placeholder'=>"Votre pseudo",'data-url'=>Router::url('users/check'))) ?>
				<?php echo $this->Form->input('email',"",array('type'=>'email', 'icon'=>"icon-envelope","required"=>"required","placeholder"=>"Votre email",'data-url'=>Router::url('users/check'))) ?>
				<?php echo $this->Form->input('password','',array('type'=>"password",'icon'=>'icon-lock','required'=>'required','placeholder'=>'Votre mot de passe')) ?>
				<?php echo $this->Form->input('confirm','', array('type'=>'password','icon'=>'icon-lock','required'=>'required','placeholder'=>'Confirmer votre mot de passe')) ?>
				<?php echo $this->Form->input('prenom',"",array('icon'=>'icon-user','required'=>'required','placeholder'=>'Votre prénom')) ?>
				<?php echo $this->Form->input('nom',"",array('icon'=>'icon-user','required'=>'required','placeholder'=>'Votre nom')) ?>
				<div class="fields_public">
					
					<div class="control-group">
						<div class="controls">
						<?php

						$this->request('world','locate',array());
						 ?>				 
						</div>
					</div>	
				</div>

				<?php echo $this->Form->checkbox('accept','',array('1'=>'I agree with the <a href="">terms of use</a>'));?>
				
				<input class="btn btn-inverse btn-large" type="submit" value="Register"/>


			</div>	
			
		</div>

	</div>
	
	</form>

</div>

<div class="bottom-vizu-protest"></div>