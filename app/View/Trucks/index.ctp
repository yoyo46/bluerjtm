<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-list-alt"></i> Kategori', array(
                    'controller' => 'trucks',
                    'action' => 'categories'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
                echo $this->Html->link('<i class="fa fa-building-o"></i> Perusahaan', array(
                    'controller' => 'trucks',
                    'action' => 'companies'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
                echo $this->Html->link('<i class="fa fa-map-marker"></i> Kota', array(
                    'controller' => 'trucks',
                    'action' => 'cities'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
                echo $this->Html->link('<i class="fa fa-user"></i> Supir', array(
                    'controller' => 'trucks',
                    'action' => 'drivers'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Truk', array(
                    'controller' => 'trucks',
                    'action' => 'add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                <th>ID</th>
                <th>Merek</th>
                <th>Perusahaan</th>
                <th>Kategori</th>
                <th>Supir</th>
                <th>Nopol</th>
                <th>Aset</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($trucks)){
                    foreach ($trucks as $key => $value) {
                        $value_truck = $value['Truck'];
                        $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $value['TruckBrand']['name'];?></td>
                <td><?php echo $value['Company']['name'];?></td>
                <td><?php echo $value['TruckCategory']['name'];?></td>
                <td><?php echo $value['Driver']['name'];?></td>
                <td><?php echo $value_truck['nopol'];?></td>
                <td>
                    <?php 
                        if(!empty($value_data['is_asset'])){
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
                <td>
                    <?php
                        echo $this->Html->link('Detail', array(
                            'controller' => 'trucks',
                            'action' => 'detail',
                            $id
                        ), array(
                            'class' => 'btn btn-info btn-sm'
                        ));

                        echo $this->Html->link('Rubah', array(
                            'controller' => 'trucks',
                            'action' => 'edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-sm'
                        ));

                        if(!empty($value_data['status'])){
                            echo $this->Html->link('Disable', array(
                                'controller' => 'trucks',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'disable status brand'
                            ));
                        }else{
                            echo $this->Html->link('Enable', array(
                                'controller' => 'trucks',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-success btn-sm',
                                'title' => 'enable status brand'
                            ));
                        }

                        echo $this->Html->link('KIR', array(
                            'controller' => 'trucks',
                            'action' => 'kir',
                            $id
                        ), array(
                            'class' => 'btn btn-warning btn-sm'
                        ));
                        echo $this->Html->link('SIUP', array(
                            'controller' => 'trucks',
                            'action' => 'siup',
                            $id
                        ), array(
                            'class' => 'btn btn-warning btn-sm'
                        ));
                    ?>
                </td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr><td colspan="5"><?php echo __('Data tidak ditemukan.');?></tr>
            <?php
                }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>