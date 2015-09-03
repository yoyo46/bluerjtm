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
                            echo $this->Html->tag('li', $this->Html->link(__('TTUJ DEPO'), array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_add',
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('TTUJ RETAIL'), array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_add',
                                'retail',
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
                            )), array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_berangkat', __('Tgl Berangkat'), array(
                                'escape' => false
                            )), array(
                                'class' => 'text-center',
                            ));
                        }

                        switch ($active_menu) {
                            case 'truk_tiba':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_berangkat', __('Tgl Berangkat'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_tiba', __('Tgl Tiba'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'bongkaran':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_tiba', __('Tgl Tiba'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_bongkaran', __('Tgl Bongkaran'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'balik':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_bongkaran', __('Tgl Bongkaran'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_balik', __('Tgl Balik'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'pool':
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.branch_id', __('Cabang'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_balik', __('Tgl Balik'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_pool', __('Tgl Sampai Pool'), array(
                                    'escape' => false
                                )), array(
                                    'class' => 'text-center',
                                ));
                                break;
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

                        if( $active_menu == 'ttuj' ) {
                            echo $this->Html->tag('th', __('Tgl Terima SJ'), array(
                                'class' => 'text-center',
                            ));
                        }

                        // echo $this->Html->tag('th', __('Closing'));
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
                            $is_draft = $this->Common->filterEmptyField($value, 'Ttuj', 'is_draft');
                            $status = $this->Common->filterEmptyField($value, 'Ttuj', 'status');
            ?>
            <tr>
                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
                <?php 
                        if( $active_menu == 'ttuj' ) {
                            echo $this->Html->tag('td', date('d M Y', strtotime($value['Ttuj']['ttuj_date'])), array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_berangkat'])), array(
                                'class' => 'text-center',
                            ));
                        }


                        switch ($active_menu) {
                            case 'truk_tiba':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_berangkat'])), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_tiba'])), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'bongkaran':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_tiba'])), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_bongkaran'])), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'balik':
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_bongkaran'])), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_balik'])), array(
                                    'class' => 'text-center',
                                ));
                                break;

                            case 'pool':
                                $branch = $this->Common->filterEmptyField($value, 'Branch', 'name', '-');
                                echo $this->Html->tag('td', $branch, array(
                                    'class' => 'text-left',
                                ));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_balik'])), array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', date('d M Y - H:i', strtotime($value['Ttuj']['tgljam_pool'])), array(
                                    'class' => 'text-center',
                                ));
                                break;
                        }
                ?>
                <td><?php echo $value['Ttuj']['nopol'];?></td>
                <td><?php echo $value['Ttuj']['customer_name'];?></td>
                <td><?php echo $value['Ttuj']['from_city_name'];?></td>
                <td><?php echo $value['Ttuj']['to_city_name'];?></td>
                <?php 
                        if( $active_menu == 'ttuj' ) {
                            $sjLabel = '-';

                            if(!empty($value['SuratJalan']['tgl_surat_jalan'])){
                                $sjLabel = $this->Common->customDate($value['SuratJalan']['tgl_surat_jalan'], 'd/m/Y');
                            }

                            echo $this->Html->tag('td', $sjLabel, array(
                                'class' => 'text-center',
                            ));
                        }
                ?>
                <!-- <td class="text-center">
                    <?php 
                            // if( !empty($value['Ttuj']['completed']) ){
                            //     echo $this->Html->tag('span', $this->Common->icon('check'), array(
                            //         'class' => 'label label-success',
                            //     ));
                            // }else{
                            //     echo $this->Html->tag('span', $this->Common->icon('times'), array(
                            //         'class' => 'label label-danger',
                            //     ));
                            // }
                    ?>
                </td> -->
                <?php
                        
                        if(empty($status)){
                            echo $this->Html->tag('td', '<span class="label label-danger">Void</span>');
                        } else if(!empty($value['Ttuj']['is_laka'])){
                            echo $this->Html->tag('td', '<span class="label label-danger">LAKA</span>');
                        } else if(!empty($value['Ttuj']['is_pool'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Sampai Pool</span>');
                        } else if(!empty($value['Ttuj']['is_balik'])){
                            echo $this->Html->tag('td', '<span class="label label-info">Balik</span>');
                        } else if(!empty($value['Ttuj']['is_bongkaran'])){
                            echo $this->Html->tag('td', '<span class="label label-warning">Bongkaran</span>');
                        } else if(!empty($value['Ttuj']['is_arrive'])){
                            echo $this->Html->tag('td', '<span class="label label-info">Tiba</span>');
                        } else if(!empty($is_draft)){
                            echo $this->Html->tag('td', '<span class="label label-default">Draft</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-primary">Commit</span>');
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
                                    'class' => 'btn btn-info btn-xs'
                                ));

                                echo $this->Html->link(__('Edit'), array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_lanjutan_edit',
                                    $active_menu,
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_toggle',
                                    $id,
                                    $active_menu
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan menghapus data ini?'));
                            } else {
                                $labelEdit = __('Edit');

                                if( empty($is_draft) && !empty($status) ) {
                                    echo $this->Html->link(__('Surat Jalan'), array(
                                        'controller' => 'revenues',
                                        'action' => 'surat_jalan',
                                        $id
                                    ), array(
                                        'class' => 'btn bg-navy btn-xs'
                                    ));
                                }

                                echo $this->Html->link($labelEdit, array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                if( !empty($status) ) {
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'revenues',
                                        'action' => 'ttuj_toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs',
                                        'title' => 'disable status brand'
                                    ), __('Apakah Anda yakin akan membatalkan data ini?'));
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
                            'colspan' => '12'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>