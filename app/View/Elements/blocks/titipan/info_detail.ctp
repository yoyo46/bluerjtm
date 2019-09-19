<tbody id="checkbox-info-table">
    <?php
            $grandTotal = 0;

            if(!empty($detail_data['TitipanDetail'])){
                $tmp = array();

                foreach ($detail_data['TitipanDetail'] as $key => $value) {
                    $id = $value['driver_id'];
                    $rel_id = $id;

                    $no_id = Common::hashEmptyField($value, 'driver_no_id');
                    $name = Common::hashEmptyField($value, 'driver_name');
                    $total = Common::hashEmptyField($value, 'total');
                    $note = Common::hashEmptyField($value, 'note');

                    $total = $this->Common->convertPriceToString($total, 0, 2);
                    $grandTotal += $total;
                    
                    if( isset($tmp[$id]) ) {
                        $tmp[$id]++;

                        $rel_id .= sprintf('-%s', $tmp[$id]);
                    } else {
                        $tmp[$id] = 0;
                    }
    ?>
    <tr class="child child-child-<?php echo $id;?>">
        <td>
            <?php
                    echo $no_id;
            ?>
        </td>
        <td>
        	<?php
            		echo $name;
            		echo $this->Form->input('TitipanDetail.driver_id.', array(
                        'type' => 'hidden',
                        'value' => $id,
                    ));
        	?>
        </td>
        <?php 
                echo $this->Html->tag('td', $this->Common->_callInputForm('TitipanDetail.note.', array(
                    'class' => 'form-control',
                    'value' => $note,
                )), array(
                    'class' => 'action-search'
                ));
                echo $this->Html->tag('td', $this->Common->_callInputForm('TitipanDetail.total.', array(
                    'class' => 'form-control input_price_coma sisa-amount text-right',
                    'value' => $total,
                    'fieldError' => __('TitipanDetail.%s.total', $key),
                )), array(
                    'class' => 'action-search'
                ));
    	?>
        <td class="document-table-action">
        	<a href="javascript:" class="delete-document-current btn btn-danger btn-xs" data-id="child-child-<?php echo $id; ?>"><i class="fa fa-times"></i> Hapus</a>
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