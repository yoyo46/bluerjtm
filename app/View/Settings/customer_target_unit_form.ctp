<?php
		$this->Html->addCrumb(__('Target Unit'), array(
			'action' => 'customer_target_unit'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('CustomerTargetUnit', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('customer_id',array(
						'label'=> __('Customer'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Customer')
					));
			?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->label('month', __('Bulan - Tahun'));
            ?>
            <div class="row">
            	<div class="col-sm-6">
		            <?php
		                    echo $this->Form->month('month', array(
		                        'label'=> false, 
		                        'class'=>'form-control',
		                        'required' => false,
		                        'empty' => false,
		                        'name' => 'data[CustomerTargetUnit][month]',
		                        'empty' => __('Pilih Bulan'),
		                    ));
		            ?>
            	</div>
            	<div class="col-sm-6">
		            <?php 
		                    echo $this->Form->year('year', 1949, date('Y') + 5, array(
		                        'label'=> false, 
		                        'class'=>'form-control',
		                        'required' => false,
		                        'empty' => false,
		                        'name' => 'data[CustomerTargetUnit][year]',
		                        'empty' => __('Pilih Tahun'),
		                    ));
		            ?>
		        </div>
            </div>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('unit',array(
						'label'=> __('Target Unit'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'customer_target_unit', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>