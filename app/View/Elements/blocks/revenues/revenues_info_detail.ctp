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
                        if(!empty($detail['RevenueDetail']['price_unit'])){
                            $price = $detail['RevenueDetail']['price_unit'];
                        }else{
                            $link = $this->Html->link(__('disini'), 
                                array('controller' => 'settings', 'action' => 'tarif_angkutan_add'),
                                array('target' => 'blank')
                            );
                            $price = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
                        }
                        
                ?>
                <tr>
                    <td class="city-data">
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
                            if(!empty($detail['RevenueDetail']['TipeMotor']['name'])){
                                echo $detail['RevenueDetail']['TipeMotor']['name'];
                            }else{
                                echo ' - ';
                            }

                            echo $this->Form->hidden('RevenueDetail.tipe_motor_id.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control input_number',
                                'required' => false,
                                'value' => (!empty($detail['RevenueDetail']['tipe_motor_id'])) ? $detail['RevenueDetail']['tipe_motor_id'] : 0
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
                                'max' => $detail['TtujTipeMotor']['qty']
                            ));
                        ?>
                    </td>
                    <td class="price-data">
                        <?php 
                            if(is_numeric($price)){
                                echo $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                            }else{
                                if(!empty($tarif_angkutan['tarif'])){
                                    echo $this->Number->format($tarif_angkutan['tarif'], Configure::read('__Site.config_currency_code'), array('places' => 0));;
                                }else{
                                    echo $price;
                                }
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
                            if(!empty($price) && is_numeric($price) && !empty($qty) && empty($tarif_angkutan)){
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
                <tr class="additional-input-revenue" id="ppn-grand-total-revenue">
                    <td align="right" colspan="6" class="relative">
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
                            $ppn = 0;
                            if(!empty($total) && !empty($this->request->data['Revenue']['ppn'])){
                                $ppn = $total * ($this->request->data['Revenue']['ppn'] / 100);
                            }
                            echo $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="additional-input-revenue" id="pph-grand-total-revenue">
                    <td align="right" colspan="6" class="relative">
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
                            $pph = 0;
                            if(!empty($total) && !empty($this->request->data['Revenue']['pph'])){
                                $pph = $total * ($this->request->data['Revenue']['pph'] / 100);
                            }
                            echo $this->Number->currency($pph, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="all-grand-total-revenue">
                    <td align="right" colspan="6"><?php echo __('Total');?></td>
                    <td align="right" id="all-total-revenue">
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