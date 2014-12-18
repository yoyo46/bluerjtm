<?php
		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Ttuj', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
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
												'disabled' => $disabled,
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
												'disabled' => $disabled,
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
										'disabled' => $disabled,
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
											'value' => !empty($uangJalan['UangJalan']['uang_jalan_1'])?number_format($uangJalan['UangJalan']['uang_jalan_1'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_jalan_2'])?number_format($uangJalan['UangJalan']['uang_jalan_2'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kuli_muat'])?number_format($uangJalan['UangJalan']['uang_kuli_muat'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?number_format($uangJalan['UangJalan']['uang_kuli_bongkar'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['asdp'])?number_format($uangJalan['UangJalan']['asdp'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kawal'])?number_format($uangJalan['UangJalan']['uang_kawal'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_keamanan'])?number_format($uangJalan['UangJalan']['uang_keamanan'], 0):false,
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
						'action' => 'index', 
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
										'action_type' => 'transaction',
										'escape' => false
									));
							?>
						</div>
				        <table class="table table-bordered table-striped" id="transDetail">
							<thead>
								<tr>
									<th><?php echo __('Tipe Motor'); ?></th>
									<th><?php echo __('Warna'); ?></th>
									<th><?php echo __('Jumlah Unit'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php 
										if( !empty($this->request->data['TransactionDetail']) ) {
											foreach ($this->request->data['TransactionDetail'] as $key => $detail) {
								?>
								<tr>
									<td>
										<?php
												echo $this->Form->input('TransactionDetail.'.$key.'.voucher_number', array(
													'type' => 'text',
													'class' => 'form-control',
													'label' => false
												));
										?>
									</td>
									<td>
										<?php
												echo $this->Form->input('TransactionDetail.'.$key.'.kiosk_id', array(
													'class' => 'form-control chosen-select',
													'label' => false,
													'required' => false,
													'div' => false,
													'options' => $kiosks,
													'empty' => __('Pilih Blok & Nomor'),
												));
										?>
									</th>
									<td>
										<?php
												echo $this->Form->input('TransactionDetail.'.$key.'.note', array(
													'type' => 'text',
													'class' => 'form-control',
													'label' => false
												));
										?>
									</th>
									<td>
										<?php
												echo $this->Form->input('TransactionDetail.'.$key.'.amount', array(
													'value' => $this->Number->currency(str_replace(',', '', $detail['amount']), '', array('places' => 0)),
													'type' => 'text',
													'class' => 'form-control input_price',
													'label' => false
												));
										?>
									</th>
									<td>
										<?php
												echo $this->Html->link('<i class="fa fa-times"></i> '.__('Hapus'), 'javascript:', array(
													'class' => 'delete-custom-field btn btn-danger btn-xs',
													'action_type' => 'transaction',
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
													'options' => array()
												));
										?>
									</td>
									<td>
										<?php 
												echo $this->Form->input('TtujTipeMotor.color_motor_id.0',array(
													'label'=> false, 
													'class'=>'form-control',
													'required' => false,
													'type' => 'text',
													'disabled' => true
												));
										?>
									</th>
									<td>
										<?php 
												echo $this->Form->input('TtujTipeMotor.qty.0',array(
													'label'=> false, 
													'class'=>'form-control',
													'required' => false,
													'type' => 'text',
													'disabled' => true
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
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Perlengkapan');?></h3>
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
											'value' => !empty($uangJalan['UangJalan']['uang_jalan_1'])?number_format($uangJalan['UangJalan']['uang_jalan_1'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_jalan_2'])?number_format($uangJalan['UangJalan']['uang_jalan_2'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kuli_muat'])?number_format($uangJalan['UangJalan']['uang_kuli_muat'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?number_format($uangJalan['UangJalan']['uang_kuli_bongkar'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['asdp'])?number_format($uangJalan['UangJalan']['asdp'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_kawal'])?number_format($uangJalan['UangJalan']['uang_kawal'], 0):false,
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
											'value' => !empty($uangJalan['UangJalan']['uang_keamanan'])?number_format($uangJalan['UangJalan']['uang_keamanan'], 0):false,
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
		    		echo $this->Html->link(__('Kembali'), '#step1', array(
						'class'=> 'btn btn-default',
						'id' => 'backTTUJ'
					));
		    		echo $this->Html->link(__('Next'), '#step2', array(
						'class'=> 'btn btn-success',
						'id' => 'nextTTUJ'
					));
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>