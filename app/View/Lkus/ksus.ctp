<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lkus/search_ksu');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));

                if( in_array('insert_lkus', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah KSU', array(
                        'controller' => 'lkus',
                        'action' => 'ksu_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
        <?php 
                }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.no_doc', __('No KSU'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No TTUJ'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', __('Customer'));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.tgl_lku', __('Tgl KSU'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.total_klaim', __('Total Klaim'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.total_price', __('Total Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.status', __('Status'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.paid', __('Status Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ksu.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($Ksus)){
                        foreach ($Ksus as $key => $value) {
                            $id = $value['Ksu']['id'];
            ?>
            <tr>
                <td><?php echo $value['Ksu']['no_doc'];?></td>
                <?php 
                        echo $this->Html->tag('td', $value['Ttuj']['no_ttuj']);

                        $customer = '';
                        if(!empty($value['Customer']['customer_name_code'])){
                            $customer = $value['Customer']['customer_name_code'];
                        }
                        echo $this->Html->tag('td', $customer);
                        echo $this->Html->tag('td', date('d M Y', strtotime($value['Ksu']['tgl_ksu'])));
                        echo $this->Html->tag('td', $this->Number->format($value['Ksu']['total_klaim']));
                        echo $this->Html->tag('td', $this->Number->currency($value['Ksu']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );

                        if(!empty($value['Ksu']['status'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Aktif</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-danger">Non-aktif</span>');
                        }

                        if(empty($value['Ksu']['kekurangan_atpm'])){
                            if(!empty($value['Ksu']['paid'])){
                                if(!empty($value['Ksu']['complete_paid'])){
                                    echo $this->Html->tag('td', '<span class="label label-success">Pembayaran Lunas</span>');
                                }else{
                                    echo $this->Html->tag('td', '<span class="label label-success">Dibayar Sebagian</span>');
                                }
                            } else{
                                echo $this->Html->tag('td', '<span class="label label-danger">Belum di bayar</span>');
                            }
                        }else{
                            echo $this->Html->tag('td', '<span class="label label-success">Dibayar Main Dealer</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['Ksu']['created']);?></td>
                <td class="action">
                    <?php
                        echo $this->Html->link('Info', array(
                            'controller' => 'lkus',
                            'action' => 'detail_ksu',
                            $id
                        ), array(
                            'class' => 'btn btn-info btn-xs'
                        ));

                        if( empty($value['Ksu']['paid']) && !empty($value['Ksu']['status']) ){
                            if( in_array('update_lkus', $allowModule) ) {
                                echo $this->Html->link('Rubah', array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_lkus', $allowModule) ) {
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan mengbatalkan data ini?'));
                            }
                        }else{
                            if(in_array('delete_lkus', $allowModule) && empty($value['Ksu']['status']) && empty($value['Ksu']['paid'])){
                                echo $this->Html->link(__('Cancel Void'), array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_toggle',
                                    $id,
                                    'activate'
                                ), array(
                                    'class' => 'btn btn-warning btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan mengaktifkan lagi data ini?'));
                            }
                        }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>