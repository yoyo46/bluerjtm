<?php
    $total = $i = 0;

    if(!empty($invoices)){
        foreach ($invoices as $key => $value) {
            $invoice = $value['Invoice'];
?>
<tr class="child child-<?php echo $invoice['id'];?>" rel="<?php echo $invoice['id'];?>">
    <td>
        <?php
            echo $this->Form->input('LkuPaymentDetail.lku_id.', array(
                'options' => $lkus,
                'class' => 'form-control lku-choose-ttuj',
                'label' => false,
                'empty' => __('Pilih Tanggal TTUJ'),
                'required' => false,
                'value' => (isset($this->request->data['LkuPaymentDetail'][$i]['lku_id']) && !empty($this->request->data['LkuPaymentDetail'][$i]['lku_id'])) ? $this->request->data['LkuPaymentDetail'][$i]['lku_id'] : ''
            ));
        ?>
    </td>
    <td class="data-nopol">
        <?php
            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['nopol'])){
                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['nopol'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-from-city">
        <?php
            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['from_city_name'])){
                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['from_city_name'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-to-city">
        <?php
            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['to_city_name'])){
                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['to_city_name'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-total-claim" align="right">
        <?php
            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim'])){

                echo $this->Form->hidden('LkuPaymentDetail.total_klaim.', array(
                    'empty' => __('Pilih Jumlah Klaim'),
                    'class' => 'lku-claim-number form-control',
                    'div' => false,
                    'label' => false,
                    'value' => $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim']
                ));

                echo $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-total-price-claim" align="right">
        <?php
            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Lku']['total_price'])){
                $max_qty = $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_price'];
                echo  $this->Form->hidden('LkuPaymentDetail.total_biaya_klaim.', array(
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control price-lku input_number',
                    'required' => false,
                    'max_price' => $max_qty,
                    'placeholder' => sprintf(__('maksimal pembayaran : %s'), $max_qty),
                    'value' => $max_qty
                ));
                $total += $max_qty;

                echo $this->Number->currency($max_qty, Configure::read('__Site.config_currency_code'), array('places' => 0));
            }else{
                echo '-';
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
?>
<tr id="field-grand-total-ttuj">
    <td align="right" colspan="5"><?php echo __('Grand Total')?></td>
    <td align="right" id="grand-total-payment"><?php printf('%s %s', Configure::read('__Site.config_currency_code'), $total); ?></td>
    <td>&nbsp;</td>
</tr>

<tr class="additional-input-invoice" id="pph-grand-total-invoice">
    <td align="right" colspan="5" class="relative">
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
    <td align="right" colspan="5"><?php echo __('Grand Total')?></td>
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
<?php
    }
?>