<?php
    $total = $key = 0;

    if(!empty($lkus)){
        foreach ($lkus as $key => $value) {
            $Lku = $value['Lku'];
?>
<tr class="child child-<?php echo $Lku['id'];?>" rel="<?php echo $Lku['id'];?>">
    <td>
        <?php
            // echo $this->Form->input('LkuPaymentDetail.lku_id.', array(
            //     'options' => $lkus,
            //     'class' => 'form-control lku-choose-ttuj',
            //     'label' => false,
            //     'empty' => __('Pilih Tanggal TTUJ'),
            //     'required' => false,
            //     'value' => (isset($this->request->data['LkuPaymentDetail'][$key]['lku_id']) && !empty($this->request->data['LkuPaymentDetail'][$key]['lku_id'])) ? $this->request->data['LkuPaymentDetail'][$key]['lku_id'] : ''
            // ));

            printf('%s (%s)', date('d F Y', strtotime($value['Ttuj']['ttuj_date'])), $value['Ttuj']['no_ttuj']);

            $keyd = (isset($this->request->data['LkuPaymentDetail'][$Lku['id']]['lku_id']) && !empty($this->request->data['LkuPaymentDetail'][$Lku['id']]['lku_id'])) ? $this->request->data['LkuPaymentDetail'][$Lku['id']]['lku_id'] : '';
            echo $this->Form->input('LkuPaymentDetail.lku_id.'.$keyd, array(
                'type' => 'hidden',
                'value' => $keyd
            ));
        ?>
    </td>
    <td class="data-nopol">
        <?php
            if(!empty($value['Ttuj']['nopol'])){
                echo $value['Ttuj']['nopol'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-from-city">
        <?php
            if(!empty($value['Ttuj']['from_city_name'])){
                echo $value['Ttuj']['from_city_name'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-to-city">
        <?php
            if(!empty($value['Ttuj']['to_city_name'])){
                echo $value['Ttuj']['to_city_name'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td>
        <?php
                echo $this->Common->customDate($Lku['tgl_lku']);
        ?>
    </td>
    <td class="text-right">
        <?php
            echo $this->Number->currency($Lku['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td class="text-right">
        <?php
            $total_biaya_klaim = 0;
            if(!empty($value['lku_has_paid'])){
                echo $this->Number->currency($value['lku_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0)); 
                $total_biaya_klaim = $value['lku_has_paid'];
            }else{
                echo '-';
            }
            
        ?>
    </td>
    <td class="text-right" valign="top">
        <?php
            echo $this->Form->input('LkuPaymentDetail.total_biaya_klaim.'.$Lku['id'], array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price invoice-price-payment',
                'value' => (!empty($this->request->data['LkuPaymentDetail'][$Lku['id']]['total_biaya_klaim'])) ? $this->request->data['LkuPaymentDetail'][$Lku['id']]['total_biaya_klaim'] : $total_biaya_klaim
            ));

            if(!empty($this->request->data['LkuPaymentDetail'][$Lku['id']]['total_biaya_klaim'])){
                $total += str_replace(',', '', $this->request->data['LkuPaymentDetail'][$Lku['id']]['total_biaya_klaim']);
            }
        ?>
    </td>
    <td>
        <?php
            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'lku_second'
            ));
        ?>
    </td>
</tr>
<?php
    }
}
?>
<tr id="field-grand-total-ttuj">
    <td align="right" colspan="7"><?php echo __('Grand Total')?></td>
    <td align="right" id="grand-total-payment"><?php printf('%s %s', Configure::read('__Site.config_currency_code'), $total); ?></td>
    <td>&nbsp;</td>
</tr>
<tr class="additional-input-invoice" id="ppn-grand-total-invoice">
    <td align="right" colspan="7" class="relative">
        <?php 
            echo $this->Form->input('LkuPayment.ppn', array(
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
                $ppn = !empty($this->request->data['LkuPayment']['ppn'])?$this->request->data['LkuPayment']['ppn']:0;
                $ppn = $this->Common->calcFloat($total, $ppn);
                echo $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr class="additional-input-invoice" id="pph-grand-total-invoice">
    <td align="right" colspan="7" class="relative">
        <?php 
                echo $this->Form->input('LkuPayment.pph', array(
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
                $pph = !empty($this->request->data['LkuPayment']['pph'])?$this->request->data['LkuPayment']['pph']:0;
                $pph = $this->Common->calcFloat($total, $pph);
                echo $this->Number->currency($pph, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr id="grand-total-invoice-payemnt">
    <td align="right" colspan="7"><?php echo __('Grand Total')?></td>
    <td align="right" id="all-total-invoice">
        <?php 
            if($pph > 0){
                $total -= $pph;
            }
            if($ppn > 0){
                $total += $ppn;
            }

            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>