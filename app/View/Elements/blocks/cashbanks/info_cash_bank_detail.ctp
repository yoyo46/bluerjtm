<tbody id="checkbox-info-table">
    <?php
            $grandTotal = 0;

            if(!empty($coa_data['CashBankDetail'])){
                $tmpCOA = array();

                foreach ($coa_data['CashBankDetail'] as $key => $value) {
                    $id = $value['coa_id'];
                    $rel_id = $id;

                    $document_detail_id = $value['document_detail_id'];
                    $nopol = $this->Common->filterEmptyField($value, 'nopol');
                  	$total = $this->Common->filterEmptyField($value, 'total');
                    $grandTotal += $total;
                    
                    if( isset($tmpCOA[$id]) ) {
                        $tmpCOA[$id]++;

                        $rel_id .= sprintf('-%s', $tmpCOA[$id]);
                    } else {
                        $tmpCOA[$id] = 0;
                    }
    ?>
    <tr class="child child-<?php echo $id;?>" rel="<?php echo $rel_id;?>">
        <td>
        	<?php
            		echo $value['code_coa'];
            		echo $this->Form->input('CashBankDetail.coa_id.', array(
                    'type' => 'hidden',
                    'value' => $id
                ));
                echo $this->Form->input('CashBankDetail.document_detail_id.', array(
                    'type' => 'hidden',
                    'value' => $document_detail_id,
                ));
        	?>
        </td>
        <td>
        	<?php
        		  echo $value['name_coa'];
        	?>
        </td>
        <?php 
                echo $this->Html->tag('td', $this->CashBank->getTruckCashbank($nopol), array(
                    'class' => 'action-search pick-truck'
                ));

                $form = $this->Form->input('CashBankDetail.total.', array(
                    'type' => 'text',
                    'class' => 'form-control input_price sisa-amount text-right',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                    'value' => $total,
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
</tbody>
<tfoot>
    <tr>
        <?php 
                echo $this->Html->tag('td', __('Total'), array(
                    'colspan' => 3,
                    'class' => 'bold text-right',
                ));
                echo $this->Html->tag('td', $this->Number->format($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                    'class' => 'text-right',
                    'id' => 'total-biaya',
                ));
        ?>
    </tr>
</tfoot>