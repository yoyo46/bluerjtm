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
                    echo $this->Form->input('model',array(
                        'label'=> __('Tipe User'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Tipe User'),
                        'options' => $listReceivers,
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
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama'),
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
                'data-action' => $data_action,
                'data-parent' => true,
                'title' => $title,
            ));
            echo $this->Common->rule_link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
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
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <?php 
                    echo $this->Html->tag('th', __('Nama'));
                    echo $this->Html->tag('th', __('Alamat'));
            ?>
        </tr>
        <?php
                if(!empty($list_result)){
                    foreach ($list_result as $key => $value) {
                        if( !empty($value[$model]['customer_name_code']) ) {
                            $receiver_name = $value[$model]['customer_name_code'];
                        } else if( !empty($value[$model]['driver_name']) ) {
                            $receiver_name = $value[$model]['driver_name'];
                        } else {
                            $receiver_name = $value[$model]['name'];
                        }
        ?>
        <tr data-value="<?php echo $value[$model]['id'];?>" data-text="<?php echo $receiver_name;?>" data-type="<?php echo $model;?>" data-change="#<?php echo $data_change;?>">
            <td>
                <?php
                        echo $receiver_name;
                ?>
            </td>
            <td><?php echo $value[$model]['address'];?></td>
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
        ));
?>