<?php 
		if( !empty($msg) && $isAjax && $msg['class'] == 'success' ) {
			echo '<div id="status">'.$msg['class'].'</div><div id="message">'.$msg['text'].'</div>';
			echo $this->element('blocks/common/alert_content', array(
				'title_alert' => __('Pesan Anda telah berhasil dikirim'),
				'content_alert' => false,
			));
		} else {
?>
<div class="form-content">
	<?php
				echo $this->Form->create('CalendarEvent', array(
					'url'=> $this->Html->url( null, true ), 
					'role' => 'form',
					'inputDefaults' => array('div' => false),
					'id' => 'form-content',
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
	    	<?php 
					echo $this->Form->label('time', __('Pukul'));
			?>
			<div class="input-group">
	            <div class="input-group-addon">
	                <i class="fa fa-clock-o"></i>
	            </div>
				<?php 
						echo $this->Form->input('time',array(
							'label'=> false, 
							'class'=>'form-control pull-right timepicker',
							'required' => false,
							'type' => 'text'
						));
				?>
	        </div>
	    </div>
	    <div class="form-group pick-icon">
	    	<?php 
					echo $this->Form->input('icon_id',array(
						'label'=> __('Icon'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Icon'),
						'options' => $optionIcons,
					));
			?>
			<div class="pick-icon-show"></div>
	    </div>
	    <div class="form-group pick-color">
	    	<?php 
					echo $this->Form->input('color_id',array(
						'label'=> __('Warna'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Warna'),
						'options' => $optionColors,
					));
			?>
			<div class="pick-color-show"></div>
	    </div>
	    <div class="form-group">
	    	<?php 
					echo $this->Form->input('note',array(
						'label'=> __('Keterangan *'), 
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
					'class'=> 'btn btn-success btn-submit-form',
					'type' => 'submit',
					'data-action' => 'event',
				));
		?>
	</div>
	<?php
			echo $this->Form->end();
	?>
</div>
<div class="hide">
	<?php 
			if( !empty($calendarIcons) ) {
				foreach ($calendarIcons as $key => $calendarIcon) {
					if( !empty($calendarIcon['CalendarIcon']['photo']) ){
                        echo $this->Html->tag('div', $this->Common->photo_thumbnail(array(
                            'save_path' => Configure::read('__Site.truck_photo_folder'), 
                            'src' => $calendarIcon['CalendarIcon']['photo'], 
                            'thumb'=>true,
                            'size' => 'ps',
                            'thumb' => true,
                        )), array(
                            'class' => 'icon-calendar',
                            'rel' => $calendarIcon['CalendarIcon']['id'],
                        ));
                    }
				}
			}
			if( !empty($calendarColors) ) {
				foreach ($calendarColors as $key => $calendarColor) {
					if( !empty($calendarColor['CalendarColor']['hex']) ){
                        echo $this->Html->tag('div', $calendarColor['CalendarColor']['hex'], array(
                            'class' => 'color-calendar',
                            'rel' => $calendarColor['CalendarColor']['id'],
                        ));
                    }
				}
			}
	?>
</div>
<?php 
		}
?>