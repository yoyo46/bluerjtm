<?php 
		$data = !empty($this->request->data)?$this->request->data:false;
		$isAjax = !empty($isAjax)?$isAjax:false;
		$tgl_jam_berangkat = !empty($tgl_jam_berangkat)?$tgl_jam_berangkat:date('Y-m-d H:i:s');

		if( !empty($isAjax) || empty($data['Ttuj']['tgl_berangkat']) ) {
			// $arrive_lead_time = $this->Common->filterEmptyField($uangJalan, 'UangJalan', 'arrive_lead_time');
			// $back_lead_time = $this->Common->filterEmptyField($uangJalan, 'UangJalan', 'back_lead_time');

			// $arriveTime = sprintf('+%s hour', $arrive_lead_time);
			// $backTime = sprintf('+%s hour', $back_lead_time);

			$tgl_jam_tiba = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($tgl_jam_berangkat)));
			$tgl_jam_bongkaran = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($tgl_jam_tiba)));
			$tgl_jam_balik = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($tgl_jam_bongkaran)));
			$tgl_jam_pool = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($tgl_jam_balik)));

			$this->request->data['Ttuj']['tgl_berangkat'] = $this->Common->formatDate($tgl_jam_berangkat, 'd/m/Y');
			$this->request->data['Ttuj']['tgl_tiba'] = $this->Common->formatDate($tgl_jam_tiba, 'd/m/Y');
			$this->request->data['Ttuj']['tgl_bongkaran'] = $this->Common->formatDate($tgl_jam_bongkaran, 'd/m/Y');
			$this->request->data['Ttuj']['tgl_balik'] = $this->Common->formatDate($tgl_jam_balik, 'd/m/Y');
			$this->request->data['Ttuj']['tgl_pool'] = $this->Common->formatDate($tgl_jam_pool, 'd/m/Y');

			$this->request->data['Ttuj']['jam_berangkat'] = $this->Common->formatDate($tgl_jam_berangkat, 'H:i');
			$this->request->data['Ttuj']['jam_tiba'] = $this->Common->formatDate($tgl_jam_tiba, 'H:i');
			$this->request->data['Ttuj']['jam_bongkaran'] = $this->Common->formatDate($tgl_jam_bongkaran, 'H:i');
			$this->request->data['Ttuj']['jam_balik'] = $this->Common->formatDate($tgl_jam_balik, 'H:i');
			$this->request->data['Ttuj']['jam_pool'] = $this->Common->formatDate($tgl_jam_pool, 'H:i');

			if( !empty($isAjax) ) {
				echo $this->Form->create('Ttuj', array(
					'id' => 'ttuj-form',
				));
			}
		}
?>
<div id="ttuj-lanjutan-lead-time">
	<div class="col-sm-6">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Waktu Perjalanan');?></h3>
	    </div>
		<div class="box box-primary">
		    <div class="box-body">
				<div class="form-group">
					<?php 
							echo $this->Form->label('Ttuj.tgljam_berangkat', __('Tgl & Jam Berangkat *'));
					?>
					<div class="row">
						<div class="col-sm-8">
							<?php 
									echo $this->Form->input('Ttuj.tgl_berangkat',array(
										'label'=> false, 
										'class'=>'form-control custom-date ajax-change',
										'required' => false,
										'type' => 'text',
										'href' => $this->Html->url(array(
											'controller' => 'ajax',
											'action' => 'change_lead_time',
											'admin' => false,
										)),
										'data-form' => '#ttuj-form',
										'data-wrapper-write' => '#ttuj-lanjutan-lead-time',
									));
							?>
						</div>
						<div class="col-sm-4">
							<div class="input-group">
				                <div class="input-group-addon">
				                    <i class="fa fa-clock-o"></i>
				                </div>
				    			<?php 
										echo $this->Form->input('Ttuj.jam_berangkat',array(
											'label'=> false, 
											'class'=>'form-control pull-right timepicker ajax-change',
											'required' => false,
											'type' => 'text',
											'href' => $this->Html->url(array(
												'controller' => 'ajax',
												'action' => 'change_lead_time',
												'admin' => false,
											)),
											'data-form' => '#ttuj-form',
											'data-wrapper-write' => '#ttuj-lanjutan-lead-time',
										));
								?>
				            </div>
						</div>
					</div>
					<?php 
							echo $this->Form->error('Ttuj.tgljam_berangkat', array(
								'notempty' => __('Tgl & Jam Berangkat harap dipilih'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
					?>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->input('Ttuj.note',array(
								'label'=> __('Keterangan Berangkat'), 
								'class'=>'form-control small',
								'required' => false,
							));
					?>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->label('Ttuj.tgljam_tiba', __('Tgl & Jam Tiba'));
					?>
					<div class="row">
						<div class="col-sm-8">
							<?php 
									echo $this->Form->input('Ttuj.tgl_tiba',array(
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
										echo $this->Form->input('Ttuj.jam_tiba',array(
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
							echo $this->Form->error('Ttuj.tgljam_tiba', array(
								'notempty' => __('Tgl & Jam Tiba harap dipilih'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
					?>
				</div>
				<?php 
						echo $this->Html->tag('div', $this->Form->input('Ttuj.note_tiba', array(
							'label'=> __('Keterangan Tiba'), 
							'class'=>'form-control small',
							'required' => false,
						)), array(
							'class'=>'form-group',
						));
				?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('Ttuj.tgljam_bongkaran', __('Tgl & Jam Bongkaran'));
					?>
					<div class="row">
						<div class="col-sm-8">
							<?php 
									echo $this->Form->input('Ttuj.tgl_bongkaran',array(
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
										echo $this->Form->input('Ttuj.jam_bongkaran',array(
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
							echo $this->Form->error('Ttuj.tgljam_bongkaran', array(
								'notempty' => __('Tgl & Jam Bongkaran harap dipilih'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
					?>
				</div>
				<?php
						echo $this->Html->tag('div', $this->Form->input('Ttuj.note_bongkaran', array(
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
							echo $this->Form->label('Ttuj.tgljam_balik', __('Tgl & Jam Balik'));
					?>
					<div class="row">
						<div class="col-sm-8">
							<?php 
									echo $this->Form->input('Ttuj.tgl_balik',array(
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
										echo $this->Form->input('Ttuj.jam_balik',array(
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
							echo $this->Form->error('Ttuj.tgljam_balik', array(
								'notempty' => __('Tgl & Jam Balik harap dipilih'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
					?>
				</div>
				<?php
						echo $this->Html->tag('div', $this->Form->input('Ttuj.note_balik', array(
							'label'=> __('Keterangan Balik'), 
							'class'=>'form-control small',
							'required' => false,
						)), array(
							'class'=>'form-group',
						));
				?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('Ttuj.tgljam_pool', __('Tgl & Jam Sampai Pool'));
					?>
					<div class="row">
						<div class="col-sm-8">
							<?php 
									echo $this->Form->input('Ttuj.tgl_pool',array(
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
										echo $this->Form->input('Ttuj.jam_pool',array(
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
							echo $this->Form->error('Ttuj.tgljam_pool', array(
								'notempty' => __('Tgl & Jam Sampai Pool harap dipilih'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
					?>
				</div>
				<?php
						echo $this->Html->tag('div', $this->Form->input('Ttuj.note_pool', array(
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
</div>
<?php 
		if( !empty($isAjax) ) {
			echo $this->Form->end();
		}
?>