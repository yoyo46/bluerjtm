<?php 
    echo $this->Form->create('Truck', array(
        'url'=> $this->Html->url( array(
            'controller' => 'ajax',
            'action' => 'getTrucks',
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
                'action' => 'getTrucks',
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
            <th>No. ID</th>
            <th>Nopol</th>
            <th>Supir</th>
            <th>Merek</th>
            <th>Jenis</th>
            <th>Kapasitas</th>
            <th>Pemilik</th>
        </tr>
        <?php
                if(!empty($trucks)){
                    foreach ($trucks as $key => $value) {
                        $value_truck = $value['Truck'];
                        $id = $value_truck['id'];
        ?>
        <tr data-value="<?php echo $id;?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo str_pad($id, 4, '0', STR_PAD_LEFT);?></td>
            <td><?php echo $value_truck['nopol'];?></td>
            <td><?php echo !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:'-';?></td>
            <td><?php echo !empty($value['TruckBrand']['name'])?$value['TruckBrand']['name']:'-';?></td>
            <td><?php echo !empty($value['TruckCategory']['name'])?$value['TruckCategory']['name']:'-';?></td>
            <td><?php echo $value_truck['capacity'];?></td>
            <td><?php echo !empty($value['Company']['name'])?$value['Company']['name']:'-';?></td>
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