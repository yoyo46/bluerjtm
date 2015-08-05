<?php 
		$dataColumns = array(
            'module' => array(
                'name' => __('Modul'),
                'field_model' => 'ApprovalModule.name',
                'display' => true,
            ),
            'position' => array(
                'name' => __('Posisi yg Mengajukan'),
                'field_model' => 'EmployePosition.name',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Approval.created',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', false, array(
            'class' => 'text-middle text-center',
        ));

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_approvals');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'settings',
                    'action' => 'approval_setting_add'
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
                <?php 
                		if( !empty($fieldColumn) ) {
                            echo $fieldColumn;
                        }
                ?>
            </tr>
            <?php
                    if(!empty($approvals)){
                        foreach ($approvals as $key => $approval) {
                            $id = !empty($approval['Approval']['id'])?$approval['Approval']['id']:false;
                            $module = !empty($approval['ApprovalModule']['name'])?$approval['ApprovalModule']['name']:false;
                            $position = !empty($approval['EmployePosition']['name'])?$approval['EmployePosition']['name']:false;
            ?>
            <tr>
                <td><?php echo $module;?></td>
                <td><?php echo $position;?></td>
                <td class="text-center"><?php echo $this->Common->customDate($approval['Approval']['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'approval_setting_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'approval_setting_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus data ini?'));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
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