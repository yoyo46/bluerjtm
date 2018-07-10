<?php
		$this->Html->addCrumb(__('Revenue'), array(
			'controller' => 'revenues',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);
		
        $data = $this->request->data;
		$allow_closing = !empty($allow_closing)?$allow_closing:false;
        $revenueDetail = $this->Common->filterEmptyField($data, 'RevenueDetail');

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
                                    !empty($data_local['Revenue']['ttuj_id'])?$data_local['Revenue']['ttuj_id']:false,
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
											'id' => 'getTtujInfoRevenue'
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
									'label'=> __('Tgl Revenue *'), 
									'class'=>'form-control custom-date',
									'required' => false,
									'placeholder' => __('Tgl Revenue'),
									'id' => 'date_revenue',
									'value' => (!empty($this->request->data['Revenue']['date_revenue'])) ? $this->request->data['Revenue']['date_revenue'] : date('d/m/Y')
								));
						?>
					</div>
					<?php
							echo $this->Common->buildInputForm('cogs_id', __('Cost Center'), array(
								'label'=> __('Cost Center'), 
								'class'=>'form-control chosen-select',
								'empty' => __('Pilih Cost Center '),
								'options' => $cogs,
							));
					?>
					<div class="form-group" id="customer-form">
						<?php 
								echo $this->Form->input('Revenue.customer_id',array(
									'label'=> __('Customer'), 
									'class'=>'form-control change-customer-revenue chosen-select',
									'required' => false,
									'options' => $customers,
									'empty' => __('Pilih Customer')
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
			        			echo $this->element('blocks/revenues/revenue_info');
			        	?>
			        </div>
			    </div>
			</div>
		</div>
		<div class="col-sm-12" id="detail-tipe-motor">
			<?php 
					if(!empty($revenueDetail)){
						echo $this->element('blocks/revenues/revenues_info_detail', array('revenueDetail' => $revenueDetail)); 
					}
			?>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));

    		if( !empty($allow_closing) ) {
				$this->Common->_getButtonPostingUnposting( $data_local );
			}
	?>
</div>
<?php
		echo $this->Form->end();
?>