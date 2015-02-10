<?php
        $name = $User['first_name'];
        if(!empty($User['last_name'])){
            $name .= ' '.$User['last_name'];
        }
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php 
                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-dashboard"></i> <span>Dashboard</span>', '/', array(
                        'escape' => false
                    )), array(
                        'class' => ( !empty($active_menu) && $active_menu == 'dashboard' )?'active':'',
                    ));

                    $activeMenu = false;
                    $dataMenu = array(
                        'list_user', 'groups'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                        $activeMenu = 'active';
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                            'view_list_user', 'view_group_user'
                        ), $allowModule ) ) {
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_list_user', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> list user', array(
                                    'controller' => 'users',
                                    'action' => 'list_user',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'list_user' )?'active':'',
                                ));
                            }

                            if( in_array('view_group_user', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> group', array(
                                    'controller' => 'users',
                                    'action' => 'groups',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'groups' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
                <?php
                    
                ?>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                            'view_customers', 'view_group_customers', 'view_customer_target_unit'
                        ), $allowModule ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'customers', 'customer_groups', 'customer_target_unit'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-male"></i>
                    <span>Customer</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_group_customers', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup Customer', array(
                                    'controller' => 'settings',
                                    'action' => 'customer_groups',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'customer_groups' )?'active':'',
                                ));
                            }

                            if( in_array('view_customers', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Customer', array(
                                    'controller' => 'settings',
                                    'action' => 'customers',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'customers' )?'active':'',
                                ));
                            }

                            if( in_array('view_customer_target_unit', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Target Unit', array(
                                    'controller' => 'settings',
                                    'action' => 'customer_target_unit',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'customer_target_unit' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                            'view_type_motors', 'view_colors', 'view_group_motors',
                            'view_code_motors'
                        ), $allowModule ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'type_motor', 'colors', 'group_motors',
                            'code_motors'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span>Tipe Motor</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_type_motors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tipe Motor', array(
                                    'controller' => 'settings',
                                    'action' => 'type_motors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'type_motor' )?'active':'',
                                ));
                            }

                            if( in_array('view_colors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Warna Motor', array(
                                    'controller' => 'settings',
                                    'action' => 'colors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'colors' )?'active':'',
                                ));
                            }

                            if( in_array('view_group_motors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup Motor', array(
                                    'controller' => 'settings',
                                    'action' => 'group_motors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'group_motors' )?'active':'',
                                ));
                            }

                            if( in_array('view_code_motors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kode Motor', array(
                                    'controller' => 'settings',
                                    'action' => 'code_motors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'code_motors' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                            'view_drivers', 'view_trucks', 'view_directions',
                            'view_truck_reports', 'view_ritase_report', 
                            'view_ritase_report_retail', 'view_stnk',
                            'view_stnk_payments', 'view_kir', 'view_kir_payments',
                            'view_siup', 'view_siup_payments', 'view_achievement_report',
                            'view_monitoring_truck', 'view_capacity_report',
                            'view_point_perday_report', 'view_point_perplant_report',
                            'view_retail_point_perplant_report'
                        ), $allowModule ) ) {
                        $activeTruck = false;
                        $truckMenu = array(
                            'drivers', 'trucks', 'directions',
                            'reports', 'ritase_report', 'ritase_report_retail', 'stnk',
                            'stnk_payments', 'kir', 'kir_payments',
                            'siup', 'siup_payments', 'achievement_report',
                            'monitoring_truck', 'capacity_report',
                            'point_perday_report', 'point_perplant_report',
                            'retail_point_perplant_report'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $truckMenu) ) {
                            $activeTruck = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeTruck; ?>">
                <a href="#">
                    <i class="fa fa-truck"></i>
                    <span>Truk</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_drivers', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Supir', array(
                                    'controller' => 'trucks',
                                    'action' => 'drivers',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'drivers' )?'active':'',
                                ));
                            }

                            if( in_array('view_trucks', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Truk', array(
                                    'controller' => 'trucks',
                                    'action' => 'index',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                                ));
                            }

                            if( in_array('view_kirs', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Perpanjang', array(
                                    'controller' => 'trucks',
                                    'action' => 'kir',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'kir' )?'active':'',
                                ));
                            }

                            if( in_array('view_kir_payments', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Pembayaran', array(
                                    'controller' => 'trucks',
                                    'action' => 'kir_payments',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'kir_payments' )?'active':'',
                                ));
                            }

                            if( in_array('view_stnk', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Perpanjang', array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'stnk' )?'active':'',
                                ));
                            }

                            if( in_array('view_stnk_payments', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Pembayaran', array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk_payments',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'stnk_payments' )?'active':'',
                                ));
                            }

                            if( in_array('view_siup', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SIUP - Perpanjang', array(
                                    'controller' => 'trucks',
                                    'action' => 'siup',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'siup' )?'active':'',
                                ));
                            }

                            if( in_array('view_siup_payments', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SIUP - Pembayaran', array(
                                    'controller' => 'trucks',
                                    'action' => 'siup_payments',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'siup_payments' )?'active':'',
                                ));
                            }

                            if( in_array('view_truck_reports', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Truk', array(
                                    'controller' => 'trucks',
                                    'action' => 'reports',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'reports' )?'active':'',
                                ));
                            }

                            if( in_array('view_ritase_depo_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ritase - DEPO', array(
                                    'controller' => 'revenues',
                                    'action' => 'ritase_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'ritase_report' )?'active':'',
                                ));
                            }

                            if( in_array('view_ritase_retail_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ritase - RETAIL', array(
                                    'controller' => 'revenues',
                                    'action' => 'ritase_report',
                                    'retail',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'ritase_report_retail' )?'active':'',
                                ));
                            }

                            if( in_array('view_achievement_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian', array(
                                    'controller' => 'revenues',
                                    'action' => 'achievement_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'achievement_report' )?'active':'',
                                ));
                            }

                            if( in_array('view_monitoring_truck', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Monitoring Truk', array(
                                    'controller' => 'revenues',
                                    'action' => 'monitoring_truck',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'monitoring_truck' )?'active':'',
                                ));
                            }

                            if( in_array('view_capacity_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Truk Per Kapasitas', array(
                                    'controller' => 'trucks',
                                    'action' => 'capacity_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'capacity_report' )?'active':'',
                                ));
                            }

                            if( in_array('view_point_perday_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Point Per Day', array(
                                    'controller' => 'trucks',
                                    'action' => 'point_perday_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'point_perday_report' )?'active':'',
                                ));
                            }

                            if( in_array('view_point_perplant_report', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Point Per Plant - DEPO', array(
                                    'controller' => 'trucks',
                                    'action' => 'point_perplant_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'point_perplant_report' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                        'view_uang_jalan', 'view_ttuj', 'view_truk_tiba',
                        'view_bongkaran', 'view_balik', 
                        'view_pool'
                    ), $allowModule ) ) {
                        $activeTtuj = false;
                        $ttujMenu = array(
                            'ttuj', 'truk_tiba', 'bongkaran',
                            'balik', 'pool', 'uang_jalan'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $ttujMenu) ) {
                            $activeTtuj = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeTtuj; ?>">
                <a href="#">
                    <i class="fa fa-tag"></i>
                    <span>TTUJ</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_uang_jalan', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Jalan', array(
                                    'controller' => 'settings',
                                    'action' => 'uang_jalan',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'uang_jalan' )?'active':'',
                                ));
                            }

                            if( in_array('view_ttuj', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> TTUJ', array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'ttuj' )?'active':'',
                                ));
                            }

                            if( in_array('view_truk_tiba', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Truk Tiba', array(
                                    'controller' => 'revenues',
                                    'action' => 'truk_tiba',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'truk_tiba' )?'active':'',
                                ));
                            }

                            if( in_array('view_bongkaran', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Bongkaran', array(
                                    'controller' => 'revenues',
                                    'action' => 'bongkaran',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'bongkaran' )?'active':'',
                                ));
                            }

                            if( in_array('view_balik', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Balik', array(
                                    'controller' => 'revenues',
                                    'action' => 'balik',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'balik' )?'active':'',
                                ));
                            }

                            if( in_array('view_pool', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Sampai di Pool', array(
                                    'controller' => 'revenues',
                                    'action' => 'pool',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'pool' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                        'view_tarif_angkutan', 'view_revenues', 'view_invoices'
                    ), $allowModule ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'index', 'tarif_angkutan', 'invoices',
                            'invoice_reports', 'revenues'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-truck"></i>
                    <span>Revenue</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                            if( in_array('view_tarif_angkutan', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tarif Angkut', array(
                                    'controller' => 'settings',
                                    'action' => 'tarif_angkutan',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'tarif_angkutan' )?'active':'',
                                ));
                            }

                            if( in_array('view_revenues', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Revenue', array(
                                    'controller' => 'revenues',
                                    'action' => 'index',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'revenues' )?'active':'',
                                ));
                            }

                            if( in_array('view_invoices', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Invoice', array(
                                    'controller' => 'revenues',
                                    'action' => 'invoices',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'invoices' )?'active':'',
                                ));
                            }

                            if( in_array('view_revenue_reports', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Revenue', array(
                                    'controller' => 'revenues',
                                    'action' => 'invoice_reports',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'invoice_reports' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                        'view_lkus', 'view_lku_payments'
                    ), $allowModule ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'lkus', 'lku_payments'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-dollar"></i>
                    <span>LKU</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_lkus', $allowModule) ) {
                                 echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> LKU', array(
                                    'controller' => 'lkus',
                                    'action' => 'index',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'lkus' )?'active':'',
                                ));
                             }

                            if( in_array('view_lku_payments', $allowModule) ) {
                                 echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran LKU', array(
                                    'controller' => 'lkus',
                                    'action' => 'payments',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'lku_payments' )?'active':'',
                                ));
                             }
                    ?>
                </ul>
            </li>
            <?php
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                        'view_lakas'
                    ), $allowModule ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'lakas'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-ambulance"></i>
                    <span>LAKA</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_lakas', $allowModule) ) {
                                 echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> LAKA', array(
                                    'controller' => 'lakas',
                                    'action' => 'index',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'lakas' )?'active':'',
                                ));
                             }
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    if( !empty($allowModule) && $this->Common->getModuleAllow( array(
                        'view_branches', 'view_perlengkapan', 'view_coas',
                        'view_banks', 'view_cities', 'view_companies',
                        'view_vendors', 'view_jenis_sim', 'view_classifications',
                        'view_calendar_colors', 'view_calendar_icons'
                    ), $allowModule ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'cities', 'vendors', 'companies',
                            'perlengkapan', 'coas', 'branches', 
                            'classifications', 'banks', 'calendar_colors',
                            'calendar_icons'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-wrench"></i>
                    <span>Data Master</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            if( in_array('view_branches', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Cabang', array(
                                    'controller' => 'settings',
                                    'action' => 'branches',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'branches' )?'active':'',
                                ));
                            }

                            if( in_array('view_perlengkapan', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Perlengkapan', array(
                                    'controller' => 'settings',
                                    'action' => 'perlengkapan',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'perlengkapan' )?'active':'',
                                ));
                            }

                            if( in_array('view_coas', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> COA', array(
                                    'controller' => 'settings',
                                    'action' => 'coas',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'coas' )?'active':'',
                                ));
                            }

                            if( in_array('view_banks', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Bank', array(
                                    'controller' => 'settings',
                                    'action' => 'banks',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'banks' )?'active':'',
                                ));
                            }

                            if( in_array('view_cities', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kota', array(
                                    'controller' => 'settings',
                                    'action' => 'cities',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'cities' )?'active':'',
                                ));
                            }

                            if( in_array('view_companies', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Company', array(
                                    'controller' => 'settings',
                                    'action' => 'companies',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'companies' )?'active':'',
                                ));
                            }

                            if( in_array('view_vendors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Vendor', array(
                                    'controller' => 'settings',
                                    'action' => 'vendors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'vendors' )?'active':'',
                                ));
                            }

                            if( in_array('view_jenis_sim', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Jenis SIM', array(
                                    'controller' => 'settings',
                                    'action' => 'jenis_sim',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'jenis_sim' )?'active':'',
                                ));
                            }

                            if( in_array('view_classifications', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> klasifikasi', array(
                                    'controller' => 'settings',
                                    'action' => 'classifications',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'classifications' )?'active':'',
                                ));
                            }

                            if( in_array('view_calendar_colors', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Warna Kalender', array(
                                    'controller' => 'settings',
                                    'action' => 'calendar_colors',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'calendar_colors' )?'active':'',
                                ));
                            }

                            if( in_array('view_calendar_icons', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Icon Kalender', array(
                                    'controller' => 'settings',
                                    'action' => 'calendar_icons',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'calendar_icons' )?'active':'',
                                ));
                            }

                            if( in_array('view_parts_motor', $allowModule) ) {
                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Icon Kalender', array(
                                    'controller' => 'settings',
                                    'action' => 'parts_motor',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'parts_motor' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
            </li>
            <?php 
                    }
            ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>