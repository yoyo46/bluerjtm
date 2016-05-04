<?php
		$this->Html->addCrumb(__('Revenue'), array(
			'controller' => 'revenues',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

        $data = $this->request->data;
        $ttujs = !empty($ttujs)?$ttujs:false;
        $revenueDetail = $this->Common->filterEmptyField($data, 'RevenueDetail');
        $ttuj_id = $this->Common->filterEmptyField($data_local, 'Revenue', 'ttuj_id');

		$dataColumns = array(
            'tujuan' => array(
                'name' => __('Tujuan'),
                'class' => 'text-top',
                'style' => 'width:15%;',
            ),
            'do' => array(
                'name' => __('No. DO'),
                'class' => 'text-top',
                'style' => 'width:13%;',
            ),
            'sj' => array(
                'name' => __('No. SJ'),
                'class' => 'text-top',
                'style' => 'width:13%;',
            ),
            'group' => array(
                'name' => __('Group Motor'),
                'class' => 'text-top',
                'style' => 'width:15%;',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-top text-center',
                'style' => 'width:7%;',
            ),
            'price' => array(
                'name' => __('Tarif'),
                'class' => 'text-top text-center',
                'style' => 'width:12%;',
            ),
            'charge' => array(
                'name' => __('Charge'),
                'class' => 'text-top text-center',
                'style' => 'width:5%;',
            ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-top text-center',
                'style' => 'width:12%;',
            ),
            'action' => array(
                'name' => '&nbsp;',
                'style' => 'width:7%;',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

		echo $this->Form->create('Revenue', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
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
									// 'readonly' => (!empty($id) && !empty($this->request->data['Revenue']['no_doc'])) ? true : false
								));
						?>
			        </div>
			        <div class="form-group">
			        	<?php 
	        					$attrBrowse = array(
                                    'class' => 'ajaxModal visible-xs browse-docs',
                                    'escape' => false,
                                    'title' => __('Data TTUJ'),
                                    'data-action' => 'browse-form',
                                    'data-change' => 'getTtujInfoRevenue',
	                            );
	        					$urlBrowse = array(
	                                'controller'=> 'ajax', 
                                    'action' => 'getTtujs',
                                    'revenues',
                                    $ttuj_id,
	                            );
                            	echo $this->Form->label('ttuj_id', __('No. TTUJ * ' ).$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
	                    ?>
	                    <div class="row">
	                        <div class="col-sm-10">
					        	<?php 
										echo $this->Form->input('ttuj_id',array(
											'label'=> false, 
											'class'=>'form-control chosen-select',
											'required' => false,
											'options' => $ttujs,
											'empty' => __('Pilih TTUJ'),
											'id' => 'getTtujInfoRevenue',
											'data-action' => 'manual',
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
					<div class="form-group" id="customer-form">
						<?php 
								echo $this->Form->input('Revenue.customer_id',array(
									'label'=> __('Customer'), 
									'class'=>'form-control chosen-select',
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
						<?php
								echo $this->element('blocks/revenues/revenue_info_manual');
						?>
					</div>
			    </div>
			</div>
		</div>
	</div>
	<div id="muatan-revenue-detail">
		<?php 
				if(!empty($revenueDetail)){
					echo $this->element('blocks/revenues/revenues_info_detail', array(
						'revenueDetail' => $revenueDetail,
						'action_type' => 'manual',
					)); 
				} else {
		?>
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Muatan Truk'); ?></h3>
		    </div>
		    <div class="box-body table-responsive">
		        <table class="table table-hover">
		            <?php
		                    if( !empty($fieldColumn) ) {
		                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
		                    }
		            ?>
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
		                    ?>
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
        echo $this->Form->hidden('action_type', array(
            'class' => 'revenue-data-type',
            'value' => 'manual',
        ));
		echo $this->Form->end();
?>