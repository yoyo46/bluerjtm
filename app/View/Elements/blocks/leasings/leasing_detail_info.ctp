<div class="box box-primary">
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
        <table class="table table-hover table-leasing">
            <thead>
                <tr>
                    <th><?php echo __('Truk');?></th>
                    <th><?php echo __('Harga');?></th>
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
                        $price = (isset($this->request->data['LeasingDetail'][$i]['price']) && !empty($this->request->data['LeasingDetail'][$i]['price'])) ? $this->request->data['LeasingDetail'][$i]['price'] : 0;

                        if(!empty($price)){
                            $total += $price;
                        }
                ?>
                <tr>
                    <td>
                        <?php
                            echo $this->Form->input('LeasingDetail.truck_id.', array(
                                'options' => !empty($trucks)?$trucks:false,
                                'label' => false,
                                'empty' => __('Pilih Truk'),
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['LeasingDetail'][$i]['truck_id']) && !empty($this->request->data['LeasingDetail'][$i]['truck_id'])) ? $this->request->data['LeasingDetail'][$i]['truck_id'] : ''
                            ));
                        ?>
                    </td>
                    <td align="right box-price">
                        <?php 
                            echo $this->Form->input('LeasingDetail.price.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-leasing-truck input_price input_number',
                                'required' => false,
                                'value' => $price
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
            </tbody>
        </table>
    </div>
</div>