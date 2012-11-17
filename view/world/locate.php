 <input type="hidden" id="submit-state" data-url="<?php echo Router::url('world/nextStateLevel'); ?>">
<?php 

	foreach($states as $lvl=>$state){
		// Form->Select(id,title,class,list,selected,javascript)	

		if($lvl!='city')							
			echo $this->Form->Select($lvl,$state['title'],'geo-select '.$class,$state['list'],$obj->$lvl,'onchange="showRegion(this.value,\''.$lvl.'\')"');
		else
			echo $this->Form->Select($lvl,$state['title'],'geo-select '.$class,$state['list'],$obj->$lvl);
	}
 ?>		