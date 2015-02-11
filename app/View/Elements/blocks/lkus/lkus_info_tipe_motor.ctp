<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail LKU'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
                        'class' => 'add-custom-field btn btn-success btn-xs',
                        'action_type' => 'lku_tipe_motor',
                        'escape' => false
                    ));
            ?>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo __('Tipe Motor');?></th>
                    <th><?php echo __('Warna');?></th>
                    <th><?php echo __('No. Rangka');?></th>
                    <th><?php echo __('Keterangan');?></th>
                    <th><?php echo __('Jumlah Unit');?></th>
                    <th><?php printf(__('Biaya Klaim (%s)'), Configure::read('__Site.config_currency_code'));?></th>
                    <th><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
                    <th><?php echo __('Action');?></th>
                </tr>
            </thead>
            <tbody class="tipe-motor-table">
                <?php
                    $count = 1;
                    if(!empty($this->request->data['LkuDetail'])){
                        $count = count($this->request->data['LkuDetail']);
                    }
                    $total = 0;
                    for ($i=0; $i < $count; $i++) { 
                        $price = (isset($this->request->data['LkuDetail'][$i]['price']) && !empty($this->request->data['LkuDetail'][$i]['price'])) ? $this->request->data['LkuDetail'][$i]['price'] : 0;
                        $qty = (isset($this->request->data['LkuDetail'][$i]['qty']) && !empty($this->request->data['LkuDetail'][$i]['qty'])) ? $this->request->data['LkuDetail'][$i]['qty'] : 0;
                ?>
                <tr>
                    <td>
                        <?php
                            echo $this->Form->input('LkuDetail.tipe_motor_id.', array(
                                'options' => !empty($tipe_motor_list)?$tipe_motor_list:false,
                                'label' => false,
                                'empty' => __('Pilih Tipe Motor'),
                                'class' => 'lku-choose-tipe-motor form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['LkuDetail'][$i]['tipe_motor_id']) && !empty($this->request->data['LkuDetail'][$i]['tipe_motor_id'])) ? $this->request->data['LkuDetail'][$i]['tipe_motor_id'] : ''
                            ));
                        ?>
                    </td>
                    <td class="lku-color-motor" align="center">
                        <?php
                            if( isset($this->request->data['LkuDetail'][$i]['ColorMotor']['name']) && !empty($this->request->data['LkuDetail'][$i]['ColorMotor']['name']) ){
                                echo $this->request->data['LkuDetail'][$i]['ColorMotor']['name'];
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $this->Form->input('LkuDetail.no_rangka.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['LkuDetail'][$i]['no_rangka']) && !empty($this->request->data['LkuDetail'][$i]['no_rangka'])) ? $this->request->data['LkuDetail'][$i]['no_rangka'] : ''
                            ));
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $this->Form->input('LkuDetail.note.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['LkuDetail'][$i]['note']) && !empty($this->request->data['LkuDetail'][$i]['note'])) ? $this->request->data['LkuDetail'][$i]['note'] : ''
                            ));
                        ?>
                    </td>
                    <td class="qty-tipe-motor" align="center">
                        <?php
                            if(!empty($qty) && !empty($this->request->data['LkuDetail'][$i]['TipeMotor']['TtujTipeMotor']['qty'])){
                                $options = array();

                                for ($a=1; $a <= $this->request->data['LkuDetail'][$i]['TipeMotor']['TtujTipeMotor']['qty'] ; $a++) { 
                                    $options[$a] = $a;
                                }

                                echo $this->Form->input('LkuDetail.qty.', array(
                                    'options' => $options,
                                    'empty' => __('Pilih Jumlah Klaim'),
                                    'class' => 'claim-number form-control',
                                    'div' => false,
                                    'label' => false,
                                    'value' => $qty
                                ));
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td align="right">
                        <?php 
                            echo $this->Form->input('LkuDetail.price.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-tipe-motor input_number',
                                'required' => false,
                                'value' => $price
                            ));
                        ?>
                    </td>
                    <td class="total-price-claim" align="right">
                        <?php 
                            $value_price = 0;
                            if(!empty($price) && !empty($qty)){
                                $value_price = $price * $qty;
                                $total += $value_price;
                            }

                            echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'lku_first'
                            ));
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr id="field-grand-total-lku">
                    <td align="right" colspan="6"><?php echo __('Total Biaya Klaim')?></td>
                    <td align="right" id="grand-total-lku">
                        <?php 
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="hide">
    <table>
        <tbody id="first-row">
            <tr>
                <td>
                    <?php
                        echo $this->Form->input('LkuDetail.tipe_motor_id.', array(
                            'options' => $tipe_motor_list,
                            'label' => false,
                            'empty' => __('Pilih Tipe Motor'),
                            'class' => 'lku-choose-tipe-motor form-control',
                            'required' => false
                        ));
                    ?>
                </td>
                <td class="lku-color-motor" align="center">-</td>
                <td>
                    <?php 
                        echo $this->Form->input('LkuDetail.no_rangka.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            'required' => false
                        ));
                    ?>
                </td>
                <td>
                    <?php 
                        echo $this->Form->input('LkuDetail.note.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            'required' => false,
                        ));
                    ?>
                </td>
                <td class="qty-tipe-motor" align="center">-</td>
                <td align="right">
                    <?php 
                        echo $this->Form->input('LkuDetail.price.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control price-tipe-motor input_number',
                            'required' => false
                        ));
                    ?>
                </td>
                <td class="total-price-claim" align="right"></td>
                <td>
                    <?php
                        echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                            'class' => 'delete-custom-field btn btn-danger btn-xs',
                            'escape' => false,
                            'action_type' => 'lku_first'
                        ));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>