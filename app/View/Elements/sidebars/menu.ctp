<?php
        $name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');;
        $current_branch_id = !empty($current_branch_id)?$current_branch_id:false;
        $list_branches = !empty($list_branches)?$list_branches:false;
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <?php
                echo $this->Form->create('GroupBranch', array(
                    'url' => array(
                        'controller' => 'users',
                        'action' => 'change_branch'
                    ),
                    'class' => 'sidebar-form'
                ));
                echo $this->Form->input('branch_id', array(
                    'options' => $list_branches,
                    'class' => 'form-control',
                    'label' => false,
                    'div' => false,
                    'onchange' => 'submit()',
                    'value' => $current_branch_id
                ));

                echo $this->Form->end();
        ?>
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
                        'users' => array(
                            'list_user', 'groups', 'employes',
                            'employe_positions',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        if( !empty($active_menu) && in_array($active_menu, $dataMenu['users']) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Posisi Karyawan', array(
                                'controller' => 'users',
                                'action' => 'employe_positions',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'employe_positions' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup User', array(
                                'controller' => 'users',
                                'action' => 'groups',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'groups' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> list user', array(
                                'controller' => 'users',
                                'action' => 'list_user',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'list_user' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Karyawan', array(
                                'controller' => 'users',
                                'action' => 'employes',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'employes' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'customer_groups', 'customers', 'customer_target_unit',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeMenu = false;

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu['settings']) ) {
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
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'type_motors', 'colors', 'group_motors',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeMenu = false;

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu['settings']) ) {
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
                            
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tipe Motor', array(
                                'controller' => 'settings',
                                'action' => 'type_motors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'type_motors' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'trucks' => array(
                            'drivers', 'index', 'add_import',
                            'kir', 'stnk', 'siup', 'reports',
                            'capacity_report', 'point_perday_report',
                            'point_perplant_report', 'licenses_report'
                        ),
                        'leasings' => array(
                            'index'
                        ),
                        'revenues' => array(
                            'ritase_report', 'achievement_report',
                            'monitoring_truck'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeTruck = false;
                        $truckMenu = array(
                            'drivers', 'trucks', 'directions',
                            'reports', 'ritase_report', 'ritase_report_retail', 'stnk', 'kir',
                            'siup', 'achievement_report',
                            'monitoring_truck', 'capacity_report',
                            'point_perday_report', 'point_perplant_report',
                            'view_leasing', 'licenses_report', 'truck_import',
                            'daily_report'
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Import Truk', array(
                                'controller' => 'trucks',
                                'action' => 'add_import',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'truck_import' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Leasing', array(
                                'controller' => 'leasings',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'view_leasing' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'kir',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'stnk',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'stnk' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Ijin Usaha - Perpanjang', array(
                                'controller' => 'trucks',
                                'action' => 'siup',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'siup' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Surat-surat Truk', array(
                                'controller' => 'trucks',
                                'action' => 'licenses_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'licenses_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Harian Kendaraan', array(
                                'controller' => 'trucks',
                                'action' => 'daily_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'daily_report' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'uang_jalan', 'uang_kuli'
                        ),
                        'revenues' => array(
                            'ttuj', 'truk_tiba', 'bongkaran',
                            'balik', 'pool'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeTtuj = false;
                        $ttujMenu = array(
                            'ttuj', 'truk_tiba', 'bongkaran',
                            'balik', 'pool', 'uang_jalan',
                            'uang_kuli_muat', 'uang_kuli_bongkar',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Kuli Muat', array(
                                'controller' => 'settings',
                                'action' => 'uang_kuli',
                                'muat',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_kuli_muat' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Kuli Bongkar', array(
                                'controller' => 'settings',
                                'action' => 'uang_kuli',
                                'bongkar',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_kuli_bongkar' )?'active':'',
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
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'tarif_angkutan'
                        ),
                        'revenues' => array(
                            'index', 'invoices', 'invoice_reports',
                            'ar_period_reports', 'list_kwitansi',
                            'report_customers', 'report_revenue_customers',
                            'report_monitoring_sj_revenue',
                            'report_revenue_monthly'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'index', 'tarif_angkutan', 'invoices',
                            'invoice_reports', 'revenues', 'ar_period_reports',
                            'list_kwitansi', 'report_customers',
                            'report_revenue_customers', 'report_monitoring_sj_revenue',
                            'report_revenue_monthly'
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
                                'class' => ( !empty($active_menu) && $active_menu == 'revenues' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Invoice', array(
                                'controller' => 'revenues',
                                'action' => 'invoices',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'invoices' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Account Receivable Aging Report', array(
                                'controller' => 'revenues',
                                'action' => 'invoice_reports',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'invoice_reports' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan AR Per Period', array(
                                'controller' => 'revenues',
                                'action' => 'ar_period_reports',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ar_period_reports' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> List Kwitansi', array(
                                'controller' => 'revenues',
                                'action' => 'list_kwitansi',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'list_kwitansi' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Piutang Per Customer', array(
                                'controller' => 'revenues',
                                'action' => 'report_customers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_customers' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan pendapatan per customer per bulan', array(
                                'controller' => 'revenues',
                                'action' => 'report_revenue_customers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_revenue_customers' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Monitoring Surat Jalan & Revenue', array(
                                'controller' => 'revenues',
                                'action' => 'report_monitoring_sj_revenue',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_monitoring_sj_revenue' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Saldo Piutang Per Bulan', array(
                                'controller' => 'revenues',
                                'action' => 'report_revenue_monthly',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_revenue_monthly' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'lkus' => array(
                            'index',
                        ),
                        'ksus' => array(
                            'index',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu  ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'lkus', 'ksus'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                            $activeMenu = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-dollar"></i>
                    <span>LKU/KSU</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> LKU', array(
                                'controller' => 'lkus',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lkus' )?'active':'',
                            ));
                         
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KSU', array(
                                'controller' => 'lkus',
                                'action' => 'ksus',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ksus' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'lakas' => array(
                            'index'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
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
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> LAKA', array(
                                'controller' => 'lakas',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lakas' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    $activeSetting = false;
                    $settingMenu = array(
                        'cash_bank', 'kir_payments',
                        'stnk_payments', 'siup_payments', 'invoice_payments', 
                        'lku_payments', 'coa_setting', 'ksu_payments',
                        'uang_jalan_commission_payments',
                        'biaya_ttuj_payments', 'journal_report',
                        'prepayment_report'
                    );
                    $dataMenu = array(
                        'cashbanks' => array(
                            'index', 'coa_setting',
                            'journal_report', 'prepayment_report',
                        ),
                        'trucks' => array(
                            'kir_payments', 'stnk_payments', 'siup_payments',
                        ),
                        'revenues' => array(
                            'invoice_payments', 'ttuj_payments'
                        ),
                        'lkus' => array(
                            'payments', 'ksu_payments',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu  ) ) {
                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Kas/Bank</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pengaturan COA</span>', array(
                                'controller' => 'cashbanks',
                                'action' => 'coa_setting'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'coa_setting' )?'active':'',
                            ));
                            
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kas/Bank</span>', array(
                                'controller' => 'cashbanks',
                                'action' => 'index'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cash_bank' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Uang Jalan/Komisi', array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_payments',
                                'uang_jalan_commission',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_jalan_commission_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Biaya TTUJ', array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_payments',
                                'biaya_ttuj',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'biaya_ttuj_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Invoice', array(
                                'controller' => 'revenues',
                                'action' => 'invoice_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'invoice_payments' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran LKU', array(
                                'controller' => 'lkus',
                                'action' => 'payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lku_payments' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran KSU', array(
                                'controller' => 'lkus',
                                'action' => 'ksu_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ksu_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Asuransi', '#', array(
                                'escape' => false
                            )), array(
                                // 'class' => ( !empty($active_menu) && $active_menu == 'invoice_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Leasing', '#', array(
                                'escape' => false
                            )), array(
                                // 'class' => ( !empty($active_menu) && $active_menu == 'invoice_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> KIR - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'kir_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> STNK - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'stnk_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'stnk_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Ijin Usaha - Pembayaran', array(
                                'controller' => 'trucks',
                                'action' => 'siup_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'siup_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran LAKA', '#', array(
                                'escape' => false
                            )), array(
                                // 'class' => ( !empty($active_menu) && $active_menu == 'invoice_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Prepayment</span>', array(
                                'controller' => 'cashbanks',
                                'action' => 'prepayment_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'prepayment_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Jurnal', array(
                                'controller' => 'cashbanks',
                                'action' => 'journal_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'journal_report' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    $dataMenu = array(
                        'products' => array(
                            'categories', 'brands',
                        ),
                        'spk' => array(
                            'internal',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'internal', 'product_categories', 'product_brands'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Gudang</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kategori Barang</span>', array(
                                'controller' => 'products',
                                'action' => 'categories'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'product_categories' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Merk Barang</span>', array(
                                'controller' => 'products',
                                'action' => 'brands'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'product_brands' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SPK Internal</span>', array(
                                'controller' => 'spk',
                                'action' => 'internal'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'internal' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'perlengkapan', 'coas', 'banks',
                            'cities', 'companies', 'vendors',
                            'jenis_sim', 'classifications',
                            'calendar_colors', 'calendar_icons',
                            'parts_motor', 'index', 'approval_setting'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'cities', 'vendors', 'companies',
                            'perlengkapan', 'coas', 'branches', 
                            'classifications', 'banks', 'calendar_colors',
                            'calendar_icons', 'settings', 'jenis_sim',
                            'parts_motor', 'approval_setting'
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

                            // echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Import Kota', array(
                            //     'controller' => 'settings',
                            //     'action' => 'city_import',
                            // ), array(
                            //     'escape' => false
                            // )), array(
                            //     'class' => ( !empty($active_menu) && $active_menu == 'cities' )?'active':'',
                            // ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> '.__('Cabang'), array(
                                'controller' => 'settings',
                                'action' => 'branches',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'branches' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Sparepart Motor', array(
                                'controller' => 'settings',
                                'action' => 'parts_motor',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'parts_motor' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pengaturan Approval</span>', array(
                                'controller' => 'settings',
                                'action' => 'approval_setting'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'approval_setting' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-wrench"></i> Pengaturan', array(
                                'controller' => 'settings',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'settings' )?'active':'',
                            ));
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