<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_supir');

        $dataColumns = array(
            'no_id' => array(
                'name' => __('No. ID'),
                'field_model' => 'Driver.no_id',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama Supir'),
                'field_model' => 'Driver.name',
                'display' => true,
            ),
            'alias' => array(
                'name' => __('alias'),
                'field_model' => 'Driver.alias',
                'display' => true,
            ),
            'identity_number' => array(
                'name' => __('No. Identitas'),
                'field_model' => 'Driver.identity_number',
                'display' => true,
            ),
            'Address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Driver.Address',
                'display' => true,
            ),
            'phone' => array(
                'name' => __('Telepon'),
                'field_model' => 'Driver.phone',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Driver.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
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
    </div><!-- /.box-header -->
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
                                if(!empty($value['Driver']['is_resign'])){
                                    $title = __('Resign');
                                }else{
                                    $title = __('Non-Aktif');    
                                }
                                
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
                            echo $this->Html->link('Edit', array(
                                'controller' => 'trucks',
                                'action' => 'driver_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if( !empty($value['Driver']['status']) ) {
                                $title = __('Non-Aktifkan');
                                $msg = sprintf(__('Anda yakin ingin Non-Aktifkan data supir %s?'), $value['Driver']['name']);
                                $class = 'danger';
                            } else {
                                $title = __('Aktifkan');
                                $msg = sprintf(__('Anda yakin ingin Aktifkan data supir %s?'), $value['Driver']['name']);
                                $class = 'success';
                            }

                            echo $this->Html->link($title, array(
                                'controller' => 'trucks',
                                'action' => 'driver_toggle',
                                $id
                            ), array(
                                'class' => sprintf('btn btn-%s btn-xs', $class),
                                'title' => 'disable status brand'
                            ), $msg);
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