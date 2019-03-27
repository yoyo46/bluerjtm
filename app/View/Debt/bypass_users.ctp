<?php 
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
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-parent' => true,
                'title' => $title,
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'debt',
                'action' => 'users',
                'bypass' => true,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'title' => $title,
            ));
    ?>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive box-user-staff" id="box-info-coa">
    <table class="table table-hover">
        <thead>
            <tr>
                <?php 
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
                            $full_name = !empty($value['ViewStaff']['full_name'])?$value['ViewStaff']['full_name']:false;
                            $type = !empty($value['ViewStaff']['type'])?$value['ViewStaff']['type']:false;
                            $rel = $type.$id;
            ?>
            <tr class="child-search click-child child-search-<?php echo $rel; ?>" rel="<?php echo $rel; ?>">
                <td>
                    <?php
                            echo $full_name;
                            echo $this->Form->hidden('DebtDetail.employe_id.', array(
                                'value' => $id,
                            ));
                            echo $this->Form->hidden('DebtDetail.type.', array(
                                'value' => $type,
                            ));
                    ?>
                </td>
                <td><?php echo $type;?></td>
                <td class="action-search hide">
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
                <td class="action-search hide">
                    <?php
                            echo $this->Form->input('DebtDetail.total.', array(
                                'type' => 'text',
                                'class' => 'form-control input_price_coma input_number sisa-amount text-right',
                                'label' => false,
                                'div' => false,
                                'required' => false,
                            ));
                    ?>
                </td>
                <td class="action-search hide">
                    <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'cashbank_first'
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '2'
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