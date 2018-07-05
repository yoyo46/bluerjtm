<?php
        $grandtotal = 0;
        $data = $this->request->data;

        $payment_date = $this->Common->filterEmptyField($data, 'InsurancePayment', 'payment_date');
        $customPaymentDate = $this->Common->getDate($payment_date);

        if(!empty($data['InsurancePaymentDetail'])){
            foreach ($data['InsurancePaymentDetail'] as $idx => $value) {
                $id = Common::hashEmptyField($value, 'Insurance.id');
                $nodoc = Common::hashEmptyField($value, 'Insurance.nodoc');
                $name = Common::hashEmptyField($value, 'Insurance.name');
                $to_name = Common::hashEmptyField($value, 'Insurance.to_name');
                $start_date = Common::hashEmptyField($value, 'Insurance.start_date');
                $end_date = Common::hashEmptyField($value, 'Insurance.end_date');
                $status = Common::hashEmptyField($value, 'Insurance.status');
                
                $total = Common::hashEmptyField($value, 'InsurancePaymentDetail.total', 0);

                $date = Common::getCombineDate($start_date, $end_date);
                
                $grandtotal += $total;
?>
<tr class="child child-<?php echo $id;?>" rel="<?php echo $id;?>">
    <?php
            echo $this->Html->tag('td', $nodoc);
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $to_name);
            echo $this->Html->tag('td', $date, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsurancePaymentDetail.total.%s', $id), false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'form-control input_price text-right leasing-trigger leasing-total',
                'fieldError' => __('InsurancePaymentDetail.%s.total', $idx),
                'attributes' => array(
                    'value' => $total,
                ),
            )), array(
                'class' => 'text-right',
            ));
            echo $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'document_first'
            )), array(
                'class' => 'text-center',
            ));
    ?>
</tr>
<?php
        }
}
?>
<tr id="field-grand-total-document">
    <td align="right" colspan="4" class="bold"><?php echo __('Total')?></td>
    <td align="right" class="total bold">
        <?php 
                echo $this->Common->getFormatPrice($grandtotal);
        ?>
    </td>
    <td>&nbsp;</td>
</tr>