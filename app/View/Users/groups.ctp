<?php 
        $this->Html->addCrumb(__('Group'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Group', array(
                        'controller' => 'users',
                        'action' => 'group_add'
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
                <th>Group</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($groups)){
                        foreach ($groups as $key => $value) {
                            $value_data = $value['Group'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'group_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '4'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
</div>