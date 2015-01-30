<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('CalendarEvent', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Judul *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Judul')
					));
			?>
        </div>
        <div class="form-group">
        	<div class="input-group pick-icon">
	        	<?php 
						echo $this->Form->input('icon_id',array(
							'label'=> __('Icon'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Icon'),
							'options' => $calendarIcons,
						));
				?>
				<div class="input-group-addon"></div>
        	</div>
        </div>
        <div class="form-group">
        	<div class="input-group pick-color">
	        	<?php 
						echo $this->Form->input('color_id',array(
							'label'=> __('Warna'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Warna'),
							'options' => $calendarColors,
						));
				?>
				<div class="input-group-addon"></div>
        	</div>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('note',array(
						'label'=> __('Keterangan'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Keterangan')
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
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>