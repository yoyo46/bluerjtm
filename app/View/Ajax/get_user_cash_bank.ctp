<?php 
        echo $this->Form->create('UserCashBank', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getUserCashBank',
                $action_type,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <?php 
            if( !empty($listReceivers) ) {
    ?>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama'),
                    ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'data-parent' => true,
                        'title' => $title,
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'ajax',
                        'action' => 'getUserCashBank',
                        $action_type,
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'title' => $title,
                    ));
            ?>
        </div>
    </div>
    <?php 
            }
    ?>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Html->tag('label', '&nbsp;');
            ?>
            <div class="row">
                <?php 
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_employee', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('Karyawan')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-4',
                        ));
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_customer', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('Customer')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-4',
                        ));
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_vendor', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('Vendor')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-4',
                        ));
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <?php 
                    echo $this->Html->tag('th', __('Nama'));
                    echo $this->Html->tag('th', __('Alamat'));
                    echo $this->Html->tag('th', __('Kategori'));
            ?>
        </tr>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Client', 'id');
                        $receiver_name = $this->Common->filterEmptyField($value, 'Client', 'name');
                        $address = $this->Common->filterEmptyField($value, 'Client', 'address');
                        $type = $this->Common->filterEmptyField($value, 'Client', 'type');
                        $dataModel = $this->Common->filterEmptyField($value, 'Client', 'model');
        ?>
        <tr data-value="<?php echo $id;?>" data-text="<?php echo $receiver_name;?>" data-type="<?php echo $dataModel;?>" data-change="#<?php echo $data_change;?>">
            <td>
                <?php
                        echo $receiver_name;
                ?>
            </td>
            <td><?php echo $address;?></td>
            <td><?php echo $type;?></td>
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
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
            ),
            'model' => false,
        ));
?>