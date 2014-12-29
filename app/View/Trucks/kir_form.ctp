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
					echo $this->Form->input('tgl_kir', array(
						'label'=> __('Tanggal Perpanjang *'), 
						'class'=>'form-control custom-date',
						'type' => 'text',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('from_date', __('Tanggal Kir ditetapkan *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('from_date', array(
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
							echo $this->Form->month('from_date', array(
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
							echo $this->Form->year('from_date', date('Y') - 10, date('Y') + 10, array(
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
					echo $this->Form->error('from_date', array(
						'notempty' => __('Tanggal Perpanjang harap diisi'),
					), array(
						'wrap' => 'div', 
						'class' => 'error-message',
					));
        	?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('to_date', __('Tgl Berlaku Kir *'));
			?>
			<div class="row">
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->day('to_date', array(
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
							echo $this->Form->month('to_date', array(
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
							echo $this->Form->year('to_date', date('Y') - 10, date('Y') + 10, array(
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
					echo $this->Form->error('to_date', array(
						'notempty' => __('Tanggal Expired harap diisi'),
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