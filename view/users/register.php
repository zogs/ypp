<div class="register">

	<header>
		<div class="row-fluid band-stripped-left">
			<span class="adaptive-title">
				<span class="title-container">
                    <h1>INSCRIPTION</h1>                                      
                </span> 
			</span>
		</div>
	</header>

	<div class="container">

		<?php echo $this->session->flash();?>


		<form id="form_register" autocomplete="on" action="<?php echo Router::url('users/register'); ?>" method="post"  >	

			<fieldset>
				<div class="form-block">


					<div class="form-fields">
						<div class="account_type">
							<input type="radio" name="account" class="choose_account_type" id="public_type" value="public" <?php echo (isset($data->account)&&$data->account=="public")? 'checked="checked"':'';?> >
							<label for="public_type">
							<img src="<?php echo Router::webroot('img/account_public.png');?>" title="Account public" />
							<strong>PUBLIC</strong>
							<p>can create, protest, and comment everywhere</p>
							</label>
						</div>
						<div class="account_type">
							<input type="radio" name="account" class="choose_account_type" id="pseudo_type" value="pseudo" <?php echo (isset($data->account)&&$data->account=="pseudo")? 'checked="checked"':'';?> >
							<label for="pseudo_type">				
							<img src="<?php echo Router::webroot('img/account_pseudonym.png');?>" title="Account public" />
							<strong>PSEUDONYM</strong>
							<p>can protest and comment open protests</p>
							</label>
						</div>
						
						<div class="account_type">
							<input type="radio" name="account" class="choose_account_type" id="anonym_type" value="anonym" <?php echo (isset($data->account)&&$data->account=="anonym")? 'checked="checked"':'';?> >
							<label name="account_type" for="anonym_type">				
							<img src="<?php echo Router::webroot('img/account_anonym.png');?>" title="Account public" />
							<strong>ANONYM</strong>
							<p>can protest but can't comment</p>
							</label>
						</div>						
					</div>

					<div class="form-title">
						<h1 class="text title-account"><span>Choose <strong>account</strong> type</span></h1>
					</div>

				</div>

			</fieldset>		
		

			<fieldset>

				<div class="form-block" id="account_info" style="display:<?php echo (isset($account) || $success==true)? "block" : "none";?>">

					<div class="form-title">
						<h1 class="text title-account"><span>Choose <strong>account</strong> type</span></h1>
						<h1 class="text title-profil" style="display:none"><span>Fill <strong>your</strong> profil</span></h1>
					</div>

					<div class="form-fields">						
						
						<span class="anonym_info" style="display:none">Your profil will not be visible by anyone</span>

						<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>	
						<?php echo $this->Form->input('login','',array('icon'=>'icon-user','required'=>'required','placeholder'=>"Votre pseudo",'data-url'=>Router::url('users/check'))) ?>
						<?php echo $this->Form->input('email',"",array('type'=>'email', 'icon'=>"icon-envelope","required"=>"required","placeholder"=>"Votre email",'data-url'=>Router::url('users/check'))) ?>
						<?php echo $this->Form->input('password','',array('type'=>"password",'icon'=>'icon-lock','required'=>'required','placeholder'=>'Votre mot de passe')) ?>
						<?php echo $this->Form->input('confirm','', array('type'=>'password','icon'=>'icon-lock','required'=>'required','placeholder'=>'Confirmer votre mot de passe')) ?>
						<?php echo $this->Form->input('aryoafuro','hidden'); ?>
						<div id="public_info" class="<?php echo (isset($data->account)&&$data->account!='public')? 'hide':'' ?>">
							<?php echo $this->Form->input('prenom',"",array('icon'=>'icon-user','placeholder'=>'Votre prénom')); ?>
							<?php echo $this->Form->input('nom',"",array('icon'=>'icon-user','placeholder'=>'Votre nom')); ?>
							<?php 
							$years = array(0=>"Votre Année de naissance");
							for($i=2000; $i>1950; $i--) $years[$i] = $i;
							echo $this->Form->select('age','',$years,array('class'=>'geo-select','style'=>'width:100%;margin:0;')); 
							?>
							
							<div id="control-location">						
								<div class="control-group">
									<div class="controls">
									<?php

									$this->request('world','locate',array(array('style'=>'width:100%')));
									 ?>				 
									</div>
								</div>	
							</div>
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
												
												//exclude uggly bonhom_3
												if($name=='bonhom_3') continue;

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
						
						<div class="control-group">
							<label class="label-group"></label>
							<div class="controls">									
								<p class="help-inline" id="acceptcheckbox">I agree the <a href="" title="not wrote yet!">Terms of use</a></p>
							</div>
						</div>
						
						<input class="btn btn-inverse btn-large" type="submit" value="start"/>
												
					</div>
				</div>
			</fieldset>
		
		</form>
	</div>
</div>

<div class="bottom-vizu-protest"></div>

<script type="text/javascript">
	

$(document).ready(function(){

	var profilform = $("#account_info");
	var publicinfo = $('#public_info');
	var anonyminfo = $(".anonym_info");
	var checkboxdisplay = 0;
	var checkboxaccept = $("#acceptcheckbox");
	var checkboxhtml = '<input type="checkbox" name="accept" value="1" />';

	//on load init disabled input
	publicinfo.find(':input').attr('disabled','disabled');

	//Add checkbox with javascript
	//for security reason
	if(checkboxdisplay==0){

			checkboxaccept.prepend(checkboxhtml);
			checkboxdisplay=1;
	}


	$(".choose_account_type").bind('click',function(){

		profilform.show();
		$('.title-account').hide();
		$('.title-profil').show();		

	});

	$("#public_type").bind('click',function(){

		profilform.show();
		anonyminfo.hide();
		publicinfo.show().find('input,select').removeAttr('disabled');

	});

	$("#pseudo_type").bind('click',function(){

		profilform.show();
		anonyminfo.hide();
		publicinfo.hide().find('input,select').attr('disabled','disabled');

	});

	$("#anonym_type").bind('click',function(){

		profilform.show();
		anonyminfo.show();
		publicinfo.hide().find('input,select').attr('disabled','disabled');	

	});




});

</script>