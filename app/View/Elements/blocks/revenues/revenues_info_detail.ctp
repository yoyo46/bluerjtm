<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Revenue'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="10%"><?php echo __('Tujuan');?></th>
                    <th width="15%"><?php echo __('No. DO');?></th>
                    <th width="15%"><?php echo __('No. SJ');?></th>
                    <th width="15%"><?php echo __('Tipe Motor');?></th>
                    <th width="5%"><?php echo __('Jumlah Unit');?></th>
                    <th><?php printf(__('Harga Unit'), Configure::read('__Site.config_currency_code'));?></th>
                    <th><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
                </tr>
            </thead>
            <tbody class="tipe-motor-table">
                <?php
                    $total = 0;
                    foreach ($data as $key => $detail) {
                        $price = $detail['RevenueDetail']['price_unit'];
                ?>
                <tr>
                    <td>
                        <?php
                            if(!empty($detail['RevenueDetail']['to_city_name'])){
                                echo $detail['RevenueDetail']['to_city_name'];
                            }else{
                                echo ' - ';
                            }

                            echo $this->Form->hidden('RevenueDetail.city_id.', array(
                                'required' => false,
                                'value' => (!empty($detail['RevenueDetail']['city_id'])) ? $detail['RevenueDetail']['city_id'] : 0
                            ));

                            echo $this->Form->hidden('RevenueDetail.ttuj_tipe_motor_id.', array(
                                'required' => false,
                                'value' => (!empty($detail['RevenueDetail']['ttuj_tipe_motor_id'])) ? $detail['RevenueDetail']['ttuj_tipe_motor_id'] : 0
                            ));
                        ?>
                    </td>
                    <td class="lku-color-motor" align="center">
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
                    <td>
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
                    <td>
                        <?php 
                            if(!empty($detail['RevenueDetail']['TipeMotor']['name'])){
                                echo $detail['RevenueDetail']['TipeMotor']['name'];
                            }else{
                                echo ' - ';
                            }

                            echo $this->Form->hidden('RevenueDetail.tipe_motor_id.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-unit-revenue input_number',
                                'required' => false,
                                'value' => (!empty($detail['RevenueDetail']['tipe_motor_id'])) ? $detail['RevenueDetail']['tipe_motor_id'] : 0
                            ));
                        ?>
                    </td>
                    <td class="qty-tipe-motor" align="center">
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
                                'max' => $detail['TtujTipeMotor']['qty']
                            ));
                        ?>
                    </td>
                    <td>
                        <?php 
                            if(is_numeric($price)){
                                echo $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                            }else{
                                echo $price;
                            }
                            echo $this->Form->hidden('RevenueDetail.price_unit.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-unit-revenue input_number',
                                'required' => false,
                                'value' => (is_numeric($price)) ? $price : 0
                            ));
                        ?>
                    </td>
                    <td class="total-price-revenue" align="right">
                        <?php 
                            $value_price = 0;
                            if(!empty($price) && !empty($qty) && empty($tarif_angkutan)){
                                $value_price = $price * $qty;
                                $total += $value_price;
                            }else{
                                $value_price = $tarif_angkutan['tarif'];
                            }

                            echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td class="handle-row">
                        <?php
                            if(is_numeric($price)){
                                echo $this->Html->link('<i class="fa fa-copy"></i>', 'javascript:', array(
                                    'class' => 'duplicate-row btn btn-warning btn-xs',
                                    'escape' => false,
                                    'title' => 'Duplicate'
                                ));
                            }
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr id="field-grand-total-revenue">
                    <td align="right" colspan="6"><?php echo __('Total')?></td>
                    <td align="right" id="grand-total-revenue">
                        <?php 
                            if(!empty($tarif_angkutan)){
                                $total = $tarif_angkutan['tarif'];
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