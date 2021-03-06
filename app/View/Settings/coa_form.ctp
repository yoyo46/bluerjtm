<?php
		$this->Html->addCrumb(__('COA'), array(
			'controller' => 'settings',
			'action' => 'coas'
		));
		$this->Html->addCrumb($sub_module_title);
		$coaCode = '';
		if(!empty($coa['Coa']['code'])){
			$coaCode = $coa['Coa']['code'];
		}

		if( !empty($coa['Coa']['with_parent_code']) ) {
			$coaCode = $coa['Coa']['with_parent_code'];
		}
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Coa', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php 
    			if( !empty($coa) ) {
    				echo '<dl>';
    				echo $this->Html->tag('dt', __('Parent COA'));
					echo $this->Html->tag('dd', sprintf('%s %s', $coaCode, $coa['Coa']['name']));
					echo '</dl>';
				}
		?>
		<div class="form-group">
			<?php 
					echo $this->Form->label('code', __('Kode COA'));
			?>
			<div class="row">
				<div class="col-sm-4">
					<div class="input-group">
						<?php 
								if( !empty($coa['Coa']['with_parent_code']) ) {
									echo $this->Html->tag('div', $coa['Coa']['with_parent_code'], array(
										'class' => 'input-group-addon',
									));
								}else if(!empty($coa['Coa']['code'])){
									echo $this->Html->tag('div', $coa['Coa']['code'], array(
										'class' => 'input-group-addon',
									));
								}

								echo $this->Form->input('code',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'placeholder' => __('Kode COA')
								));
						?>
		            </div>
				</div>
			</div>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama COA *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama COA')
				)), array(
					'class' => 'form-group'
				));

				
				echo $this->Html->tag('div', $this->Form->input('name_en',array(
					'label'=> __('Nama COA English'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama COA')
				)), array(
					'class' => 'form-group'
				));

				if( empty($parent_id) ){
					echo $this->Html->tag('div', $this->Form->input('type', array(
						'label'=> __('Tipe COA *'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Tipe COA'),
						'options' => array(
							'debit' => __('Debit'),
							'credit' => __('Credit'),
						)
					)), array(
						'class' => 'form-group'
					));
				}

				if(!empty($coa['Coa']['level']) && $coa['Coa']['level'] == 3){
					// if( empty($saldo) ) {
						echo $this->Html->tag('div', $this->Form->input('balance', array(
							'label'=> __('Balance'), 
							'class'=>'form-control input_price_min',
							'required' => false,
							'placeholder' => __('Balance'),
							'type' => 'text'
						)), array(
							'class' => 'form-group'
						));
					// } else {
					// 	$customSaldo = $this->Common->getFormatPrice($saldo);
					// 	$lbl = $this->Html->tag('label', __('Balance'));
						
					// 	echo $this->Html->tag('div', $lbl.$this->Html->tag('div', $customSaldo), array(
					// 		'class' => 'form-group'
					// 	));
					// }

					echo $this->Html->tag('div', $this->Form->input('periode_reset', array(
						'label'=> __('Reset Balance'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Tidak Reset Balance'),
						'options' => array(
							'monthly' => __('Per Bulan'),
							'yearly' => __('Per Tahun'),
						),
					)), array(
						'class' => 'form-group'
					));

					echo $this->Html->tag('div', $this->Form->input('transaction_category', array(
						'label'=> __('Jenis Transaksi'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Jenis Transaksi'),
						'options' => array(
							'revenue' => __('Revenue'),
							'expense' => __('Biaya Jalan'),
							'maintenance' => __('Maintenance'),
							'other' => __('Biaya Lain-lain'),
						),
					)), array(
						'class' => 'form-group'
					));
		?>
			    	<div class="form-group">
			    		<div class="checkbox">
			                <label>
			                	<?php 
			                			echo $this->Form->checkbox('is_cash_bank').' Termasuk Kas/Bank?';
			                	?>
			                </label>
			            </div>
			    	</div>
			    	<div class="form-group">
			    		<div class="checkbox">
			                <label>
			                	<?php 
			                			echo $this->Form->checkbox('is_profit_loss').' Laba Rugi Berjalan?';
			                	?>
			                </label>
			            </div>
			    	</div>
			    	<div class="form-group">
			    		<div class="checkbox">
			                <label>
			                	<?php 
			                			echo $this->Form->checkbox('is_neraca').' Tampil di Neraca?';
			                	?>
			                </label>
			            </div>
			    	</div>
			    	<div class="form-group">
			    		<div class="checkbox">
			                <label>
			                	<?php 
			                			echo $this->Form->checkbox('is_laba_rugi').' Tampil di Laba Rugi?';
			                	?>
			                </label>
			            </div>
			    	</div>
		<?php
				}
		?>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'coas', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>