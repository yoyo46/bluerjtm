<?php 
        $title = !empty($title)?$title:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'leasings',
                'action' => 'search',
                'leasings_dp_unpaid',
                'vendor_id' => $vendor_id,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Form->label('fromMonth', __('Tgl Kontrak Dari Bulan'));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->month('from', array(
                                'label'=> false, 
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                // 'value' => $monthFrom,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->year('from', 1949, date('Y'), array(
                                'label'=> false, 
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                // 'value' => $yearFrom,
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <?php 
                echo $this->Form->label('fromMonth', __('Sampai Bulan'));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->month('to', array(
                                'label'=> false, 
                                'class'=>'form-control target-month',
                                'required' => false,
                                'empty' => false,
                                // 'disabled' => true,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->year('to', 1949, date('Y'), array(
                                'label'=> false, 
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                // 'value' => $yearFrom,
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No Kontrak'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
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
                        'action' => 'leasings_dp_unpaid',
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
    </div>
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
                <th class="text-center"><?php echo __('DP');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Leasing', 'id');
                            $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');
                            $leasing_date = $this->Common->filterEmptyField($value, 'Leasing', 'paid_date');
                            $dp = $this->Common->filterEmptyField($value, 'Leasing', 'down_payment');

                            $customLeasingDate = $this->Common->formatDate($leasing_date, 'd M Y');
                            $customDP = $this->Common->getFormatPrice($dp);
            ?>
            <tr class="child-search child-search-<?php echo $id;?>" rel="<?php echo $id;?>">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('leasing_id.', array(
                            'class' => 'check-option',
                            'value' => $id,
                        )), array(
                            'class' => 'checkbox-detail',
                        ));

                        $contentTd = $no_contract;
                        $contentTd .= $this->Form->input('LeasingPaymentDetail.leasing_id.'.$id, array(
                            'type' => 'hidden',
                            'value' => $id,
                        ));
                        echo $this->Html->tag('td', $contentTd);
                        echo $this->Html->tag('td', $customLeasingDate, array(
                            'class' => 'on-remove',
                        ));
                        echo $this->Html->tag('td', $this->Html->tag('span', $customDP, array(
                            'class' => 'on-remove',
                        )).$this->Form->input('LeasingPaymentDetail.installment.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price installment hide on-show text-right leasing-trigger red',
                            'value' => $dp,
                        )), array(
                            'class' => 'text-right',
                        ));
                        echo $this->Html->tag('td', $this->Form->input('LeasingPaymentDetail.denda.'.$id, array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price denda text-right leasing-trigger red',
                        )), array(
                            'class' => 'text-right hide on-show',
                        ));
                        echo $this->Html->tag('td', $customDP, array(
                            'class' => 'text-right leasing-total red hide on-show',
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