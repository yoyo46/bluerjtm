<?php 
        $data = $this->request->data;
        $currency = Configure::read('__Site.config_currency_code');
        $action_type = !empty($action_type)?$action_type:false;

        $dataColumns = array(
            'tujuan' => array(
                'name' => __('Tujuan'),
                'class' => 'text-top',
                'style' => 'width:15%;',
            ),
            'do' => array(
                'name' => __('No. DO'),
                'class' => 'text-top',
                'style' => 'width:13%;',
            ),
            'sj' => array(
                'name' => __('No. SJ'),
                'class' => 'text-top',
                'style' => 'width:13%;',
            ),
            'group' => array(
                'name' => __('Group Motor'),
                'class' => 'text-top',
                'style' => 'width:15%;',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-top text-center',
                'style' => 'width:7%;',
            ),
            'price' => array(
                'name' => __('Tarif'),
                'class' => 'text-top text-center',
                'style' => 'width:12%;',
            ),
            'charge' => array(
                'name' => __('Charge'),
                'class' => 'text-top text-center',
                'style' => 'width:5%;',
            ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-top text-center',
                'style' => 'width:12%;',
            ),
            'action' => array(
                'name' => '&nbsp;',
                'style' => 'width:7%;',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Muatan Truk'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody class="tipe-motor-table">
                <?php
                        $total = 0;
                        $totalQty = 0;

                        if( !empty($revenueDetail) ) {
                            foreach ($revenueDetail as $key => $detail) {
                                $no_do = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'no_do');
                                $no_sj = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'no_sj');
                                $group_motor_id = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'group_motor_id');
                                $qty = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'qty_unit', 0);
                                $is_charge = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'is_charge');
                                $price_unit = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'price_unit');
                                $total_price_unit = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'total_price_unit');
                                $city_id = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'city_id');
                                $tarif_angkutan_id = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'tarif_angkutan_id');
                                $jenis_unit = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'payment_type', 'per_unit');
                                $tarif_angkutan_type = $this->Common->filterEmptyField($detail, 'RevenueDetail', 'tarif_angkutan_type', 'angkut');
                                $price = 0;
                                
                                $tarif_extra = Common::hashEmptyField($detail, 'RevenueDetail.tarif_extra', 0);
                                $tarif_extra_min_capacity = Common::hashEmptyField($detail, 'RevenueDetail.tarif_extra_min_capacity', 0);
                                $tarif_extra_per_unit = Common::hashEmptyField($detail, 'RevenueDetail.tarif_extra_per_unit', 0);

                                if( !empty($is_charge) ) {
                                    $checkedCharge = true;

                                    if( $jenis_unit == 'per_truck' ) {
                                        $price = $total_price_unit;
                                    } else {
                                        $price = $price_unit * $qty;
                                    }


                                    if( $tarif_extra_min_capacity != 0 ) {
                                        if( $qty > $tarif_extra_min_capacity ) {
                                            if( $tarif_extra_per_unit != 0 ) {
                                                $sisa_muatan = $qty - $tarif_extra_min_capacity;
                                                $tarif_extra = $tarif_extra * $sisa_muatan;
                                            }

                                            $price = $price + $tarif_extra;
                                        }
                                    }
                                } else {
                                    $checkedCharge = false;
                                }

                                $priceCustom = $this->Common->getFormatPrice($price);

                                $totalQty += $qty;
                                $total += $price;

                                if( empty($tarif_angkutan_id) ){
                                    $link = $this->Html->link(__('disini'), array(
                                        'controller' => 'settings',
                                        'action' => 'tarif_angkutan_add'
                                    ), array(
                                        'target' => 'blank'
                                    ));
                                    $price_msg = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
                                } else {
                                    $price_msg = false;
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
                                    'class' => 'form-control city-revenue-change chosen-select',
                                    'value' => $city_id,
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.city_id');

                                echo $this->Form->hidden('RevenueDetail.tarif_angkutan_id.', array(
                                    'required' => false,
                                    'class' => 'tarif_angkutan_id',
                                    'value' => $tarif_angkutan_id
                                ));

                                echo $this->Form->hidden('RevenueDetail.tarif_angkutan_type.', array(
                                    'required' => false,
                                    'class' => 'tarif_angkutan_type',
                                    'value' => $tarif_angkutan_type,
                                ));

                                echo $this->Form->hidden('RevenueDetail.tarif_extra.', array(
                                    'required' => false,
                                    'class' => 'tarif_extra',
                                    'value' => $tarif_extra,
                                ));
                                echo $this->Form->hidden('RevenueDetail.tarif_extra_min_capacity.', array(
                                    'required' => false,
                                    'class' => 'tarif_extra_min_capacity',
                                    'value' => $tarif_extra_min_capacity,
                                ));
                                echo $this->Form->hidden('RevenueDetail.tarif_extra_per_unit.', array(
                                    'required' => false,
                                    'class' => 'tarif_extra_per_unit',
                                    'value' => $tarif_extra_per_unit,
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
                                    'value' => $no_do,
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.no_do');
                        ?>
                    </td>
                    <td class="no-sj-data">
                        <?php 
                                echo $this->Form->input('RevenueDetail.no_sj.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control',
                                    'required' => false,
                                    'value' => $no_sj,
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.no_sj');
                        ?>
                    </td>
                    <td class="tipe-motor-data">
                        <?php 
                                echo $this->Form->input('RevenueDetail.group_motor_id.', array(
                                    'label' => false,
                                    'class' => 'form-control revenue-group-motor',
                                    'required' => false,
                                    'value' => $group_motor_id,
                                    'options' => $groupMotors,
                                    'empty' => __('Pilih Group Motor'),
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.group_motor_id');
                        ?>
                    </td>
                    <td class="qty-tipe-motor-data" align="center">
                        <?php
                                echo $this->Form->input('RevenueDetail.qty_unit.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control revenue-qty input_number text-center',
                                    'required' => false,
                                    'value' =>  $qty,
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.qty_unit');
                                echo $this->Form->hidden('RevenueDetail.payment_type.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'jenis_unit',
                                    'required' => false,
                                    'value' =>  $jenis_unit,
                                ));
                        ?>
                    </td>
                    <td class="price-data text-right">
                        <?php 
                                $inputType = 'hidden';
                                $inputClass = '';

                                if( $action_type == 'manual' ) {
                                    $inputType = 'text';
                                    $inputClass = 'input_price text-right';
                                } else if( !empty($price_msg) ) {
                                    echo $this->Html->tag('span', $price_msg);
                                } else if( !empty($is_charge) ) {
                                    echo $this->Html->tag('span', $this->Common->getFormatPrice($price_unit));
                                }

                                echo $this->Form->input('RevenueDetail.price_unit.', array(
                                    'type' => $inputType,
                                    'label' => false,
                                    'class' => 'form-control price-unit-revenue '.$inputClass,
                                    'required' => false,
                                    'value' => $price_unit,
                                ));
                                echo $this->Form->error('RevenueDetail.'.$key.'.price_unit');
                        ?>
                    </td>
                    <td class="additional-charge-data" align="center">
                        <?php
                                echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                                    'label' => false,
                                    'class' => 'additional-charge',
                                    'required' => false,
                                    'hiddenField' => false,
                                    'checked' => $checkedCharge,
                                    'value' => 1,
                                ));
                                echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                                    'value' => $checkedCharge,
                                    'class' => 'additional-charge-hidden',
                                ));
                        ?>
                    </td>
                    <td class="total-price-revenue text-right">
                        <?php 
                                // if( $action_type == 'manual' && $jenis_unit == 'per_truck' && !empty($is_charge) ) {
                                //     echo $this->Form->input('RevenueDetail.total_price_unit.', array(
                                //         'type' => 'text',
                                //         'label' => false,
                                //         'class' => 'form-control total-revenue-perunit input_price text-right',
                                //         'required' => false,
                                //         'value' => $price,
                                //     ));
                                //     echo $this->Form->error('RevenueDetail.'.$key.'.total_price_unit');
                                // } else {
                                    if( !empty($is_charge) ) {
                                        echo $this->Html->tag('span', $priceCustom, array(
                                            'class' => 'total-revenue-perunit-text'
                                        ));
                                    }
                                    
                                    echo $this->Form->hidden('RevenueDetail.total_price_unit.', array(
                                        'class' => 'total-revenue-perunit',
                                        'value' => $price,
                                    ));
                                // }
                        ?>
                    </td>
                    <td class="handle-row action text-center">
                        <?php
                                echo $this->Html->link($this->Common->icon('copy'), '#', array(
                                    'class' => 'duplicate-row btn btn-warning btn-xs',
                                    'escape' => false,
                                    'title' => 'Duplicate'
                                ));
                                echo $this->Html->link($this->Common->icon('times'), '#', array(
                                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                                    'escape' => false,
                                    'action_type' => 'revenue_detail',
                                    'title' => __('Hapus Muatan')
                                ));
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        }
                ?>
            </tbody>
            <?php                     
                    $ppn = $this->Common->filterEmptyField($data, 'Revenue', 'ppn_total', 0);
                    $grandtotal = $total + $ppn;

                    $totalCustom = $this->Common->getFormatPrice($total);
                    $grandtotalCustom = $this->Common->getFormatPrice($grandtotal);
            ?>
            <tfoot>
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

                            echo $this->Html->tag('td', $totalCustom, array(
                                'align' => 'right',
                                'id' => 'grand-total-document',
                            ));
                            echo $this->Html->tag('td', '&nbsp;');
                    ?>
                </tr>
                <tr class="additional-input-revenue" id="ppn-grand-total-revenue">
                    <td align="right" colspan="7" class="relative">
                        <?php 
                            echo $this->Form->input('Revenue.ppn', array(
                                'type' => 'text',
                                'label' => __('PPN'),
                                'class' => 'input_number ppn-persen',
                                'required' => false,
                                'div' => false
                            )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
                        ?>
                    </td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('Revenue.ppn_total', array(
                                    'type' => 'text',
                                    'id' => 'ppn-total',
                                    'label' => false,
                                    'class' => 'input_price_coma',
                                    'required' => false,
                                    'div' => false,
                                    'data-decimal' => '0',
                                ));
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
                                    'class' => 'input_number pph-persen',
                                    'required' => false,
                                    'div' => false
                                )).$this->Html->tag('span', '%', array('class' => 'notation-input'));
                        ?>
                    </td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('Revenue.pph_total', array(
                                    'type' => 'text',
                                    'id' => 'pph-total',
                                    'label' => false,
                                    'class' => 'input_price_coma',
                                    'required' => false,
                                    'div' => false,
                                    'data-decimal' => '0',
                                ));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="all-grand-total-revenue">
                    <td align="right" colspan="7"><?php echo __('Total');?></td>
                    <td align="right" id="all-total">
                        <?php 
                                echo $grandtotalCustom;
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>