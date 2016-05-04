<?php 
		echo $this->Form->hidden('Revenue.date_revenue',array(
			'id' => 'date_revenue',
		));
?>
<div id="form-ttuj-main">
	<?php
			if( !empty($data_action) && $data_action == 'manual' ) {
				echo $this->element('blocks/revenues/revenue_info_manual');
			} else {
				echo $this->element('blocks/revenues/revenue_info');
			}
	?>
</div>
<div id="form-ttuj-detail">
	<?php
			echo $this->element('blocks/revenues/revenues_info_detail', array(
				'revenueDetail' => $data_revenue_detail,
			));
	?>
</div>
<div id="form-customer">
	<?php 
			echo $this->Form->input('Revenue.customer_id',array(
				'label'=> __('Customer'), 
				'class'=>'form-control change-customer-revenue chosen-select',
				'required' => false,
				'options' => $customers,
				'empty' => __('Pilih Customer'),
				'id' => 'customer-revenue-manual',
			));
	?>
</div>
<?php 
		echo $this->Form->hidden('Revenue.revenue_tarif_type', array(
			'class'=>'form-control',
			'value' => !empty($tarifTruck['jenis_unit'])?$tarifTruck['jenis_unit']:'per_unit',
			'id' => 'revenue_tarif_type',
		));
?>