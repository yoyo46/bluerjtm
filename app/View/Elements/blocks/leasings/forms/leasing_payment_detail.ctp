<?php
        $grandtotal = 0;
        $data = $this->request->data;

        if(!empty($data['LeasingPaymentDetail'])){
            foreach ($data['LeasingPaymentDetail'] as  $value) {
                $id = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'id');
                $leasing_id = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'leasing_id');
                $leasing_installment_id = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'leasing_installment_id');
                $expired_date = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'expired_date');
                $pokok = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'installment');
                $bunga = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'installment_rate');
                $denda = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'denda');
                $total = $this->Common->filterEmptyField($value, 'LeasingPaymentDetail', 'total');

                $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');

                $customDate = $this->Common->customDate($expired_date, 'd/m/Y');
                $customTotal = $this->Common->getFormatPrice($total);
                
                $grandtotal += $total;

                echo $this->Form->input('LeasingPaymentDetail.leasing_id.'.$leasing_id, array(
                    'type' => 'hidden',
                    'value' => $leasing_id
                ));
                echo $this->Form->input('LeasingPaymentDetail.leasing_installment_id.'.$leasing_id, array(
                    'type' => 'hidden',
                    'value' => $leasing_installment_id
                ));
?>
<tr class="child child-<?php echo $leasing_id;?>" rel="<?php echo $leasing_id;?>">
    <?php
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $customDate.$this->Form->input('LeasingPaymentDetail.expired_date.'.$leasing_id, array(
                'type' => 'hidden',
                'value' => $expired_date,
            )));
            echo $this->Html->tag('td', $this->Form->input('LeasingPaymentDetail.installment.'.$leasing_id, array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price installment text-right leasing-trigger',
                'value' => $pokok,
            )));
            echo $this->Html->tag('td', $this->Form->input('LeasingPaymentDetail.installment_rate.'.$leasing_id, array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price installment-rate text-right leasing-trigger',
                'value' => $bunga,
            )));
            echo $this->Html->tag('td', $this->Form->input('LeasingPaymentDetail.denda.'.$leasing_id, array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price denda text-right leasing-trigger',
                'value' => $denda,
            )));
            echo $this->Html->tag('td', $customTotal, array(
                'class' => 'text-right leasing-total',
            ));
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('time').__(' Hapus'), 'javascript:', array(
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
    <td align="right" colspan="5"><?php echo __('Total')?></td>
    <td align="right" class="total">
        <?php 
                echo $this->Common->getFormatPrice($grandtotal);
        ?>
    </td>
    <td>&nbsp;</td>
</tr>