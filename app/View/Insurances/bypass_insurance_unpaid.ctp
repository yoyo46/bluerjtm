<?php 
        $title = !empty($title)?$title:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'insurances',
                'action' => 'search',
                'insurance_unpaid',
                'bypass',
                'bypass' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No. Polis'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama Asuransi'),
                        'class'=>'form-control',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-parent' => true,
                'title' => $title,
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'insurances',
                'action' => 'insurance_unpaid',
                'bypass' => true,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'title' => $title,
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>
<div class="box-body table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <?php
                        $input_all = $this->Form->checkbox('checkbox_all', array(
                            'class' => 'checkAll'
                        ));
                        echo $this->Html->tag('th', $input_all);
                ?>
                <th><?php echo __('No. Polis');?></th>
                <th><?php echo __('Asuransi');?></th>
                <th><?php echo __('Nama Tertanggung');?></th>
                <th class="text-center"><?php echo __('Tgl Polis');?></th>
                <th class="text-center"><?php echo __('Total Premi');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'Insurance.id');
                            $nodoc = Common::hashEmptyField($value, 'Insurance.nodoc');
                            $name = Common::hashEmptyField($value, 'Insurance.name');
                            $to_name = Common::hashEmptyField($value, 'Insurance.to_name');
                            $start_date = Common::hashEmptyField($value, 'Insurance.start_date');
                            $end_date = Common::hashEmptyField($value, 'Insurance.end_date');
                            $status = Common::hashEmptyField($value, 'Insurance.status');
                            $grandtotal = Common::hashEmptyField($value, 'Insurance.grandtotal', 0, array(
                                'type' => 'currency',
                            ));

                            $date = Common::getCombineDate($start_date, $end_date);
            ?>
            <tr class="child-search child-search-<?php echo $id;?>" rel="<?php echo $id;?>">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('insurance_insurance_id.', array(
                            'class' => 'check-option',
                            'value' => $id,
                        )), array(
                            'class' => 'checkbox-detail',
                        ));

                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $name);
                        echo $this->Html->tag('td', $to_name);

                        echo $this->Html->tag('td', $date, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $this->Html->tag('span', $grandtotal, array(
                            'class' => 'on-remove',
                        )).$this->Form->input('InsurancePaymentDetail.total.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price hide on-show text-right leasing-trigger leasing-total',
                            'value' => $grandtotal,
                        )), array(
                            'class' => 'text-right',
                        ));
                ?>
                <td class="action-search hide text-center">
                    <?php
                            echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'document_first'
                            ));
                    ?>
                </td>
            </tr>
            <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                        'colspan' => 7,
                        'class' => 'text-center alert alert-warning',
                    )));
                }
            ?>
        </tbody>
    </table>
</div>
<?php
        if(!empty($values)){
            echo $this->element('pagination', array(
                'options' => array(
                    'class' => 'ajaxModal',
                    'title' => $title,
                ),
            ));
        }
?>