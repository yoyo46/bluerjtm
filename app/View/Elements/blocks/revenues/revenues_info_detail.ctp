<?php 
        $data_type = !empty($data_type)?$data_type:false;
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Muatan Truk'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="15%" class="text-top"><?php echo __('Tujuan');?></th>
                    <th width="13%" class="text-top"><?php echo __('No. DO');?></th>
                    <th width="13%" class="text-top"><?php echo __('No. SJ');?></th>
                    <th width="15%" class="text-top"><?php echo __('Group Motor');?></th>
                    <th width="7%" class="text-top"><?php echo __('Jumlah Unit');?></th>
                    <th width="5%" class="text-top text-center"><?php echo __('Charge');?></th>
                    <th width="15%" class="text-top text-center"><?php printf(__('Harga Unit'), Configure::read('__Site.config_currency_code'));?></th>
                    <th width="15%" class="text-top text-center"><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
                </tr>
            </thead>
            <tbody class="tipe-motor-table">
                <?php
                        $total = 0;
                        $totalQty = 0;
                        $flagTruck = false;

                        if( !empty($tarifTruck['jenis_unit']) ) {
                            $jenis_unit = $tarifTruck['jenis_unit'];
                        } else if( !empty($data_local['Revenue']['revenue_tarif_type']) ) {
                            $jenis_unit = $data_local['Revenue']['revenue_tarif_type'];
                        } else {
                            $jenis_unit = false;
                        }

                        if( $jenis_unit == 'per_truck' ) {
                            $flagTruck = true;
                        }

                        foreach ($data as $key => $detail) {
                            $qty = !empty($detail['RevenueDetail']['qty_unit'])?$detail['RevenueDetail']['qty_unit']:0;
                            $totalQty += $qty;
                            $tarif_angkutan_type = !empty($detail['RevenueDetail']['price_unit']['tarif_angkutan_type'])?$detail['RevenueDetail']['price_unit']['tarif_angkutan_type']:0;
                            $is_charge = !empty($detail['RevenueDetail']['is_charge'])?$detail['RevenueDetail']['is_charge']:false;
                            $jenis_unit_angkutan = !empty($detail['RevenueDetail']['price_unit']['jenis_unit'])?$detail['RevenueDetail']['price_unit']['jenis_unit']:0;
                            $flagShowPrice = false;
                            $total_price_unit = !empty($detail['RevenueDetail']['total_price_unit'])?$detail['RevenueDetail']['total_price_unit']:0;
                            $from_ttuj = !empty($detail['RevenueDetail']['from_ttuj'])?true:false;

                            if( $data_type == 'revenue-manual' ) {
                                $from_ttuj = false;
                            }

                            if( !empty($detail['RevenueDetail']['price_unit']) ){
                                $price = $detail['RevenueDetail']['price_unit'];
                            } else if( empty($id) ) {
                                $link = $this->Html->link(__('disini'), array(
                                    'controller' => 'settings', 
                                    'action' => 'tarif_angkutan_add'
                                ), array(
                                    'target' => 'blank'
                                ));
                                $price = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
                            } else {
                                $price = '';
                            }

                            if( ( $jenis_unit_angkutan != 'per_truck' && ( $tarif_angkutan_type != 'angkut' && !empty($is_charge) ) ) || $jenis_unit == 'per_unit' ) {
                                $flagShowPrice = true;
                            }
                ?>
                <tr rel="<?php echo $key; ?>" class="list-revenue">
                    <td class="city-data">
                        <?php
                                echo $this->Form->input('RevenueDetail.city_id.', array(
                                    'empty' => __('Pilih Kota Tujuan'),
                                    'options' => $toCities,
                                    'required' => false,
                                    'label' => false,
                                    'class' => 'form-control city-revenue-change',
                                    'value' => (!empty($detail['RevenueDetail']['city_id'])) ? $detail['RevenueDetail']['city_id'] : 0,
                                    'data-type' => $data_type,
                                ));

                                echo $this->Form->hidden('RevenueDetail.tarif_angkutan_id.', array(
                                    'required' => false,
                                    'class' => 'tarif_angkutan_id',
                                    'value' => (is_array($price) && !empty($price['tarif_angkutan_id'])) ? $price['tarif_angkutan_id'] : 0
                                ));

                                echo $this->Form->hidden('RevenueDetail.tarif_angkutan_type.', array(
                                    'required' => false,
                                    'class' => 'tarif_angkutan_type',
                                    'value' => ( is_array($price) && !empty($tarif_angkutan_type)) ? $tarif_angkutan_type : 'angkut'
                                ));
                        ?>
                    </td>
                    <td class="no-do-data" align="center">
                        <?php
                            echo $this->Form->input('RevenueDetail.no_do.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($detail['RevenueDetail']['no_do']) && !empty($detail['RevenueDetail']['no_do'])) ? $detail['RevenueDetail']['no_do'] : ''
                            ));
                        ?>
                    </td>
                    <td class="no-sj-data">
                        <?php 
                            echo $this->Form->input('RevenueDetail.no_sj.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($detail['RevenueDetail']['no_sj']) && !empty($detail['RevenueDetail']['no_sj'])) ? $detail['RevenueDetail']['no_sj'] : ''
                            ));
                        ?>
                    </td>
                    <td class="tipe-motor-data">
                        <?php 
                                echo $this->Form->input('RevenueDetail.group_motor_id.', array(
                                    'label' => false,
                                    'class' => 'form-control revenue-group-motor',
                                    'required' => false,
                                    'value' => (!empty($detail['RevenueDetail']['group_motor_id'])) ? $detail['RevenueDetail']['group_motor_id'] : 0,
                                    'options' => $groupMotors,
                                    'empty' => __('Pilih Group Motor'),
                                    'data-type' => $data_type,
                                ));
                        ?>
                    </td>
                    <td class="qty-tipe-motor-data" align="center">
                        <?php
                                echo $this->Form->input('RevenueDetail.qty_unit.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control revenue-qty input_number',
                                    'required' => false,
                                    'value' =>  $qty,
                                ));
                                echo $this->Form->hidden('RevenueDetail.payment_type.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'jenis_unit',
                                    'required' => false,
                                    'value' =>  !empty($jenis_unit_angkutan) ? $jenis_unit_angkutan : 'per_unit'
                                ));
                        ?>
                    </td>
                    <td class="additional-charge-data" align="center">
                        <?php
                                if( !empty($detail['RevenueDetail']['is_charge']) && empty($from_ttuj) ) {
                                    $checkedCharge = true;
                                } else if( empty($from_ttuj) ) {
                                    $checkedCharge = false;
                                } else {
                                    $checkedCharge = false;
                                }

                                if( ( !empty($flagTruck) || !empty($is_charge) ) && empty($from_ttuj) ) {
                                    $disabledCharge = false;
                                } else {
                                    $disabledCharge = true;
                                }

                                echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                                    'label' => false,
                                    'class' => 'additional-charge',
                                    'required' => false,
                                    'hiddenField' => false,
                                    'checked' => $checkedCharge,
                                    'value' => 1,
                                    'disabled' => $disabledCharge,
                                    'data-type' => $data_type,
                                ));
                                echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                                    'value' => $checkedCharge,
                                    'class' => 'additional-charge-hidden',
                                ));
                                echo $this->Form->hidden('RevenueDetail.from_ttuj.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control from-ttuj',
                                    'required' => false,
                                    'value' => $from_ttuj,
                                ));
                        ?>
                    </td>
                    <td class="price-data text-right">
                        <?php 
                                $spanPrice = '';
                                
                                if( $jenis_unit_angkutan != 'per_truck' ) {
                                    if( empty($is_charge) || $tarif_angkutan_type != 'angkut' ) {
                                        if(is_array($price)){
                                            $price = $price['tarif'];

                                            if( empty($price) && $jenis_unit == 'per_truck' ) {
                                                $spanPrice = '';
                                            } else {
                                                $spanPrice = $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                                            }
                                        }else{
                                            $spanPrice = $price;
                                        }
                                    } else if( is_string($price) ) {
                                        $spanPrice = $price;
                                    }
                                } else if( $jenis_unit_angkutan == 'per_truck' && $jenis_unit == 'per_unit' ) {
                                    $price = $total_price_unit;
                                } else {
                                    $price = '';
                                }

                                echo $this->Html->tag('span', $spanPrice);

                                echo $this->Form->hidden('RevenueDetail.price_unit.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control price-unit-revenue input_number',
                                    'required' => false,
                                    'value' => is_numeric($price)?$price:0,
                                ));
                        ?>
                    </td>
                    <td class="total-price-revenue text-right">
                        <?php 
                                $value_price = 0;

                                if( $flagShowPrice ) {
                                    if(is_array($price)){
                                        if(!empty($price) && !empty($qty) && $jenis_unit_angkutan == 'per_unit'){
                                            $value_price = $price['tarif'] * $qty;
                                        }else if(!empty($price) && !empty($qty) && $jenis_unit_angkutan == 'per_truck'){
                                            $value_price = $price['tarif'];
                                        }
                                    } else {
                                        if( $jenis_unit_angkutan == 'per_truck' ) {
                                            $value_price = $price;
                                        } else {
                                            $value_price = $price * $qty;
                                        }
                                    }
                                    $total += $value_price;
                                } else if ( !empty($detail['RevenueDetail']['is_charge']) && !empty($total_price_unit) ) {
                                    $value_price = $total_price_unit;
                                }
                                
                                if( empty($value_price) && $jenis_unit == 'per_truck' ) {
                                    $priceFormat = '';
                                } else {
                                    $priceFormat = $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                                }

                                echo $this->Html->tag('span', $priceFormat, array(
                                    'class' => 'total-revenue-perunit'
                                ));

                                echo $this->Form->hidden('RevenueDetail.total_price_unit.', array(
                                    'class' => 'total-price-perunit',
                                    'required' => false,
                                    'value' => $value_price
                                ));
                        ?>
                    </td>
                    <td class="handle-row">
                        <?php
                                // if( isset($price['tarif']) && is_numeric($price['tarif'])){
                                if( $data_type != 'revenue-manual' ) {
                                    $open_duplicate = false;

                                    if( !empty($from_ttuj) ){
                                        $open_duplicate = true;
                                    }

                                    if($open_duplicate){
                                        echo $this->Html->link('<i class="fa fa-copy"></i>', 'javascript:', array(
                                            'class' => 'duplicate-row btn btn-warning btn-xs',
                                            'escape' => false,
                                            'title' => 'Duplicate'
                                        ));
                                    }else{
                                        echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                                        'class' => 'delete-custom-field btn btn-danger btn-xs',
                                        'escape' => false,
                                        'action_type' => 'revenue_detail',
                                        'title' => __('hapus baris')
                                    ));
                                    }
                                } else {
                                    echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                                        'class' => 'delete-custom-field btn btn-danger btn-xs',
                                        'action_type' => 'revenue-detail',
                                        'title' => __('Hapus Muatan'),
                                        'rel' => $key,
                                        'escape' => false,
                                    ));
                                }
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tbody>
                <tr id="field-grand-total-revenue">
                    <?php 
                            echo $this->Html->tag('td', __('Total Muatan'), array(
                                'align' => 'right',
                                'colspan' => 4,
                            ));
                            echo $this->Html->tag('td', $totalQty, array(
                                'align' => 'center',
                                'id' => 'qty-revenue',
                            ));
                            echo $this->Html->tag('td', __('Total'), array(
                                'align' => 'right',
                                'colspan' => 2,
                            ));

                            if( !empty($tarifTruck) ) {
                                $total = $tarifTruck['tarif'];
                            }

                            echo $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                'align' => 'right',
                                'id' => 'grand-total-revenue',
                            ));
                            echo $this->Html->tag('td', '&nbsp;');

                            echo $this->Form->hidden('total_temp', array(
                                'id' => 'total_retail_revenue',
                                'value' => $total
                            ));
                            echo $this->Form->hidden('Revenue.tarif_per_truck', array(
                                'class' => 'tarif_per_truck',
                                'value' => $total,
                            ));
                    ?>
                </tr>
                <tr id="field-additional-total-revenue" class="<?php echo ($flagTruck)?'':'hide'; ?>">
                    <td align="right" colspan="7"><?php echo __('Additional Charge')?></td>
                    <td align="right" id="additional-total-revenue">
                        <?php 
                                if( !empty($this->request->data['Revenue']['additional_charge']) ) {
                                    $addCharge = $this->request->data['Revenue']['additional_charge'];
                                    $total += $addCharge;
                                    echo $this->Number->currency($addCharge, Configure::read('__Site.config_currency_code'), array('places' => 0));   
                                } else if( !empty($tarifTruck['addCharge']) ) {
                                    $total += $tarifTruck['addCharge'];
                                    echo $this->Number->currency($tarifTruck['addCharge'], Configure::read('__Site.config_currency_code'), array('places' => 0));
                                } else {
                                    echo 0;
                                }
                        ?>
                    </td>
                </tr>
                <tr class="additional-input-revenue" id="ppn-grand-total-revenue">
                    <td align="right" colspan="7" class="relative">
                        <?php 
                            echo $this->Form->input('Revenue.ppn', array(
                                'type' => 'text',
                                'label' => __('PPN'),
                                'class' => 'input_number revenue-ppn',
                                'required' => false,
                                'div' => false
                            )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
                        ?>
                    </td>
                    <td align="right" id="ppn-total-revenue">
                        <?php 
                                $ppn = !empty($this->request->data['Revenue']['ppn'])?$this->request->data['Revenue']['ppn']:0;
                                $ppn = $this->Common->calcFloat($total, $ppn);
                                echo $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="additional-input-revenue" id="pph-grand-total-revenue">
                    <td align="right" colspan="7" class="relative">
                        <?php 
                                echo $this->Form->input('Revenue.pph', array(
                                    'type' => 'text',
                                    'label' => __('PPh'),
                                    'class' => 'input_number revenue-pph',
                                    'required' => false,
                                    'div' => false
                                )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
                        ?>
                    </td>
                    <td align="right" id="pph-total-revenue">
                        <?php 
                                $pph = !empty($this->request->data['Revenue']['pph'])?$this->request->data['Revenue']['pph']:0;
                                $pph = $this->Common->calcFloat($total, $pph);
                                echo $this->Number->currency($pph, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="all-grand-total-revenue">
                    <td align="right" colspan="7"><?php echo __('Total');?></td>
                    <td align="right" id="all-total-revenue">
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
            </tbody>
        </table>
    </div>
</div>
<?php 
        echo $this->Form->hidden('Revenue.revenue_tarif_type', array(
            'class' => 'revenue_tarif_type',
        ));
        echo $this->Form->hidden('Revenue.additional_charge', array(
            'class' => 'additional_charge',
        ));
?>