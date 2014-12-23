<?php
		$this->Html->addCrumb('Histori KIR Truk', array(
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
		echo $this->Form->create('Kir', array(
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
				echo $this->Form->input('tgl_kir', array(
					'type' => 'text',
					'label'=> __('Tanggal KIR *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal KIR')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('price', __('Biaya KIR *')); 
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
						'placeholder' => __('Biaya KIR')
					));
				?>
			</div>
        </div>
        <div class="form-group">
			<?php 
				echo $this->Form->input('tgl_next_kir', array(
					'type' => 'text',
					'label'=> __('Tanggal KIR selanjutnya *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal KIR selanjutnya')
				));
			?>
        </div>
        <div class="form-group">
	        <div class="checkbox aset-handling">
                <label>
                    <?php 
						echo $this->Form->checkbox('paid',array(
							'label'=> false, 
							'required' => false,
						)).__('status bayar?');
					?>
                </label>
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
					'action' => 'kir'
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>