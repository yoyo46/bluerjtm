<?php
        $grandtotal = 0;

        if(!empty($details)){
            foreach ($details as  $value) {
                $id = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'id');
                $leasing_detail_id = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'leasing_detail_id');
                $expired_date = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'expired_date');
                $pokok = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'pokok');
                $bunga = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'bunga');
                $denda = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'denda');
                $total = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'total');

                $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');

                $customDate = $this->Common->customDate($expired_date, 'd/m/Y');
                $customPokok = $this->Common->getFormatPrice($pokok);
                $customBunga = $this->Common->getFormatPrice($bunga);
                $customDenda = $this->Common->getFormatPrice($denda);
                $customTotal = $this->Common->getFormatPrice($total);
                
                $grandtotal += $total;

                echo $this->Form->input('LeasingPaymentDetail.leasing_detail_id.'.$leasing_detail_id, array(
                    'type' => 'hidden',
                    'value' => $leasing_detail_id
                ));
?>
<tr class="child child-<?php echo $leasing_detail_id;?>" rel="<?php echo $leasing_detail_id;?>">
    <?php
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $customPokok);
            echo $this->Html->tag('td', $customBunga);
            echo $this->Html->tag('td', $customDenda);
            echo $this->Html->tag('td', $customTotal);
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('time').__(' Hapus'), 'javascript:', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'lku_second'
            )));
    ?>
</tr>
<?php
        }
}
?>
<tr>
    <td align="right" colspan="8"><?php echo __('Total')?></td>
    <td align="right">
        <?php 
                echo $this->Common->getFormatPrice($grandtotal);
        ?>
    </td>
    <td>&nbsp;</td>
</tr>