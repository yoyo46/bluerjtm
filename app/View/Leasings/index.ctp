<?php 
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => false,
                'display' => true,
            ),
            'no_contract' => array(
                'name' => __('No Kontrak'),
                'field_model' => 'Leasing.no_contract',
                'display' => true,
            ),
            // 'company' => array(
            //     'name' => __('Perusahaan'),
            //     'field_model' => 'LeasingCompany.name',
            //     'display' => true,
            // ),
            'company' => array(
                'name' => __('Vendor'),
                'field_model' => 'Vendor.name',
                'display' => true,
            ),
            'installment' => array(
                'name' => __('Cicilan PerBln'),
                'field_model' => 'Leasing.installment',
                'display' => true,
            ),
            'paid_date' => array(
                'name' => __('Tgl Bayar PerBln'),
                'field_model' => 'Leasing.paid_date',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Leasing.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/leasings/search_index');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    // echo $this->Html->link('<i class="fa fa-plus"></i> Perusahaan Leasing', array(
                    //     'controller' => 'leasings',
                    //     'action' => 'leasing_companies',
                    // ), array(
                    //     'escape' => false,
                    //     'class' => 'btn btn-app btn-success pull-right'
                    // ));
                    
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'leasings',
                        'action' => 'add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php
                    if(!empty($leasings)){
                        foreach ($leasings as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Leasing', 'id');
                            $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');
                            $installment = $this->Common->filterEmptyField($value, 'Leasing', 'installment');
                            $paid_date = $this->Common->filterEmptyField($value, 'Leasing', 'paid_date');
                            $status = $this->Common->filterEmptyField($value, 'Leasing', 'status');
                            $branch_id = $this->Common->filterEmptyField($value, 'Leasing', 'branch_id');
                            // $company = $this->Common->filterEmptyField($value, 'LeasingCompany', 'name');
                            $company = $this->Common->filterEmptyField($value, 'Vendor', 'name');
                            $branch = $this->Common->filterEmptyField($value, 'City', 'name');
            ?>
            <tr>
                <td><?php echo $branch;?></td>
                <td><?php echo $no_contract;?></td>
                <td><?php echo $company;?></td>
                <td><?php echo $this->Number->currency($installment, Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></td>
                <td><?php echo date('d M Y', strtotime($paid_date));?></td>
                <td>
                    <?php 
                            if(!empty($status)){
                                echo $this->Html->tag('span', __('Active'), array(
                                    'class' => 'label label-success',
                                ));
                                $labelBtn = __('Edit');
                            }else{
                                echo $this->Html->tag('span', __('Void'), array(
                                    'class' => 'label label-danger',
                                ));
                                $labelBtn = __('Detail');
                            }
                    ?>
                </td>
                <td class="action">
                    <?php
                            echo $this->Html->link($labelBtn, array(
                                'controller' => 'leasings',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs',
                                'branch_id' => $branch_id,
                            ));

                            if(!empty($status)){
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'leasings',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'branch_id' => $branch_id,
                                ), __('Apakah Anda yakin akan void kontrak ini?'));
                            }else{
                                // echo $this->Html->link('Enable', array(
                                //     'controller' => 'leasings',
                                //     'action' => 'toggle',
                                //     $id
                                // ), array(
                                //     'class' => 'btn btn-success btn-xs',
                                //     'title' => 'enable status brand'
                                // ), __('Apakah Anda yakin akan mengaktifkan kontrak ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '9'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>