<?php 
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'getInfoCoa',
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('code',array(
                        'label'=> __('Kode COA'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Kode COA'),
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                        'placeholder' => __('Nama'),
                    ));
            ?>
        </div>
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->hidden('find',array(
                'value' => true,
            ));
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-action' => $data_action,
                'data-parent' => true,
                'title' => $title,
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'ajax',
                'action' => 'getInfoCoa',
                'code' => 'none',
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'data-action' => $data_action,
                'title' => $title,
            ));
    ?>
</div>
<div class="box-body table-responsive" id="box-info-coa">
    <table class="table table-hover">
        <thead>
            <tr>
                <?php
                        // $input_all = $this->Form->checkbox('checkbox_all', array(
                        //     'class' => 'checkAll-coa'
                        // ));
                        // echo $this->Html->tag('th', $input_all);
                ?>
                <th><?php echo __('Kode Acc');?>    </th>
                <th><?php echo __('Nama Acc');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                    if(!empty($coas)){
                        $trucks = !empty($trucks)?$trucks:false;
                    	echo $this->Common->getRowCoa($coas, false, $trucks);
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
                    }
            ?>
        </tbody>
    </table>
</div>
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
                'title' => $title,
            ),
        ));
?>