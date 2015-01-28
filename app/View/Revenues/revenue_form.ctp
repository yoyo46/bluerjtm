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
	<div id="step1">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Revenue'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_doc',array(
										'label'=> __('No. Dokumen *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Dokumen'),
										'readonly' => (!empty($id)) ? true : false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ttuj_id',array(
										'label'=> __('No. TTUJ *'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $ttujs,
										'empty' => __('Pilih TTUJ'),
										'id' => 'getTtujInfoRevenue'
									));
							?>
				        </div>
				        <div id="ttuj-info">
				        	<?php
				        		echo $this->element('blocks/revenues/revenue_info');
				        	?>
				        </div>
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
			?>
		</div>
</div>
<?php
		echo $this->Form->end();
?>