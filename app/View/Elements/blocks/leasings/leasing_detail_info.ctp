<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Leasing'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
                        'class' => 'add-custom-field btn btn-success btn-xs',
                        'action_type' => 'leasing',
                        'escape' => false
                    ));
            ?>
        </div>
        <table class="table table-hover" id="table-leasing">
            <thead>
                <tr>
                    <th><?php echo __('Truk');?></th>
                    <th width="20%"><?php echo __('Harga');?></th>
                    <th><?php echo __('Action');?></th>
                </tr>
            </thead>
            <tbody class="leasing-body">
                <?php
                        $count = 1;
                        if(!empty($this->request->data['LeasingDetail'])){
                            $count = count($this->request->data['LeasingDetail']);
                        }
                        $total = 0;

                        for ($i=0; $i < $count; $i++) { 
                            $detail = $this->request->data['LeasingDetail'][$i];
                            $price = $this->Common->filterEmptyField($detail, 'price');
                            $truck_id = $this->Common->filterEmptyField($detail, 'truck_id');
                            $nopol = $this->Common->filterEmptyField($detail, 'Truck', 'nopol');

                            $customPrice = $this->Common->convertPriceToString($price, 0);

                            if(!empty($customPrice)){
                                $total += $customPrice;
                            }
                ?>
                <tr classs="child child-<?php echo $i; ?>" rel="<?php echo $i; ?>">
                    <td>
                        <?php
                                if( !empty($value) && !empty($nopol) ) {
                                    echo $nopol;
                                } else {
                                    echo $this->Form->input('LeasingDetail.truck_id.', array(
                                        'options' => !empty($trucks)?$trucks:false,
                                        'label' => false,
                                        'empty' => __('Pilih Truk'),
                                        'class' => 'form-control',
                                        'required' => false,
                                        'value' => $truck_id,
                                    ));
                                }
                        ?>
                    </td>
                    <td align="right box-price">
                        <?php 
                            echo $this->Form->input('LeasingDetail.price.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-leasing-truck input_price input_number text-right',
                                'required' => false,
                                'value' => $customPrice
                            ));
                        ?>
                    </td>
                    <td class="action-table">
                        <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'leasing_first'
                            ));
                        ?>
                    </td>
                </tr>
                <?php
                        }
                ?>
                <tr id="field-grand-total-leasing">
                    <td align="right" colspan="1"><?php echo __('Total')?></td>
                    <td align="right" id="grand-total-leasing">
                        <?php 
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="1"><?php echo __('Pokok Angsuran *')?></td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('installment',array(
                                    'type' => 'text',
                                    'label'=> false, 
                                    'class'=>'form-control input_price total-installment text-right',
                                    'required' => false,
                                ));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="1"><?php echo __('Bunga *')?></td>
                    <td align="right">
                        <div class="input-group">
                            <?php 
                                    echo $this->Form->input('installment_rate',array(
                                        'type' => 'text',
                                        'label'=> false, 
                                        'class'=>'form-control input_price bunga-leasing text-right',
                                        'required' => false,
                                        'error' => false,
                                    ));
                                    echo $this->Html->tag('span', __('/Bulan'), array(
                                        'class' => 'input-group-addon'
                                    ));
                            ?>
                        </div>
                        <?php 
                                echo $this->Form->error('installment_rate');
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="1"><?php echo __('Denda')?></td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('denda',array(
                                    'type' => 'text',
                                    'label'=> false, 
                                    'class'=>'form-control input_price denda-leasing text-right',
                                    'required' => false,
                                ));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>