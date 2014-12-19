<?php 
	$this->Html->addCrumb(__('Data Truk'), array(
		'controller' => 'trucks',
		'action' => 'index'
	));
    $this->Html->addCrumb($sub_module_title);

    echo $this->element('blocks/trucks/info_truck');
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
                echo $this->Html->link('<i class="fa fa-truck"></i> Tambah Alokasi', array(
                    'controller' => 'trucks',
                    'action' => 'alocation_add',
                    $id
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>ID</th>
                <th>Lokasi</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($alocations)){
                    foreach ($alocations as $key => $value) {
                        $value_truck = $value['TruckAlocation'];
                        $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $value['City']['name'];?></td>
                <td>
                    <?php
                        echo $this->Html->link('Rubah', array(
                            'controller' => 'trucks',
                            'action' => 'alocation_edit',
                            $value_truck['truck_id'],
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-sm'
                        ));
                    ?>
                </td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr><td colspan="3"><?php echo __('Data tidak ditemukan.');?></tr>
            <?php
                }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>