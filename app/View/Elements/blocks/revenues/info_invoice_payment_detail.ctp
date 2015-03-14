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
            if(!empty($value['invoice_has_paid'])){
                echo $this->Number->currency($value['invoice_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0)); 
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
                'value' => (!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']])) ? $this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']] : 0
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