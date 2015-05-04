<?php 
        echo $this->Form->create('Revenue', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getCashBankPpnRevenue',
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_doc',array(
                        'label'=> __('No. Doc'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Doc')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal'),
                        'class'=>'form-control date-range',
                        'required' => false,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('transaction_status',array(
                        'label'=> __('Status Revenue'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Status Revenue'),
                        'options' => array(
                            'unposting' => 'Unposting',
                            'posting' => 'Posting',
                            'invoiced' => 'Invoiced',
                            'paid' => 'Paid',
                        )
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nopol',array(
                        'label'=> __('No. Pol Truk'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Pol Truk')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_ttuj',array(
                        'label'=> __('No. TTUJ'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. TTUJ')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('customer_id',array(
                        'label'=> __('Customer'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Customer'),
                        'options' => $customers
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_reference',array(
                        'label'=> __('No. Reference'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Reference')
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
                        'action' => 'getCashBankPpnRevenue',
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'title' => $title,
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
        <tr>
            <?php 
                    echo $this->Html->tag('th', __('No. Ref'));
                    echo $this->Html->tag('th', __('No. Dokumen'));
                    echo $this->Html->tag('th', __('Jenis Tarif'));
                    echo $this->Html->tag('th', __('Tanggal Revenue'));
                    echo $this->Html->tag('th', __('No. TTUJ'));
                    echo $this->Html->tag('th', __('Truk'));
                    echo $this->Html->tag('th', __('Customer'));
                    echo $this->Html->tag('th', __('Status'));
                    echo $this->Html->tag('th', __('Dibuat'));
            ?>
        </tr>
        <?php
                if(!empty($revenues)){
                    foreach ($revenues as $key => $value) {
                        $id = $value['Revenue']['id'];
        ?>
        <tr data-value="<?php echo $id;?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo str_pad($value['Revenue']['id'], 5, '0', STR_PAD_LEFT);?></td>
            <td><?php echo $value['Revenue']['no_doc'];?></td>
            <td><?php echo ucfirst($value['Revenue']['type']);?></td>
            <td><?php echo $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');?></td>
            <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
            <td><?php echo !empty($value['Ttuj']['nopol'])?$value['Ttuj']['nopol']:'-';?></td>
            <td><?php echo !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:'-';?></td>
            <td>
                <?php 
                        $class_status = 'label label-warning';
                        $statusRevenue = ucfirst($value['Revenue']['transaction_status']);

                        if(!empty($value['Invoice']['complete_paid'])){
                            $class_status = 'label label-success';
                            $statusRevenue = __('Paid');
                        } else if($value['Revenue']['transaction_status'] == 'invoiced'){
                            $class_status = 'label label-primary';
                        } elseif($value['Revenue']['transaction_status'] == 'posting'){
                            $class_status = 'label label-info';
                        }

                    echo $this->Html->tag('span', $statusRevenue, array('class' => $class_status));
                ?>
            </td>
            <td><?php echo $this->Common->customDate($value['Revenue']['created']);?></td>
        </tr>
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '10'
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