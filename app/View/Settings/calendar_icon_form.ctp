<?php
		$this->Html->addCrumb(__('Warna Kalender'), array(
			'action' => 'calendar_icons'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('CalendarIcon', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
				'type' => 'file',
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Icon *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Icon')
					));
			?>
        </div>
        <?php
    			if( !empty($data_local['CalendarIcon']['photo']) ){
    				$photo = $this->Common->photo_thumbnail(array(
						'save_path' => Configure::read('__Site.truck_photo_folder'), 
						'src' => $data_local['CalendarIcon']['photo'], 
						'thumb'=>true,
						'size' => 'ps',
						'thumb' => true,
					));

					echo $this->Html->tag('div', $photo, array(
						'class' => 'form-group',
					));
    			}
    	?>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('image',array(
						'type' => 'file',
						'label'=> __('Icon *'), 
						'class'=>'form-control',
						'required' => false
					));
					echo $this->Form->error('photo', array(
						'notempty' => __('Icon Kalender harap diisi'),
					), array(
						'wrap' => 'span', 
						'class' => 'error-message',
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
					'action' => 'calendar_icons', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>