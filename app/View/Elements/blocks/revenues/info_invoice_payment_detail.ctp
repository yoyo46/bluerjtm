<?php
        $data = $this->request->data;
        $invoices = $this->Common->filterEmptyField($data, 'InvoicePaymentDetail');

        $grantotalPayment = 0;
        $grantotal = 0;
        $grantotalPpn = 0;
        $grantotalPpnNominal = 0;
        $grantotalPph = 0;
        $grantotalPphNominal = 0;

        if(!empty($invoices)){
            foreach ($invoices as $key => $value) {
                $invoice_id = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'invoice_id');
                $invoice_has_paid = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'invoice_has_paid', '-', true, array(
                    'price' => true,
                ));
                $price_pay = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'price_pay');
                $ppn = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'ppn');
                $ppn_nominal = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'ppn_nominal');
                $pph = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'pph');
                $pph_nominal = $this->Common->filterEmptyField($value, 'InvoicePaymentDetail', 'pph_nominal');

                $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
                $total = $this->Common->filterEmptyField($value, 'Invoice', 'total', false, true, array(
                    'price' => true,
                ));
                $invoice_date = $this->Common->filterEmptyField($value, 'Invoice', 'invoice_date', false, true, array(
                    'date' => 'd M Y',
                ));
                $period_from = $this->Common->filterEmptyField($value, 'Invoice', 'period_from', false, true, array(
                    'date' => 'd M Y',
                ));
                $period_to = $this->Common->filterEmptyField($value, 'Invoice', 'period_to', false, true, array(
                    'date' => 'd M Y',
                ));
                
                $grantotalPayment += $price_pay;
                $total_payment = $total + $ppn_nominal;
                $grantotal += $total_payment;
                $grantotalPpn += $ppn;
                $grantotalPpnNominal += $ppn_nominal;
                $grantotalPph += $pph;
                $grantotalPphNominal += $pph_nominal;

                $customPpnNominal = $this->Common->getFormatPrice($ppn_nominal);
                $customPphNominal = $this->Common->getFormatPrice($pph_nominal);
                $customTotalPayment = $this->Common->getFormatPrice($total_payment);
?>
<tr class="child child-<?php echo $invoice_id;?>" rel="<?php echo $invoice_id;?>">
    <td>
        <?php
                echo $no_invoice;
                echo $this->Form->input('InvoicePaymentDetail.invoice_id.'.$invoice_id, array(
                    'type' => 'hidden',
                    'value' => $invoice_id
                ));
        ?>
    </td>
    <?php
            echo $this->Html->tag('td', $invoice_date);
            echo $this->Html->tag('td', __('%s s/d %s', $period_from, $period_to), array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $total, array(
                'class' => 'text-right total-payment',
            ));
            echo $this->Html->tag('td', $invoice_has_paid, array(
                'class' => 'text-right',
            ));
        
    ?>
    <td class="text-right" valign="top">
        <?php
                echo $this->Common->_callInputForm('InvoicePaymentDetail.price_pay.'.$invoice_id, array(
                    'div' => false,
                    'required' => false,
                    'class' => 'form-control input_price document-pick-price text-right',
                    'value' => $price_pay,
                    'fieldError' => __('InvoicePaymentDetail.%s.price_pay', $invoice_id),
                ));
        ?>
    </td>
    <td class="text-right row" valign="top">
        <?php
                echo $this->Common->_callInputForm('InvoicePaymentDetail.ppn.'.$invoice_id, array(
                    'frameClass' => false,
                    'div' => 'col-sm-5 no-padding',
                    'required' => false,
                    'class' => 'form-control input_number text-center tax-percent',
                    'placeholder' => '%',
                    'data-type' => 'percent',
                    'rel' => 'ppn',
                    'value' => $ppn,
                    'fieldError' => __('InvoicePaymentDetail.%s.ppn', $invoice_id),
                ));
                echo $this->Common->_callInputForm('InvoicePaymentDetail.ppn_total.'.$invoice_id, array(
                    'frameClass' => false,
                    'div' => 'col-sm-7 no-padding',
                    'required' => false,
                    'class' => 'form-control text-right tax-nominal',
                    'placeholder' => 'Rp.',
                    'data-decimal' => '0',
                    'data-type' => 'nominal',
                    'rel' => 'ppn',
                    'value' => $customPpnNominal,
                    'fieldError' => __('InvoicePaymentDetail.%s.ppn_total', $invoice_id),
                ));
        ?>
    </td>
    <td class="text-right row" valign="top">
        <?php
                echo $this->Common->_callInputForm('InvoicePaymentDetail.pph.'.$invoice_id, array(
                    'frameClass' => false,
                    'div' => 'col-sm-5 no-padding',
                    'required' => false,
                    'class' => 'form-control input_number text-center tax-percent',
                    'placeholder' => '%',
                    'data-type' => 'percent',
                    'rel' => 'pph',
                    'value' => $pph,
                    'fieldError' => __('InvoicePaymentDetail.%s.pph', $invoice_id),
                ));
                echo $this->Common->_callInputForm('InvoicePaymentDetail.pph_total.'.$invoice_id, array(
                    'frameClass' => false,
                    'div' => 'col-sm-7 no-padding',
                    'required' => false,
                    'class' => 'form-control text-right tax-nominal',
                    'placeholder' => 'Rp.',
                    'data-decimal' => '0',
                    'data-type' => 'nominal',
                    'rel' => 'pph',
                    'value' => $customPphNominal,
                    'fieldError' => __('InvoicePaymentDetail.%s.pph_total', $invoice_id),
                ));
        ?>
    </td>
    <?php
            echo $this->Html->tag('td', $customTotalPayment, array(
                'class' => 'text-right total-document',
            ));
        
            if( empty($view) ) {
                echo $this->Html->tag('td', 
                    $this->Html->link(__('%s Hapus', $this->Common->icon('times')), 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'invoice_first'
                )));
            }
    ?>
</tr>
<?php
        }
    }
?>
<tr id="field-grand-total-document">
    <td align="right" colspan="5"><?php echo __('Total')?></td>
    <?php
            echo $this->Html->tag('td', $this->Common->getFormatPrice($grantotalPayment), array(
                'id' => 'grand-total-document',
                'class' => 'text-right',
            ));
        
            echo $this->Html->tag('td', 
                $this->Html->tag('div', $this->Common->getFormatPrice($grantotalPpn), array(
                    'id' => 'total-ppn-percent',
                    'class' => 'col-sm-5 no-padding text-center',
                )).
                $this->Html->tag('div', $this->Common->getFormatPrice($grantotalPpnNominal), array(
                    'id' => 'total-ppn-nominal',
                    'class' => 'col-sm-7 no-padding text-center',
                )), array(
                'class' => 'text-right row',
            ));
            echo $this->Html->tag('td', 
                $this->Html->tag('div', $this->Common->getFormatPrice($grantotalPph), array(
                    'id' => 'total-pph-percent',
                    'class' => 'col-sm-5 no-padding text-center',
                )).
                $this->Html->tag('div', $this->Common->getFormatPrice($grantotalPphNominal), array(
                    'id' => 'total-pph-nominal',
                    'class' => 'col-sm-7 no-padding text-center',
                )), array(
                'class' => 'text-right row',
            ));

            echo $this->Html->tag('td', 
                $this->Html->tag('div', $this->Common->getFormatPrice($grantotal), array(
                    'id' => 'grandtotal-document',
                )), array(
                'class' => 'text-right row',
            ));
    ?>
    <td>&nbsp;</td>
</tr>