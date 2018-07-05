<?php
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;
		$status = Common::hashEmptyField($value, 'Insurance.transaction_status');

		$this->Html->addCrumb(__('Asuransi'), array(
			'controller' => 'insurances',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Insurance', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'id' => 'insurance-form',
		));
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
    	<div class="row">
    		<div class="col-sm-6">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('nodoc',array(
								'label'=> __('No. Polis *'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('name',array(
								'label'=> __('Nama Asuransi *'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('date',array(
								'type' => 'text',
								'label'=> __('Tgl Asuransi *'), 
								'class'=>'form-control date-range',
								'required' => false,
								'placeholder' => __('Tgl Asuransi'),
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('note',array(
								'type' => 'textarea',
								'label'=> __('Keterangan'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
    		</div>
    		<div class="col-sm-6">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('to_name',array(
								'type' => 'text',
								'label'=> __('Nama Tertanggung *'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('to_address',array(
								'type' => 'textarea',
								'label'=> __('Alamat Tertanggung *'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
    		</div>
    	</div>
    </div>
</div>
<?php
	echo $this->element('blocks/insurances/detail_info'); 
?>
<div class="box-footer text-center action">
	<?php
			if( empty($view) ) {
				if( $status == 'unpaid' || empty($value) ) {
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    	}
		    }
	    	
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