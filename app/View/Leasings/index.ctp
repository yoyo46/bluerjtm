<?php 
        $dataColumns = array(
            'branch' => array(
                'no_contract' => __('No Kontrak'),
                'field_model' => 'Leasing.no_contract',
                'display' => true,
            ),
            'company' => array(
                'name' => __('Perusahaan'),
                'field_model' => 'LeasingCompany.name',
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
                    echo $this->Html->link('<i class="fa fa-plus"></i> Perusahaan Leasing', array(
                        'controller' => 'leasings',
                        'action' => 'leasing_companies',
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
                    
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
        <table class="table table-hover">
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
                            $company = $this->Common->filterEmptyField($value, 'LeasingCompany', 'name');
            ?>
            <tr>
                <td><?php echo $no_contract;?></td>
                <td><?php echo $company;?></td>
                <td><?php echo $this->Number->currency($installment, Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></td>
                <td><?php echo date('d M Y', strtotime($paid_date));?></td>
                <td>
                    <?php 
                            if(!empty($status)){
                                echo '<span class="label label-success">Active</span>'; 
                            }else{
                                echo '<span class="label label-danger">Non Active</span>';  
                            }
                    ?>
                </td>
                <td class="action">
                    <?php
                            echo $this->Html->link('Rubah', array(
                                'controller' => 'leasings',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if(!empty($status)){
                                echo $this->Html->link('Void', array(
                                    'controller' => 'leasings',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan menon-aktifkan kontrak ini?'));
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