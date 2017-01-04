<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_revenue');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Revenue'), array(
                                'controller' => 'revenues',
                                'action' => 'add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Manual Revenue'), array(
                                'controller' => 'revenues',
                                'action' => 'add',
                                'manual',
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel'), array(
                                'controller' => 'revenues',
                                'action' => 'import',
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel by TTUJ'), array(
                                'controller' => 'revenues',
                                'action' => 'import_by_ttuj',
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <?php
            echo $this->Form->create('Revenue', array(
                'url'=> array(
                    'controller' => 'revenues',
                    'action' => 'action_post_revenue'
                ), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
                'autocomplete'=> 'off', 
                'id' => 'rev_post_form'
            ));

            echo $this->Form->hidden('posting_type', array(
                'id' => 'posting_type'
            ));
    ?>
    <div class="box-body table-responsive">
        <div class="trigger-posting btn-group">
            <?php
                if(!empty($postingUnposting)){
                    echo $this->Html->tag('button', __('Posting'), array(
                        'class' => 'btn btn-default submit_butt',
                        'data-val' => 'posting'
                    ));
                    echo $this->Html->tag('button', __('Unposting'), array(
                        'class' => 'btn btn-default submit_butt',
                        'data-val' => 'unposting'
                    ));
                }
            ?>
        </div>
        <table class="table table-hover">
            <tr>
                <?php 
                        $input_all = $this->Form->checkbox('checkbox_all', array(
                            'class' => 'checkAll'
                        ));
                        echo $this->Html->tag('th', $input_all);

                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.id', __('No. Ref'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.no_doc', __('No. Dok'), array(
                            'escape' => false
                        )));
                        // echo $this->Html->tag('th', $this->Paginator->sort('Revenue.type', __('Jenis Tarif'), array(
                        //     'escape' => false
                        // )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.date_revenue', __('Tgl Revenue'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No. TTUJ'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.nopol', __('Truk'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Customer.code', __('Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.from_city_name', __('Dari-Tujuan'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.transaction_status', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($revenues)){
                        foreach ($revenues as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
                            $is_manual = $this->Common->filterEmptyField($value, 'Revenue', 'is_manual');
                            $periode = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue', false, false, array(
                                'date' => 'Y-m',
                            ));
                            
                            $from_city = $this->Common->filterEmptyField($value, 'FromCity', 'name');
                            $to_city = $this->Common->filterEmptyField($value, 'ToCity', 'name');

                            $from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name', $from_city);
                            $to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name', $to_city);
            ?>
            <tr>
                <td>
                    <?php
                            if( !in_array($value['Revenue']['transaction_status'], array( 'invoiced', 'half_invoiced' )) ){
                                echo $this->Form->checkbox('revenue_id.', array(
                                    'class' => 'check-option',
                                    'value' => $id
                                ));
                            }
                    ?>
                </td>
                <td><?php echo str_pad($value['Revenue']['id'], 5, '0', STR_PAD_LEFT);?></td>
                <td><?php echo $value['Revenue']['no_doc'];?></td>
                <!-- <td><?php // echo ucfirst($value['Revenue']['type']);?></td> -->
                <td><?php echo $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');?></td>
                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
                <td>
                    <?php
                            if( !empty($value['Ttuj']['nopol']) ) {
                                echo $value['Ttuj']['nopol'];
                            } elseif( !empty($value['Truck']['nopol']) ) {
                                echo $value['Truck']['nopol'];
                            } else {
                                echo '-';
                            }
                    ?>
                </td>
                <td>
                    <?php
                            echo !empty($value['Customer']['code'])?$value['Customer']['code']:'-';
                    ?>
                </td>
                <?php 
                        echo $this->Html->tag('td', sprintf('%s - %s', $from_city, $to_city));
                ?>
                <td>
                    <?php 
                            $class_status = 'label label-default';
                            $statusRevenue = ucfirst($value['Revenue']['transaction_status']);
                            $labelEdit = __('Ubah');

                            if(empty($value['Revenue']['status'])){
                                $class_status = 'label label-danger';
                                $statusRevenue = __('Non-Aktif');
                                $labelEdit = __('Detail');
                            } else if(!empty($value['Invoice']['complete_paid'])){
                                $class_status = 'label label-success';
                                $statusRevenue = __('Paid');
                                $labelEdit = __('Detail');
                            } else if($value['Revenue']['transaction_status'] == 'half_invoiced'){
                                $class_status = 'label label-warning';
                                $statusRevenue = __('Half Invoiced');
                                $labelEdit = __('Detail');
                            } else if($value['Revenue']['transaction_status'] == 'invoiced'){
                                $class_status = 'label label-primary';
                                $labelEdit = __('Detail');
                            } elseif($value['Revenue']['transaction_status'] == 'posting'){
                                $class_status = 'label label-info';
                            }

                        echo $this->Html->tag('span', $statusRevenue, array('class' => $class_status));
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value['Revenue']['created']);?></td>
                <td class="action">
                    <?php
                            $urlEdit = array(
                                'controller' => 'revenues',
                                'action' => 'edit',
                                $id
                            );

                            if( !empty($is_manual) ) {
                                $urlEdit[] = 'manual';
                            }

                            echo $this->Html->link($labelEdit, $urlEdit, array(
                                'class' => 'btn btn-primary btn-xs',
                                'closing' => true,
                                'periode' => $periode,
                                'data-btn-replace' => array(
                                    'controller' => 'revenues',
                                    'action' => 'edit',
                                    $id
                                ),
                                'data-btn-replace-label' => __('Detail'),
                            ));
                            
                            if( !in_array($value['Revenue']['transaction_status'], array( 'invoiced', 'half_invoiced' )) && !empty($value['Revenue']['status']) ){
                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'revenues',
                                    'action' => 'revenue_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'title' => 'disable status',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-alert' => __('Anda yakin ingin menghapus revenue ini?'),
                                ));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '11'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php
        echo $this->Form->end();

        echo $this->element('pagination');
    ?>
</div>