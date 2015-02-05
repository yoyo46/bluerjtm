<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_supir');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_drivers', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Supir', array(
                        'controller' => 'trucks',
                        'action' => 'driver_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.no_id', __('No. ID'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.name', __('Nama Supir'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.alias', __('Panggilan'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.identity_number', __('No. Identitas'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.Address', __('Alamat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.phone', __('Telepon'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.status', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if( !empty($truck_drivers) ){
                        foreach ($truck_drivers as $key => $value) {
                            $id = $value['Driver']['id'];
            ?>
            <tr>
                <td><?php echo $value['Driver']['no_id'];?></td>
                <td><?php echo $value['Driver']['name'];?></td>
                <td><?php echo !empty($value['Driver']['alias'])?$value['Driver']['alias']:'-';?></td>
                <td><?php echo $value['Driver']['identity_number'];?></td>
                <td><?php echo $value['Driver']['address'];?></td>
                <td>
                    <?php 
                            echo $value['Driver']['phone'];
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                            if( !empty($value['Driver']['status']) ) {
                                $title = __('Aktif');
                                $class = 'success';
                            } else {
                                $title = __('Non-Aktif');
                                $class = 'danger';
                            }

                            echo $this->Html->tag('span', $title, array(
                                'class' => sprintf('label label-%s', $class),
                            ));
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['Driver']['created']);?></td>
                <td class="action">
                    <?php 
                            if( in_array('update_drivers', $allowModule) ) {
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'trucks',
                                    'action' => 'driver_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_drivers', $allowModule) ) {
                                if( !empty($value['Driver']['status']) ) {
                                    $title = __('Non-Aktifkan');
                                    $msg = sprintf(__('Anda yakin ingin Non-Aktifkan data supir %s?'), $value['Driver']['name']);
                                    $class = 'danger';
                                } else {
                                    $title = __('Aktifkan');
                                    $msg = sprintf(__('Anda yakin ingin Aktifkan data supir %s?'), $value['Driver']['name']);
                                    $class = 'warning';
                                }

                                echo $this->Html->link($title, array(
                                    'controller' => 'trucks',
                                    'action' => 'driver_toggle',
                                    $id
                                ), array(
                                    'class' => sprintf('btn btn-%s btn-xs', $class),
                                    'title' => 'disable status brand'
                                ), $msg);
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
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