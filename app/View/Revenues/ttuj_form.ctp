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
    		'autocomplete'=> 'off', 
    		'novalidate' => true,
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
										'value' => (!empty($this->request->data['Ttuj']['ttuj_date'])) ? $this->request->data['Ttuj']['ttuj_date'] : date('d/m/Y')
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
		        					$attrBrowse = array(
	                                    'class' => 'ajaxModal visible-xs',
                                        'escape' => false,
                                        'title' => __('Data Truk'),
                                        'data-action' => 'browse-form',
                                        'data-change' => 'truckID',
                                        'id' => 'truckBrowse',
										'disabled' => $disabled,
	                                );
		        					$urlBrowse = array(
	                                    'controller'=> 'ajax', 
                                        'action' => 'getTrucks',
                                        'ttuj',
                                        !empty($data_local['Ttuj']['id'])?$data_local['Ttuj']['id']:false,
	                                );
		                            echo $this->Form->label('truck_id', __('No. Pol * ').$this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
		                    ?>
		                    <div class="row">
		                        <div class="col-sm-10">
						        	<?php 
											$disabled = !empty($this->request->data['Ttuj']['to_city_id'])?false:true;
											echo $this->Form->input('truck_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Pilih No. Pol --'),
												'disabled' => $disabled,
												'div' => array(
													'class' => 'truck_id'
												),
												'id' => 'truckID',
											));
									?>
		                        </div>
		        				<div class="col-sm-2 hidden-xs">
			                        <?php 
		        							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                                echo $this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
			                        ?>
			                    </div>
		                    </div>
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
									echo $this->Form->input('driver_name',array(
										'label'=> sprintf(__('Supir %s'), $this->Html->tag('small', '', array(
											'class' => 'sj_outstanding'
										))), 
										'class'=>'form-control driver_name',
										'required' => false,
										'readonly' => true,
									));
									echo $this->Form->hidden('driver_id',array(
										'class' => 'driver_id',
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
												'type' => 'text',
												'value' => (!empty($this->request->data['Ttuj']['tgl_berangkat'])) ? $this->request->data['Ttuj']['tgl_berangkat'] : date('d/m/Y')
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
									echo $this->Form->input('driver_penganti_id',array(
										'label'=> sprintf(__('Supir Pengganti %s'), $this->Html->tag('small', '', array(
											'class' => 'sj_outstanding_pengganti'
										))), 
										'class'=>'form-control driver-penganti',
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
			<?php 
					/*
					if( !empty($data_local) ) {
			?>
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Surat Jalan Kembali'); ?></h3>
				    </div>
				    <div class="box-body">
		    			<?php echo $this->Html->tag('p', __('Centang apabila semua Surat Jalan telah diterima'));?>
				        <div class="checkbox">
			                <label>
			                    <?php 
									echo $this->Form->checkbox('getting_sj',array(
										'label'=> false, 
										'required' => false,
										'id' => 'sj-handle',
									)).__('SJ sudah diterima??');
								?>
			                </label>
			            </div>
			            <div class="sj-date <?php echo (!empty($this->request->data['Ttuj']['getting_sj'])) ? '' : 'hide'; ?>">
							<div class="row">
								<div class="col-sm-9">
									<div class="form-group">
										<?php 
												echo $this->Form->input('date_sj',array(
													'label'=> __('Tgl SJ diterima'), 
													'class'=>'form-control custom-date',
													'type' => 'text'
												));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<?php 
												echo $this->Form->input('qty_sj',array(
													'label'=> __('Qty SJ'), 
													'class'=>'form-control',
													'type' => 'text'
												));
										?>
									</div>
								</div>
							</div>
			            </div>
				    </div>
				</div>
			</div>
			<?php 
					}
		            */
			?>
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Muatan'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<div class="form-group">
							<?php
									$dataCustom = '';
									$totalUnitMuatan = 0;
									$colSpan = 2;

									if( $data_action == 'retail' ) {
										$dataCustom = 'retail';
										$colSpan ++;
									}

									echo $this->Common->rule_link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
										'class' => 'add-custom-field btn btn-success btn-xs',
										'action_type' => 'ttuj',
										'data-custom' => $dataCustom,
										'escape' => false
									));
							?>
						</div>
				        <table class="table table-bordered table-striped" id="ttujDetail">
							<thead>
								<tr>
									<?php 
											if( $data_action == 'retail' ) {
												echo $this->Html->tag('th', __('Tujuan'));
											}
											echo $this->Html->tag('th', __('Tipe Motor'));
											echo $this->Html->tag('th', __('Warna Motor'));
											echo $this->Html->tag('th', __('Jumlah Unit'));
											echo $this->Html->tag('th', __('Action'));
									?>
								</tr>
							</thead>
							<tbody>
								<?php 
										if( !empty($this->request->data['TtujTipeMotor']['tipe_motor_id']) ) {
											foreach ($this->request->data['TtujTipeMotor']['tipe_motor_id'] as $key => $tipe_motor_id) {
												$qty = !empty($this->request->data['TtujTipeMotor']['qty'][$key])?$this->request->data['TtujTipeMotor']['qty'][$key]:false;
												$color_motor_id = !empty($this->request->data['TtujTipeMotor']['color_motor_id'][$key])?$this->request->data['TtujTipeMotor']['color_motor_id'][$key]:false;
												$totalUnitMuatan += $qty;
								?>
								<tr>
									<?php
											if( $data_action == 'retail' ) {
												$city_id = !empty($this->request->data['TtujTipeMotor']['city_id'][$key])?$this->request->data['TtujTipeMotor']['city_id'][$key]:false;

												echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.city_id.',array(
													'label'=> false, 
													'class'=>'form-control city-retail-id',
													'required' => false,
													'empty' => __('Pilih Tujuan --'),
													'options' => $cities,
													'value' => $city_id,
												)));
											}

											echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.tipe_motor_id.', array(
												'class' => 'form-control tipe_motor_id',
												'label' => false,
												'empty' => __('Pilih Tipe Motor --'),
												'options' => $tipeMotors,
												'value' => $tipe_motor_id,
												'required' => false,
												'rel' => $key,
											)));
											echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.color_motor_id.', array(
												'class' => 'form-control',
												'label' => false,
												'empty' => __('Pilih Warna Motor --'),
												'options' => $colors,
												'value' => $color_motor_id,
												'required' => false,
												'rel' => $key,
											)));
											echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.qty.', array(
												'class' => 'form-control qty-muatan',
												'label' => false,
												'required' => false,
												'div' => false,
												'value' => $qty,
												'rel' => $key,
											)));
											echo $this->Html->tag('td', $this->Common->rule_link('<i class="fa fa-times"></i> '.__('Hapus'), 'javascript:', array(
												'class' => 'delete-custom-field btn btn-danger btn-xs',
												'action_type' => 'ttuj',
												'escape' => false
											)));
									?>
								</tr>
								<?php
											}
										}
								?>
							</tbody>
							<tfoot>
								<tr>
									<?php 
											echo $this->Html->tag('th', __('Total'), array(
												'colspan' => $colSpan,
												'class' => 'text-right',
											));
											echo $this->Html->tag('th', $totalUnitMuatan, array(
												'class' => 'total-unit-muatan',
											));
									?>
								</tr>
							</tfoot>
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
			<div class="col-sm-6">
				<div class="box box-primary" id="biaya-uang-jalan">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Biaya Perjalanan');?></h3>
				    </div>
				    <div class="box-body">
				    	<div class="row">
				    		<div class="col-sm-6">
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
													'error' => false,
												));
												echo $this->Form->hidden('uang_jalan_1_ori',array(
													'class'=>'uang_jalan_1_ori',
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_uang_jalan_2 <?php echo (isset($this->request->data['Ttuj']['uang_jalan_2']) && !$this->request->data['Ttuj']['uang_jalan_2'])?'hide':''; ?>">
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
													'class'=>'form-control uang_jalan_2 input_price',
													'required' => false,
													'type' => 'text',
													'readonly' => true,
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_uang_jalan_extra <?php echo (isset($this->request->data['Ttuj']['uang_jalan_extra']) && !$this->request->data['Ttuj']['uang_jalan_extra'])?'hide':''; ?>">
						    	<div class="form-group">
						    		<?php 
						    				echo $this->Form->label('uang_jalan_extra', __('Uang Jalan Extra'));
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
												echo $this->Form->hidden('uang_jalan_extra_ori',array(
													'class'=>'uang_jalan_extra_ori',
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_min_capacity <?php echo (isset($this->request->data['Ttuj']['min_capacity']) && !$this->request->data['Ttuj']['min_capacity'])?'hide':''; ?>">
						    	<div class="form-group">
							    	<?php 
											echo $this->Form->input('min_capacity',array(
												'label'=> __('Minimum Kapasitas'), 
												'class'=>'form-control min_capacity',
												'required' => false,
												'empty' => __('Minimum Kapasitas'),
												'type' => 'text',
												'readonly' => true,
											));
									?>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_uang_kuli_muat <?php echo (isset($this->request->data['Ttuj']['uang_kuli_muat']) && !$this->request->data['Ttuj']['uang_kuli_muat'])?'hide':''; ?>">
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
												echo $this->Form->hidden('uang_kuli_muat_ori',array(
													'class'=>'uang_kuli_muat_ori',
												));
										?>
									</div>
								</div>
				    		</div>
				    		<div class="col-sm-6 wrapper_uang_kuli_bongkar <?php echo (isset($this->request->data['Ttuj']['uang_kuli_bongkar']) && !$this->request->data['Ttuj']['uang_kuli_bongkar'])?'hide':''; ?>">
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
												echo $this->Form->hidden('uang_kuli_bongkar_ori',array(
													'class'=>'uang_kuli_bongkar_ori',
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_asdp <?php echo (isset($this->request->data['Ttuj']['asdp']) && !$this->request->data['Ttuj']['asdp'])?'hide':''; ?>">
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
												echo $this->Form->hidden('asdp_ori',array(
													'class'=>'asdp_ori',
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_uang_kawal <?php echo (isset($this->request->data['Ttuj']['uang_kawal']) && !$this->request->data['Ttuj']['uang_kawal'])?'hide':''; ?>">
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
												echo $this->Form->hidden('uang_kawal_ori',array(
													'class'=>'uang_kawal_ori',
												));
										?>
									</div>
								</div>
							</div>
				    		<div class="col-sm-6 wrapper_uang_keamanan <?php echo (isset($this->request->data['Ttuj']['uang_keamanan']) && !$this->request->data['Ttuj']['uang_keamanan'])?'hide':''; ?>">
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
												echo $this->Form->hidden('uang_keamanan_ori',array(
													'class'=>'uang_keamanan_ori',
												));
										?>
									</div>
								</div>
				    		</div>
				    	</div>
				        <?php 
								echo $this->Form->error('uang_jalan_1', array(
									'notempty' => __('Biaya Uang Jalan belum dibuat'),
								), array(
									'wrap' => 'div', 
									'class' => 'error-message',
								));
			        	?>
				    </div>
				</div>
				<div id="informasi-sj"></div>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
					$allowSave = true;

					if( isset($data_local['Ttuj']['status']) && empty($data_local['Ttuj']['status']) ) {
						$allowSave = false;
					}

					echo $this->Common->rule_link(__('Kembali'), array(
						'action' => 'ttuj', 
					), array(
						'class'=> 'btn btn-default',
					));

                    if( empty($data_local['Ttuj']['is_invoice']) && $allowSave ) {
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
				    	} else if( in_array('update_ttuj_commit', $allowModule) ) {
				    		echo $this->Form->button(__('Simpan'), array(
								'class'=> 'btn btn-success submit-form btn-lg',
								'type' => 'submit',
								'action_type' => 'commit'
							));
				    	}
				    	
			    		echo $this->Form->hidden('is_draft', array(
							'value'=> 1,
							'id' => 'is_draft'
						));
				    }
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->hidden('uang_jalan_per_unit',array(
			'class'=>'uang_jalan_per_unit',
		));
		echo $this->Form->hidden('uang_kuli_muat_per_unit',array(
			'class'=>'uang_kuli_muat_per_unit',
		));
		echo $this->Form->hidden('uang_kuli_bongkar_per_unit',array(
			'class'=>'uang_kuli_bongkar_per_unit',
		));
		echo $this->Form->hidden('asdp_per_unit',array(
			'class'=>'asdp_per_unit',
		));
		echo $this->Form->hidden('uang_kawal_per_unit',array(
			'class'=>'uang_kawal_per_unit',
		));
		echo $this->Form->hidden('uang_keamanan_per_unit',array(
			'class'=>'uang_keamanan_per_unit',
		));
		echo $this->Form->hidden('uang_jalan_extra_per_unit',array(
			'class'=>'uang_jalan_extra_per_unit',
		));
		echo $this->Form->hidden('uang_jalan_extra_per_unit',array(
			'class'=>'uang_jalan_extra_per_unit',
		));
		echo $this->Form->hidden('commission',array(
			'class'=>'commission',
		));
		echo $this->Form->hidden('commission_ori',array(
			'class'=>'commission_ori',
		));
		echo $this->Form->hidden('commission_per_unit',array(
			'class'=>'commission_per_unit',
		));
		echo $this->Form->hidden('commission_extra',array(
			'class'=>'commission_extra',
		));
		echo $this->Form->hidden('commission_extra_ori',array(
			'class'=>'commission_extra_ori',
		));
		echo $this->Form->hidden('commission_extra_per_unit',array(
			'class'=>'commission_extra_per_unit',
		));
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
	<div id="group_tipe_motor_id">
		<?php 
				echo $this->Form->input('group_tipe_motor_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Group Motor --'),
					'options' => $groupTipeMotors
				));
		?>
	</div>
	<div id="color_motor_id">
		<?php 
				echo $this->Form->input('color_motor_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Warna Motor --'),
					'options' => $colors
				));
		?>
	</div>
	<div id="data-cities-options">
		<?php 
				echo $this->Form->input('city_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Tujuan --'),
					'options' => $cities
				));
		?>
	</div>
	<div class="list-tipe-motor">
		<?php 
				if( !empty($uangJalan['UangJalanTipeMotor']) ) {
					foreach ($uangJalan['UangJalanTipeMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['uang_jalan_1'], array(
							'class' => sprintf('uang-jalan-1-%s', $value['tipe_motor_id'])
						));
						echo $this->Html->tag('div', $value['uang_jalan_2'], array(
							'class' => sprintf('uang-jalan-2-%s', $value['tipe_motor_id'])
						));
					}
				}

				if( !empty($uangKuli['UangKuliMuat']['UangKuliGroupMotor']) ) {
					foreach ($uangKuli['UangKuliMuat']['UangKuliGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['uang_kuli'], array(
							'class' => sprintf('uang-kuli-muat-%s', $value['group_motor_id'])
						));
					}
				}

				if( !empty($uangKuli['UangKuliBongkar']['UangKuliGroupMotor']) ) {
					foreach ($uangKuli['UangKuliBongkar']['UangKuliGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['uang_kuli'], array(
							'class' => sprintf('uang-kuli-bongkar-%s', $value['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['CommissionGroupMotor']) ) {
					foreach ($uangJalan['CommissionGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['commission'], array(
							'class' => sprintf('commission-%s', $value['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['AsdpGroupMotor']) ) {
					foreach ($uangJalan['AsdpGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['asdp'], array(
							'class' => sprintf('asdp-%s', $value['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['UangKawalGroupMotor']) ) {
					foreach ($uangJalan['UangKawalGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['uang_kawal'], array(
							'class' => sprintf('uang-kawal-%s', $value['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['UangKeamananGroupMotor']) ) {
					foreach ($uangJalan['UangKeamananGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['uang_keamanan'], array(
							'class' => sprintf('uang-keamanan-%s', $value['group_motor_id'])
						));
					}
				}
		?>
	</div>
</div>