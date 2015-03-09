<div class="form-group">
	<?php 
			echo $this->Form->input('Invoice.bank_id',array(
				'label'=> __('Bank *'), 
				'class'=>'form-control',
				'required' => false,
				'empty' => __('Pilih Bank'),
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Html->tag('label', __('Total'));
	?>
	<div class="row">
		<div class="col-sm-12">
			<?php 
					$total = 0;
					if(!empty($this->request->data['Invoice']['total'])){
						$total = $this->request->data['Invoice']['total'];
						
					}
					echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
					
					echo $this->Form->hidden('Invoice.total',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'value' => $total
					));
			?>
		</div>
	</div>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->label('Invoice.period_from', __('Periode'));
	?>
	<div class="row">
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Invoice.period_from',array(
						'type' => 'text',
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true
					));
			?>
		</div>
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Invoice.period_to',array(
						'type' => 'text',
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
					));
			?>
		</div>
	</div>
</div>