<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Merek Truk', array(
                    'controller' => 'trucks',
                    'action' => 'brand_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                <th>No.</th>
                <th>Merek Truk</th>
                <th>Dibuat</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($truck_brands)){
                    foreach ($truck_brands as $key => $value) {
                        $value_data = $value['TruckBrand'];
                        $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td>
                    <?php 
                        if(!empty($value_data['status'])){
                            echo '<span class="label label-success">Active</span>'; 
                        }else{
                            echo '<span class="label label-danger">Non Active</span>';  
                        }
                        
                    ?>
                </td>
                <td>
                    <?php 
                        echo $this->Html->link('Edit', array(
                            'controller' => 'trucks',
                            'action' => 'brand_edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-sm'
                        ));

                        if(!empty($value_data['status'])){
                            echo $this->Html->link('Disable', array(
                                'controller' => 'trucks',
                                'action' => 'brand_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'disable status brand'
                            ));
                        }else{
                            echo $this->Html->link('Enable', array(
                                'controller' => 'trucks',
                                'action' => 'brand_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-success btn-sm',
                                'title' => 'enable status brand'
                            ));
                        }
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