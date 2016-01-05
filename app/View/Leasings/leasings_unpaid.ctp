<?php 
        $title = !empty($title)?$title:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'leasings',
                'action' => 'search',
                'leasings_unpaid',
                'vendor_id' => $vendor_id,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tgl Jatuh Tempo'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tgl Jatuh Tempo')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No Kontrak'),
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
                'data-action' => $data_action,
                'title' => $title,
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'leasings',
                'action' => 'leasings_unpaid',
                $vendor_id,
                'admin' => false,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'data-action' => $data_action,
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
                <th><?php echo __('No Kontrak');?></th>
                <th><?php echo __('Tgl Kontrak');?></th>
                <th><?php echo __('Tgl Jth Tempo');?></th>
                <th class="text-center"><?php echo __('Pokok');?></th>
                <th class="text-center"><?php echo __('Bunga');?></th>
                <!-- <th class="text-center"><?php // echo __('Denda');?></th> -->
                <th class="text-center"><?php echo __('Total');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Leasing', 'id');
                            $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');
                            $leasing_date = $this->Common->filterEmptyField($value, 'Leasing', 'paid_date');
                            $installment = $this->Common->filterEmptyField($value, 'LeasingInstallment', 'installment');
                            $installment_rate = $this->Common->filterEmptyField($value, 'Leasing', 'installment_rate');
                            $total = $installment+$installment_rate;

                            // $denda = $this->Common->filterEmptyField($value, 'Leasing', 'denda');
                            // $total = $installment+$installment_rate+$denda;

                            $leasing_installment_id = $this->Common->filterEmptyField($value, 'LeasingInstallment', 'id');
                            $paid_date = $this->Common->filterEmptyField($value, 'LeasingInstallment', 'paid_date');

                            $customLeasingDate = $this->Common->formatDate($leasing_date, 'd/m/Y');
                            $customPaidDate = $this->Common->formatDate($paid_date, 'd/m/Y');
                            $customInstallment = $this->Common->getFormatPrice($installment);
                            $customInstallmentRate = $this->Common->getFormatPrice($installment_rate);
                            $customTotal = $this->Common->getFormatPrice($total);
                            // $customDenda = $this->Common->getFormatPrice($denda);
            ?>
            <tr class="child-search child-search-<?php echo $leasing_installment_id;?>" rel="<?php echo $leasing_installment_id;?>">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('leasing_installment_id.', array(
                            'class' => 'check-option',
                            'value' => $leasing_installment_id,
                        )), array(
                            'class' => 'checkbox-detail',
                        ));

                        $contentTd = $no_contract;
                        $contentTd .= $this->Form->input('LeasingPaymentDetail.leasing_installment_id.'.$id, array(
                            'type' => 'hidden',
                            'value' => $leasing_installment_id,
                        ));
                        $contentTd .= $this->Form->input('LeasingPaymentDetail.leasing_id.'.$id, array(
                            'type' => 'hidden',
                            'value' => $id,
                        ));
                        echo $this->Html->tag('td', $contentTd);
                        echo $this->Html->tag('td', $customLeasingDate, array(
                            'class' => 'on-remove',
                        ));

                        echo $this->Html->tag('td', $customPaidDate.$this->Form->input('LeasingPaymentDetail.expired_date.'.$id, array(
                            'type' => 'hidden',
                            'class' => 'leasing-expired-date',
                            'value' => $paid_date,
                        )), array(
                            'class' => 'red',
                        ));
                        echo $this->Html->tag('td', $this->Html->tag('span', $customInstallment, array(
                            'class' => 'on-remove',
                        )).$this->Form->input('LeasingPaymentDetail.installment.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price installment hide on-show text-right leasing-trigger red',
                            'value' => $installment,
                        )), array(
                            'class' => 'text-right',
                        ));
                        echo $this->Html->tag('td', $this->Html->tag('span', $customInstallmentRate, array(
                            'class' => 'on-remove',
                        )).$this->Form->input('LeasingPaymentDetail.installment_rate.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price installment-rate hide on-show text-right leasing-trigger red',
                            'value' => $installment_rate,
                        )), array(
                            'class' => 'text-right',
                        ));
                        echo $this->Html->tag('td', $this->Form->input('LeasingPaymentDetail.denda.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price denda text-right leasing-trigger red',
                            // 'value' => $denda,
                        )), array(
                            'class' => 'text-right hide on-show',
                        ));
                        echo $this->Html->tag('td', $customTotal, array(
                            'class' => 'text-right leasing-total red',
                        ));
                ?>
                <td class="action-search hide">
                    <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
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
                    'data-action' => $data_action,
                    'class' => 'ajaxModal',
                    'title' => $title,
                ),
            ));
        }
?>