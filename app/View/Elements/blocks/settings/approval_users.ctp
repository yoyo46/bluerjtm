<tr class="cash-auth-row" id="cash-auth" rel="<?php echo $idx; ?>">
    <td>
		<?php 
        		echo $this->Form->input('ApprovalDetailPosition.employe_position_id.'.$parent_idx.'.'.$idx, array(
        			'label' => false,
        			'empty' => __('Pilih Posisi'),
        			'options' => $employePositions,
                    'class' => 'form-control approval-position-0 approval-position',
        			'div' => false,
					'error' => false,
        		));
    	?>
    </td>
    <td class="text-center">
        <label>
        	<?php 
        			echo $this->Form->checkbox('ApprovalDetailPosition.is_priority.'.$parent_idx.'.'.$idx);
        	?>
        </label>
    </td>
    <td class="action text-center">
		<?php 
                echo $this->Html->link('<i class="fa fa-times-circle"></i>', 'javascript:', array(
                	'escape' => false,
                	'class' => 'btn btn-danger delete-custom-field btn-xs',
    				'action_type' => 'auth-cash-bank-user-approval',
    				'rel' => $idx,
                ));
        ?>
    </td>
</tr>