<div id="create">

	<header>
		<div class="row-fluid band-stripped-left">
			<span class="adaptive-title">
				<img class="logo" src="<?php echo Router::webroot($manif->getLogo());?>" />
                <span class="title-container">
                    <h1><?php echo $manif->getTitle();?></h1>
                    <div class="undertitle">

                        <div class="author"><?php echo $manif->getCreator()->getLogin();?></div>
                        <div class="date"><?php echo $manif->getNumerus();?> manifestants - le <?php echo Date::datefr($manif->getCreateDate());?></div>
                    </div>                    
                </span>   
			</span>
		</div>
	</header>

	<div class="container">
		
		<div class="formulaire">

			<?php echo Session::flash(); ?>
			
			<div class="arianne">

				<?php 
				//========================
				//If protest exist
				if($manif->exist()): ?>
				<a href="<?php echo Router::url('manifs/view/'.$manif->getID().'/'.$manif->getSlug());?>">Voir la manif</a>
				<?php endif; ?>

				<?php 
				//=========================
				//If translation available
				if(!empty($translates)){ 
					echo '<span class="translate-available">';
					foreach ($translates as $translate) {
						echo "<a href='".Router::url('manifs/create/'.$manif->getID().'/'.$manif->getSlug().'?lang='.$translate->lang)."' ><i class='flag flag-".$this->getFlagLang($translate->lang)."'></i> ".Conf::$languageAvailable[$translate->lang]."</a> ";
					} 
					echo '</span>';
				}
				?>
				
			</div>


			<form id="create-manif" class="" action="<?php echo Router::url('manifs/create/'.$manif->getID().'/'.$manif->getSlug()); ?>" method="POST" enctype="multipart/form-data">

				<div class="form-block">


						<?php echo $this->Form->input('nommanif',"The Name",array('placeholder'=>'Ex: Occupy YouPorn','value'=>(($manif->getID()!=''))? $manif->getTitle():'')); ?>
						<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/ckeditor.js');?>"></script>
						<script type="text/javascript" src="<?php echo Router::webroot('js/ckeditor/adapters/jquery.js');?>"></script>
						<script type="text/javascript">
							$(document).ready(function(){
								$( '#inputdescription').ckeditor();
							});
						</script>
						<?php echo $this->Form->input('description','Description',array('type'=>'textarea','placeholder'=>'Type your sex here','value'=>htmlentities($manif->getDescription()))); ?>
						<?php echo $this->Form->input('keywords','Keywords',array('placeholder'=>'Separate them with sperm','value'=>$manif->keywords)); ?>
					
						<p class="help-inline helper">Using this means you understand the <a href="">Terms of Use</a></p>
					

						
						<div class="control-group">
							<label class="control-label"></label>
							<div class="controls">
								<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>

								<div class="lang-select">
									<p class="help-inline helper" style="display:inline">Language :</p>
									<?php echo $this->Form->_select("lang",Conf::$languageAvailable,array("placeholder"=>"Select language","default"=>$this->getLang())); ;?>
								</div>
								
								
							</div>
						</div>
						
						
					

				</div>

				<div class="form-block">
				
					<div class="control-group">
						<label for="cat2" class="control-label">Category</label>
						<p class='label-helper'>Choose a category or a sub-category</p>
						<div class="controls">
							<input type="hidden" id="submit-category" data-url="<?php echo Router::url('manifs/getCategory'); ?>">
							<?php echo $this->Form->_select('cat2',$cats2,array('placeholder'=>'Select category',"default"=>$manif->cat2,"javascript"=>'onchange="showCategory(this.value,3)"')); ?>
							<?php if($manif->cat3): ?>
							<?php echo $this->Form->_select('cat3',$cats3,array('placeholder'=>'Select sub-category',"default"=>$manif->cat3)); ?>
							<?php endif; ?>
						</div>					
					</div>


					<div class="control-group">
						<label for="" class="control-label">Localisation</label>
						<p class='label-helper'>Locate your protest at country/state/city level</p>
						<div class="controls">
							<?php

							$this->request('world','locate',array(array('obj'=>$manif)));
							 ?>				 
						</div>					
					</div>
					
					<?php echo $this->Form->input('logo','Logo',array('type'=>'file','info'=>'height & width :150px, max-weight: 100ko','src'=>Router::webroot($manif->getLogo()))); ?>								

					<div class="submit">						
						<?php echo $this->Form->input("manif_id","hidden",array("value"=>$manif->getID())) ;?>
						<?php echo $this->Form->input('token','hidden',array('value'=>Session::token())) ;?>
						<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>
					</div>
					
				</div>

				<div class="form-block">
					
					<?php echo $this->Form->select("comments","Commentaires",array("open"=>"Les commentaires sont ouverts","onlypublic"=>"Les commentaires sont limités aux comptes public","close"=>"Les commentaires sont fermés"),array('default'=>$manif->comments)) ;?>
					<div class="submit">						
						<?php echo $this->Form->input("manif_id","hidden",array("value"=>$manif->getID())) ;?>
						<?php echo $this->Form->input('token','hidden',array('value'=>Session::token())) ;?>
						<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>
					</div>
				</div>

				<div class='form-block'>
					<div class="control-group">
						<label for="" class="control-label">Effets</label>
						<p class='label-helper'>Personnaliser votre manif</p>
						<div class="controls">
							<?php echo $this->Form->selectNumber("colored","Couleur",100,0,array("placeholder"=>"Pourcentage","helper"=>"% de bonhom en couleur (peut ralentir la page...)",'default'=>$manif->colored)) ;?>				 
							<?php echo $this->Form->select("bonhoms","Bonhom",array("bonhom_1"=>"Homme 1",
																						"bonhom_2"=>'Homme 2',
																						'bonhom_4'=>'Homme 3',
																						'bonhom_5'=>'Homme 4',
																						'bonhom_6'=>'Femme 1',
																						'bonhom_7'=>'Femme 2',
																						'bonhom_8'=>'Femme 3',
																						'bonhom_9'=>'Femme 4'),
																				array('helper'=>'Afffiche un seul type de bonhom','placeholder'=>'Tous','default'=>$manif->bonhoms)) ;?>						

							<?php echo $this->Form->radio("rain","Pluie de bonhommes",array(0=>"Désactivé",1=>"Activé"),array("default"=>$manif->rain)) ;?>
							<?php echo $this->Form->radio("hover","Couleur au survol",array(0=>"Désactivé",1=>"Activé"),array("default"=>$manif->hover)) ;?>
							<?php echo $this->Form->radio("signs","Avec banderolles",array(0=>"Désactivé",1=>"Activé"),array("default"=>$manif->signs)) ;?>
						</div>					
					</div>

					<div class="submit">						
						<?php echo $this->Form->input("manif_id","hidden",array("value"=>$manif->getID())) ;?>
						<?php echo $this->Form->input('token','hidden',array('value'=>Session::token())) ;?>
						<input class="btn btn-info" type="submit" value="<?php echo $submit_text; ?>"/>
					</div>
				</div>

			</form>

		</div>

	</div>


</div>
