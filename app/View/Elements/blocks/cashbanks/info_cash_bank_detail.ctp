<?php
    if(!empty($coa_data['CashBankDetail'])){
        foreach ($coa_data['CashBankDetail'] as $key => $value) {
        	$id = $value['coa_id'];
?>
<tr class="child child-<?php echo $id;?>" rel="<?php echo $id;?>">
    <td>
    	<?php
    		echo $value['code_coa'];

    		echo $this->Form->input('CashBankDetail.coa_id.', array(
                'type' => 'hidden',
                'value' => $id
            ));
    	?>
    </td>
    <td>
    	<?php
    		echo $value['name_coa'];
    	?>
    </td>
    <td class="action-search">
    	<?php
    		echo $this->Form->input('CashBankDetail.debit.', array(
	            'type' => 'text',
	            'class' => 'form-control input_price',
	            'label' => false,
	            'div' => false,
	            'required' => false,
	            'value' => $value['debit']
	        ))
    	?>
    </td>
    <td class="action-search">
    	<?php
    		echo $this->Form->input('CashBankDetail.credit.', array(
	            'type' => 'text',
	            'class' => 'form-control input_price',
	            'label' => false,
	            'div' => false,
	            'required' => false,
	            'value' => $value['credit']
	        ))
    	?>
    </td>
    <td class="action-search">
    	<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="cashbank_first"><i class="fa fa-times"></i> Hapus</a>
    </td>
</tr>
<?php
        }
    }
?>