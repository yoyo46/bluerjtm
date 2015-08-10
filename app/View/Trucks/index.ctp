<?php 
        $dataColumns = array(
            'id' => array(
                'name' => __('ID'),
                'field_model' => 'Truck.id',
                'display' => true,
            ),
            // 'cabang' => array(
            //     'name' => __('Cabang'),
            //     'field_model' => false,
            //     'display' => true,
            // ),
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
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Truk', array(
                        'controller' => 'trucks',
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
                        if(!empty($trucks)){
                            foreach ($trucks as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Truck', 'id');
                                // $city = $this->Common->filterEmptyField($value, 'City', 'name');
                                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                                $is_asset = $this->Common->filterEmptyField($value, 'Truck', 'is_asset');
                                $sold = $this->Common->filterEmptyField($value, 'Truck', 'sold');
                                // $branch_id = $this->Common->filterEmptyField($value, 'Truck', 'branch_id');
                                $brand = $this->Common->filterEmptyField($value, 'TruckBrand', 'name', '-');
                                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name', '-');
                                $company = $this->Common->filterEmptyField($value, 'Company', 'name', '-');
                                $driver_name = $this->Common->filterEmptyField($value, 'Driver', 'driver_name', '-');
                                $laka_id = $this->Common->filterEmptyField($value, 'Laka', 'id');
                                $ttuj_id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                ?>
                <tr>
                    <td><?php echo $id;?></td>
                    <!-- <td><?php // echo $city;?></td> -->
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
                        <?php
                                echo $this->Html->link('Detail', array(
                                    'controller' => 'trucks',
                                    'action' => 'detail',
                                    $id,
                                ), array(
                                    'class' => 'btn btn-info btn-xs',
                                    'allow' => true,
                                ));

                                echo $this->Html->link('Edit', array(
                                    'controller' => 'trucks',
                                    'action' => 'edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    // 'branch_id' => $branch_id,
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'trucks',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    // 'branch_id' => $branch_id,
                                ), sprintf(__('Apakah Anda yakin akan menghapus truk dengan nopol %s?'), $nopol));
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