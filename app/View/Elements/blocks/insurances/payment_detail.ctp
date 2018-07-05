<?php
        $grandtotal = 0;

        if(!empty($value['InsurancePaymentDetail'])){
            foreach ($value['InsurancePaymentDetail'] as  $value) {
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
<tr>
    <?php
            echo $this->Html->tag('td', $nodoc);
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $to_name);
            echo $this->Html->tag('td', $date, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', Common::getFormatPrice($total, 2), array(
                'class' => 'text-right',
            ));
    ?>
</tr>
<?php
        }
}
?>
<tr>
    <td align="right" colspan="4" class="bold"><?php echo __('Total')?></td>
    <td align="right" class="total bold">
        <?php 
                echo Common::getFormatPrice($grandtotal, 2);
        ?>
    </td>
</tr>