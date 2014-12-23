<?php
		$this->Html->addCrumb('Histori SIUP Truk', array(
			'controller' => 'trucks',
			'action' => 'kir'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Siup', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<div class="form-group">
        	<?php 
				echo $this->Form->input('truck_id', array(
					'label'=> __('Truk *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Truk'),
					'options' => $trucks
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->input('tgl_siup', array(
					'type' => 'text',
					'label'=> __('Tanggal SIUP *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal SIUP')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->input('tgl_next_siup', array(
					'type' => 'text',
					'label'=> __('Tanggal SIUP selanjutnya *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal SIUP selanjutnya')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('price', __('Biaya SIUP *')); 
			?>
			<div class="input-group">
				<?php 
					echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
	    				'class' => 'input-group-addon'
    				));
					echo $this->Form->input('price', array(
						'type' => 'text',
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Biaya SIUP')
					));
				?>
			</div>
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
					'action' => 'siup'
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>