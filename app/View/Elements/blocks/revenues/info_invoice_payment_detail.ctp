<?php
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
            echo $this->Number->currency($invoice['total'], Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td class="text-right">
        <?php
            $price_pay = 0;
            if(!empty($value['invoice_has_paid'])){
                echo $this->Number->currency($value['invoice_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0)); 
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
                'class' => 'form-control input_price invoice-price-payment',
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
<tr id="field-grand-total-ttuj">
    <td align="right" colspan="5"><?php echo __('Total')?></td>
    <td align="right" id="grand-total-payment">
        <?php 
            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr class="additional-input-invoice" id="ppn-grand-total-invoice">
    <td align="right" colspan="5" class="relative">
        <?php 
            echo $this->Form->input('InvoicePayment.ppn', array(
                'type' => 'text',
                'label' => __('PPN'),
                'class' => 'input_number invoice-ppn',
                'required' => false,
                'div' => false
            )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
        ?>
    </td>
    <td align="right" id="ppn-total-invoice">
        <?php 
                $ppn = !empty($this->request->data['InvoicePayment']['ppn'])?$this->request->data['InvoicePayment']['ppn']:0;
                $ppn = $this->Common->calcFloat($total, $ppn);
                echo $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr class="additional-input-invoice" id="pph-grand-total-invoice">
    <td align="right" colspan="5" class="relative">
        <?php 
                echo $this->Form->input('InvoicePayment.pph', array(
                    'type' => 'text',
                    'label' => __('PPh'),
                    'class' => 'input_number invoice-pph',
                    'required' => false,
                    'div' => false
                )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
        ?>
    </td>
    <td align="right" id="pph-total-invoice">
        <?php 
                $pph = !empty($this->request->data['InvoicePayment']['pph'])?$this->request->data['InvoicePayment']['pph']:0;
                $pph = $this->Common->calcFloat($total, $pph);
                echo $this->Number->currency($pph, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr id="grand-total-invoice-payemnt">
    <td align="right" colspan="5"><?php echo __('Grand Total')?></td>
    <td align="right" id="all-total-invoice">
        <?php 
            // if($pph > 0){
            //     $total -= $pph;
            // }
            if($ppn > 0){
                $total += $ppn;
            }

            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>