

<div class="container-fluid band-stripped-left">

	<!-- Top bar -->
	<div class="container">
		<div id="top-toolbar" class="btn-toolbar">
			<form id="search-manif" class="form-search" action="<?php echo Router::url('manifs/index/'); ?>" method="GET">

			<div class="btn-group">
				<button type="submit" class="btn btn-inverse disabled" disabled="disabled">Search</button>
		  		<input type="text" name="rch" placeholder="Ex: Occupy" value="<?php echo $this->CookieRch->read('rch'); ?>">
			  	<button type="submit" class="btn" ><span class="icon-white icon-refresh"></span></button>
			</div>

			<div class="btn-group">
				<button class="btn btn-inverse disabled" disabled="disabled">Location</button>									
					<?php 
						//debug($this->CookieRch->read());
						$arr = $this->CookieRch->arr();
						$arr['selectClass'] = '';
						$this->request('world','locate',array(array('obj'=>$arr)));

					?>				
				<button type="submit" class="btn" id="submit-state" data-url="<?php echo Router::url('world/getStates'); ?>"><span class="icon-white icon-refresh"></span></button>		
			</div>

			<div class="btn-group">
				<button class="btn btn-inverse disabled" disabled="disabled">Category</button>	
				<?php echo $this->Form->_select('cat2',$cat2,array('placeholder'=>'',"class"=>'cat-select',"default"=>$this->CookieRch->read('cat2'),"javascript"=>'onchange="showCategory(this.value,3)"')); ?>
				<?php if($this->CookieRch->read('cat3')): ?>
				<?php echo $this->Form->_select('cat3',$cat3,array('placeholder'=>'',"class"=>'cat-select',"default"=>$this->CookieRch->read('cat3'))); ?>
				<?php endif; ?>
				<button type="submit" class="btn" id="submit-category" data-url="<?php echo Router::url('manifs/getCategory'); ?>"><span class="icon-white icon-refresh"></span></button>	
			</div>	

			<div class="btn-group">
				<button class="btn btn-inverse disabled" disabled="disabled">Order</button>
				<?php 
					echo $this->Form->_select('order',$order,array('placeholder'=>'',"default"=>$this->CookieRch->read('order'))); ?>			
				<button type="submit" class="btn" ><span class="icon-white icon-refresh"></span></button>
				
			</div>

			<div class="btn-group">					
				<input type="hidden" name="token" value="<?php echo $this->session->token(); ?>" />		
				<input type="submit" class="btn btn-primary" value="SEARCH"/>
			</div>
			</form>

				
		</div>
	</div>

</div>
<div class="container protest-list">

	<?php echo $this->session->flash(); ?>
	
	<div class="arianne">
		<?php 

			$rch = $this->CookieRch->read('rch');
			$rch = '<a href="?rch='.$rch.'" title="Search for '.$rch.'">'.$rch.'</a>';
			$cat = '';				
			if(!empty($Ccat2)) $cat = '<a href="?cat2='.$Ccat2[0]->id.'" title="Search for category '.utf8_encode($Ccat2[0]->name).'">'.utf8_encode($Ccat2[0]->name).'</a>';
			if(!empty($Ccat3)) $cat = '<a href="?cat2='.$Ccat3[0]->id.'" title="Search for category '.utf8_encode($Ccat3[0]->name).'">'.utf8_encode($Ccat3[0]->name).'</a>';
			if(!empty($rch) && !empty($cat) ) echo '<a href="?reset=1">reset</a>';
			if(!empty($rch)) echo "<span>".$rch."</span>";
			if(!empty($cat)) echo "<span>".$cat."</span>";
		?>
	</div>	

	<!-- Right Bar -->
	<div id="rightbar">	
						
		
		<div class="nav-timeline">
			<ul class="nav-timeline-ul">
<?php 		

	$order = $this->CookieRch->read('order');
	if($order=='new' || $order=='old' || $order=='' || !isset($order))
	{
		$c_year = '';
		$c_month = '';
		foreach ($manifs as $manif) {

			$metas = $manif['metas'];

			if(isset($metas['year']) && $metas['year'] != $c_year) {
				echo '<li class="nav-timeline-li"><a class="nav-timeline-a nav-timeline-year" href="#'.$metas['year'].$metas['month'].'">'.$metas['year'].'</a></li>';
			}

			if(isset($metas['month']) && $metas['month'] != $c_month || $metas['year'] != $c_year) {
				echo '<li class="nav-timeline-li"><a class="nav-timeline-a nav-timeline-month" href="#'.$metas['year'].$metas['month'].'">'.Date::month($metas['month'],$this->getLang()) .'</a></li>';
			}

			$c_year = $metas['year'];
			$c_month = $metas['month'];

		}
	}

	if($order=='big' ||$order=='tiny')
	{
		if($order == "big")
			$scale = array(500000,250000,100000,50000,25000,10000,5000,2500,1000,500);
		elseif($order == "tiny")
			$scale = array(500,1000,2500,5000,10000,25000,50000,100000,250000,500000);
		
		
		foreach ($scale as $value) {

			$done = array();
			foreach ($manifs as $manif) {

				$metas = $manif['metas'];
				$numerus = $manif['metas']['numerus'];

				if( $order=="big" && (isset($numerus) && $numerus < $value )) {

					if(!isset($done[$value])) {
						echo '<li class="nav-timeline-li"><a class="nav-timeline-a nav-timeline-month" href="#'.$metas['year'].$metas['month'].'">< '.$value.'</a></li>';
						$done[$value] = $numerus;
					}
				}
				if( $order=="tiny" && (isset($numerus) && $numerus > $value )){

					if(!isset($done[$value])) {
						echo '<li class="nav-timeline-li"><a class="nav-timeline-a nav-timeline-month" href="#'.$metas['year'].$metas['month'].'">> '.$value.'</a></li>';
						$done[$value] = $numerus;
					}
			
				}				

			}
		}
		

	}
	 ?>
		 	</ul>
		</div>
			
	</div>
	


	<!--Liste des manifs-->
	<div class="listed">
		<?php if(count($manifs)==0): ?>
		<div class="zeroresult">
			No protest here<br /><a class="btn btn-small btn-primary" href="<?php echo Router::url('manifs/create'); ?>">Create a protest</a>
		</div>
		<?php endif; ?>

		<?php if(count($manifs)>0): ?>
		<table class="table table-striped">
			<tbody>	
				<?php 

				$metas = array();

				reset($manifs);
				foreach ($manifs as $value) {
					

					$metas = $value['metas'];
					$manif = $value['manif'];
					$id = $metas['year'].$metas['month'];
?>
					<div class="manif" id="<?php echo $id;?>">

						<?php if($manif->getLogo() != $this->logo_default): ?>
						<div class="logo">
				 			<img class="image" src="<?php echo Router::webroot($manif->logo); ?>" />	
				 		</div>	
				 		<?php endif; ?>

				 		<div class="title">
				 			<a class="nommanif" href="<?php echo Router::url('manifs/view/'.$manif->getID().'/'.$manif->getSlug()); ?>" ><?php echo $manif->getTitle(); ?></a>														
				 		</div>	

				 		<div class="description"><?php echo $manif->getDescription(); ?></div>			 					 	
				 		<div class="actions">
				 			<div class="numerus" id="numerus<?php echo $manif->getID();?>"><?php echo number_format($manif->getNumerus(),0,' ',' '); ?></div>

				 			<?php if($this->session->user()->isLog()): ?>
				 			<div class="switchProtest mini">
		                        <input type="checkbox" <?php echo ($manif->isUserProtesting())? 'checked="checked"' : '';?> class="btn-switch-protested" data-protest-id="<?php echo $manif->getID();?>" data-url-protest="<?php echo Router::url('manifs/addUser');?>" data-url-cancel="<?php echo Router::url('manifs/removeUser');?>">
		                        <label><i></i></label>
                   			</div>                   			
                   		<?php endif; ?>
							<div class="btn-group dropup fright">
							  
							  <button class="btn dropdown-toggle" data-toggle="dropdown">
							    <span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu">
							  		<?php if($manif->isUserAdmin( $this->session->user()->getID() )): ?>
						      	<li><a href="<?php echo Router::url('manifs/create/'.$manif->getID().'/'.$manif->getSlug()); ?>">Administrer</a></li>
					    			<?php endif;?>
							    <li><a href="#">Signaler</a></li>							    
							  </ul>
							</div>						
				 		</div>
				 	</div>

				<?php			
										
				}
									
				 ?>
			</tbody>
		</table>

		<div class="pagination">
		  <ul>
		<?php for($i=1; $i <= $nbpage; $i++):?>
				<li <?php if($i==$this->request->get('page')) echo 'class="active"';?>
				><a href="?page=<?php echo $i;?>"><?php echo $i;?></a></li>
		<?php endfor; ?>	    
		  </ul>
		</div>
		<?php endif; ?>
	</div>
	<div class="span3">
		
	</div>

</div>


<script type="text/javascript" src="http://localhost:1337/socket.io/socket.io.js"></script>
<script type="text/javascript">


	$(function(){


		//Bootstrap spyscroll (dont work?)
		$('body').attr('data-spy', 'scroll').attr('data-target', '#nav-timeline').attr('data-offset', '50');
		


		

	});
	


</script>