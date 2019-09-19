<?php 
        $data_action = 'checkbox-option';
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'titipan',
                'action' => 'search',
                'drivers',
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
                        'label'=> __('ID Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('ID Supir'),
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir'),
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
                        'controller' => 'titipan',
                        'action' => 'drivers',
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
                        echo $this->Html->tag('th', __('ID Supir'));
                        echo $this->Html->tag('th', __('Nama'));
                        echo $this->Html->tag('th', __('No. Telp'));
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = !empty($value['Driver']['id'])?$value['Driver']['id']:false;
                            $no_id = !empty($value['Driver']['no_id'])?$value['Driver']['no_id']:'-';
                            $driver_name = !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:false;
                            $phone = !empty($value['Driver']['phone'])?$value['Driver']['phone']:false;
                            $no_hp = !empty($value['Driver']['no_hp'])?$value['Driver']['no_hp']:$phone;
            ?>
            <tr data-value="child-<?php echo $id; ?>" class="child child-<?php echo $id; ?>">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('ttuj_checked.', array(
                            'class' => 'check-option',
                            'value' => $id,
                        )), array(
                            'class' => 'checkbox-action',
                        ));
                ?>
                <td>
                    <?php
                            echo $no_id;
                    ?>
                </td>
                <td>
                    <?php
                            echo $driver_name;
                            echo $this->Form->hidden('TitipanDetail.driver_id.', array(
                                'value' => $id,
                            ));
                    ?>
                </td>
                <td class="on-remove"><?php echo $no_hp;?></td>
                <td class="hide on-show">
                    <?php
                            echo $this->Form->input('TitipanDetail.note.', array(
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
                            echo $this->Form->input('TitipanDetail.total.', array(
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
                                'data-id' => sprintf('child-child-%s', $id),
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