<?php if($states != false) {

	$array = array(
		'SelectID'=>$ADM,
		'SelectELEMENT'=> $this->Form->generateSelect($ADM,'btn geo-select',$states,'code','name','','onchange="showRegion(this.value,\''.$ADM.'\')"')
		);

	echo json_encode($array);
	//echo $this->Form->generateSelect($ADM,'btn geo-select',$states,'code','name','','onchange="showRegion(this.value,\''.$ADM.'\')"');
}else {
	echo 'empty';
}?>