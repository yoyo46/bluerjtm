<?php 
    echo $this->Form->create('Driver', array(
        'url'=> $this->Html->url( array(
            'controller' => 'ajax',
            'action' => 'getDrivers',
            $id,
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
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('alias',array(
                        'label'=> __('Nama Panggilan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Panggilan')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('identity_number',array(
                        'label'=> __('No. Identitas'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Identitas')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('phone',array(
                        'label'=> __('Telepon'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Telepon')
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
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'ajax',
                'action' => 'getDrivers',
                $id,
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
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.no_id', __('No. ID'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.name', __('Nama Supir'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.alias', __('Panggilan'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.identity_number', __('No. Identitas'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.Address', __('Alamat'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Driver.phone', __('Telepon'), array(
                        'escape' => false
                    )));
            ?>
        </tr>
        <?php
                if( !empty($drivers) ){
                    foreach ($drivers as $key => $value) {
                        $id = $value['Driver']['id'];
        ?>
        <tr data-value="<?php echo $id;?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo $value['Driver']['no_id'];?></td>
            <td><?php echo $value['Driver']['name'];?></td>
            <td><?php echo !empty($value['Driver']['alias'])?$value['Driver']['alias']:'-';?></td>
            <td><?php echo $value['Driver']['identity_number'];?></td>
            <td><?php echo $value['Driver']['address'];?></td>
            <td>
                <?php 
                        echo $value['Driver']['phone'];
                ?>
            </td>
        </tr>
        <?php
                    }
                } else {
                     echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '9'
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