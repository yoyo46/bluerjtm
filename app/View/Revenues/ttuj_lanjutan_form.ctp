<?php
		$classJamBongkaran = 'timepicker';
		
		switch ($action_type) {
			case 'bongkaran':
				$backUrl = array(
					'controller' => 'revenues',
					'action' => 'bongkaran'
				);
				$this->Html->addCrumb(__('Bongkaran'), $backUrl);
    			$disabledTglJamBongkaran = false;
    			$disabledTglJamTiba = true;
    			$classJamTiba = '';

    			if( !empty($ttuj_id) ) {
        			$disabledTglJamBongkaran = true;
    			}
				break;

			case 'balik':
				$backUrl = array(
					'controller' => 'revenues',
					'action' => 'balik'
				);
				$this->Html->addCrumb(__('TTUJ Balik'), $backUrl);
    			$disabledTglJamBalik = false;
    			$disabledTglJamBongkaran = true;
    			$disabledTglJamTiba = true;
    			$classJamTiba = '';
    			$classJamBongkaran = '';

    			if( !empty($ttuj_id) ) {
        			$disabledTglJamBalik = true;
    			}
				break;

			case 'pool':
				$backUrl = array(
					'controller' => 'revenues',
					'action' => 'pool'
				);
				$this->Html->addCrumb(__('TTUJ Sampai Pool'), $backUrl);
    			$disabledTglJamPool = false;
    			$disabledTglJamBalik = true;
    			$disabledTglJamBongkaran = true;
    			$disabledTglJamTiba = true;
    			$classJamTiba = '';
    			$classJamBongkaran = '';
    			$classJamPool = '';

    			if( !empty($ttuj_id) ) {
        			$disabledTglJamPool = true;
    			}
				break;
			
			default:
				$backUrl = array(
					'controller' => 'revenues',
					'action' => 'truk_tiba'
				);
				$this->Html->addCrumb(__('Truk Tiba'), $backUrl);
    			$disabledTglJamTiba = false;
    			$classJamTiba = 'timepicker';

    			if( !empty($ttuj_id) ) {
        			$disabledTglJamTiba = true;
    			}
				break;
		}
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
				        			if( !empty($ttuj_id) ) {
										echo $this->Form->input('no_ttuj',array(
											'label'=> __('No. TTUJ'), 
											'class'=>'form-control',
											'required' => false,
											'disabled' => true,
										));
									} else {
			        					$attrBrowse = array(
	                                    	'class' => 'ajaxModal visible-xs',
	                                        'escape' => false,
	                                        'title' => __('Data TTUJ'),
	                                        'data-action' => 'browse-form',
	                                        'data-change' => 'no_ttuj',
		                                );
			        					$urlBrowse = array(
	                                     	'controller'=> 'ajax', 
	                                        'action' => 'getTtujs',
	                                        $action_type,
		                                );
		                            	echo $this->Form->label('no_ttuj', __('No. TTUJ * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
		                    ?>
		                    <div class="row">
		                        <div class="col-sm-10">
									<?php
												echo $this->Form->input('no_ttuj',array(
													'label'=> false, 
													'class'=>'form-control submit-change',
													'required' => false,
													'empty' => __('Pilih No. TTUJ --'),
													'options' => $ttujs,
													'id' => 'no_ttuj',
													'action_type' => $action_type,
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
							<?php
									}
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ttuj_date',array(
										'label'=> __('Tgl TTUJ'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text',
										'disabled' => true,
										'id' => 'ttuj_date',
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('customer_name',array(
										'label'=> __('Customer'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => true,
										'id' => 'customer_name',
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
											echo $this->Form->input('from_city_name',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'div' => false,
												'disabled' => true,
												'id' => 'from_city_name',
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('to_city_name',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'disabled' => true,
												'div' => false,
												'id' => 'to_city_name',
											));
									?>
								</div>
							</div>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('nopol',array(
										'label'=> __('No. Pol'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => true,
										'div' => false,
										'id' => 'nopol',
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
										'id' => 'driver_name',
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('DriverPenganti.name',array(
										'label'=> __('Supir Pengganti'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => true,
										'id' => 'driver_penganti_name',
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
												'class'=>'form-control',
												'required' => false,
												'type' => 'text',
												'disabled' => true,
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
													'class'=>'form-control pull-right',
													'required' => false,
													'type' => 'text',
													'disabled' => true,
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('tgljam_tiba', __('Tgl & Jam Tiba *'));
							?>
				        	<div class="row">
				        		<div class="col-sm-8">
				        			<?php 
											echo $this->Form->input('tgl_tiba',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'type' => 'text',
												'disabled' => $disabledTglJamTiba,
											));
									?>
				        		</div>
				        		<div class="col-sm-4">
				        			<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
					        			<?php 
												echo $this->Form->input('jam_tiba',array(
													'label'=> false, 
													'class'=>'form-control pull-right '.$classJamTiba,
													'required' => false,
													'type' => 'text',
													'disabled' => $disabledTglJamTiba,
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        	<?php 
									echo $this->Form->error('tgljam_tiba', array(
										'notempty' => __('Tgl & Jam Tiba harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
				        	?>
				        </div>
				        <?php 
				        		if ( in_array($action_type, array( 'bongkaran', 'balik', 'pool' )) ) {
						?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('tgljam_bongkaran', __('Tgl & Jam Bongkaran *'));
							?>
				        	<div class="row">
				        		<div class="col-sm-8">
				        			<?php 
											echo $this->Form->input('tgl_bongkaran',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'type' => 'text',
												'disabled' => $disabledTglJamBongkaran,
											));
									?>
				        		</div>
				        		<div class="col-sm-4">
				        			<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
					        			<?php 
												echo $this->Form->input('jam_bongkaran',array(
													'label'=> false, 
													'class'=>'form-control pull-right '.$classJamBongkaran,
													'required' => false,
													'type' => 'text',
													'disabled' => $disabledTglJamBongkaran,
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        	<?php 
									echo $this->Form->error('tgljam_bongkaran', array(
										'notempty' => __('Tgl & Jam Bongkaran harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
				        	?>
				        </div>
						<?php
				        			if ( $action_type == 'bongkaran' ) {
				        				echo $this->Html->tag('div', $this->Form->input('note_bongkaran', array(
											'label'=> __('Keterangan'), 
											'class'=>'form-control',
											'required' => false,
											'disabled' => $disabledTglJamBongkaran,
										)), array(
											'class'=>'form-group',
										));
				        			}
		        				}

				        		if ( in_array($action_type, array( 'balik', 'pool' )) ) {
						?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('tgljam_balik', __('Tgl & Jam Balik *'));
							?>
				        	<div class="row">
				        		<div class="col-sm-8">
				        			<?php 
											echo $this->Form->input('tgl_balik',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'type' => 'text',
												'disabled' => $disabledTglJamBalik,
											));
									?>
				        		</div>
				        		<div class="col-sm-4">
				        			<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
					        			<?php 
												echo $this->Form->input('jam_balik',array(
													'label'=> false, 
													'class'=>'form-control pull-right timepicker',
													'required' => false,
													'type' => 'text',
													'disabled' => $disabledTglJamBalik,
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        	<?php 
									echo $this->Form->error('tgljam_balik', array(
										'notempty' => __('Tgl & Jam Balik harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
				        	?>
				        </div>
						<?php
				        			if ( $action_type == 'balik' ) {
				        				echo $this->Html->tag('div', $this->Form->input('note_balik', array(
											'label'=> __('Keterangan'), 
											'class'=>'form-control',
											'required' => false,
											'disabled' => $disabledTglJamBalik,
										)), array(
											'class'=>'form-group',
										));
				        			}
		        				}

				        		if ( $action_type == 'pool' ) {
						?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('tgljam_pool', __('Tgl & Jam Sampai Pool *'));
							?>
				        	<div class="row">
				        		<div class="col-sm-8">
				        			<?php 
											echo $this->Form->input('tgl_pool',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'type' => 'text',
												'disabled' => $disabledTglJamPool,
											));
									?>
				        		</div>
				        		<div class="col-sm-4">
				        			<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
					        			<?php 
												echo $this->Form->input('jam_pool',array(
													'label'=> false, 
													'class'=>'form-control pull-right timepicker',
													'required' => false,
													'type' => 'text',
													'disabled' => $disabledTglJamPool,
												));
										?>
                                    </div>
				        		</div>
				        	</div>
				        	<?php 
									echo $this->Form->error('tgljam_pool', array(
										'notempty' => __('Tgl & Jam Sampai Pool harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
				        	?>
				        </div>
						<?php
			        				echo $this->Html->tag('div', $this->Form->input('note_pool', array(
										'label'=> __('Keterangan'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => $disabledTglJamPool,
									)), array(
										'class'=>'form-group',
									));
		        				}
				        			
				        		if ( $action_type == 'truk_tiba' ) {
			        				echo $this->Html->tag('div', $this->Form->input('note_tiba', array(
										'label'=> __('Keterangan'), 
										'class'=>'form-control',
										'required' => false,
										'disabled' => $disabledTglJamTiba,
									)), array(
										'class'=>'form-group',
									));
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
				    <div class="box-body">
				        <table class="table table-bordered table-striped" id="ttujDetail">
							<thead>
								<tr>
									<?php 
											if( $data_action == 'retail' ) {
												echo $this->Html->tag('th', __('Tujuan'));
											}
											echo $this->Html->tag('th', __('Tipe Motor'));
											echo $this->Html->tag('th', __('Jumlah Unit'));
									?>
								</tr>
							</thead>
							<tbody>
								<?php 
										if( !empty($this->request->data['TtujTipeMotor']['tipe_motor_id']) ) {
											foreach ($this->request->data['TtujTipeMotor']['tipe_motor_id'] as $key => $tipe_motor_id) {
												$qty = !empty($this->request->data['TtujTipeMotor']['qty'][$key])?$this->request->data['TtujTipeMotor']['qty'][$key]:false;
												$city = !empty($this->request->data['TtujTipeMotor']['city'][$key])?$this->request->data['TtujTipeMotor']['city'][$key]:false;
								?>
								<tr>
									<?php
											if( $data_action == 'retail' ) {
												echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.city_id.'.$key,array(
													'label'=> false, 
													'class'=>'form-control',
													'required' => false,
													'value' => $city,
													'disabled' => true,
													'required' => false,
												)));
											}

											echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.tipe_motor_id.'.$key, array(
												'class' => 'form-control',
												'label' => false,
												'empty' => __('Pilih Tipe Motor --'),
												'options' => $tipeMotors,
												'value' => $tipe_motor_id,
												'required' => false,
												'disabled' => true,
											)));
											echo $this->Html->tag('td', $this->Form->input('TtujTipeMotor.qty.'.$key, array(
												'class' => 'form-control',
												'label' => false,
												'required' => false,
												'div' => false,
												'value' => $qty,
												'disabled' => true,
											)));
									?>
								</tr>
								<?php
											}
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
												'disabled' => true,
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

					switch ($action_type) {
						case 'bongkaran':
			?>
			<div class="col-sm-6">
				<div class="box box-primary" id="biaya-uang-jalan">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Biaya Bongkaran');?></h3>
				    </div>
				    <div class="box-body">
				    	<?php 
				    			if( !empty($this->request->data['Ttuj']['uang_jalan_2']) ) {
				    	?>
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
						<?php 
								}
						?>
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
				</div>
			</div>
			<?php
							break;
					}
			?>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), $backUrl, array(
						'class'=> 'btn btn-default',
					));

        			if( empty($ttuj_id) ) {
			    		echo $this->Form->button(__('Simpan'), array(
							'class'=> 'btn btn-success',
							'type' => 'submit',
						));
			    	}
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