<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/ttuj/search_ttuj');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    switch ($active_menu) {
                        case 'truk_tiba':
                            echo $this->Html->link('<i class="fa fa-plus"></i> Truk Tiba', array(
                                'controller' => 'revenues',
                                'action' => 'truk_tiba_add'
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-app btn-success pull-right'
                            ));
                            break;

                        case 'bongkaran':
                            echo $this->Html->link('<i class="fa fa-plus"></i> Bongkaran Truk', array(
                                'controller' => 'revenues',
                                'action' => 'bongkaran_add'
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-app btn-success pull-right'
                            ));
                            break;

                        case 'balik':
                            echo $this->Html->link('<i class="fa fa-plus"></i> Truk Balik', array(
                                'controller' => 'revenues',
                                'action' => 'balik_add'
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-app btn-success pull-right'
                            ));
                            break;

                        case 'pool':
                            echo $this->Html->link('<i class="fa fa-plus"></i> Sampai Pool', array(
                                'controller' => 'revenues',
                                'action' => 'pool_add'
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-app btn-success pull-right'
                            ));
                            break;
                        
                        default:
            ?>
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('TTUJ'), array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_add',
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('TTUJ Depo'), array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_add',
                                'depo',
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
            <?php
                            break;
                    }
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No TTUJ'), array(
                            'escape' => false
                        )));

                        if( $active_menu == 'ttuj' ) {
                            echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.ttuj_date', __('Tgl TTUJ'), array(
                                'escape' => false
                            )));

                        }

                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.nopol', __('No Pol'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.customer_name', __('Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.from_city_name', __('Dari'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.to_city_name', __('Tujuan'), array(
                            'escape' => false
                        )));

                        switch ($active_menu) {
                            case 'truk_tiba':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_berangkat', __('Tgl Berangkat'), array(
                                    'escape' => false
                                )));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_tiba', __('Tgl Tiba'), array(
                                    'escape' => false
                                )));
                                break;

                            case 'bongkaran':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_tiba', __('Tgl Tiba'), array(
                                    'escape' => false
                                )));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_bongkaran', __('Tgl Bongkaran'), array(
                                    'escape' => false
                                )));
                                break;

                            case 'balik':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_bongkaran', __('Tgl Bongkaran'), array(
                                    'escape' => false
                                )));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_balik', __('Tgl Balik'), array(
                                    'escape' => false
                                )));
                                break;

                            case 'pool':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_balik', __('Tgl Balik'), array(
                                    'escape' => false
                                )));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_pool', __('Tgl Sampai Pool'), array(
                                    'escape' => false
                                )));
                                break;
                        }
                        echo $this->Html->tag('th', __('Status'));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($ttujs)){
                        foreach ($ttujs as $key => $value) {
                            $id = $value['Ttuj']['id'];
            ?>
            <tr>
                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
                <?php 
                        if( $active_menu == 'ttuj' ) {
                            echo $this->Html->tag('td', date('d M Y', strtotime($value['Ttuj']['ttuj_date'])));
                        }
                ?>
                <td><?php echo $value['Ttuj']['nopol'];?></td>
                <td><?php echo $value['Ttuj']['customer_name'];?></td>
                <td><?php echo $value['Ttuj']['from_city_name'];?></td>
                <td><?php echo $value['Ttuj']['to_city_name'];?></td>
                <?php 
                        switch ($active_menu) {
                            case 'truk_tiba':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_berangkat'])));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_tiba'])));
                                break;

                            case 'bongkaran':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_tiba'])));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_bongkaran'])));
                                break;

                            case 'balik':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_bongkaran'])));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_balik'])));
                                break;

                            case 'pool':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_balik'])));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_pool'])));
                                break;
                        }

                        if(!empty($value['Ttuj']['is_pool'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Sampai Pool</span>');
                        } else if(!empty($value['Ttuj']['is_balik'])){
                            echo $this->Html->tag('td', '<span class="label label-info">Balik</span>');
                        } else if(!empty($value['Ttuj']['is_bongkaran'])){
                            echo $this->Html->tag('td', '<span class="label label-warning">Bongkaran</span>');
                        } else if(!empty($value['Ttuj']['is_arrive'])){
                            echo $this->Html->tag('td', '<span class="label label-info">Tiba</span>');
                        } else if(!empty($value['Ttuj']['is_draft'])){
                            echo $this->Html->tag('td', '<span class="label label-default">Unposting</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-primary">Posting</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['Ttuj']['created']);?></td>
                <td class="action">
                    <?php
                            if( in_array($active_menu, array( 'truk_tiba', 'bongkaran', 'balik', 'pool' )) ) {
                                echo $this->Html->link('Info', array(
                                    'controller' => 'revenues',
                                    'action' => 'info_truk',
                                    $active_menu,
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Batalkan'), array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_toggle',
                                    $id,
                                    $active_menu
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan membatalkan data ini?'));
                            } else {
                                echo $this->Html->link('Rubah', array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan membatalkan data ini?'));
                            }
                    ?>
                </td>
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
    <?php echo $this->element('pagination');?>
</div>