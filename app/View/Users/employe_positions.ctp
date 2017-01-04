<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'EmployePosition.code',
                'display' => true,
            ),
            'position' => array(
                'name' => __('Posisi'),
                'field_model' => 'EmployePosition.name',
                'display' => true,
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'EmployePosition.modified',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb(__('Karyawan'), array(
            'action' => 'employes',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'users',
                        'action' => 'employe_position_add'
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
                        if(!empty($employe_positions)){
                            foreach ($employe_positions as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'EmployePosition', 'id');
                                $name = $this->Common->filterEmptyField($value, 'EmployePosition', 'name');
                                $code = $this->Common->filterEmptyField($value, 'EmployePosition', 'code');
                                $modified = $this->Common->filterEmptyField($value, 'EmployePosition', 'modified');
                ?>
                <tr>
                    <td><?php echo $code;?></td>
                    <td><?php echo $name;?></td>
                    <td><?php echo $this->Time->niceShort($modified);?></td>
                    <td class="action">
                        <?php 
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'users',
                                    'action' => 'employe_position_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link('Hapus', array(
                                    'controller' => 'users',
                                    'action' => 'employe_position_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'title' => 'disable status brand',
                                    'data-alert' => sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $name),
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
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>