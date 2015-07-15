<?php
		$this->Html->addCrumb(__('Approval Setting'));

		echo $this->Form->create('CashBank', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('List Approval Kas/Bank'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('module_type',array(
						'label'=> __('Modul *'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('requester_id',array(
						'label'=> __('Posisi yg Mengajukan *'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
    	<div class="form-group action">
    		<?php 
	                echo $this->Common->rule_link('<i class="fa fa-plus-square"></i> '.__('Tambah List Approval'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-success add-custom-field btn-xs',
	                	'action_type' => 'auth-cash-bank'
	                ));
	        ?>
    	</div>
	</div>
</div>
<div class="list-approval-setting">
	<div class="box box-primary wrapper-approval-setting" rel="0">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('List Approval #1'); ?></h3>
	        <div class="pull-right box-tools">
	            <button class="btn btn-danger btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
	        </div>
	    </div>
	    <div class="box-body">
	        <div class="form-group">
	        	<?php 
						echo $this->Form->label('CashBankDetail.min_amount.0', __('Range Jumlah Approval *'));
				?>

		        <div class="row">
		        	<div class="col-sm-6">
			        	<?php 
								echo $this->Form->input('CashBankDetail.min_amount.0',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'placeholder'=> __('Jumlah dari'), 
								));
						?>
		        	</div>
		        	<div class="col-sm-6">
			        	<?php 
								echo $this->Form->input('CashBankDetail.max_amount.0',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'placeholder'=> __('Sampai Jumlah'), 
								));
						?>
		        	</div>
		        </div>
	        </div>
	    	<div class="form-group action">
	    		<?php 
		                echo $this->Common->rule_link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
		                	'escape' => false,
		                	'class' => 'btn btn-success add-custom-field btn-xs',
		                	'action_type' => 'auth-cash-bank-user-approval'
		                ));
		        ?>
	    	</div>
	        <table class="table table-bordered">
	        	<thead>
	        		<tr>
	        			<th><?php echo __('Nama Approval');?></th>
	                    <th><?php echo __('Grup');?></th>
	                    <th class="text-center"><?php echo __('Approval Prioritas');?></th>
	                    <th class="text-center"><?php echo __('Action');?></th>
	        		</tr>
	        	</thead>
	        	<tbody class="cashbanks-auth-table" rel="0">
		            <tr class="cash-auth-row" id="cash-auth" rel="0">
		                <td>
	                		<div class="col-sm-10">
	                			<?php 
				                		echo $this->Form->input('CashBankAuthMaster.employe_id.0.', array(
				                			'label' => false,
				                			'empty' => __('Pilih Karyawan'),
				                			'options' => $employes,
				                			'class' => 'form-control cash-bank-auth-user-0 cash-bank-auth-user',
				                			'div' => false,
				                		));

				                		echo $this->Form->input('CashBankAuthMaster.id.0.', array(
				                			'type' => 'hidden',
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
			                                'data-change' => 'cash-bank-auth-user-0',
			                            );
			        					$urlBrowse = array(
			                                'controller'=> 'ajax', 
			                                'action' => 'getUserEmploye',
			                                0,
			                            );
										$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                            echo $this->Common->rule_link('<i class="fa fa-search"></i>', $urlBrowse, $attrBrowse);
			                    ?>
	                		</div>
	                		<div class="clear"></div>
		                </td>
		                <td class="group_auth text-center">
		                	-
		                </td>
		                <td class="text-center">
			                <label>
			                	<?php 
			                			echo $this->Form->checkbox('CashBankAuthMaster.is_priority.0.');
			                	?>
			                </label>
		                </td>
		                <td class="action text-center">
				    		<?php 
					                echo $this->Common->rule_link('<i class="fa fa-times-circle"></i>', 'javascript:', array(
					                	'escape' => false,
					                	'class' => 'btn btn-danger delete-custom-field btn-xs',
		                				'action_type' => 'auth-cash-bank-user-approval',
		                				'rel' => '0',
					                ));
					        ?>
		                </td>
		            </tr>
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
	?>
</div>
<?php
	echo $this->Form->end();
?>
<div id="form-authorize" class="hide">
	<?php 
			echo $this->Form->input('CashBankAuthMaster.employe_id.', array(
				'label' => false,
				'empty' => __('Pilih Karyawan'),
				'options' => $employes,
				'class' => 'form-control cash-bank-auth-user',
				'div' => false
			));
	?>
</div>