<?php
        if(!empty($values)){
            $grandtotal = 0;
            $grandtotalSisa = 0;
            $grandtotalPayment = 0;

            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'Insurance.id');
                $nodoc = Common::hashEmptyField($value, 'Insurance.nodoc');
                $name = Common::hashEmptyField($value, 'Insurance.name');
                $to_name = Common::hashEmptyField($value, 'Insurance.to_name');
                $start_date = Common::hashEmptyField($value, 'Insurance.start_date');
                $end_date = Common::hashEmptyField($value, 'Insurance.end_date');
                $status = Common::hashEmptyField($value, 'Insurance.status');
                $transaction_status = Common::hashEmptyField($value, 'Insurance.transaction_status');
                $total = Common::hashEmptyField($value, 'Insurance.grandtotal', 0);
                $branch_id = Common::hashEmptyField($value, 'Insurance.branch_id');
                $branch = Common::hashEmptyField($value, 'Branch.code');

                $date = Common::getCombineDate($start_date, $end_date);
                $status_paid = $this->Common->_callTransactionStatus($value, 'Insurance');
                
                $statusArr = Common::_callInsuranceStatus($value);
                $status = Common::hashEmptyField($statusArr, 'status');
                $status_color = Common::hashEmptyField($statusArr, 'color');

                $total_payment = $this->Common->filterEmptyField($value, 'InsurancePayment', 'grandtotal');
                $sisa = $total - $total_payment;
                
                $grandtotal += $total;
                $grandtotalSisa += $sisa;
                $grandtotalPayment += $total_payment;
?>
<tr>
    <td><?php echo $nodoc;?></td>
    <td><?php echo $name;?></td>
    <td class="text-center"><?php echo $date;?></td>
    <td><?php echo $to_name;?></td>
    <td class="text-center">
        <?php
                echo $this->Html->tag('span', $status, array(
                    'class' => 'label label-'.$status_color,
                ));
        ?>
    </td>
    <td class="text-center"><?php echo $status_paid;?></td>
    <td class="text-right"><?php echo Common::getFormatPrice($total, 2);?></td>
    <td class="text-right"><?php echo Common::getFormatPrice($total_payment, 2);?></td>
    <td class="text-right"><?php echo Common::getFormatPrice($sisa, 2);?></td>
</tr>
<?php
            }

            $grandtotal = Common::getFormatPrice($grandtotal, 2);
            $grandtotalSisa = Common::getFormatPrice($grandtotalSisa, 2);
            $grandtotalPayment = Common::getFormatPrice($grandtotalPayment, 2);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $grandtotal, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $grandtotalPayment, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $grandtotalSisa, array(
                'style' => 'text-align: right',
            ));
    ?>
</tr>
<?php
        }
?>