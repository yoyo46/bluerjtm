<tbody id="checkbox-info-table">
    <?php
            $grandTotal = 0;
            $idx = 0;

            if(!empty($detail_data['DebtDetail'])){
                foreach ($detail_data['DebtDetail'] as $key => $value) {
                    $id = $value['employe_id'];

                    $name = Common::hashEmptyField($value, 'karyawan_name');
                    $total = Common::hashEmptyField($value, 'total');
                    $note = Common::hashEmptyField($value, 'note');
                  	$type = Common::hashEmptyField($value, 'type');

                    $total = $this->Common->convertPriceToString($total, 0, 2);
                    $grandTotal += $total;
                    $rel = $type.$id;
    ?>
    <tr class="child child-child-<?php echo $rel;?>">
        <td>
        	<?php
            		echo $name;
            		echo $this->Form->input('DebtDetail.employe_id.', array(
                        'type' => 'hidden',
                        'value' => $id,
                    ));
                    echo $this->Form->input('DebtDetail.type.', array(
                        'type' => 'hidden',
                        'value' => $type,
                    ));
        	?>
        </td>
        <td>
        	<?php
        		  echo Common::hashEmptyField($value, 'type');
        	?>
        </td>
        <?php 
                echo $this->Html->tag('td', $this->Form->input('DebtDetail.note.', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                    'value' => $note,
                )), array(
                    'class' => 'action-search'
                ));
                echo $this->Html->tag('td', $this->Common->_callInputForm('DebtDetail.total.', array(
                    'type' => 'text',
                    'class' => 'form-control input_price_coma input_number sisa-amount text-right',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                    'value' => $total,
                    'fieldError' => array(
                        'DebtDetail.'.$idx.'.total',
                    ),
                )), array(
                    'class' => 'action-search'
                ));
    	?>
        <td class="document-table-action">
            <?php
                    echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                        'class' => 'delete-custom-field btn btn-danger btn-xs',
                        'escape' => false,
                        'action_type' => 'cashbank_first',
                        'data-id' => sprintf('child-child-%s', $rel),
                    ));
            ?>
        </td>
    </tr>
    <?php
                    $idx++;
                }
            }
    ?>
</tbody>
<tfoot>
    <tr>
        <?php 
                echo $this->Html->tag('td', __('Total'), array(
                    'colspan' => 3,
                    'class' => 'bold text-right',
                ));
                echo $this->Html->tag('td', $this->Common->getFormatPrice($grandTotal, 0, 2), array(
                    'class' => 'text-right',
                    'id' => 'total-biaya',
                    'data-cent' => 2,
                ));
        ?>
    </tr>
</tfoot>