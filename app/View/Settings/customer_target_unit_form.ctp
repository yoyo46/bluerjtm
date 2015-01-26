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
        			echo $this->Form->label('year', __('Tahun'));
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
        <div class="form-group">
        	<?php
	        		echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah Bulan'), 'javascript:', array(
						'class' => 'add-custom-field btn btn-success btn-xs',
						'action_type' => 'target-unit',
						'escape' => false,
					));
        	?>
        </div>
        <div id="box-field-input">
        	<?php 
        			if( !empty($this->request->data['CustomerTargetUnitDetail']['month']) ) {
        				foreach ($this->request->data['CustomerTargetUnitDetail']['month'] as $key => $value) {
        					echo $this->element('blocks/settings/list_month', array(
        						'rel' => $key,
        						'month' => $value,
        						'unit' => !empty($this->request->data['CustomerTargetUnitDetail']['unit'][$key])?$this->request->data['CustomerTargetUnitDetail']['unit'][$key]:'',
    						));
        				}
        			} else {
        				echo $this->element('blocks/settings/list_month', array(
        					'rel' => 0,
    					));
        			}
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
<div class="hide">
	<div id="target_unit">
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
</div>