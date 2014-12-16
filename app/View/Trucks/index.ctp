<?php 
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
                    // echo $this->Html->link('<i class="fa fa-tint"></i> Bahan Bakar', array(
                    //     'controller' => 'trucks',
                    //     'action' => 'gas_edit'
                    // ), array(
                    //     'escape' => false,
                    //     'class' => 'btn btn-app pull-right'
                    // ));
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
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                <th>Merek</th>
                <th>Pemilik</th>
                <th>Jenis</th>
                <th>Supir</th>
                <th>Nopol</th>
                <th>Aset</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $value) {
                            $value_truck = $value['Truck'];
                            $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo $value['TruckBrand']['name'];?></td>
                <td><?php echo $value['Company']['name'];?></td>
                <td><?php echo $value['TruckCategory']['name'];?></td>
                <td><?php echo $value['Driver']['name'];?></td>
                <td><?php echo $value_truck['nopol'];?></td>
                <td>
                    <?php 
                        if(!empty($value_truck['is_asset'])){
                            echo '<span class="label label-success">Ya</span>'; 
                        }else{
                            echo '<span class="label label-danger">Tidak</span>';  
                        }
                    ?>
                </td>
                <td>
                    <?php 
                        if(!empty($value_truck['status'])){
                            echo '<span class="label label-success">Active</span>'; 
                        }else{
                            echo '<span class="label label-danger">Non Active</span>';  
                        }
                        
                    ?>
                </td>
                <td class="action">
                    <?php
                        echo $this->Html->link('Detail', array(
                            'controller' => 'trucks',
                            'action' => 'detail',
                            $id
                        ), array(
                            'class' => 'btn btn-info btn-xs'
                        ));

                        echo $this->Html->link('Rubah', array(
                            'controller' => 'trucks',
                            'action' => 'edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-xs'
                        ));

                        if(!empty($value_truck['status'])){
                            echo $this->Html->link('Disable', array(
                                'controller' => 'trucks',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), sprintf(__('Apakah Anda yakin akan menon-aktifkan truk dengan nopol %s?'), $value_truck['nopol']));
                        }else{
                            echo $this->Html->link('Enable', array(
                                'controller' => 'trucks',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-success btn-xs',
                                'title' => 'enable status brand'
                            ), sprintf(__('Apakah Anda yakin akan mengaktifkan truk dengan nopol %s?'), $value_truck['nopol']));
                        }

                        echo $this->Html->link('KIR', array(
                            'controller' => 'trucks',
                            'action' => 'kir',
                            $id
                        ), array(
                            'class' => 'btn bg-olive btn-xs'
                        ));
                        echo $this->Html->link('STNK', array(
                            'controller' => 'trucks',
                            'action' => 'stnk',
                            $id
                        ), array(
                            'class' => 'btn bg-olive btn-xs'
                        ));
                        echo $this->Html->link('SIUP', array(
                            'controller' => 'trucks',
                            'action' => 'siup',
                            $id
                        ), array(
                            'class' => 'btn bg-purple btn-xs'
                        ));
                        echo $this->Html->link('Alokasi', array(
                            'controller' => 'trucks',
                            'action' => 'alocations',
                            $id
                        ), array(
                            'class' => 'btn bg-navy btn-xs'
                        ));
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