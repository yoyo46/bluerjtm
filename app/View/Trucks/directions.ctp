<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Rute', array(
                    'controller' => 'trucks',
                    'action' => 'direction_add'
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
                <th>No.</th>
                <th>Rute</th>
                <th>Jarak Tempuh</th>
                <th>Bahan Bakar</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($directions)){
                    foreach ($directions as $key => $value) {
                        $id = $value['Direction']['id'];
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value['CityFrom']['name'].' sampai '.$value['CityTo']['name'];?></td>
                <td><?php echo $value['Direction']['distance'].' Km';?></td>
                <td><?php echo $value['Direction']['gas'].' liter';?></td>
                <td><?php echo $this->Common->customDate($value['Direction']['created']);?></td>
                <td>
                    <?php 
                        echo $this->Html->link('Edit', array(
                            'controller' => 'trucks',
                            'action' => 'direction_edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-sm'
                        ));

                        echo $this->Html->link(__('Hapus'), array(
                            'controller' => 'trucks',
                            'action' => 'direction_toggle',
                            $id
                        ), array(
                            'class' => 'btn btn-danger btn-sm trigger-disabled',
                            'title' => 'disable status brand',
                            'data-alert' => __('Anda yakin ingin menghapus data Rute ini?'),
                        ));
                    ?>
                </td>
            </tr>
            <?php
                    }
                }else {
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '7'
                    )));
                }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>