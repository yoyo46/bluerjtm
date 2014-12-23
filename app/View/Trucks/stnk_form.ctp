<?php
		$this->Html->addCrumb('Histori STNK Truk', array(
			'controller' => 'trucks',
			'action' => 'stnk'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Stnk', array(
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
				echo $this->Form->input('tgl_bayar', array(
					'type' => 'text',
					'label'=> __('Tanggal Bayar *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal Bayar')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->input('tgl_berakhir', array(
					'type' => 'text',
					'label'=> __('Tanggal Berakhir *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal Berakhir')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('price', __('Harga Perpanjang STNK *')); 
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
						'placeholder' => __('Harga Perpanjang STNK')
					));
				?>
			</div>
        </div>
        <div class="form-group">
	        <div class="checkbox aset-handling">
                <label>
                    <?php 
						echo $this->Form->checkbox('is_change_plat',array(
							'label'=> false, 
							'required' => false,
						)).__('pergantian plat nomor truk?');
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
					'action' => 'stnk',
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>