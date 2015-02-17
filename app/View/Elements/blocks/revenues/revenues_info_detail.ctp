<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Revenue'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="15%" class="text-top"><?php echo __('Tujuan');?></th>
                    <th width="15%" class="text-top"><?php echo __('No. DO');?></th>
                    <th width="15%" class="text-top"><?php echo __('No. SJ');?></th>
                    <th width="15%" class="text-top"><?php echo __('Group Motor');?></th>
                    <th width="5%" class="text-top"><?php echo __('Jumlah Unit');?></th>
                    <th width="5%" class="text-top text-center"><?php echo __('Charge');?></th>
                    <th class="text-top text-center"><?php printf(__('Harga Unit'), Configure::read('__Site.config_currency_code'));?></th>
                    <th class="text-top text-center"><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
                </tr>
            </thead>
            <tbody class="tipe-motor-table">
                <?php
                        // $total = !empty($this->request->data['Revenue']['total_without_tax'])?$this->request->data['Revenue']['total_without_tax']:0;
                        $arr_duplicate = array();
                        $total = 0;

                        foreach ($data as $key => $detail) {
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

                            $flagShowPrice = false;
                            $flagTruck = false;

                            if( empty($tarifTruck) && $price['jenis_unit'] != 'per_truck' ) {
                                $flagShowPrice = true;
                            }

                            if( $price['jenis_unit'] == 'per_truck' ) {
                                $flagTruck = true;
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
                                    'value' => (!empty($detail['RevenueDetail']['city_id'])) ? $detail['RevenueDetail']['city_id'] : 0
                                ));

                                echo $this->Form->hidden('RevenueDetail.tarif_angkutan_id.', array(
                                    'required' => false,
                                    'value' => (!empty($price['tarif_angkutan_id'])) ? $price['tarif_angkutan_id'] : 0
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
                                ));
                        ?>
                    </td>
                    <td class="qty-tipe-motor-data" align="center">
                        <?php
                                $qty = '';

                                if( (isset($detail['RevenueDetail']['qty_unit']) && !empty($detail['RevenueDetail']['qty_unit'])) ){
                                    $qty = $detail['RevenueDetail']['qty_unit'];
                                }else if( !empty($detail['TtujTipeMotor']['qty']) ){
                                    $qty = $detail['TtujTipeMotor']['qty'];
                                }

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
                                    'value' =>  !empty($price['jenis_unit']) ? $price['jenis_unit'] : 0
                                ));
                        ?>
                    </td>
                    <td class="additional-charge-data" align="center">
                        <?php
                                echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                                    'label' => false,
                                    'class' => 'additional-charge',
                                    'required' => false,
                                    'hiddenField' => false,
                                    'checked' => (!empty($detail['RevenueDetail']['is_charge'])) ? true : false,
                                    'value' => 1,
                                    'disabled' => ( !empty($flagTruck) || !empty($is_charge) )?false:true,
                                ));
                                echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                                    'value' => !empty($is_charge)?1:0,
                                    'class' => 'additional-charge-hidden',
                                ));
                        ?>
                    </td>
                    <td class="price-data text-right">
                        <?php 

                                if( $flagShowPrice ) {
                                    if( is_array($price) ){
                                        $price = $price['tarif'];
                                    }

                                    if( is_numeric($price) ) {
                                        echo $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                                    } else {
                                        echo $price;
                                    }

                                    echo $this->Form->hidden('RevenueDetail.price_unit.', array(
                                        'type' => 'text',
                                        'label' => false,
                                        'class' => 'form-control price-unit-revenue input_number',
                                        'required' => false,
                                        'value' => $price,
                                    ));
                                }
                        ?>
                    </td>
                    <td class="total-price-revenue text-right">
                        <?php 
                                $value_price = 0;

                                if( $flagShowPrice ) {
                                    if(is_array($price)){
                                        if(!empty($price) && !empty($qty) && $price['jenis_unit'] == 'per_unit'){
                                            $value_price = $price['tarif'] * $qty;
                                        }else if(!empty($price) && !empty($qty) && $price['jenis_unit'] == 'per_truck'){
                                            $value_price = $price['tarif'];
                                        }
                                    } else {
                                        $value_price = $price * $qty;
                                    }
                                    $total += $value_price;
                                } else if ( !empty($detail['RevenueDetail']['is_charge']) && !empty($detail['RevenueDetail']['total_price_unit']) ) {
                                    $value_price = $detail['RevenueDetail']['total_price_unit'];
                                }
                                    
                                echo $this->Html->tag('span', $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
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
                                if( isset($price['tarif']) && is_numeric($price['tarif'])){
                                    $open_duplicate = false;

                                    if(empty($arr_duplicate[$detail['RevenueDetail']['group_motor_id']])){
                                        $arr_duplicate[$detail['RevenueDetail']['group_motor_id']] = true;
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
                                }
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr id="field-grand-total-revenue">
                    <td align="right" colspan="7"><?php echo __('Total')?></td>
                    <td align="right" id="grand-total-revenue">
                        <?php 
                                if( !empty($tarifTruck) ) {
                                    $total = $tarifTruck['tarif'];
                                }

                                echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                                echo $this->Form->hidden('Revenue.tarif_per_truck', array(
                                    'class' => 'tarif_per_truck',
                                    'value' => $total,
                                ));
                        ?>
                    </td>
                    <td>
                        <?php
                                echo $this->Form->hidden('total_temp', array(
                                    'id' => 'total_retail_revenue',
                                    'value' => $total
                                ));
                        ?>
                    </td>
                </tr>
                <tr id="field-additional-total-revenue" class="<?php echo ($flagTruck)?'':'hide'; ?>">
                    <td align="right" colspan="7"><?php echo __('Additional Charge')?></td>
                    <td align="right" id="additional-total-revenue">
                        <?php 
                                if( !empty($tarifTruck['addCharge']) ) {
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
                                    'label' => __('PPH'),
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