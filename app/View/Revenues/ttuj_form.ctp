<?php 
		if( !empty($step) ) {
?>
<script type="text/javascript">
	window.location.hash = '<?php echo $step; ?>';
</script>
<?php
		}

		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Ttuj', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div id="step1">
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
										'type' => 'text',
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
											$disabled = !empty($this->request->data['Ttuj']['customer_id'])?false:true;
											echo $this->Form->input('from_city_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Dari Kota --'),
												'div' => array(
													'class' => 'from_city'
												),
												'readonly' => $disabled,
												'id' => 'getKotaTujuan',
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											$disabled = !empty($this->request->data['Ttuj']['from_city_id'])?false:true;
											echo $this->Form->input('to_city_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Kota Tujuan --'),
												'readonly' => $disabled,
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
									$disabled = !empty($this->request->data['Ttuj']['to_city_id'])?false:true;
									echo $this->Form->input('truck_id',array(
										'label'=> __('No. Pol *'), 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Pilih No. Pol --'),
										'readonly' => $disabled,
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
										'readonly' => true,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('tgljam_berangkat', __('Tgl & Jam Berangkat'));
							?>
				        	<div class="row">
				        		<div class="col-sm-8">
				        			<?php 
											echo $this->Form->input('tgl_berangkat',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'type' => 'text'
											));
									?>
				        		</div>
				        		<div class="col-sm-4">
				        			<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
					        			<?php 
												echo $this->Form->input('jam_berangkat',array(
													'label'=> false, 
													'class'=>'form-control pull-right timepicker',
													'required' => false,
													'type' => 'text'
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        	<?php 
									echo $this->Form->error('tgljam_berangkat', array(
										'notempty' => __('Tgl & Jam Berangkat harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
				        	?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('truck_capacity',array(
										'label'=> __('Kapasitas Truk'), 
										'class'=>'form-control truck_capacity',
										'required' => false,
										'readonly' => true,
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
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('note',array(
										'label'=> __('Keterangan'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				    </div>
				</div>
			</div>
			<div class="col-sm-6">

				<div class="box box-primary" id="biaya-uang-jalan">
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
											'class'=>'form-control uang_jalan_1 input_price',
											'required' => false,
											'type' => 'text',
											'readonly' => true,
										));
								?>
							</div>
						</div>
				    	<div class="form-group wrapper_uang_jalan_2 <?php echo (isset($this->request->data['Ttuj']['uang_jalan_2']) && !$this->request->data['Ttuj']['uang_jalan_2'])?'hide':''; ?>">
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
											'class'=>'form-control uang_jalan_2 input_price',
											'required' => false,
											'type' => 'text',
											'readonly' => true,
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
											'class'=>'form-control uang_kuli_muat input_price',
											'required' => false,
											'empty' => __('Uang Kuli Muat'),
											'type' => 'text',
											'readonly' => true,
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
											'class'=>'form-control uang_kuli_bongkar input_price',
											'required' => false,
											'empty' => __('Uang Kuli Bongkar'),
											'type' => 'text',
											'readonly' => true,
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
											'class'=>'form-control asdp input_price',
											'required' => false,
											'empty' => __('Uang Penyebrangan'),
											'type' => 'text',
											'readonly' => true,
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
											'class'=>'form-control uang_kawal input_price',
											'required' => false,
											'empty' => __('Uang Kawal'),
											'type' => 'text',
											'readonly' => true,
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
											'class'=>'form-control uang_keamanan input_price',
											'required' => false,
											'empty' => __('Uang Keamanan'),
											'type' => 'text',
											'readonly' => true,
										));
								?>
							</div>
						</div>
				    	<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('uang_jalan_extra', __('Uang Jalan Extra *'));
				    		?>
		                    <div class="input-group">
						    	<?php 
						    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
						    				'class' => 'input-group-addon'
					    				));
										echo $this->Form->input('uang_jalan_extra',array(
											'label'=> false, 
											'class'=>'form-control input_price uang_jalan_extra',
											'required' => false,
											'empty' => __('Uang Jalan Extra'),
											'type' => 'text',
											'readonly' => true,
										));
								?>
							</div>
						</div>
				    	<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('min_capacity', __('Minimum Kapasitas *'));
				    		?>
		                    <div class="input-group">
						    	<?php 
										echo $this->Form->input('min_capacity',array(
											'label'=> false, 
											'class'=>'form-control min_capacity',
											'required' => false,
											'empty' => __('Minimum Kapasitas'),
											'type' => 'text',
											'readonly' => true,
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
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'ttuj', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Html->link(__('Next'), '#step2', array(
						'class'=> 'btn btn-success',
						'id' => 'nextTTUJ'
					));
			?>
		</div>
	</div>
	<div id="step2">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Muatan'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<div class="form-group">
							<?php
									echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
										'class' => 'add-custom-field btn btn-success btn-xs',
										'action_type' => 'ttuj',
										'escape' => false
									));
							?>
						</div>
				        <table class="table table-bordered table-striped" id="ttujDetail">
							<thead>
								<tr>
									<th><?php echo __('Tipe Motor'); ?></th>
									<th><?php echo __('Jumlah Unit'); ?></th>
									<th><?php echo __('Action'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php 
										if( !empty($this->request->data['TtujTipeMotor']['tipe_motor_id']) ) {
											foreach ($this->request->data['TtujTipeMotor']['tipe_motor_id'] as $key => $tipe_motor_id) {
												$qty = !empty($this->request->data['TtujTipeMotor']['qty'][$key])?$this->request->data['TtujTipeMotor']['qty'][$key]:false;
								?>
								<tr>
									<td>
										<?php
												echo $this->Form->input('TtujTipeMotor.tipe_motor_id.'.$key, array(
													'class' => 'form-control',
													'label' => false,
													'empty' => __('Pilih Tipe Motor --'),
													'options' => $tipeMotors,
													'value' => $tipe_motor_id,
													'required' => false,
												));
										?>
									</td>
									<td>
										<?php
												echo $this->Form->input('TtujTipeMotor.qty.'.$key, array(
													'class' => 'form-control',
													'label' => false,
													'required' => false,
													'div' => false,
													'value' => $qty,
												));
										?>
									</th>
									<td>
										<?php
												echo $this->Html->link('<i class="fa fa-times"></i> '.__('Hapus'), 'javascript:', array(
													'class' => 'delete-custom-field btn btn-danger btn-xs',
													'action_type' => 'ttuj',
													'escape' => false
												));
										?>
									</th>
								</tr>
								<?php
											}
										} else {
								?>
								<tr class="removed">
									<td>
										<?php 
												echo $this->Form->input('TtujTipeMotor.tipe_motor_id.0',array(
													'label'=> false, 
													'class'=>'form-control',
													'required' => false,
													'empty' => __('Pilih Tipe Motor --'),
													'options' => $tipeMotors
												));
										?>
									</td>
									<td>
										<?php 
												echo $this->Form->input('TtujTipeMotor.qty.0',array(
													'label'=> false, 
													'class'=>'form-control',
													'required' => false,
													'type' => 'text',
												));
										?>
									</th>
									<td>
										<?php
												echo $this->Html->link('<i class="fa fa-times"></i> '.__('Hapus'), 'javascript:', array(
													'class' => 'delete-custom-field btn btn-danger btn-xs',
													'action_type' => 'ttuj',
													'escape' => false
												));
										?>
									</th>
								</tr>
								<?php 
										}
								?>
							</tbody>
						</table>
				    </div>
				</div>
			</div>
			<?php 
					if( !empty($perlengkapans) ) {
			?>
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Perlengkapan');?></h3>
				    </div>
				    <div class="box-body">
				        <div class="row" id="ttuj-perlengkapan">
				        	<?php 
				        			$i = 0;
				        			foreach ($perlengkapans as $perlengkapanid => $perlengkapan) {
				        	?>
				        	<div class="col-sm-6">
				        		<div class="form-group has-feedback">
				                    <?php 
											echo $this->Form->input('TtujPerlengkapan.qty.'.$i,array(
												'label'=> false, 
												'required' => false,
												'class' => 'form-control',
												'value' => !empty($this->request->data['TtujPerlengkapan'][$perlengkapanid])?$this->request->data['TtujPerlengkapan'][$perlengkapanid]:false,
											));
											echo $this->Form->hidden('TtujPerlengkapan.id.'.$i,array(
												'value' => $perlengkapanid,
											));
									?>
									<span class="form-control-feedback"><?php echo $perlengkapan; ?></span>
						        </div>
				        	</div>
				        	<?php 
				        				$i++;
				        			}
				        	?>
				        </div>
				    </div>
				</div>
			</div>
			<?php 
					}
			?>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), '#step1', array(
						'class'=> 'btn btn-default',
						'id' => 'backTTUJ'
					));

					if( !empty($data_local['Ttuj']['is_draft']) || empty($data_local) ) {
			    		echo $this->Form->button(__('Commit'), array(
							'class'=> 'btn btn-success submit-form btn-lg',
							'type' => 'submit',
							'action_type' => 'commit'
						));
			    		echo $this->Form->button(__('Draft'), array(
							'class'=> 'btn btn-primary submit-form',
							'type' => 'submit',
							'action_type' => 'draft'
						));
			    		echo $this->Form->hidden('is_draft', array(
							'value'=> 1,
							'id' => 'is_draft'
						));
			    	}
		    		echo $this->Form->hidden('is_unit', array(
						'value'=> 1,
						'class' => 'is_unit'
					));
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>
<div class="hide">
	<div id="tipe_motor_id">
		<?php 
				echo $this->Form->input('tipe_motor_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Tipe Motor --'),
					'options' => $tipeMotors
				));
		?>
	</div>
</div>