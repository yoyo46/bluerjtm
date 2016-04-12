<?php 
		$datForm = !empty($this->request->data)?$this->request->data:false;
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
	    		<div class="col-sm-6 wrapper_uang_jalan_2 <?php echo (isset($datForm['Ttuj']['uang_jalan_2']) && empty($datForm['Ttuj']['uang_jalan_2']))?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_uang_jalan_extra <?php echo (isset($datForm['Ttuj']['uang_jalan_extra']) && !$datForm['Ttuj']['uang_jalan_extra'])?'hide':''; ?>">
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
										'class'=>'form-control input_price_min uang_jalan_extra',
										'required' => false,
										'empty' => __('Uang Jalan Extra'),
										'type' => 'text',
										'readonly' => true,
									));
									echo $this->Form->hidden('uang_jalan_extra_ori',array(
										'class'=>'uang_jalan_extra_ori',
									));

									if( !empty($converterUjs) ) {
										echo $this->element('blocks/ttuj/tipe_motor_converter', array(
											'idName' => 'converter-uang-jalan-extra',
											'class' => 'hide',
											'values' => $converterUjs,
										));
									}
							?>
						</div>
					</div>
				</div>
	    		<div class="col-sm-6 wrapper_min_capacity <?php echo ( !empty($datForm['Ttuj']['uang_jalan_extra']) )?'':'hide'; ?>">
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
	    		<div class="col-sm-6 wrapper_uang_kuli_muat <?php echo (isset($datForm['Ttuj']['uang_kuli_muat']) && !$datForm['Ttuj']['uang_kuli_muat'])?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_uang_kuli_bongkar <?php echo (isset($datForm['Ttuj']['uang_kuli_bongkar']) && !$datForm['Ttuj']['uang_kuli_bongkar'])?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_asdp <?php echo (isset($datForm['Ttuj']['asdp']) && !$datForm['Ttuj']['asdp'])?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_uang_kawal <?php echo (isset($datForm['Ttuj']['uang_kawal']) && !$datForm['Ttuj']['uang_kawal'])?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_uang_keamanan <?php echo (isset($datForm['Ttuj']['uang_keamanan']) && !$datForm['Ttuj']['uang_keamanan'])?'hide':''; ?>">
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
	    		<div class="col-sm-6 wrapper_commission <?php echo (isset($datForm['Ttuj']['commission']) && !$datForm['Ttuj']['commission'])?'hide':''; ?>">
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
										'class'=>'form-control commission input_price',
										'required' => false,
										'empty' => __('Komisi'),
										'type' => 'text',
										'readonly' => true,
									));
									echo $this->Form->hidden('commission_ori',array(
										'class'=>'commission_ori',
									));
							?>
						</div>
					</div>
	    		</div>
	    		<div class="col-sm-6 wrapper_commission_extra <?php echo (isset($datForm['Ttuj']['commission_extra']) && !$datForm['Ttuj']['commission_extra'])?'hide':''; ?>">
			    	<div class="form-group">
			    		<?php 
			    				echo $this->Form->label('commission_extra', __('Komisi Extra'));
			    		?>
			            <div class="input-group">
					    	<?php 
					    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
					    				'class' => 'input-group-addon'
				    				));
									echo $this->Form->input('commission_extra',array(
										'label'=> false, 
										'class'=>'form-control commission_extra input_price',
										'required' => false,
										'empty' => __('Komisi Extra'),
										'type' => 'text',
										'readonly' => true,
									));
									echo $this->Form->hidden('commission_extra_ori',array(
										'class'=>'commission_extra_ori',
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