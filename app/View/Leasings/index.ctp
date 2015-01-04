<?php 
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
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Kontrak', array(
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
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Truck.nopol', __('No Pol'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Leasing.installment', __('Cicilan PerBln'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Leasing.paid_date', __('Tgl Bayar PerBln'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Leasing.fine', __('Denda'), array(
                            'escape' => false
                        )));
                ?>
            </tr>
            <?php
                    if(!empty($leasings)){
                        foreach ($leasings as $key => $value) {
                            $value_leasing = $value['Leasing'];
                            $id = $value_leasing['id'];
            ?>
            <tr>
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $this->Number->currency($value['Leasing']['installment'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></td>
                <td><?php echo date('d M Y', strtotime($value['Leasing']['paid_date']));?></td>
                <td><?php echo $this->Number->currency($value['Leasing']['fine'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></td>
                <td>
                    <?php 
                        if(!empty($value_leasing['status'])){
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

                            if(!empty($value_leasing['status'])){
                                echo $this->Html->link('Disable', array(
                                    'controller' => 'leasings',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan menon-aktifkan kontrak ini?'));
                            }else{
                                echo $this->Html->link('Enable', array(
                                    'controller' => 'leasings',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status brand'
                                ), __('Apakah Anda yakin akan mengaktifkan kontrak ini?'));
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
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>