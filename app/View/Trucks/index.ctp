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
        <table class="table table-hover">
            <tr>
                <th>No. ID</th>
                <th>Nopol</th>
                <th>Merek</th>
                <th>Jenis</th>
                <th>Kapasitas</th>
                <th>Pemilik</th>
                <th>Supir</th>
                <th>Aset</th>
                <th>Action</th>
            </tr>
            <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $value) {
                            $value_truck = $value['Truck'];
                            $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo str_pad($id, 4, '0', STR_PAD_LEFT);?></td>
                <td><?php echo $value_truck['nopol'];?></td>
                <td><?php echo !empty($value['TruckBrand']['name'])?$value['TruckBrand']['name']:'-';?></td>
                <td><?php echo !empty($value['TruckCategory']['name'])?$value['TruckCategory']['name']:'-';?></td>
                <td><?php echo $value_truck['capacity'];?></td>
                <td><?php echo !empty($value['Company']['name'])?$value['Company']['name']:'-';?></td>
                <td><?php echo !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:'-';?></td>
                <td>
                    <?php 
                        if(!empty($value_truck['is_asset'])){
                            echo '<span class="label label-success">Ya</span>'; 
                        }else{
                            echo '<span class="label label-danger">Tidak</span>';  
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

                        echo $this->Html->link(__('Hapus'), array(
                            'controller' => 'trucks',
                            'action' => 'toggle',
                            $id
                        ), array(
                            'class' => 'btn btn-danger btn-xs',
                            'title' => 'disable status brand'
                        ), sprintf(__('Apakah Anda yakin akan menghapus truk dengan nopol %s?'), $value_truck['nopol']));

                        // echo $this->Html->link('Alokasi', array(
                        //     'controller' => 'trucks',
                        //     'action' => 'alocations',
                        //     $id
                        // ), array(
                        //     'class' => 'btn bg-navy btn-xs'
                        // ));
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