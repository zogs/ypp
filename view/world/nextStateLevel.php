<?php if($state != false) {

	$array = array(
		'SelectID'=>$state['lvl'],
		'SelectELEMENT'=> $this->Form->Select($state['lvl'],$state['title'],'geo-select',$state['list'],'','onchange="showRegion(this.value,\''.$state["lvl"].'\')"')
		);

	echo json_encode($array);
	
}else {
	echo 'empty';
}?>