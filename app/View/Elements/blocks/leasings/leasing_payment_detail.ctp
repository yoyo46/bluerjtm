<?php
        $grandtotal = 0;

        $payment_date = $this->Common->filterEmptyField($value, 'LeasingPayment', 'payment_date');
        $customPaymentDate = $this->Common->getDate($payment_date);

        if(!empty($value['LeasingPaymentDetail'])){
            foreach ($value['LeasingPaymentDetail'] as  $val) {
                $id = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'id');
                $expired_date = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'expired_date');
                $pokok = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'installment');
                $bunga = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'installment_rate');
                $denda = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'denda');
                $total = $this->Common->filterEmptyField($val, 'LeasingPaymentDetail', 'total');

                $no_contract = $this->Common->filterEmptyField($val, 'Leasing', 'no_contract');

                $customDate = $this->Common->customDate($expired_date, 'd/m/Y');
                $customTotal = $this->Common->getFormatPrice($total);
                $pokok = $this->Common->getFormatPrice($pokok);
                $bunga = $this->Common->getFormatPrice($bunga);
                $denda = $this->Common->getFormatPrice($denda);
                
                $grandtotal += $total;
                $addClass = '';

                if( $expired_date < $customPaymentDate ) {
                    $addClass .= ' expired';
                }
?>
<tr class="<?php echo $addClass; ?>">
    <?php
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $customDate, array(
                'class' => 'text-center red',
            ));
            echo $this->Html->tag('td', $pokok, array(
                'class' => 'text-right red',
            ));
            echo $this->Html->tag('td', $bunga, array(
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
    <td align="right" colspan="5" class="bold"><?php echo __('Total')?></td>
    <td align="right" class="total bold">
        <?php 
                $grandtotal = $this->Common->getFormatPrice($grandtotal);
                echo $grandtotal;
        ?>
    </td>
</tr>