<?php 
        echo $this->Form->create('Ttuj', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getTtujs',
                $action_type,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Dari')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nottuj',array(
                        'label'=> __('No. Doc'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Doc')
                    ));
            ?>
        </div>
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
        <div class="form-group">
            <?php 
                    echo $this->Form->input('City.name',array(
                        'label'=> __('Tujuan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Tujuan')
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
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Customer.name',array(
                        'label'=> __('Customer'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Customer')
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
                'action' => 'getTtujs',
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
                    echo $this->Html->tag('th', __('No TTUJ'));
                    echo $this->Html->tag('th', __('Tanggal'));
                    echo $this->Html->tag('th', __('No Pol'));
                    echo $this->Html->tag('th', __('Supir'));
                    echo $this->Html->tag('th', __('Supir Pengganti'));
                    echo $this->Html->tag('th', __('Customer'));
                    echo $this->Html->tag('th', __('Dari'));
                    echo $this->Html->tag('th', __('Tujuan'));
            ?>
        </tr>
        <?php
                if(!empty($ttujs)){
                    foreach ($ttujs as $key => $value) {
        ?>
        <tr data-value="<?php echo $value['Ttuj']['id'];?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
            <td><?php echo date('d/m/Y', strtotime($value['Ttuj']['ttuj_date']));?></td>
            <td><?php echo $value['Ttuj']['nopol'];?></td>
            <td><?php echo $value['Ttuj']['driver_name'];?></td>
            <td><?php echo !empty($value['DriverPenganti']['driver_name'])?$value['DriverPenganti']['driver_name']:'-';?></td>
            <td><?php echo $value['Ttuj']['customer_name'];?></td>
            <td><?php echo $value['Ttuj']['from_city_name'];?></td>
            <td><?php echo $value['Ttuj']['to_city_name'];?></td>
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