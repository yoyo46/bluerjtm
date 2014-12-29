<?php
		$this->Html->addCrumb('STNK Truk', array(
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
					echo $this->Form->label('tgl_bayar', __('Tanggal Perpanjang *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('tgl_bayar', array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'required' => false,
								'empty' => __('Hari'),
								'id' => 'day',
								'required' => false,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->month('tgl_bayar', array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'required' => false,
								'empty' => __('Bulan'),
								'id' => 'month',
								'required' => false,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->year('tgl_bayar', date('Y') - 10, date('Y') + 10, array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'empty' => __('Tahun'),
								'id' => 'year',
								'required' => false,
							));
					?>
				</div>
			</div>
        	<?php 
					echo $this->Form->error('tgl_bayar', array(
						'notempty' => __('Tanggal Perpanjang harap diisi'),
					), array(
						'wrap' => 'div', 
						'class' => 'error-message',
					));
        	?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('tgl_berakhir', __('Tanggal Expired *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('tgl_berakhir', array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'required' => false,
								'empty' => __('Hari'),
								'id' => 'day',
								'required' => false,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->month('tgl_berakhir', array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'required' => false,
								'empty' => __('Bulan'),
								'id' => 'month',
								'required' => false,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->year('tgl_berakhir', date('Y') - 10, date('Y') + 10, array(
								'label'=> false, 
								'class'=>'form-control selectbox-date',
								'empty' => __('Tahun'),
								'id' => 'year',
								'required' => false,
							));
					?>
				</div>
			</div>
        	<?php 
					echo $this->Form->error('tgl_berakhir', array(
						'notempty' => __('Tanggal Expired harap diisi'),
					), array(
						'wrap' => 'div', 
						'class' => 'error-message',
					));
        	?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('price', __('Biaya Perpanjang STNK *')); 
			?>
			<div class="input-group">
	        	<?php 
	        		echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
	    				'class' => 'input-group-addon'
    				));

					echo $this->Form->input('price', array(
						'type' => 'text',
						'label'=> false, 
						'class'=>'form-control input_price',
						'required' => false,
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