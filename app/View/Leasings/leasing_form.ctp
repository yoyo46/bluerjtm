<?php
		$value = !empty($value)?$value:false;
		$vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');
        $paid_date = $this->Common->filterEmptyField($value, 'Leasing', 'paid_date');
        $date_first_installment = $this->Common->filterEmptyField($value, 'Leasing', 'date_first_installment');
        $date_last_installment = $this->Common->filterEmptyField($value, 'Leasing', 'date_last_installment');

		$this->Html->addCrumb(__('Leasing'), array(
			'controller' => 'leasings',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Leasing', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
    	<div class="row">
    		<div class="col-sm-6">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('no_contract',array(
								'label'=> __('No. Kontrak *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Kontrak')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		        			if( !empty($vendor) ) {
		        				echo $this->Common->_callStaticForm(__('Vendor Leasing'), $vendor);
		        			} else {
								echo $this->Form->input('vendor_id',array(
									'label'=> __('Vendor Leasing *'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Vendor'),
									'options' => $leasing_companies
								));
							}
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		        			if( !empty($paid_date) ) {
		        				echo $this->Common->_callStaticForm(__('Tgl Leasing'), $paid_date);
		        			} else {
								echo $this->Form->input('paid_date',array(
									'type' => 'text',
									'label'=> __('Tgl Leasing *'), 
									'class'=>'form-control custom-date',
									'required' => false,
									'placeholder' => __('Tgl Leasing'),
									'value' => (!empty($this->request->data['Leasing']['paid_date'])) ? $this->request->data['Leasing']['paid_date'] : date('d/m/Y')
								));
							}
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		        			if( !empty($date_first_installment) ) {
		        				echo $this->Common->_callStaticForm(__('Tgl Angsuran Pertama'), $date_first_installment);
		        			} else {
								echo $this->Form->input('date_first_installment',array(
									'type' => 'text',
									'label'=> __('Tgl Angsuran Pertama *'), 
									'class'=>'form-control custom-date leasing-date-installment',
									'required' => false,
									'placeholder' => __('Tgl Angsuran Pertama'),
								));
							}
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		        			if( !empty($date_last_installment) ) {
		        				echo $this->Common->_callStaticForm(__('Tgl Angsuran Terakhir'), $date_last_installment);
		        			} else {
								echo $this->Form->label('tgl_lahir', __('Tgl Angsuran Terakhir *'));
					?>
					<div class="row">
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->day('tgl_last_installment', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date leasing-last-day-installment',
										'required' => false,
										'empty' => __('Hari'),
										'id' => 'day',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->month('tgl_last_installment', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date leasing-last-month-installment',
										'required' => false,
										'empty' => __('Bulan'),
										'id' => 'month',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->year('tgl_last_installment', 1949, date('Y') + 10, array(
										'label'=> false, 
										'class'=>'form-control selectbox-date leasing-last-year-installment',
										'empty' => __('Tahun'),
										'id' => 'year',
										'required' => false,
									));
							?>
						</div>
					</div>
		        	<?php 
								echo $this->Form->error('date_last_installment');
							}
		        	?>
		        </div>
    		</div>
    		<div class="col-sm-6">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('total_leasing',array(
								'type' => 'text',
								'label'=> __('Total Leasing *'), 
								'class'=>'form-control input_price total-leasing',
								'required' => false,
								'placeholder' => __('Total Leasing'),
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		     //    			$months = array();
							// for ($i = 1; $i <= 12; $i++) {
							//     $timestamp = mktime(0, 0, 0, $i, 1);
							//     $months[date('n', $timestamp)] = date('F', $timestamp);
							// }
							echo $this->Form->input('leasing_month',array(
								'type' => 'text',
								'label'=> __('Bulan *'), 
								'class'=>'form-control month-leasing input_number',
								'required' => false,
								'empty' => __('Pilih Bulan'),
								// 'options' => $months,
								'value' => (!empty($this->request->data['Leasing']['leasing_month'])) ? $this->request->data['Leasing']['leasing_month'] : ''
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('down_payment',array(
								'type' => 'text',
								'label'=> __('DP *'), 
								'class'=>'form-control input_price leasing-dp',
								'required' => false,
								'placeholder' => __('DP'),
							));
					?>
		        </div>
		        <div class="form-group">
			        <?php 
			        		echo $this->Form->label('annual_interest', __('Bunga Pertahun *'));
			        ?>
                    <div class="row">
                    	<div class="col-sm-4">
                    		<div class="input-group">
					        	<?php 
										echo $this->Form->input('annual_interest',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control input_number annual-interest',
											'required' => false,
											'placeholder' => __('Bunga Pertahun'),
										));
		                                echo $this->Html->tag('span', __('%'), array(
		                                    'class' => 'input-group-addon'
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
<?php
	echo $this->element('blocks/leasings/leasing_detail_info'); 
?>
<div class="box-footer text-center action">
	<?php
			if( empty($value) ) {
				$status = $this->Common->filterEmptyField($data_local, 'Leasing', 'status');
				
				if( !empty($status) || empty($data_local) ) {
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    	}
		    }
	    	
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
<div id="form-truck-id" class="hide">
	<?php
		echo $this->Form->input('LeasingDetail.truck_id.',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'empty' => __('Pilih Truk'),
			'options' => $trucks
		));
	?>
</div>