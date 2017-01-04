<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_facilities');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Fasilitas', array(
                    'controller' => 'trucks',
                    'action' => 'facility_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Fasilitas Truk</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($truckFacilities)){
                        foreach ($truckFacilities as $key => $value) {
                            $value_data = $value['TruckFacility'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                        echo $this->Html->link('Edit', array(
                            'controller' => 'trucks',
                            'action' => 'facility_edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-xs'
                        ));

                        echo $this->Html->link('Hapus', array(
                            'controller' => 'trucks',
                            'action' => 'facility_toggle',
                            $id
                        ), array(
                            'class' => 'btn btn-danger btn-xs trigger-disabled',
                            'title' => 'disable status brand',
                            'data-alert' => sprintf(__('Apakah Anda yakin akan menghapus data Fasilitas %s?'), $value_data['name']),
                        ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '5'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>