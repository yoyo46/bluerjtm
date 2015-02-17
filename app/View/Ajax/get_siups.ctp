<?php 
        echo $this->Form->create('Siup', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getSiups',
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nopol',array(
                        'label'=> __('Nopol'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nopol')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Driver.name',array(
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir')
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
                'action' => 'getSiups',
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
                    echo $this->Html->tag('th', __('No. Pol'));
                    echo $this->Html->tag('th', __('Supir'));
                    echo $this->Html->tag('th', __('Tgl Perpanjang'));
                    echo $this->Html->tag('th', __('Berlaku Hingga'));
                    echo $this->Html->tag('th', __('Biaya Perpanjang'));
            ?>
        </tr>
        <?php
                if(!empty($trucks)){
                    foreach ($trucks as $key => $value) {
        ?>
        <tr data-value="<?php echo $value['Siup']['id'];?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo $value['Siup']['no_pol'];?></td>
            <td><?php echo !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:'-';?></td>
            <td><?php echo $this->Common->customDate($value['Siup']['tgl_siup']);?></td>
            <td><?php echo $this->Common->customDate($value['Siup']['to_date']);?></td>
            <td>
                <?php echo $this->Number->currency($value['Siup']['price'], 'Rp. ', array('places' => 0));?>
            </td>
        </tr>
        <?php
                    }
                }else{
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