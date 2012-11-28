<input type="hidden" id="submit-state" data-url="<?php echo Router::url('world/nextStateLevel'); ?>">
<?php 

	foreach($states as $lvl=>$state){
		// Form->Select(id,title,class,list,selected,javascript)	

		if($lvl!='city')							
			echo $this->Form->_select($lvl,$state['list'],array('placeholder'=>$state['title'],"class"=>'geo-select',"default"=>$obj->$lvl,"javascript"=>'onchange="showRegion(this.value,\''.$lvl.'\')"'));
		else
			echo $this->Form->_select($lvl,$state['list'],array("class"=>'geo-select',"default"=>$obj->$lvl));
	}
 ?>		