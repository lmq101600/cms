
<?php 

if(!empty($arrAllMenuKv)) {
	foreach ($arrAllMenuKv as $v) {
		if(in_array($v['id'],$arrCurentRight)) {
			echo '*'. $v['id'] . "\t" . $v['mname'] . '<br/>';
		}
		
		if(!empty($v['info'])) {
			foreach ($v['info'] as $val) {
				echo '-------' . $val['id'] . "\t" . $val['mname'] . '<br/>';
				
			}
		}
	}
}

?>