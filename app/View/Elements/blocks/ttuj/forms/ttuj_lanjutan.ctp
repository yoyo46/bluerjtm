<?php 
		$data = !empty($this->request->data)?$this->request->data:false;
		$this->request->data['Ttuj']['tgl_berangkat'] = $this->Common->filterEmptyField($data, 'Ttuj', 'tgl_berangkat', date('d/m/Y'));
		$this->request->data['Ttuj']['tgl_tiba'] = $this->Common->filterEmptyField($data, 'Ttuj', 'tgl_tiba', date('d/m/Y'));
		$this->request->data['Ttuj']['tgl_bongkaran'] = $this->Common->filterEmptyField($data, 'Ttuj', 'tgl_bongkaran', date('d/m/Y'));
		$this->request->data['Ttuj']['tgl_balik'] = $this->Common->filterEmptyField($data, 'Ttuj', 'tgl_balik', date('d/m/Y'));
		$this->request->data['Ttuj']['tgl_pool'] = $this->Common->filterEmptyField($data, 'Ttuj', 'tgl_pool', date('d/m/Y'));
?>
<div class="col-sm-6">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Waktu Perjalanan');?></h3>
    </div>
	<div class="box box-primary">
	    <div class="box-body">
			<div class="form-group">
				<?php 
						echo $this->Form->label('tgljam_berangkat', __('Tgl & Jam Berangkat *'));
				?>
				<div class="row">
					<div class="col-sm-8">
						<?php 
								echo $this->Form->input('tgl_berangkat',array(
									'label'=> false, 
									'class'=>'form-control custom-date',
									'required' => false,
									'type' => 'text',
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
										'type' => 'text',
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
							'label'=> __('Keterangan Berangkat'), 
							'class'=>'form-control small',
							'required' => false,
						));
				?>
			</div>
			<div class="form-group">
				<?php 
						echo $this->Form->label('tgljam_tiba', __('Tgl & Jam Tiba'));
				?>
				<div class="row">
					<div class="col-sm-8">
						<?php 
								echo $this->Form->input('tgl_tiba',array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control custom-date',
									'required' => false,
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
										'class'=>'form-control pull-right timepicker',
										'required' => false,
										'type' => 'text',
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
					echo $this->Html->tag('div', $this->Form->input('note_tiba', array(
						'label'=> __('Keterangan Tiba'), 
						'class'=>'form-control small',
						'required' => false,
					)), array(
						'class'=>'form-group',
					));
			?>
			<div class="form-group">
				<?php 
						echo $this->Form->label('tgljam_bongkaran', __('Tgl & Jam Bongkaran'));
				?>
				<div class="row">
					<div class="col-sm-8">
						<?php 
								echo $this->Form->input('tgl_bongkaran',array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control custom-date',
									'required' => false,
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
										'type' => 'text',
										'label'=> false, 
										'class'=>'form-control pull-right timepicker',
										'required' => false,
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
					echo $this->Html->tag('div', $this->Form->input('note_bongkaran', array(
						'label'=> __('Keterangan Bongkaran'), 
						'class'=>'form-control small',
						'required' => false,
					)), array(
						'class'=>'form-group',
					));
			?>
		</div>
	</div>
</div>
<div class="col-sm-6">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Waktu Pulang');?></h3>
    </div>
	<div class="box box-primary">
	    <div class="box-body">
			<div class="form-group">
				<?php 
						echo $this->Form->label('tgljam_balik', __('Tgl & Jam Balik'));
				?>
				<div class="row">
					<div class="col-sm-8">
						<?php 
								echo $this->Form->input('tgl_balik',array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control custom-date',
									'required' => false,
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
					echo $this->Html->tag('div', $this->Form->input('note_balik', array(
						'label'=> __('Keterangan Balik'), 
						'class'=>'form-control small',
						'required' => false,
					)), array(
						'class'=>'form-group',
					));
			?>
			<div class="form-group">
				<?php 
						echo $this->Form->label('tgljam_pool', __('Tgl & Jam Sampai Pool'));
				?>
				<div class="row">
					<div class="col-sm-8">
						<?php 
								echo $this->Form->input('tgl_pool',array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control custom-date',
									'required' => false,
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
										'type' => 'text',
										'label'=> false, 
										'class'=>'form-control pull-right timepicker',
										'required' => false,
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
						'label'=> __('Keterangan Sampai Pool'), 
						'class'=>'form-control small',
						'required' => false,
					)), array(
						'class'=>'form-group',
					));
			?>
		</div>
	</div>
</div>