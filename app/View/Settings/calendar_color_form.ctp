<?php
		$this->Html->addCrumb(__('Warna Kalender'), array(
			'action' => 'calendar_colors'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('CalendarColor', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Label Warna *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Label Warna')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('hex', __('Kode Warna *'));
			?>
            <div class="input-group colorpicker">
                <div class="input-group-addon">
                    <i></i>
                </div>
	        	<?php 
						echo $this->Form->input('hex', array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Kode Warna')
						));
				?>
            </div><!-- /.input group -->
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
					'action' => 'calendar_colors', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>