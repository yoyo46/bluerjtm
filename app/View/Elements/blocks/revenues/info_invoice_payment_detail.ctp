<?php
        $data = $this->request->data;
        $total = $i = 0;

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoice = $value['Invoice'];
?>
<tr class="child child-<?php echo $invoice['id'];?>" rel="<?php echo $invoice['id'];?>">
    <td>
        <?php
            echo $invoice['no_invoice'];

            echo $this->Form->input('InvoicePaymentDetail.invoice_id.'.$invoice['id'], array(
                'type' => 'hidden',
                'value' => $invoice['id']
            ));
        ?>
    </td>
    <td>
        <?php
                echo $this->Common->customDate($invoice['invoice_date']);
        ?>
    </td>
    <td class="text-center">
        <?php
                printf('%s s/d %s', $this->Common->customDate($invoice['period_from'], 'd/m/Y'), $this->Common->customDate($invoice['period_to'], 'd/m/Y'));
        ?>
    </td>
    <td class="text-right">
        <?php
            echo $this->Common->getFormatPrice($invoice['total']);
        ?>
    </td>
    <td class="text-right">
        <?php
            $price_pay = 0;
            if(!empty($value['invoice_has_paid'])){
                echo $this->Common->getFormatPrice($value['invoice_has_paid']); 
                $price_pay = $value['invoice_has_paid'];
            }else{
                echo '-';
            }
            
        ?>
    </td>
    <td class="text-right" valign="top">
        <?php
            echo $this->Form->input('InvoicePaymentDetail.price_pay.'.$invoice['id'], array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price document-pick-price text-right',
                'value' => (!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']])) ? $this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']] : $price_pay
            ));

            if(!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']])){
                $total += str_replace(',', '', $this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']]);
            }
        ?>
    </td>
    <td>
        <?php
            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'invoice_first'
            ));
        ?>
    </td>
</tr>
<?php
        }
    }
?>
<tr id="field-grand-total-document">
    <td align="right" colspan="5"><?php echo __('Total')?></td>
    <td align="right" id="grand-total-document">
        <?php 
            echo $this->Common->getFormatPrice($total);
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr id="ppn-grand-total-invoice">
    <td align="right" colspan="5" class="relative additional-input-invoice">
        <?php 
            echo $this->Form->input('InvoicePayment.ppn', array(
                'type' => 'text',
                'label' => __('PPN'),
                'class' => 'input_number ppn-persen',
                'required' => false,
                'div' => false
            )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
        ?>
    </td>
    <td align="right">
        <?php 
                echo $this->Form->input('InvoicePayment.ppn_total', array(
                    'type' => 'text',
                    'id' => 'ppn-total',
                    'label' => false,
                    'class' => 'input_price_coma text-right form-control',
                    'data-decimal' => '0',
                    'required' => false,
                    'div' => false,
                ));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr id="pph-grand-total-invoice">
    <td align="right" colspan="5" class="relative additional-input-invoice">
        <?php 
                echo $this->Form->input('InvoicePayment.pph', array(
                    'type' => 'text',
                    'label' => __('PPh'),
                    'class' => 'input_number pph-persen',
                    'required' => false,
                    'div' => false
                )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
        ?>
    </td>
    <td align="right">
        <?php 
                echo $this->Form->input('InvoicePayment.pph_total', array(
                    'type' => 'text',
                    'label' => false,
                    'class' => 'input_price_coma text-right form-control',
                    'data-decimal' => '0',
                    'id' => 'pph-total',
                    'required' => false,
                    'div' => false,
                ));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr id="grand-total-invoice-payemnt">
    <td align="right" colspan="5"><?php echo __('Grand Total')?></td>
    <td align="right" id="all-total">
        <?php 
                $ppn = $this->Common->filterEmptyField($data, 'InvoicePayment', 'ppn_total', 0);
                $ppn = $this->Common->convertPriceToString($ppn);
                $total += $ppn;

                echo $this->Common->getFormatPrice($total);
        ?>
    </td>
    <td>&nbsp;</td>
</tr>