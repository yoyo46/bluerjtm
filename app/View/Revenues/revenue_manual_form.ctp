<?php
		$this->Html->addCrumb(__('Revenue'), array(
			'controller' => 'revenues',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Revenue', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));

		echo $this->Form->hidden('transaction_status', array(
			'id' => 'transaction_status'
		));
?>
<div class="ttuj-form">
	<div class="row">
		<div class="col-sm-6">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Informasi Revenue'); ?></h3>
			    </div>
			    <div class="box-body">
			    	<?php 
			    			if( !empty($id) ) {
			    	?>
			        <div class="form-group">
			        	<?php 
								echo $this->Form->input('id',array(
									'label'=> __('No. Ref'), 
									'class'=>'form-control',
									'required' => false,
									'value' => str_pad($id, 5, '0', STR_PAD_LEFT),
									'disabled' => true,
									'type' => 'text',
								));
						?>
			        </div>
			        <?php 
			        		}
			        ?>
			        <div class="form-group">
			        	<?php 
								echo $this->Form->input('no_doc',array(
									'label'=> __('No. Dokumen'), 
									'class'=>'form-control',
									'required' => false,
									'placeholder' => __('No. Dokumen'),
									'readonly' => (!empty($id) && !empty($this->request->data['Revenue']['no_doc'])) ? true : false
								));
						?>
			        </div>
			        <div class="form-group">
						<?php 
								echo $this->Form->input('Revenue.date_revenue',array(
									'type' => 'text',
									'label'=> __('Tanggal Revenue *'), 
									'class'=>'form-control custom-date',
									'required' => false,
									'placeholder' => __('Tanggal Revenue'),
									'id' => 'date_revenue',
									'value' => (!empty($this->request->data['Revenue']['date_revenue'])) ? $this->request->data['Revenue']['date_revenue'] : date('d/m/Y')
								));
						?>
					</div>
					<div class="form-group">
						<?php 
								echo $this->Form->input('Revenue.customer_id',array(
									'label'=> __('Customer'), 
									'class'=>'form-control',
									'required' => false,
									'options' => $customers,
									'empty' => __('Pilih Customer'),
									'id' => 'customer-revenue-manual',
								));
						?>
					</div>
			    	<div class="form-group action">
			    		<?php 
				                echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Muatan'), 'javascript:', array(
				                	'escape' => false,
				                	'class' => 'btn btn-success add-custom-field btn-xs',
				                	'action_type' => 'revenue-detail'
				                ));
				        ?>
			    	</div>
			    </div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Informasi Truk'); ?></h3>
			    </div>
			    <div class="box-body">
			        <div id="ttuj-info">
						
				        <div class="form-group">
		                    <?php 
		        					$attrBrowse = array(
	                                    'class' => 'ajaxModal visible-xs',
                                        'escape' => false,
                                        'title' => __('Data Truk'),
                                        'data-action' => 'browse-form',
                                        'data-change' => 'truckID',
                                        'id' => 'truckBrowse',
	                                );
		        					$urlBrowse = array(
	                                    'controller'=> 'ajax', 
                                        'action' => 'getTrucks',
                                        'revenue',
	                                );
		                            echo $this->Form->label('truck_id', __('No. Pol * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
		                    ?>
		                    <div class="row">
		                        <div class="col-sm-10">
						        	<?php 
											echo $this->Form->input('truck_id',array(
												'label'=> false, 
												'class'=>'form-control truck-revenue-id',
												'required' => false,
												'empty' => __('Pilih No. Pol --'),
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
										'label'=> __('Kapasitas'), 
										'class'=>'form-control',
										'required' => false,
										'readonly' => true,
										'id' => 'revenue-truck-capacity',
									));
							?>
						</div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('from_city_id', __('Tujuan Dari'));
							?>
							<div class="row">
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('from_city_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Dari Kota --'),
												'options' => !empty($toCities)?$toCities:false,
												'id' => 'from-city-revenue-id',
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('to_city_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Kota Tujuan --'),
												'options' => !empty($toCities)?$toCities:false,
												'id' => 'to-city-revenue-id',
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
	<div id="muatan-revenue-detail">
		<?php 
				if(!empty($data_revenue_detail)){
					echo $this->element('blocks/revenues/revenues_info_detail', array(
						'data' => $data_revenue_detail,
						'data_type' => 'revenue-manual',
					)); 
				} else {
		?>
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Muatan Truk'); ?></h3>
		    </div>
		    <div class="box-body table-responsive">
		        <table class="table table-hover">
		            <thead>
		                <tr>
		                    <th width="15%" class="text-top"><?php echo __('Tujuan');?></th>
		                    <th width="13%" class="text-top"><?php echo __('No. DO');?></th>
		                    <th width="13%" class="text-top"><?php echo __('No. SJ');?></th>
		                    <th width="15%" class="text-top"><?php echo __('Group Motor');?></th>
		                    <th width="7%" class="text-top"><?php echo __('Jumlah Unit');?></th>
		                    <th width="5%" class="text-top text-center"><?php echo __('Charge');?></th>
		                    <th width="15%" class="text-top text-center"><?php printf(__('Harga Unit'), Configure::read('__Site.config_currency_code'));?></th>
		                    <th width="15%" class="text-top text-center"><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
		                </tr>
		            </thead>
		            <tbody class="tipe-motor-table">
		            </tbody>
		            <tbody>
		                <tr id="field-grand-total-revenue">
		                    <?php 
		                            echo $this->Html->tag('td', __('Total Muatan'), array(
		                                'align' => 'right',
		                                'colspan' => 4,
		                            ));
		                            echo $this->Html->tag('td', '', array(
		                                'align' => 'center',
		                                'id' => 'qty-revenue',
		                            ));
		                            echo $this->Html->tag('td', __('Total'), array(
		                                'align' => 'right',
		                                'colspan' => 2,
		                            ));

		                            echo $this->Html->tag('td', '', array(
		                                'align' => 'right',
		                                'id' => 'grand-total-revenue',
		                            ));
		                            echo $this->Html->tag('td', '&nbsp;');

		                            echo $this->Form->hidden('total_temp', array(
		                                'id' => 'total_retail_revenue',
		                            ));
		                            echo $this->Form->hidden('Revenue.tarif_per_truck', array(
		                                'class' => 'tarif_per_truck',
		                            ));
		                    ?>
		                </tr>
		                <tr id="field-additional-total-revenue">
		                    <td align="right" colspan="7"><?php echo __('Additional Charge')?></td>
		                    <td align="right" id="additional-total-revenue"></td>
		                </tr>
		                <tr class="additional-input-revenue" id="ppn-grand-total-revenue">
		                    <td align="right" colspan="7" class="relative">
		                        <?php 
			                            echo $this->Form->input('Revenue.ppn', array(
			                                'type' => 'text',
			                                'label' => __('PPN'),
			                                'class' => 'input_number revenue-ppn',
			                                'required' => false,
			                                'div' => false
			                            )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
		                        ?>
		                    </td>
		                    <td align="right" id="ppn-total-revenue"></td>
		                    <td>&nbsp;</td>
		                </tr>
		                <tr class="additional-input-revenue" id="pph-grand-total-revenue">
		                    <td align="right" colspan="7" class="relative">
		                        <?php 
		                                echo $this->Form->input('Revenue.pph', array(
		                                    'type' => 'text',
		                                    'label' => __('PPh'),
		                                    'class' => 'input_number revenue-pph',
		                                    'required' => false,
		                                    'div' => false
		                                )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
		                        ?>
		                    </td>
		                    <td align="right" id="pph-total-revenue"></td>
		                    <td>&nbsp;</td>
		                </tr>
		                <tr id="all-grand-total-revenue">
		                    <td align="right" colspan="7"><?php echo __('Total');?></td>
		                    <td align="right" id="all-total-revenue"></td>
		                    <td>&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
		    </div>
		</div>
		<?php 
			        echo $this->Form->hidden('Revenue.revenue_tarif_type', array(
			            'class' => 'revenue_tarif_type',
			        ));
			        echo $this->Form->hidden('Revenue.additional_charge', array(
			            'class' => 'additional_charge',
			        ));
		    	}
		?>
	</div>
</div>
<div class="hide">
	<?php 
			echo $this->Form->input('group_motor_id',array(
				'label'=> false, 
				'class'=>'form-control',
				'required' => false,
				'empty' => __('Pilih Group Motor'),
				'options' => !empty($groupMotors)?$groupMotors:false,
				'id' => 'group-motor-revenue'
			));
			echo $this->Form->input('city_id',array(
				'label'=> false, 
				'class'=>'form-control',
				'required' => false,
				'empty' => __('Pilih Kota Tujuan'),
				'options' => !empty($toCities)?$toCities:false,
				'id' => 'city-revenue'
			));
	?>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));

			$this->Common->_getButtonPostingUnposting( $data_local );
	?>
</div>
<?php
		echo $this->Form->end();
?>