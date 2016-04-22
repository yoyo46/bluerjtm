<?php 
        $count = 1;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'LeasingDetail');
?>
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
                    <th width="15%"><?php echo __('Truk');?></th>
                    <th width="20%"><?php echo __('Note');?></th>
                    <th width="15%"><?php echo __('Group Asset');?></th>
                    <th width="20%"><?php echo __('Harga');?></th>
                    <th width="5%"><?php echo __('Action');?></th>
                </tr>
            </thead>
            <tbody class="leasing-body">
                <?php
                        $total = 0;

                        if(!empty($dataDetail)){
                            foreach ($dataDetail as $key => $value) {
                                $price = $this->Common->filterEmptyField($value, 'LeasingDetail', 'price');

                                $total += $price;
                                $customTotal = $this->Common->getFormatPrice($price);

                                echo $this->element('blocks/leasings/tables/items', array(
                                    'value' => $value,
                                    'total' => $customTotal,
                                    'idx' => $key,
                                ));
                            }
                        } else {
                            echo $this->element('blocks/leasings/tables/items', array(
                                'idx' => 0,
                            ));
                        }

                        echo $this->Form->hidden('total_leasing',array(
                            'id'=>'hid-total-leasing',
                            'value' => $total,
                        ));
                ?>
            </tbody>
            <tfoot>
                <tr id="field-grand-total-leasing">
                    <td align="right" colspan="3"><?php echo __('Total')?></td>
                    <td align="right" id="grand-total-leasing">
                        <?php 
                                echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="3"><?php echo __('Pokok Angsuran *')?></td>
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
                    <td align="right" colspan="3"><?php echo __('Bunga *')?></td>
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
                                    echo $this->Html->tag('span', __('/ Bln'), array(
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
                    <td align="right" colspan="3"><?php echo __('Denda')?></td>
                    <td align="right">
                        <div class="input-group">
                            <?php 
                                    echo $this->Form->input('denda',array(
                                        'type' => 'text',
                                        'label'=> false, 
                                        'class'=>'form-control input_price denda-leasing text-right',
                                        'required' => false,
                                        'error' => false,
                                    ));
                                    echo $this->Html->tag('span', __('%'), array(
                                        'class' => 'input-group-addon'
                                    ));
                            ?>
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>