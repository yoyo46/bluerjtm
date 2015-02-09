<div id="form-ttuj-main">
	<?php
			echo $this->element('blocks/revenues/revenue_info', array(
				'info' => true,
			));
	?>
</div>
<div id="form-ttuj-detail">
	<?php
		echo $this->element('blocks/revenues/revenues_info_detail', array('data' => $data_revenue_detail));
	?>
</div>