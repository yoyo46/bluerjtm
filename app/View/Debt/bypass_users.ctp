<?php 
        $data_action = 'checkbox-option';
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'debt',
                'action' => 'search',
                'users',
                'bypass' => true,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_id',array(
                        'type' => 'text',
                        'label'=> __('ID Karyawan'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                        'placeholder' => __('ID Karyawan'),
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama Karyawan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Karyawan'),
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('type',array(
                        'label'=> __('Kategori'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Kategori'),
                        'options' => array(
                            'Karyawan' => __('Karyawan'),
                            'Supir' => __('Supir'),
                        )
                    ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-parent' => true,
                        'title' => $title,
                        'data-action' => $data_action,
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'debt',
                        'action' => 'users',
                        'bypass' => true,
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'title' => $title,
                        'data-action' => $data_action,
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <thead>
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Form->checkbox('checkbox_all', array(
                            'class' => 'checkAll',
                        )), array(
                            'width' => '5%',
                        ));
                        echo $this->Html->tag('th', __('ID'));
                        echo $this->Html->tag('th', __('Nama'));
                        echo $this->Html->tag('th', __('Kategory'));
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = !empty($value['ViewStaff']['id'])?$value['ViewStaff']['id']:false;
                            $no_id = !empty($value['ViewStaff']['no_id'])?$value['ViewStaff']['no_id']:'-';
                            $full_name = !empty($value['ViewStaff']['full_name'])?$value['ViewStaff']['full_name']:false;
                            $name_code = !empty($value['ViewStaff']['name_code'])?$value['ViewStaff']['name_code']:false;
                            $type = !empty($value['ViewStaff']['type'])?$value['ViewStaff']['type']:false;
                            $rel = $type.$id;
            ?>
            <tr data-value="child-<?php echo $rel; ?>" class="child child-<?php echo $rel; ?>">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('ttuj_checked.', array(
                            'class' => 'check-option',
                            'value' => $id,
                        )), array(
                            'class' => 'checkbox-action',
                        ));
                ?>
                <td class="on-remove"><?php echo $no_id;?></td>
                <td class="hide on-show">
                    <?php
                            echo $name_code;
                            echo $this->Form->hidden('DebtDetail.employe_id.', array(
                                'value' => $id,
                            ));
                            echo $this->Form->hidden('DebtDetail.type.', array(
                                'value' => $type,
                            ));
                    ?>
                </td>
                <td class="on-remove"><?php echo $full_name;?></td>
                <td><?php echo $type;?></td>
                <td class="hide on-show">
                    <?php
                            echo $this->Form->input('DebtDetail.note.', array(
                                'type' => 'text',
                                'class' => 'form-control',
                                'label' => false,
                                'div' => false,
                                'required' => false,
                            ));
                    ?>
                </td>
                <td class="hide on-show">
                    <?php
                            echo $this->Form->input('DebtDetail.total.', array(
                                'type' => 'text',
                                'class' => 'form-control input_price_coma sisa-amount text-right',
                                'label' => false,
                                'div' => false,
                                'required' => false,
                            ));
                    ?>
                </td>
                <td class="document-table-action hide on-show">
                    <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'cashbank_first',
                                'data-id' => sprintf('child-child-%s', $rel),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '4'
                        )));
                    }
            ?>
        </tbody>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
            ),
        ));
?>