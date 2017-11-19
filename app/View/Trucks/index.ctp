<?php 
        $dataColumns = array(
            'id' => array(
                'name' => __('ID'),
                'field_model' => 'Truck.id',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => 'Truck.nopol',
                'display' => true,
            ),
            'merek' => array(
                'name' => __('Merek'),
                'field_model' => false,
                'display' => true,
            ),
            'jenis' => array(
                'name' => __('Jenis'),
                'field_model' => false,
                'display' => true,
            ),
            'kapasitas' => array(
                'name' => __('Kapasitas'),
                'field_model' => 'Truck.capacity',
                'display' => true,
            ),
            'pemilik' => array(
                'name' => __('Pemilik'),
                'field_model' => false,
                'display' => true,
            ),
            'supir' => array(
                'name' => __('Supir'),
                'field_model' => 'Driver.driver_name',
                'display' => true,
            ),
            'aset' => array(
                'name' => __('Aset'),
                'field_model' => 'Truck.is_asset',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Truck.status',
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
        echo $this->element('blocks/trucks/search_truck');
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
                    echo $this->Html->link('<i class="fa fa-puzzle-piece"></i> Fasilitas Truk', array(
                        'controller' => 'trucks',
                        'action' => 'facilities'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-list-alt"></i> Jenis Truk', array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-truck"></i> Merek Truk', array(
                        'controller' => 'trucks',
                        'action' => 'brands'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
                    // echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Truk', array(
                    //     'controller' => 'trucks',
                    //     'action' => 'add'
                    // ), array(
                    //     'escape' => false,
                    //     'class' => 'btn btn-app btn-success pull-right'
                    // ));
            ?>
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Truk'), array(
                                'controller' => 'trucks',
                                'action' => 'add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel'), array(
                                'controller' => 'trucks',
                                'action' => 'add_import',
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting red">
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
                        if(!empty($trucks)){
                            foreach ($trucks as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Truck', 'id');
                                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                                $is_asset = $this->Common->filterEmptyField($value, 'Truck', 'is_asset');
                                $sold = $this->Common->filterEmptyField($value, 'Truck', 'sold');
                                $brand = $this->Common->filterEmptyField($value, 'TruckBrand', 'name', '-');
                                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name', '-');
                                $company = $this->Common->filterEmptyField($value, 'Company', 'name', '-');
                                $driver_name = $this->Common->filterEmptyField($value, 'Driver', 'driver_name', '-');
                                $laka_id = $this->Common->filterEmptyField($value, 'Laka', 'id');
                                $ttuj_id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                ?>
                <tr>
                    <td><?php echo $id;?></td>
                    <td><?php echo $nopol;?></td>
                    <td><?php echo $brand;?></td>
                    <td><?php echo $category;?></td>
                    <td><?php echo $capacity;?></td>
                    <td><?php echo $company;?></td>
                    <td><?php echo $driver_name;?></td>
                    <td>
                        <?php 
                                if(!empty($is_asset)){
                                    echo '<span class="label label-success"><i class="fa fa-check"></i></span>'; 
                                }else{
                                    echo '<span class="label label-danger"><i class="fa fa-times"></i></span>';  
                                }
                        ?>
                    </td>
                    <td>
                        <?php 
                                $status = '<span class="label label-success">AVAILABLE</span>';

                                if( !empty($laka_id) ){
                                    $status = '<span class="label label-danger">LAKA</span>';
                                } else if( !empty($sold) ){
                                    $status = '<span class="label label-warning">SOLD</span>';
                                } else if( !empty($ttuj_id) ){
                                    $status = '<span class="label label-primary">Away</span>';
                                }

                                echo $status;
                        ?>
                    </td>
                    <td class="action">
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle btn-xs" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                Lihat Detil
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <?php
                                        echo $this->Html->tag('li', $this->Html->link(__('Info Truk'), array(
                                            'controller' => 'trucks',
                                            'action' => 'detail',
                                            $id,
                                        ), array(
                                            'allow' => true,
                                        )));
                                        echo $this->Html->tag('li', $this->Html->link(__('Info Ritase'), array(
                                            'controller' => 'revenues',
                                            'action' => 'detail_ritase',
                                            $id,
                                        ), array(
                                            'allow' => true,
                                        )));
                                ?>
                            </ul>
                        </div>
                        <?php
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'trucks',
                                    'action' => 'edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'trucks',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => sprintf(__('Apakah Anda yakin akan menghapus truk dengan nopol %s?'), $nopol),
                                ));
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        }else{
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => '12'
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>