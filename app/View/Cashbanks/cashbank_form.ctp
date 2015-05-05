<?php
		$this->Html->addCrumb(__('Kas Bank'), array(
			'controller' => 'cashbanks',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('CashBank', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
		<div class="form-group">
			<?php
					echo $this->Form->input('nodoc',array(
						'label'=> __('No. Dokumen'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('No. Dokumen')
					));
			?>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('coa_id',array(
					'label'=> __('Kas Bank *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Kas Bank '),
					'options' => $coas
				)), array(
					'class' => 'form-group'
				));

				echo $this->Html->tag('div', $this->Form->input('receiving_cash_type',array(
					'label'=> __('Jenis Kas Bank *'), 
					'class'=>'form-control cash-bank-handle',
					'required' => false,
					'options' => array(
						'out' => __('Cash - OUT'),
						'in' => __('Cash - IN'),
						'ppn_out' => __('PPN - OUT'),
						'ppn_in' => __('PPN - IN'),
						'prepayment_in' => __('Prepayment - IN'),
						'prepayment_out' => __('Prepayment - Out'),
					),
					'empty' => __('Pilih Jenis Kas Bank'),
				)), array(
					'class' => 'form-group'
				));
		?>
		<div id="form-content-document">
			<?php 
        			if( !empty($receiving_cash_type) && ( $receiving_cash_type == 'ppn_in' || $receiving_cash_type == 'prepayment_in' ) ) {
						echo $this->element('blocks/cashbanks/pick_document');
					}
			?>
		</div>
		<?php

				echo $this->Form->input('receiver_type',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'type' => 'hidden',
					'id' => 'receiver-type'
				));
				echo $this->Form->input('receiver_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'type' => 'hidden',
					'id' => 'receiver-id'
				));

				echo $this->Html->tag('div', $this->Form->input('tgl_cash_bank',array(
					'label'=> __('Tanggal Kas Bank *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal Kas Bank'),
					'type' => 'text',
					'value' => (!empty($this->request->data['CashBank']['tgl_cash_bank'])) ? $this->request->data['CashBank']['tgl_cash_bank'] : date('d/m/Y')
				)), array(
					'class' => 'form-group'
				));

		?>
		<div class="form-group">
			<?php
				echo $this->Form->label('receiver', __('Diterima dari'), array(
					'class' => 'cash_bank_user_type',
				));
			?>
			<div class="row">
				<div class="col-sm-10">
					<?php
						echo $this->Form->input('receiver',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('User'),
							'id' => 'cash-bank-user',
							'readonly' => true
						));
					?>
				</div>
				<div class="col-sm-2 hidden-xs">
					<?php 
							$attrBrowse = array(
                                'class' => 'ajaxModal visible-xs',
                                'escape' => false,
                                'title' => __('Data User Kas Bank'),
                                'data-action' => 'browse-form',
                                'data-change' => 'cash-bank-user',
                            );
        					$urlBrowse = array(
                                'controller'=> 'ajax', 
                                'action' => 'getUserCashBank'
                            );
							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                            echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                    ?>
				</div>
			</div>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('description',array(
					'label'=> __('Keterangan'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Keterangan'),
					'type' => 'textarea'
				)), array(
					'class' => 'form-group'
				));
		?>
		<div class="form-group">
        	<?php 
        			$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs',
                        'escape' => false,
                        'title' => __('Detail Kas Bank'),
                        'data-action' => 'browse-cash-banks',
                        'data-change' => 'cashbanks-info-table',
                        'url' => $this->Html->url( array(
                            'controller'=> 'ajax', 
                            'action' => 'getInfoCoa',
                        ))
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Pilih COA'), 'javascript:', $attrBrowse);
            ?>
        </div>
    </div>
</div>

<div class="cashbank-info-detail <?php echo (!empty($this->request->data['CashBankDetail'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Info Kas Bank'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<th><?php echo __('Kode Acc');?></th>
	                    <th><?php echo __('Nama Acc');?></th>
                    	<?php 
                    		// echo $this->Html->tag('th', __('Debit'));
                    		// echo $this->Html->tag('th', __('Kredit'));

                    		echo $this->Html->tag('th', __('Total'), array(
                    			'width' => '30%'
                    		));
                    	?>
	        		</tr>
	        	</thead>
	        	<tbody class="cashbanks-info-table">
	                <?php
			    		echo $this->element('blocks/cashbanks/info_cash_bank_detail');
			    	?>
	        	</tbody>
	    	</table>
	    </div>
	</div>
</div>

<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
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