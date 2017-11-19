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
					$totalWithoutTax = 0;
					$totalPPh = 0;

					if(!empty($this->request->data['Invoice']['total_revenue'])){
						$totalWithoutTax = $this->request->data['Invoice']['total_revenue'];
					}
					if(!empty($this->request->data['Invoice']['total'])){
						$total = $this->request->data['Invoice']['total'];
					}
					if(!empty($this->request->data['Invoice']['total_pph'])){
						$totalPPh = $this->request->data['Invoice']['total_pph'];
					}

					echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
					
					echo $this->Form->hidden('Invoice.total',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'value' => $total
					));
					echo $this->Form->hidden('Invoice.total_revenue',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'value' => $totalWithoutTax
					));
					echo $this->Form->hidden('Invoice.total_pph',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'value' => $totalPPh
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
<?php 
		echo $this->Form->hidden('Invoice.pattern',array(
			'id' => 'pattern-code',
		));
?>