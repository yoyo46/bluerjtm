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
	        					$attrBrowse = array(
                                    'class' => 'ajaxModal visible-xs',
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
											'class'=>'form-control',
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
									'label'=> __('Tanggal Revenue *'), 
									'class'=>'form-control custom-date',
									'required' => false,
									'placeholder' => __('Tanggal Revenue'),
									'id' => 'date_revenue',
									'value' => (!empty($this->request->data['Revenue']['date_revenue'])) ? $this->request->data['Revenue']['date_revenue'] : date('Y-m-d')
								));
						?>
					</div>
					<div class="form-group" id="customer-form">
						<?php 
								echo $this->Form->input('Revenue.customer_id',array(
									'label'=> __('Customer'), 
									'class'=>'form-control change-customer-revenue',
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
			        <?php 
			        /*
			        <div class="checkbox">
		                <label>
		                    <?php 
								echo $this->Form->checkbox('Revenue.getting_sj',array(
									'label'=> false, 
									'required' => false,
									'id' => 'sj-handle',
								)).__('SJ sudah diterima??');
							?>
		                </label>
		            </div>
					<div class="form-group sj-date <?php echo (!empty($this->request->data['Revenue']['getting_sj'])) ? '' : 'hide'; ?>">
						<?php 
								echo $this->Form->input('Revenue.date_sj',array(
									'label'=> __('Tgl SJ diterima'), 
									'class'=>'form-control custom-date',
									'type' => 'text'
								));
						?>
					</div>
		            */
		            ?>
			    </div>
			</div>
		</div>
		<div class="col-sm-12" id="detail-tipe-motor">
			<?php 
					if(!empty($data_revenue_detail)){
						echo $this->element('blocks/revenues/revenues_info_detail', array('data' => $data_revenue_detail)); 
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

			$posting = false;

			if( !empty($data_local['Revenue']['transaction_status']) && $data_local['Revenue']['transaction_status'] == 'posting' ) {
				$posting = true;
			}

			// if( !$posting ) {
	    		echo $this->Form->button(__('Posting'), array(
	    			'type' => 'submit',
					'class'=> 'btn btn-success submit-form btn-lg',
					'action_type' => 'posting'
				));
				
				echo $this->Form->button(__('Unposting'), array(
	    			'type' => 'submit',
					'class'=> 'btn btn-primary submit-form',
					'action_type' => 'unposting'
				));
			// }
	?>
</div>
<?php
		echo $this->Form->end();
?>