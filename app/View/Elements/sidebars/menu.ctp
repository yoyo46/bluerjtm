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
            ?>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> list user', array(
                                'controller' => 'users',
                                'action' => 'list_user',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'user' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> group', array(
                                'controller' => 'users',
                                'action' => 'groups',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'user' )?'active':'',
                            ));

                            // if(in_array($GroupId, array('3'))){
                            //     $link_text = '<i class="fa fa-angle-double-right"></i> '.$this->Html->tag('span', 'Access List Manager');
                            //     echo $this->Html->tag('li', $this->Html->link($link_text, array(
                            //         'controller' => 'acl_manager',
                            //         'action' => 'acl',
                            //     ), array(
                            //         'escape' => false
                            //     )), array(
                            //         'class' => ( !empty($active_menu) && $active_menu == 'acl' )?'active':'',
                            //     ));
                            // }
                    ?>
                </ul>
                <?php
                    
                ?>
            </li>
            <?php
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup Customer', array(
                                'controller' => 'settings',
                                'action' => 'customer_groups',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'customer_groups' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Customer', array(
                                'controller' => 'settings',
                                'action' => 'customers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'customers' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Target Unit', array(
                                'controller' => 'settings',
                                'action' => 'customer_target_unit',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'customer_target_unit' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tipe Motor', array(
                                'controller' => 'settings',
                                'action' => 'type_motors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'type_motor' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Warna Motor', array(
                                'controller' => 'settings',
                                'action' => 'colors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'colors' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup Motor', array(
                                'controller' => 'settings',
                                'action' => 'group_motors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'group_motors' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kode Motor', array(
                                'controller' => 'settings',
                                'action' => 'code_motors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'code_motors' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php

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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Supir', array(
                                'controller' => 'trucks',
                                'action' => 'drivers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'drivers' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Truk', array(
                                'controller' => 'trucks',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'kir',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'kir_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir_payments' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'stnk',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'stnk' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'stnk_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'stnk_payments' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SIUP - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'siup',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'siup' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SIUP - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'siup_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'siup_payments' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Truk', array(
                                'controller' => 'trucks',
                                'action' => 'reports',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'reports' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ritase - DEPO', array(
                                'controller' => 'revenues',
                                'action' => 'ritase_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ritase_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ritase - RETAIL', array(
                                'controller' => 'revenues',
                                'action' => 'ritase_report',
                                'retail',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ritase_report_retail' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian', array(
                                'controller' => 'revenues',
                                'action' => 'achievement_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'achievement_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Monitoring Truk', array(
                                'controller' => 'revenues',
                                'action' => 'monitoring_truck',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'monitoring_truck' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Truk Per Kapasitas', array(
                                'controller' => 'trucks',
                                'action' => 'capacity_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'capacity_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Point Per Day', array(
                                'controller' => 'trucks',
                                'action' => 'point_perday_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'point_perday_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Point Per Plant - DEPO', array(
                                'controller' => 'trucks',
                                'action' => 'point_perplant_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'point_perplant_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Point Per Plant - RETAIL', array(
                                'controller' => 'trucks',
                                'action' => 'point_perplant_report',
                                'retail',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'retail_point_perplant_report' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Jalan', array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_jalan' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> TTUJ', array(
                                'controller' => 'revenues',
                                'action' => 'ttuj',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ttuj' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Truk Tiba', array(
                                'controller' => 'revenues',
                                'action' => 'truk_tiba',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'truk_tiba' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Bongkaran', array(
                                'controller' => 'revenues',
                                'action' => 'bongkaran',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'bongkaran' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Balik', array(
                                'controller' => 'revenues',
                                'action' => 'balik',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'balik' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Sampai di Pool', array(
                                'controller' => 'revenues',
                                'action' => 'pool',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'pool' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php

                    $activeMenu = false;
                    $dataMenu = array(
                        'index', 'tarif_angkutan'
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tarif Angkut', array(
                                'controller' => 'settings',
                                'action' => 'tarif_angkutan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'tarif_angkutan' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Revenue', array(
                                'controller' => 'revenues',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'revenue' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    $activeMenu = false;
                    $dataMenu = array(
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
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori LKU', array(
                                'controller' => 'lkus',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lkus' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran LKU', array(
                                'controller' => 'lkus',
                                'action' => 'payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lkus' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    $activeMenu = false;
                    $dataMenu = array(
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
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori LAKA', array(
                                'controller' => 'lakas',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'Lakas' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Cabang', array(
                                'controller' => 'settings',
                                'action' => 'branches',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'branches' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Perlengkapan', array(
                                'controller' => 'settings',
                                'action' => 'perlengkapan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'perlengkapan' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> COA', array(
                                'controller' => 'settings',
                                'action' => 'coas',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'coas' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Bank', array(
                                'controller' => 'settings',
                                'action' => 'banks',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'banks' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kota', array(
                                'controller' => 'settings',
                                'action' => 'cities',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cities' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Company', array(
                                'controller' => 'settings',
                                'action' => 'companies',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'companies' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Vendor', array(
                                'controller' => 'settings',
                                'action' => 'vendors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'vendors' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Jenis SIM', array(
                                'controller' => 'settings',
                                'action' => 'jenis_sim',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'jenis_sim' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> klasifikasi', array(
                                'controller' => 'settings',
                                'action' => 'classifications',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'classifications' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Warna Kalender', array(
                                'controller' => 'settings',
                                'action' => 'calendar_colors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'calendar_colors' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Icon Kalender', array(
                                'controller' => 'settings',
                                'action' => 'calendar_icons',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'calendar_icons' )?'active':'',
                            ));
                            // echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Jenis Perlengkapan', array(
                            //     'controller' => 'settings',
                            //     'action' => 'jenis_perlengkapan',
                            // ), array(
                            //     'escape' => false
                            // )), array(
                            //     'class' => ( !empty($active_menu) && $active_menu == 'jenis_perlengkapan' )?'active':'',
                            // ));
                    ?>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>