<?php
        $grandtotal = 0;

        $payment_date = $this->Common->filterEmptyField($value, 'LeasingPayment', 'payment_date');
        $customPaymentDate = $this->Common->getDate($payment_date);

        if(!empty($value['LeasingPaymentDetail'])){
            foreach ($value['LeasingPaymentDetail'] as  $val) {
                $id = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'id');
                $dp = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'installment');
                $denda = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'denda');
                $total = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'total');

                $no_contract = $this->Common->filterEmptyField($val, 'Leasing', 'no_contract');

                $customTotal = $this->Common->getFormatPrice($total);
                $dp = $this->Common->getFormatPrice($dp);
                $denda = $this->Common->getFormatPrice($denda);
                
                $grandtotal += $total;
?>
<tr>
    <?php
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $dp, array(
                'class' => 'text-right red',
            ));
            echo $this->Html->tag('td', $denda, array(
                'class' => 'text-right red',
            ));
            echo $this->Html->tag('td', $customTotal, array(
                'class' => 'text-right red',
            ));
    ?>
</tr>
<?php
        }
}
?>
<tr>
    <td align="right" colspan="3" class="bold"><?php echo __('Total')?></td>
    <td align="right" class="total bold">
        <?php 
                $grandtotal = $this->Common->getFormatPrice($grandtotal);
                echo $grandtotal;
        ?>
    </td>
</tr>