<div class="register">

	<div class="intro">
		<div class="charly">
			<img src="<?php echo Router::webroot('img/sign.png');?>" alt="Welcome" />
			<strong>REGISTER</strong>
		</div>
	</div>

	<?php echo $this->session->flash();?>

	<form id="form_register" autocomplete="on" action="<?php echo Router::url('users/register'); ?>" method="post" <?php echo (isset($Success))? 'class="hide"':''; ?>>	

	<fieldset>
		<div class="form-block">
		<div class="form-title">
			<span class="number">1</span><h1 class="text"><span>Choose <strong>account</strong> type</span></h1>
		</div>

		<div class="form-fields">

			<div class="account_type">
				<input type="radio" name="account" id="public_type" value="public" <?php echo (isset($data->account)&&$data->account=="public")? 'checked="checked"':'';?> >
				<label for="public_type">
				<strong>Public</strong>
				<p>Can protest, create protest, and comment all protests.</p>
				</label>
			</div>
			<div class="account_type">
				<input type="radio" name="account" id="pseudo_type" value="pseudo" <?php echo (isset($data->account)&&$data->account=="pseudo")? 'checked="checked"':'';?> >
				<label for="pseudo_type">				
				<strong>Pseudonym</strong>
				<p>Can protest and comment open protests.</p>
				</label>
			</div>
			
			<div class="account_type">
				<input type="radio" name="account" id="anonym_type" value="anonym" <?php echo (isset($data->account)&&$data->account=="anonym")? 'checked="checked"':'';?> >
				<label name="account_type" for="anonym_type">				
				<strong>ANONYM</strong>
				<p>Can protest but can't comment. Noboby can see your profil</p>
				</label>
			</div>
			
		</div>
	</div>

	</fieldset>


	<fieldset>
		<div class="form-block">
		<div class="form-title">
			<span class="number">2</span><h1 class="text"><span>Choose a <strong>protester</strong></span></h1>			
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
								echo (isset($data->bonhom)&&$data->bonhom==$name)? ' checked="checked" ' : '';
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

	</fieldset>	
	

	<fieldset>
		<div class="form-block">
			<div class="form-title">
				<span class="number">3</span><h1 class="text"><span>Fill <strong>your</strong> information</span></h1>
			</div>

			<div class="form-fields">

				<div class="account_info">
					<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>	
					<?php echo $this->Form->input('login','',array('icon'=>'icon-user','required'=>'required','placeholder'=>"Votre pseudo",'data-url'=>Router::url('users/check'))) ?>
					<?php echo $this->Form->input('email',"",array('type'=>'email', 'icon'=>"icon-envelope","required"=>"required","placeholder"=>"Votre email",'data-url'=>Router::url('users/check'))) ?>
					<?php echo $this->Form->input('password','',array('type'=>"password",'icon'=>'icon-lock','required'=>'required','placeholder'=>'Votre mot de passe')) ?>
					<?php echo $this->Form->input('confirm','', array('type'=>'password','icon'=>'icon-lock','required'=>'required','placeholder'=>'Confirmer votre mot de passe')) ?>
					<div id="public_info" class="<?php echo (isset($data->account)&&$data->account!='public')? 'hide':'' ?>">
						<?php echo $this->Form->input('prenom',"",array('icon'=>'icon-user','placeholder'=>'Votre prénom')); ?>
						<?php echo $this->Form->input('nom',"",array('icon'=>'icon-user','placeholder'=>'Votre nom')); ?>
						<?php echo $this->Form->input('age',"",array('icon'=>'icon-gift','placeholder'=>'Votre année de naissance')); ?>
						<div class="fields_public" id="control-location">						
							<div class="control-group">
								<div class="controls">
								<?php

								$this->request('world','locate',array());
								 ?>				 
								</div>
							</div>	
						</div>
					</div>
					<?php echo $this->Form->checkbox('accept','',array('1'=>'I agree with the <a href="">terms of use</a>'));?>
					
					<input class="btn btn-inverse btn-large" type="submit" value="Register"/>


				</div>	
				
			</div>

		</div>
	</fieldset>
	
	
	</form>

</div>

<div class="bottom-vizu-protest"></div>

<script type="text/javascript">
	

$(document).ready(function(){

	var publicinfo = $('#public_info');

	$("#public_type").bind('click',function(){

		publicinfo.show().find('input,select').removeAttr('disabled');

	});

	$("#pseudo_type").bind('click',function(){

		publicinfo.hide().find('input,select').attr('disabled','disabled');

	});

	$("#anon_type").bind('click',function(){

		publicinfo.hide().find('input,select').attr('disabled','disabled');	

	});




});

</script>