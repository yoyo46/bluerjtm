<?php 

		if( !empty($sjOutstanding) ) {
			echo $this->Html->tag('div', $this->Html->link(sprintf(__(' ( %s SJ belum kembali )'), $sjOutstanding), 'javascript:', array(
				'id' => 'view_sj_outstanding',
				'data-driver' => $driver_id,
			)), array(
				'id' => 'sj_outstanding',
			));
		}
?>	
