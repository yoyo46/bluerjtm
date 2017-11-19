<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Grup User'),
                'field_model' => 'Group.code',
                'display' => true,
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'Group.modified',
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
        echo $this->element('blocks/users/search_list_groups');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'users',
                        'action' => 'group_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));

                    echo $this->Html->link('<i class="fa fa-archive"></i> Action Modules', array(
                        'controller' => 'users',
                        'action' => 'action_modules'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div><!-- /.box-header -->
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
                        if(!empty($groups)){
                            foreach ($groups as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Group', 'id');
                                $name = $this->Common->filterEmptyField($value, 'Group', 'name');
                                $modified = $this->Common->filterEmptyField($value, 'Group', 'modified');
                ?>
                <tr>
                    <td><?php echo $name;?></td>
                    <td><?php echo $this->Time->niceShort($modified);?></td>
                    <td class="action">
                        <?php 
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'users',
                                    'action' => 'group_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'users',
                                    'action' => 'group_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin menghapus posisi karyawan ini?'),
                                ));

                                if( $id != 1 ) {
                                    echo $this->Html->link('Otorisasi', array(
                                        'controller' => 'users',
                                        'action' => 'authorization_privilage',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-success btn-xs'
                                    ));
                                }
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        } else {
                             echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => 4
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>