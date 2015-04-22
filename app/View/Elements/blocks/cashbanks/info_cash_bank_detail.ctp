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
	<?php
		// $form = $this->Form->input('CashBankDetail.debit.', array(
  //           'type' => 'text',
  //           'class' => 'form-control input_price',
  //           'label' => false,
  //           'div' => false,
  //           'required' => false,
  //           'value' => $value['debit']
  //       ));

  //       echo $this->Html->tag('td', $form, array(
  //           'class' => 'action-search'
  //       ));

  //       $form = $this->Form->input('CashBankDetail.credit.', array(
  //           'type' => 'text',
  //           'class' => 'form-control input_price',
  //           'label' => false,
  //           'div' => false,
  //           'required' => false,
  //           'value' => $value['credit']
  //       ));

  //       echo $this->Html->tag('td', $form, array(
  //           'class' => 'action-search'
  //       ));

        $form = $this->Form->input('CashBankDetail.total.', array(
            'type' => 'text',
            'class' => 'form-control input_price',
            'label' => false,
            'div' => false,
            'required' => false,
            'value' => $value['total']
        ));

        echo $this->Html->tag('td', $form, array(
            'class' => 'action-search'
        ));
	?>
    <td class="action-search">
    	<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="cashbank_first"><i class="fa fa-times"></i> Hapus</a>
    </td>
</tr>
<?php
        }
    }
?>