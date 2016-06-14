<?php
    $total = $key = 0;

    if(!empty($ksu_details)){
        foreach ($ksu_details as $key => $value) {
            $Ksu = $value['KsuDetail'];
            $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
?>
<tr class="child child-<?php echo $Ksu['id'];?>" rel="<?php echo $Ksu['id'];?>">
    <td>
        <?php
            echo $value['Ksu']['no_doc'];

            $keyd = (isset($this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_detail_id']) && !empty($this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_detail_id'])) ? $this->request->data['KsuPaymentDetail'][$Ksu['id']]['ksu_detail_id'] : '';

            echo $this->Form->input('KsuPaymentDetail.ksu_detail_id.'.$keyd, array(
                'type' => 'hidden',
                'value' => $keyd
            ));
        ?>
    </td>
    <td>
        <?php
            echo date('d F Y', strtotime($value['Ksu']['tgl_ksu']));
        ?>
    </td>
    <td class="data-no-ttuj">
        <?php
            if(!empty($value['Ttuj']['no_ttuj'])){
                echo $value['Ttuj']['no_ttuj'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td>
        <?php
                echo $driver_name;
        ?>
    </td>
    <td class="data-from-city">
        <?php
            if(!empty($value['Ttuj']['nopol'])){
                echo $value['Ttuj']['nopol'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="data-to-city">
        <?php
            if(!empty($value['Perlengkapan']['name'])){
                echo $value['Perlengkapan']['name'];
            }else{
                echo '-';
            }
        ?>
    </td>
    <td class="text-right">
        <?php
            echo $this->Number->currency($value['KsuDetail']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));

            echo $this->Form->hidden('KsuPaymentDetail.total_biaya_klaim.'.$value['KsuDetail']['id'], array(
                'class' => 'ksu-price-payment',
                'value' => (!empty($value['KsuDetail']['total_price'])) ? $value['KsuDetail']['total_price'] : 0
            ));
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
                'class' => 'form-control input_price document-pick-price text-right',
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
<tr id="field-grand-total-document">
    <td align="right" colspan="8"><?php echo $this->Html->tag('strong', __('Grand Total'));?></td>
    <td align="right" id="grand-total-document">
        <?php 
            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
        ?>
    </td>
    <td>&nbsp;</td>
</tr>