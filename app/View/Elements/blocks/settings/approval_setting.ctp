<div class="box box-primary wrapper-approval-setting" rel="<?php echo $idx; ?>">
    <div class="box-header">
        <h3 class="box-title"><?php printf(__('List Approval #%s'), $idx+1); ?></h3>
        <div class="pull-right box-tools">
            <button class="btn btn-danger btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->label('ApprovalDetail.min_amount.'.$idx, __('Range Nominal Transaksi'));
			?>

	        <div class="row">
	        	<div class="col-sm-6">
		        	<?php 
							echo $this->Form->input('ApprovalDetail.min_amount.'.$idx,array(
								'label'=> false, 
								'class'=>'form-control input_price',
								'required' => false,
								'placeholder'=> __('Jumlah dari'), 
								'error' => false,
							));
					?>
	        	</div>
	        	<div class="col-sm-6 range-text">
		        	<?php 
							echo $this->Form->input('ApprovalDetail.max_amount.'.$idx,array(
								'label'=> false, 
								'class'=>'form-control input_price',
								'required' => false,
								'placeholder'=> __('Sampai Jumlah'), 
								'error' => false,
							));
					?>
	        	</div>
	        </div>
        </div>
    	<div class="form-group action">
    		<?php 
	                echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-success add-custom-field btn-xs',
	                	'action_type' => 'auth-cash-bank-user-approval'
	                ));
	        ?>
    	</div>
        <table class="table table-bordered">
        	<thead>
        		<tr>
        			<th><?php echo __('Posisi yg Menyetujui');?></th>
                    <th class="text-center"><?php echo __('Prioritas Approval');?></th>
                    <th class="text-center"><?php echo __('Action');?></th>
        		</tr>
        	</thead>
        	<tbody class="cashbanks-auth-table" rel="<?php echo $idx; ?>">
	            <?php 
	            		if( !empty($this->request->data['ApprovalDetailPosition']['employe_position_id'][$idx]) ) {
				            foreach ($this->request->data['ApprovalDetailPosition']['employe_position_id'][$idx] as $key => $employe_position_id) {
								echo $this->element('blocks/settings/approval_users', array(
									'parent_idx' => $idx,
									'idx' => $key,
								));
				            }
				        } else {
							echo $this->element('blocks/settings/approval_users', array(
								'parent_idx' => 0,
								'idx' => 0,
							));
						}
	            ?>
        	</tbody>
    	</table>
    </div>
</div>