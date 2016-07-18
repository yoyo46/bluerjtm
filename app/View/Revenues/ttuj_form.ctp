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
			'id' => 'ttuj-form',
		));

		$datForm = !empty($this->request->data)?$this->request->data:false;
		$ttujDate = (!empty($datForm['Ttuj']['ttuj_date'])) ? $datForm['Ttuj']['ttuj_date'] : date('d/m/Y');
		$tglBerangkat = (!empty($datForm['Ttuj']['tgl_berangkat'])) ? $datForm['Ttuj']['tgl_berangkat'] : date('d/m/Y');
		$completedDate = (!empty($datForm['Ttuj']['completed_date'])) ? $datForm['Ttuj']['completed_date'] : date('d/m/Y');
		$branches = !empty($branches)?$branches:false;
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
				    	<?php 
				    			if( !empty($id) ) {
									echo $this->Form->input('no_ttuj',array(
										'label'=> __('No. TTUJ *'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => true,
										'placeholder' => __('No. TTUJ'),
										'div' => array(
											'class' => 'form-group',
										),
									));

				        			if( !empty($allowEditTtujBranch) ) {
										echo $this->Form->input('branch_id',array(
											'label'=> __('Cabang *'), 
											'class'=>'form-control',
											'required' => false,
											'options' => $branches,
											'div' => array(
												'class' => 'form-group',
											),
										));
				        			}
				        		}
				        ?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ttuj_date',array(
										'label'=> __('Tgl TTUJ *'), 
										'class'=>'form-control custom-date ajax-change',
										'required' => false,
										'type' => 'text',
										'value' => $ttujDate,
										'href' => $this->Html->url(array(
											'controller' => 'ajax',
											'action' => 'change_lead_time',
											'ttuj_date',
											'admin' => false,
										)),
										'data-form' => '#ttuj-form',
										'data-wrapper-write' => '#ttuj-lanjutan-lead-time',
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('customer_id',array(
										'label'=> __('Customer *'), 
										'class'=>'form-control customer chosen-select',
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
												'class'=>'form-control chosen-select',
												'required' => false,
												'empty' => __('Dari Kota --'),
												'div' => array(
													'class' => 'from_city'
												),
												'id' => 'getKotaTujuan',
												'options' => !empty($fromCities)?$fromCities:false,
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											$disabled = !empty($datForm['Ttuj']['from_city_id'])?false:true;
											$toCityClass = '';

											if( !$disabled ) {
												$toCityClass = 'chosen-select';
											}

											echo $this->Form->input('to_city_id',array(
												'label'=> false, 
												'class'=>'form-control '.$toCityClass,
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
	                                    'class' => 'ajaxModal visible-xs browse-docs',
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
		                            echo $this->Form->label('truck_id', __('No. Pol * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
		                    ?>
		                    <div class="row">
		                        <div class="col-sm-10">
						        	<?php 
											$disabled = !empty($datForm['Ttuj']['to_city_id'])?false:true;
											echo $this->Form->input('truck_id',array(
												'label'=> false, 
												'class'=>'form-control chosen-select',
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
			                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
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
		                            $attrBrowse = array(
		                                'class' => 'ajaxModal visible-xs browse-docs',
		                                'escape' => false,
										'title' => __('Supir Pengganti'),
										'data-action' => 'browse-form',
										'data-change' => 'driverID',
		                            );
		                            $urlBrowse = array(
		                                'controller'=> 'ajax', 
										'action' => 'getDrivers',
										!empty($data_local['Ttuj']['driver_penganti_id'])?$data_local['Ttuj']['driver_penganti_id']:0,
										'pengganti',
		                            );
									echo $this->Form->label('driver_penganti_id', sprintf(__('Supir Pengganti %s'), $this->Html->tag('small', '', array(
										'class' => 'sj_outstanding_pengganti'
									))).$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
							?>
				        	<div class="row">
				        		<div class="col-sm-10">
						        	<?php 
											echo $this->Form->input('driver_penganti_id',array(
												'label'=> false, 
												'class'=>'form-control driver-penganti chosen-select',
												'required' => false,
												'empty' => __('Pilih Supir Pengganti --'),
												'id' => 'driverID',
											));
									?>
				        		</div>
		        				<div class="col-sm-2 hidden-xs">
			                        <?php 
		        							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
			                        ?>
			                    </div>
				        	</div>
				        </div>
				        <?php 
   								if( !in_array($data_action, array( 'retail', 'demo' )) ) {
				        ?>
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
												'value' => $tglBerangkat,
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
									echo $this->Form->input('note',array(
										'label'=> __('Keterangan'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <?php 
				        		}
				        ?>
				    </div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Muatan'); ?></h3>
				    </div>
				    <div class="box-body form-added">
				    	<div class="form-group">
							<?php
									$totalUnitMuatan = '';
									$colSpan = 2;

       								if( in_array($data_action, array( 'retail', 'demo' )) ) {
										$colSpan ++;
									}

									echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
										'class' => 'field-added btn btn-success btn-xs',
										'escape' => false
									));
							?>
						</div>
						<div id="ttujDetail">
							<div class="thead">
								<div class="row">
									<?php 
       										if( in_array($data_action, array( 'retail', 'demo' )) ) {
												echo $this->Html->tag('div', __('Tujuan'), array(
													'class' => 'col-sm-3 th',
												));
												echo $this->Html->tag('div', __('Tipe Motor'), array(
													'class' => 'col-sm-3 th',
												));
												echo $this->Html->tag('div', __('Warna'), array(
													'class' => 'col-sm-2 th',
												));
											} else {
												echo $this->Html->tag('div', __('Tipe Motor'), array(
													'class' => 'col-sm-5 th',
												));
												echo $this->Html->tag('div', __('Warna'), array(
													'class' => 'col-sm-3 th',
												));
											}
											echo $this->Html->tag('div', __('Jumlah'), array(
												'class' => 'col-sm-2 th',
											));
											echo $this->Html->tag('div', __('Action'), array(
												'class' => 'col-sm-2 th text-center',
											));
									?>
								</div>
							</div>
							<div class="tbody field-content">
								<?php 
										if( !empty($datForm['TtujTipeMotor']['tipe_motor_id']) ) {
											$idx = 0;
											foreach ($datForm['TtujTipeMotor']['tipe_motor_id'] as $key => $tipe_motor_id) {
												$city_id = !empty($datForm['TtujTipeMotor']['city_id'][$key])?$datForm['TtujTipeMotor']['city_id'][$key]:false;
												$qty = !empty($datForm['TtujTipeMotor']['qty'][$key])?$datForm['TtujTipeMotor']['qty'][$key]:false;
												$color_motor_id = !empty($datForm['TtujTipeMotor']['color_motor_id'][$key])?$datForm['TtujTipeMotor']['color_motor_id'][$key]:false;
												$totalUnitMuatan += $qty;

												echo $this->element('blocks/ttuj/forms/tipe_motors', array(
													'idx' => $idx,
													'city_id' => $city_id,
													'tipe_motor_id' => $tipe_motor_id,
													'qty' => $qty,
													'color_motor_id' => $color_motor_id,
												));
												$idx++;
											}
										} else {
											echo $this->element('blocks/ttuj/forms/tipe_motors');
										}
								?>
							</div>
							<div class="tfoot">
								<div class="row total-desktop">
									<?php 
											echo $this->Html->tag('div', __('Total'), array(
												'class' => 'col-sm-8 text-right',
											));
											echo $this->Html->tag('div', $this->Html->tag('span', $totalUnitMuatan, array(
												'class' => 'total-unit-muatan',
											)), array(
												'class' => 'col-sm-2 text-center',
											));
									?>
								</div>
								<div class="total-mobile visible-xs text-center">
									<?php 
											echo $this->Html->tag('div', __('Total: ').$this->Html->tag('span', $totalUnitMuatan, array(
												'class' => 'total-unit-muatan',
											)), array(
												'class' => 'text-right',
											));
									?>
								</div>
							</div>
						</div>
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
												'value' => !empty($datForm['TtujPerlengkapan'][$perlengkapanid])?$datForm['TtujPerlengkapan'][$perlengkapanid]:false,
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

					if( in_array($data_action, array( 'retail', 'demo' )) ) {
						echo $this->Common->clearfix();
	        			echo $this->element('blocks/ttuj/forms/ttuj_lanjutan');
					}

					echo $this->element('blocks/revenues/biaya_ttuj');

					if( !empty($id) && !empty($allowClosingTtuj) ){
			?>
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Closing Ttuj'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<?php 
				    		echo $this->Html->tag('p', __('Closing digunakan jika proses Ttuj sudah selesai atau sampai pool.'));
				    	?>
				    	<div class="form-group">
				    		<div class="checkbox">
		                        <label class="completed-handle">
		                        	<?php 
		                        		echo $this->Form->checkbox('completed').' Proses Ttuj sudah selesai?';
		                        	?>
		                        </label>
		                    </div>
				    	</div>
	                    <div id="desc-complete" class="<?php echo !empty($datForm['Ttuj']['completed']) ? '' : 'hide';?>">
				    		<div class="form-group">
		                    	<?php 
										echo $this->Form->input('completed_date',array(
											'label'=> __('Tgl Selesai *'), 
											'class'=>'form-control custom-date',
											'required' => false,
											'type' => 'text',
											'value' => $completedDate,
										));

										if ($this->Form->isFieldError('completed')) {
										    echo $this->Form->error('completed');
										}
								?>
	                    	</div>
				    		<div class="form-group">
		                    	<?php 
										echo $this->Form->input('complete_desc',array(
											'label'=> __('Keterangan *'), 
											'class'=>'form-control',
											'required' => false,
											'type' => 'textarea'
										));

										if ($this->Form->isFieldError('completed')) {
										    echo $this->Form->error('completed');
										}
								?>
	                    	</div>
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
                    $allowSave = $this->Revenue->_callTtujPaid($data_local);

					echo $this->Html->link(__('Kembali'), array(
						'action' => 'ttuj', 
					), array(
						'class'=> 'btn btn-default',
					));

                    if( $allowSave ) {
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
				    	} else {
				    		echo $this->Form->button(__('Simpan'), array(
								'class'=> 'btn btn-success submit-form btn-lg',
								'type' => 'submit',
								'action_type' => 'commit'
							));

							$status = strtolower($this->Revenue->_callStatusTTUJ($data_local));
							$GroupId = !empty($GroupId)?$GroupId:false;

							if( $status == 'commit' && $GroupId == 1 ) {
					    		echo $this->Form->button(__('Draft'), array(
									'class'=> 'btn btn-primary submit-form',
									'type' => 'submit',
									'action_type' => 'draft'
								));
					    	}
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
		echo $this->Form->hidden('commission_per_unit',array(
			'class'=>'commission_per_unit',
		));
		echo $this->Form->hidden('commission_extra_per_unit',array(
			'class'=>'commission_extra_per_unit',
		));
		echo $this->Form->hidden('commission_min_qty',array(
			'class'=>'commission_min_qty',
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
					'empty' => __('Tipe Motor'),
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
					'empty' => __('Warna Motor'),
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
					'options' => !empty($tmpCities)?$tmpCities:false,
				));
		?>
	</div>
	<div class="list-tipe-motor">
		<?php 
				if( !empty($uangJalan['UangJalanTipeMotor']) ) {
					foreach ($uangJalan['UangJalanTipeMotor'] as $key => $value) {
						$uang_jalan_1 = $this->Common->filterEmptyField($value, 'UangJalanTipeMotor', 'uang_jalan_1');
						$uang_jalan_2 = $this->Common->filterEmptyField($value, 'UangJalanTipeMotor', 'uang_jalan_2');
						$group_motor_id = $this->Common->filterEmptyField($value, 'UangJalanTipeMotor', 'group_motor_id');

						echo $this->Html->tag('div', $uang_jalan_1, array(
							'class' => sprintf('uang-jalan-1-%s', $group_motor_id)
						));
						echo $this->Html->tag('div', $uang_jalan_2, array(
							'class' => sprintf('uang-jalan-2-%s', $group_motor_id)
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
						echo $this->Html->tag('div', $value['CommissionGroupMotor']['commission'], array(
							'class' => sprintf('commission-%s', $value['CommissionGroupMotor']['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['AsdpGroupMotor']) ) {
					foreach ($uangJalan['AsdpGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['AsdpGroupMotor']['asdp'], array(
							'class' => sprintf('asdp-%s', $value['AsdpGroupMotor']['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['UangKawalGroupMotor']) ) {
					foreach ($uangJalan['UangKawalGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['UangKawalGroupMotor']['uang_kawal'], array(
							'class' => sprintf('uang-kawal-%s', $value['UangKawalGroupMotor']['group_motor_id'])
						));
					}
				}

				if( !empty($uangJalan['UangKeamananGroupMotor']) ) {
					foreach ($uangJalan['UangKeamananGroupMotor'] as $key => $value) {
						echo $this->Html->tag('div', $value['UangKeamananGroupMotor']['uang_keamanan'], array(
							'class' => sprintf('uang-keamanan-%s', $value['UangKeamananGroupMotor']['group_motor_id'])
						));
					}
				}
		?>
	</div>
</div>