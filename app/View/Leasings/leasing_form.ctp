<?php
		$this->Html->addCrumb(__('Leasing'), array(
			'controller' => 'leasings',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Leasing', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('no_contract',array(
						'label'=> __('No. Kontrak *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('No. Kontrak')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('leasing_company_id',array(
						'label'=> __('Perusahan Leasing *'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Perusahan Leasing'),
						'options' => $leasing_companies
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('paid_date',array(
						'type' => 'text',
						'label'=> __('Tanggal Leasing *'), 
						'class'=>'form-control custom-date',
						'required' => false,
						'placeholder' => __('Tanggal Leasing'),
						'value' => (!empty($this->request->data['Leasing']['paid_date'])) ? $this->request->data['Leasing']['paid_date'] : date('d/m/Y')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('date_first_installment',array(
						'type' => 'text',
						'label'=> __('Tanggal Angsuran Pertama *'), 
						'class'=>'form-control custom-date',
						'required' => false,
						'placeholder' => __('Tanggal Angsuran Pertama'),
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('date_last_installment',array(
						'type' => 'text',
						'label'=> __('Tanggal Angsuran Terakhir *'), 
						'class'=>'form-control custom-date',
						'required' => false,
						'placeholder' => __('Tanggal Angsuran Terakhir'),
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
     //    			$months = array();
					// for ($i = 1; $i <= 12; $i++) {
					//     $timestamp = mktime(0, 0, 0, $i, 1);
					//     $months[date('n', $timestamp)] = date('F', $timestamp);
					// }
					echo $this->Form->input('leasing_month',array(
						'type' => 'text',
						'label'=> __('Bulan *'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Bulan'),
						// 'options' => $months,
						'value' => (!empty($this->request->data['Leasing']['leasing_month'])) ? $this->request->data['Leasing']['leasing_month'] : ''
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('down_payment',array(
						'type' => 'text',
						'label'=> __('DP *'), 
						'class'=>'form-control input_price',
						'required' => false,
						'placeholder' => __('DP'),
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('installment',array(
						'type' => 'text',
						'label'=> __('Pokok Angsuran *'), 
						'class'=>'form-control input_price',
						'required' => false,
						'placeholder' => __('Pokok Angsuran'),
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('installment_rate',array(
						'type' => 'text',
						'label'=> __('Bunga *'), 
						'class'=>'form-control input_price',
						'required' => false,
						'placeholder' => __('Bunga'),
					));
			?>
        </div>
    </div>
</div>
<?php
	echo $this->element('blocks/leasings/leasing_detail_info'); 
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>
<div id="form-truck-id" class="hide">
	<?php
		echo $this->Form->input('LeasingDetail.truck_id.',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'empty' => __('Pilih Truk'),
			'options' => $trucks
		));
	?>
</div>