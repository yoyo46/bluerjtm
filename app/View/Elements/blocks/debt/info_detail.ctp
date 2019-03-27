<tbody id="checkbox-info-table">
    <?php
            $grandTotal = 0;

            if(!empty($detail_data['DebtDetail'])){
                $tmp = array();

                foreach ($detail_data['DebtDetail'] as $key => $value) {
                    $id = $value['employe_id'];
                    $rel_id = $id;

                    $name = Common::hashEmptyField($value, 'karyawan_name');
                    $total = Common::hashEmptyField($value, 'total');
                    $note = Common::hashEmptyField($value, 'note');
                  	$type = Common::hashEmptyField($value, 'type');

                    $total = $this->Common->convertPriceToString($total, 0, 2);
                    $grandTotal += $total;
                    
                    if( isset($tmp[$id]) ) {
                        $tmp[$id]++;

                        $rel_id .= sprintf('-%s', $tmp[$id]);
                    } else {
                        $tmp[$id] = 0;
                    }
    ?>
    <tr class="child child-<?php echo $id;?>" rel="<?php echo $rel_id;?>">
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
                echo $this->Html->tag('td', $this->Form->input('DebtDetail.total.', array(
                    'type' => 'text',
                    'class' => 'form-control input_price_coma input_number sisa-amount text-right',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                    'value' => $total,
                )), array(
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