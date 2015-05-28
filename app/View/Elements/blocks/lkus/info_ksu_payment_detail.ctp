<?php
    $total = $key = 0;

    if(!empty($ksus)){
        foreach ($ksus as $key => $value) {
            $Ksu = $value['Ksu'];
?>
<tr class="child child-<?php echo $Ksu['id'];?>" rel="<?php echo $Ksu['id'];?>">
    <td>
        <?php
            // echo $this->Form->input('KsuPaymentDetail.ksu_id.', array(
            //     'options' => $ksus,
            //     'class' => 'form-control lku-choose-ttuj',
            //     'label' => false,
            //     'empty' => __('Pilih Tanggal TTUJ'),
            //     'required' => false,
            //     'value' => (isset($this->request->data['KsuPaymentDetail'][$key]['ksu_id']) && !empty($this->request->data['KsuPaymentDetail'][$key]['ksu_id'])) ? $this->request->data['KsuPaymentDetail'][$key]['ksu_id'] : ''
            // ));

            printf('%s (%s)', date('d F Y', strtotime($value['Ttuj']['ttuj_date'])), $value['Ttuj']['no_ttuj']);

            $keyd = (isset($this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_id']) && !empty($this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_id'])) ? $this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_id'] : '';
            echo $this->Form->input('KsuPaymentDetail.ksu_id.'.$keyd, array(
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
                echo $this->Common->customDate($Ksu['tgl_ksu']);
        ?>
    </td>
    <td class="text-right">
        <?php
            echo $this->Number->currency($Ksu['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td class="text-right">
        <?php
            $total_biaya_klaim = 0;
            if(!empty($value['ksu_has_paid'])){
                echo $this->Number->currency($value['ksu_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0)); 
                $total_biaya_klaim = $value['ksu_has_paid'];
            }else{
                echo '-';
            }
            
        ?>
    </td>
    <td class="text-right" valign="top">
        <?php
            echo $this->Form->input('KsuPaymentDetail.total_biaya_klaim.'.$Ksu['id'], array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'required' => false,
                'class' => 'form-control input_price invoice-price-payment',
                'value' => (!empty($this->request->data['KsuPaymentDetail'][$Ksu['id']]['total_biaya_klaim'])) ? $this->request->data['KsuPaymentDetail'][$Ksu['id']]['total_biaya_klaim'] : $total_biaya_klaim
            ));

            if(!empty($this->request->data['KsuPaymentDetail'][$Ksu['id']]['total_biaya_klaim'])){
                $total += str_replace(',', '', $this->request->data['KsuPaymentDetail'][$Ksu['id']]['total_biaya_klaim']);
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
            echo $this->Form->input('KsuPayment.ppn', array(
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
                $ppn = !empty($this->request->data['KsuPayment']['ppn'])?$this->request->data['KsuPayment']['ppn']:0;
                $ppn = $this->Common->calcFloat($total, $ppn);
                echo $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>
<tr class="additional-input-invoice" id="pph-grand-total-invoice">
    <td align="right" colspan="7" class="relative">
        <?php 
                echo $this->Form->input('KsuPayment.pph', array(
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
                $pph = !empty($this->request->data['KsuPayment']['pph'])?$this->request->data['KsuPayment']['pph']:0;
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