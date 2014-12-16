<?php
		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Truck', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi TTUJ'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('no_ttuj',array(
								'label'=> __('No. TTUJ *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. TTUJ')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('ttuj_date',array(
								'label'=> __('Tgl TTUJ *'), 
								'class'=>'form-control custom-date',
								'required' => false,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('customer_id',array(
								'label'=> __('Customer *'), 
								'class'=>'form-control customer',
								'required' => false,
								'empty' => __('Pilih Customer --'),
								'id' => 'getKotaAsal',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('uang_jalan_id', __('Tujuan Dari'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php 
									echo $this->Form->input('from_city_id',array(
										'label'=> false, 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Dari Kota --'),
										'div' => array(
											'class' => 'from_city'
										),
										'disabled' => true,
										'id' => 'getKotaTujuan',
									));
							?>
						</div>
						<div class="col-sm-6">
							<?php 
									echo $this->Form->input('to_city_id',array(
										'label'=> false, 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Kota Tujuan --'),
										'disabled' => true,
										'div' => array(
											'class' => 'to_city'
										),
										'id' => 'getTruck',
									));
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('truck_id',array(
								'label'=> __('No. Pol *'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih No. Pol --'),
								'disabled' => true,
								'div' => array(
									'class' => 'truck_id'
								),
								'id' => 'getInfoTruck',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('driver_name',array(
								'label'=> __('Supir'), 
								'class'=>'form-control driver_name',
								'required' => false,
								'disabled' => true,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('truck_capacity',array(
								'label'=> __('Kapasitas Truk'), 
								'class'=>'form-control truck_capacity',
								'required' => false,
								'disabled' => true,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('driver_penganti_id',array(
								'label'=> __('Supir Pengganti'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih Supir Pengganti --'),
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">

		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Biaya Perjalanan');?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_jalan_1', __('Uang Jalan Pertama'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_jalan_1',array(
									'label'=> false, 
									'class'=>'form-control uang_jalan_1',
									'required' => false,
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_jalan_2', __('Uang Jalan Kedua'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_jalan_2',array(
									'label'=> false, 
									'class'=>'form-control uang_jalan_2',
									'required' => false,
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_kuli_muat', __('Uang Kuli Muat'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_kuli_muat',array(
									'label'=> false, 
									'class'=>'form-control uang_kuli_muat',
									'required' => false,
									'empty' => __('Uang Kuli Muat'),
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_kuli_bongkar', __('Uang Kuli Bongkar'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_kuli_bongkar',array(
									'label'=> false, 
									'class'=>'form-control uang_kuli_bongkar',
									'required' => false,
									'empty' => __('Uang Kuli Bongkar'),
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('asdp', __('Uang Penyebrangan'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('asdp',array(
									'label'=> false, 
									'class'=>'form-control asdp',
									'required' => false,
									'empty' => __('Uang Penyebrangan'),
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_kawal', __('Uang Kawal'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_kawal',array(
									'label'=> false, 
									'class'=>'form-control uang_kawal',
									'required' => false,
									'empty' => __('Uang Kawal'),
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_keamanan', __('Uang Keamanan'));
		    		?>
		            <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_keamanan',array(
									'label'=> false, 
									'class'=>'form-control uang_keamanan',
									'required' => false,
									'empty' => __('Uang Keamanan'),
									'type' => 'text',
									'disabled' => true,
								));
						?>
					</div>
				</div>
		    </div>
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
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>