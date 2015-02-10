<div id="form-ttuj-main">
	<?php
			echo $this->element('blocks/revenues/revenue_info');
	?>
</div>
<div id="form-ttuj-detail">
	<?php
		echo $this->element('blocks/revenues/revenues_info_detail', array('data' => $data_revenue_detail));
	?>
</div>
<div id="form-customer">
	<?php 
			echo $this->Form->input('Revenue.customer_id',array(
				'label'=> __('Customer'), 
				'class'=>'form-control change-customer-revenue',
				'required' => false,
				'options' => $customers,
				'empty' => __('Pilih Customer'),
			));
	?>
</div>