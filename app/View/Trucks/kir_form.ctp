<?php
		$this->Html->addCrumb('KIR Truk', array(
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
					echo $this->Form->label('tgl_kir', __('Tanggal Perpanjang *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('tgl_kir', array(
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
							echo $this->Form->month('tgl_kir', array(
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
							echo $this->Form->year('tgl_kir', date('Y') - 10, date('Y') + 10, array(
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
					echo $this->Form->error('tgl_kir', array(
						'notempty' => __('Tanggal Perpanjang harap diisi'),
					), array(
						'wrap' => 'div', 
						'class' => 'error-message',
					));
        	?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('tgl_next_kir', __('Tanggal Expired *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('tgl_next_kir', array(
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
							echo $this->Form->month('tgl_next_kir', array(
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
							echo $this->Form->year('tgl_next_kir', date('Y') - 10, date('Y') + 10, array(
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
					echo $this->Form->error('tgl_next_kir', array(
						'notempty' => __('Tanggal KIR harap diisi'),
					), array(
						'wrap' => 'div', 
						'class' => 'error-message',
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
						'class'=>'form-control input_price',
						'required' => false,
						'placeholder' => __('Biaya KIR')
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