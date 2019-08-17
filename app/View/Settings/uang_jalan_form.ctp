<?php
		$this->Html->addCrumb(__('Uang Jalan'), array(
			'controller' => 'settings',
			'action' => 'uang_jalan'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('UangJalan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'id' => 'UangJalanForm'
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Uang Jalan');?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
						echo $this->Html->tag('div', $this->Form->input('title',array(
							'label'=> __('Nama *'), 
							'class'=>'form-control',
							'required' => false,
						)), array(
							'class' => 'form-group'
						));
						echo $this->Html->tag('div', $this->Form->input('from_city_id',array(
							'label'=> __('Dari *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Kota Asal'),
							'options' => $fromCities,
						)), array(
							'class' => 'form-group'
						));
						echo $this->Html->tag('div', $this->Form->input('to_city_id',array(
							'label'=> __('Tujuan *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Kota Tujuan'),
							'options' => $toCities,
						)), array(
							'class' => 'form-group'
						));
						echo $this->Html->tag('div', $this->Form->input('capacity',array(
							'label'=> __('Kapasitas Truk *'), 
							'class'=>'form-control',
							'required' => false,
						)), array(
							'class' => 'form-group'
						));
				?>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('distance', __('Jarak Tempuh *'));
		    		?>
                    <div class="input-group">
				    	<?php 
								echo $this->Form->input('distance',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Jarak Tempuh'),
									'type' => 'text',
								));
				    			echo $this->Html->tag('span', __('KM'), array(
				    				'class' => 'input-group-addon'
			    				));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('arrive_lead_time', __('Lead Time Sampai Tujuan *'));
		    		?>
                    <div class="input-group">
				    	<?php 
								echo $this->Form->input('arrive_lead_time',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Lead Time'),
									'type' => 'text',
								));
				    			echo $this->Html->tag('span', __('Jam'), array(
				    				'class' => 'input-group-addon'
			    				));
						?>
					</div>
				</div>
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('back_lead_time', __('Lead Time Pulang ke Pool *'));
		    		?>
                    <div class="input-group">
				    	<?php 
								echo $this->Form->input('back_lead_time',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Lead Time'),
									'type' => 'text',
								));
				    			echo $this->Html->tag('span', __('Jam'), array(
				    				'class' => 'input-group-addon'
			    				));
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<?php 
								echo $this->Html->tag('div', $this->Form->input('group_classification_1_id',array(
									'label'=> __('Klasifikasi 1'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Klasifikasi'),
									'options' => $groupClassifications,
								)), array(
									'class' => 'form-group'
								));
								echo $this->Html->tag('div', $this->Form->input('group_classification_2_id',array(
									'label'=> __('Klasifikasi 2'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Klasifikasi'),
									'options' => $groupClassifications,
								)), array(
									'class' => 'form-group'
								));
						?>
					</div>
					<div class="col-sm-6">
						<?php 
								echo $this->Html->tag('div', $this->Form->input('group_classification_3_id',array(
									'label'=> __('Klasifikasi 3'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Klasifikasi'),
									'options' => $groupClassifications,
								)), array(
									'class' => 'form-group'
								));
								echo $this->Html->tag('div', $this->Form->input('group_classification_4_id',array(
									'label'=> __('Klasifikasi 4'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Klasifikasi'),
									'options' => $groupClassifications,
								)), array(
									'class' => 'form-group'
								));
						?>
					</div>
				</div>
				<?php 
						echo $this->Html->tag('div', $this->Form->input('note',array(
							'label'=> __('Keterangan'), 
							'class'=>'form-control',
							'required' => false,
						)), array(
							'class' => 'form-group'
						));
				?>
                <!-- <div class="form-group" id="UangJalanPerUnit">
                    <label>
                    	<?php 
        //             			echo $this->Form->input('is_unit',array(
								// 	'label'=> false, 
								// 	'required' => false,
								// 	'type' => 'checkbox',
								// 	'value' => 1
								// ));
								// echo __('Per Unit ?');
						?>
                    </label>
                </div> -->
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Uang Jalan');?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_jalan_1', __('Uang Jalan Pertama *'));
		    		?>
                    <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_jalan_1',array(
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Jalan Pertama'),
									'type' => 'text',
								));
						?>
					</div>
					<?php 
                			echo $this->Form->input('uang_jalan_per_unit',array(
								'label'=> __('Uang Jalan Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-uang-jalan',
							));
					?>
				</div>
		    	<div class="form-group uang_jalan_2 <?php echo !empty($this->request->data['UangJalan']['uang_jalan_per_unit'])?'hide':''; ?>">
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
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Jalan Kedua'),
									'type' => 'text',
								));
						?>
					</div>
				</div>
				<div class="biaya-per-unit <?php echo !empty($this->request->data['UangJalan']['uang_jalan_per_unit'])?'':'hide'; ?>">
					<?php 
		    				echo $this->Form->label('uang_jalan', __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'));
		    		?>
			        <div class="form-group">
			            <?php
			                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
			                        'class' => 'add-custom-field btn btn-success btn-xs',
			                        'action_type' => 'uang_jalan',
			                        'escape' => false
			                    ));
			            ?>
			        </div>
			        <div id="box-field-input">
			        	<?php 
			        			if( !empty($this->request->data['UangJalanTipeMotor']['group_motor_id']) ) {
									foreach ($this->request->data['UangJalanTipeMotor']['group_motor_id'] as $key => $group_motor_id) {
										echo $this->element('blocks/settings/list_uang_jalan', array(
											'idx' => $key,
										));
									}
								}
			        	?>
			        </div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Uang Jalan Extra');?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_jalan_extra', __('Uang Jalan Extra'));
		    		?>
					<div class="row">
						<div class="col-sm-3 no-pright">
		                    <div class="input-group">
								<span class="input-group-addon">></span>
						    	<?php 
										echo $this->Form->input('min_capacity',array(
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
											'placeholder' => __('Kapasitas'),
											'type' => 'text',
										));
								?>
							</div>
						</div>
						<div class="col-sm-9">
		                    <div class="input-group">
						    	<?php 
						    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
						    				'class' => 'input-group-addon'
					    				));
										echo $this->Form->input('uang_jalan_extra',array(
											'label'=> false, 
											'class'=>'form-control input_price_min',
											'required' => false,
											'placeholder' => __('Uang Jalan Extra'),
											'type' => 'text',
											'error' => false,
										));
								?>
							</div>
						</div>
					</div>
					<?php 
							echo $this->Form->error('uang_jalan_extra', array(
								'notempty' => __('Mohon lengkapi data Uang Jalan Extra'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
                			echo $this->Form->input('uang_jalan_extra_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-uang-jalan-extra',
							));
		        	?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Komisi');?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('commission', __('Komisi'));
		    		?>
                    <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('commission',array(
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Komisi'),
									'type' => 'text',
								));
						?>
					</div>
					<?php 
                			echo $this->Form->input('commission_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-commission',
							));
					?>
				</div>
				<div class="commission-unit <?php echo !empty($this->request->data['UangJalan']['commission_per_unit'])?'':'hide'; ?>">
					<?php 
		    				echo $this->Form->label('uang_jalan', __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'));
		    		?>
			        <div class="form-group">
			            <?php
			                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
			                        'class' => 'add-custom-field btn btn-success btn-xs',
			                        'action_type' => 'commission',
			                        'escape' => false
			                    ));
			            ?>
			        </div>
			        <div id="box-field-input-commission">
			        	<?php 
			        			if( !empty($this->request->data['CommissionGroupMotor']['group_motor_id']) ) {
									foreach ($this->request->data['CommissionGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
										echo $this->element('blocks/settings/list_commission', array(
											'idx' => $key,
										));
									}
								}
			        	?>
			        </div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Komisi Extra');?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('commission_extra', __('Komisi Extra'));
		    		?>
					<div class="row">
						<div class="col-sm-3 no-pright">
		                    <div class="input-group">
								<span class="input-group-addon">></span>
						    	<?php 
										echo $this->Form->input('commission_min_qty',array(
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
											'placeholder' => __('Muatan'),
											'type' => 'text',
											'title' => __('Muatan'),
										));
								?>
							</div>
						</div>
						<div class="col-sm-9">
		                    <div class="input-group">
						    	<?php 
						    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
						    				'class' => 'input-group-addon'
					    				));
										echo $this->Form->input('commission_extra',array(
											'label'=> false, 
											'class'=>'form-control input_price',
											'required' => false,
											'placeholder' => __('Komisi'),
											'type' => 'text',
											'error' => false,
										));
								?>
							</div>
						</div>
					</div>
		        	<?php 
							echo $this->Form->error('commission_extra', array(
								'notempty' => __('Mohon lengkapi data Komisi Extra'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
                			echo $this->Form->input('commission_extra_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-commission-extra',
							));
		        	?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Uang Penyebrangan');?></h3>
		    </div>
		    <div class="box-body">
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
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Penyebrangan'),
									'type' => 'text',
								));
						?>
					</div>
					<?php 
                			echo $this->Form->input('asdp_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-asdp',
							));
					?>
				</div>
				<div class="asdp-unit <?php echo !empty($this->request->data['UangJalan']['asdp_per_unit'])?'':'hide'; ?>">
					<?php 
		    				echo $this->Form->label('uang_jalan', __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'));
		    		?>
			        <div class="form-group">
			            <?php
			                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
			                        'class' => 'add-custom-field btn btn-success btn-xs',
			                        'action_type' => 'asdp',
			                        'escape' => false
			                    ));
			            ?>
			        </div>
			        <div id="box-field-input-asdp">
			        	<?php 
			        			if( !empty($this->request->data['AsdpGroupMotor']['group_motor_id']) ) {
									foreach ($this->request->data['AsdpGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
										echo $this->element('blocks/settings/list_asdp', array(
											'idx' => $key,
										));
									}
								}
			        	?>
			        </div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Uang Kawal');?></h3>
		    </div>
		    <div class="box-body">
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
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Kawal'),
									'type' => 'text',
								));
						?>
					</div>
					<?php 
                			echo $this->Form->input('uang_kawal_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-uang-kawal',
							));
					?>
				</div>
				<div class="uang-kawal-unit <?php echo !empty($this->request->data['UangJalan']['uang_kawal_per_unit'])?'':'hide'; ?>">
					<?php 
		    				echo $this->Form->label('uang_jalan', __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'));
		    		?>
			        <div class="form-group">
			            <?php
			                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
			                        'class' => 'add-custom-field btn btn-success btn-xs',
			                        'action_type' => 'uang_kawal',
			                        'escape' => false
			                    ));
			            ?>
			        </div>
			        <div id="box-field-input-uang-kawal">
			        	<?php 
			        			if( !empty($this->request->data['UangKawalGroupMotor']['group_motor_id']) ) {
									foreach ($this->request->data['UangKawalGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
										echo $this->element('blocks/settings/list_uang_kawal', array(
											'idx' => $key,
										));
									}
								}
			        	?>
			        </div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Uang Keamanan');?></h3>
		    </div>
		    <div class="box-body">
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
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Keamanan'),
									'type' => 'text',
								));
						?>
					</div>
					<?php 
                			echo $this->Form->input('uang_keamanan_per_unit',array(
								'label'=> __('Per Unit ?'), 
								'required' => false,
								'type' => 'checkbox',
								'value' => 1,
								'class' => 'chk-uang-keamanan',
							));
					?>
				</div>
				<div class="uang-keamanan-unit <?php echo !empty($this->request->data['UangJalan']['uang_keamanan_per_unit'])?'':'hide'; ?>">
					<?php 
		    				echo $this->Form->label('uang_keamanan', __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'));
		    		?>
			        <div class="form-group">
			            <?php
			                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
			                        'class' => 'add-custom-field btn btn-success btn-xs',
			                        'action_type' => 'uang_keamanan',
			                        'escape' => false
			                    ));
			            ?>
			        </div>
			        <div id="box-field-input-uang-keamanan">
			        	<?php 
			        			if( !empty($this->request->data['UangKeamananGroupMotor']['group_motor_id']) ) {
									foreach ($this->request->data['UangKeamananGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
										echo $this->element('blocks/settings/list_uang_keamanan', array(
											'idx' => $key,
										));
									}
								}
			        	?>
			        </div>
				</div>
		    </div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Titipan'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
		    		<div class="col-sm-4">
		    			<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('potongan_tabungan', __('Titipan'));
				    		?>
		                    <div class="input-group">
						    	<?php 
						    			echo $this->Html->tag('span', $this->Form->input('potongan_tabungan_type',array(
											'label'=> false, 
											'required' => false,
											'options' => array(
												'percent' => '%',
												'nominal' => 'Rp.',
											),
										)), array(
						    				'class' => 'input-group-addon'
					    				));
										echo $this->Form->input('potongan_tabungan',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
										));
								?>
							</div>
						</div>
		    		</div>
		    	</div>
		    </div>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
			if( empty($view) ) {
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    	}
	    	
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'uang_jalan', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>
<div class="hide">
	<div id="tipe_motor">
		<?php
                echo $this->Form->input('group_motor_id', array(
                    'label'=> false, 
                    'class'=>'form-control',
                    'required' => false,
                    'empty' => false,
                    'empty' => __('Pilih Group Motor'),
               	 	'options' => $tipeMotors,
                ));
        ?>
	</div>
</div>