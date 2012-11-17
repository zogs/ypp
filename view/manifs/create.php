<div id="create">
	
	<div class="formulaire">

		<?php echo $this->session->flash(); ?>

		<form id="create-manif" class="" action="<?php echo Router::url('manifs/create/id:'.$manif->id.'/slug:'.$manif->slug); ?>" method="POST" enctype="multipart/form-data">

			<div class="form-block">


					<?php echo $this->Form->input('nommanif',"The Name",array('placeholder'=>'Ex: Occupy YouPorn','value'=>$manif->nommanif)); ?>
					<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/ckeditor.js');?>"></script>
					<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/adapters/jquery.js');?>"></script>
					<script type="text/javascript">
						$(document).ready(function(){
							$( '#inputdescription').ckeditor();
						});
					</script>
					<?php echo $this->Form->input('description','Description',array('type'=>'textarea','placeholder'=>'Type your sex here','value'=>htmlentities($manif->description))); ?>
					<?php echo $this->Form->input('keywords','Keywords',array('placeholder'=>'Separate them with sperm','value'=>$manif->keywords)); ?>
				

				<div class="submit">
					<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>
					<p class="help-inline helper"> By clicking i accept and understand the <a href="">Terms of Use</a></p>
				</div>

			</div>

			<div class="form-block">

				<div class="control-group">
					<label for="cat2" class="control-label">Category</label>
					<p class='label-helper'>Choose a category or a sub-category</p>
					<div class="controls">
						<input type="hidden" id="submit-category" data-url="<?php echo Router::url('manifs/getCategory'); ?>">
						<?php echo $this->Form->Select('cat2','Select category','btn',$cats2,$manif->cat2,'onchange="showCategory(this.value,3)"'); ?>
						<?php if($manif->cat3): ?>
						<?php echo $this->Form->Select('cat3','Select sub-category','btn',$cats3,$manif->cat3,''); ?>
						<?php endif; ?>
					</div>					
				</div>


				<div class="control-group">
					<label for="" class="control-label">Localisation</label>
					<p class='label-helper'>Locate your protest at country/state/city level</p>
					<div class="controls">
						<?php

						$this->request('world','locate',array($manif));
						 ?>				 
					</div>					
				</div>
				
				<?php echo $this->Form->input('logo','Logo',array('type'=>'file','info'=>'height & width :150px, max-weight: 100ko')); ?>								

				<div class="submit">
					<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>
					<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>
				</div>
				
			</div>

		</form>

	</div>



</div>
