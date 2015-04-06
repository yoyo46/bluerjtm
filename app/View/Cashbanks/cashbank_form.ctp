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
				echo $this->Html->tag('div', $this->Form->input('receiving_cash_type',array(
					'label'=> __('Kas Bank *'), 
					'class'=>'form-control cash-bank-handle',
					'required' => false,
					'options' => array(
						'in' => __('Cash IN'),
						'out' => __('Cash OUT')
					)
				)), array(
					'class' => 'form-group'
				));

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
	                    <th><?php echo __('Debit');?></th>
	                    <th><?php echo __('Kredit');?></th>
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

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Otorisasi Kas Bank'); ?></h3>
    </div>
    <div class="box-body table-responsive">
    	<div class="form-group action">
    		<?php 
	                echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-success add-custom-field',
	                	'action_type' => 'auth-cash-bank'
	                ));

	                echo $this->Html->link('<i class="fa fa-times-circle"></i> '.__('Hapus'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-danger delete-custom-field',
	                	'action_type' => 'auth-cash-bank'
	                ));
	        ?>
    	</div>
        <table class="table table-hover">
        	<thead>
        		<tr>
        			<th width="30%"><?php echo __('Nama');?></th>
                    <th width="30%"><?php echo __('Grup');?></th>
                    <th width="30%"><?php echo __('level Otorisasi');?></th>
        		</tr>
        	</thead>
        	<tbody class="cashbanks-auth-table">
        		<?php
        			$count = 1;
        			if(!empty($auth_data['CashBankAuth'])){
        				$count = count($auth_data['CashBankAuth']);
        			}

        			for ($i=1; $i <= $count; $i++) { 
        				$id = $i-1;
        		?>
	            <tr class="cash-auth-row" id="cash-auth-<?php echo $i?>" rel="<?php echo $i?>">
	                <td>
	                	<div class="row">
	                		<div class="col-sm-10">
	                			<?php 
			                		echo $this->Form->input('CashBankAuth.employe_id.', array(
			                			'label' => false,
			                			'empty' => __('Pilih Karyawan'),
			                			'options' => $employes,
			                			'class' => 'form-control cash-bank-auth-user',
			                			'div' => false,
			                			'value' => (isset($auth_data) && !empty($auth_data['CashBankAuth'][$id]['employe_id'])) ? $auth_data['CashBankAuth'][$id]['employe_id'] : ''
			                		));
			                	?>
	                		</div>
	                		<div class="col-sm-2">
	                			<?php 
										$attrBrowse = array(
			                                'class' => 'ajaxModal visible-xs',
			                                'escape' => false,
			                                'title' => __('Data Karyawan'),
			                                'data-action' => 'browse-form',
			                                'data-change' => 'cash-bank-auth-user',
			                            );
			        					$urlBrowse = array(
			                                'controller'=> 'ajax', 
			                                'action' => 'getUserEmploye',
			                                $i
			                            );
										$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                            echo $this->Html->link('<i class="fa fa-search"></i>', $urlBrowse, $attrBrowse);
			                    ?>
	                		</div>
	                	</div>
	                </td>
	                <td align="center" class="group_auth">
	                	<?php
	                		if(isset($auth_data) && !empty($auth_data['CashBankAuth'][$id]['group'])){
	                			echo $auth_data['CashBankAuth'][$id]['group'];
	                		}else{
	                			echo '-';
	                		}
	                	?>
	                </td>
	                <td align="center">
	                	<?php
	                		if(isset($auth_data) && !empty($auth_data['CashBankAuth'][$id]['level'])){
	                			echo $auth_data['CashBankAuth'][$id]['level'];
	                		}else{
	                			echo '1';
	                		}
	                	?>
	                </td>
	            </tr>
	            <?php
        			}
        		?>
        	</tbody>
    	</table>
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
<div id="form-authorize" class="hide">
	<?php 
		echo $this->Form->input('CashBankAuth.employe_id.', array(
			'label' => false,
			'empty' => __('Pilih Karyawan'),
			'options' => $employes,
			'class' => 'form-control cash-bank-auth-user',
			'div' => false
		));
	?>
</div>