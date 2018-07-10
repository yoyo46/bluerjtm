<?php
        $name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');;
        $current_branch_id = !empty($current_branch_id)?$current_branch_id:false;
        $list_branches = !empty($list_branches)?$list_branches:false;
        $active_menu = !empty($active_menu)?$active_menu:false;
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas hidden-print">
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
                            'employe_positions', 'report_logins',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Log User', array(
                                'controller' => 'users',
                                'action' => 'report_logins',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_logins' )?'active':'',
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
                            'point_perplant_report', 'licenses_report',
                            'daily_report', 'mutations', 'driver_reports',
                            'leadtime_report', 'profit_loss',
                        ),
                        'revenues' => array(
                            'ritase_report', 'achievement_report',
                            'monitoring_truck', 'ttuj_report',
                            'report_expense_per_truck', 'achievement_rit_report',
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
                            'licenses_report', 'truck_import',
                            'daily_report', 'mutations', 'driver_reports',
                            'ttuj_report', 'leadtime_report',
                            'report_expense_per_truck', 'achievement_rit_report',
                            'profit_loss_truck'
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

                            // echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Import Truk', array(
                            //     'controller' => 'trucks',
                            //     'action' => 'add_import',
                            // ), array(
                            //     'escape' => false
                            // )), array(
                            //     'class' => ( !empty($active_menu) && $active_menu == 'truck_import' )?'active':'',
                            // ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Mutasi Truk', array(
                                'controller' => 'trucks',
                                'action' => 'mutations',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'mutations' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Supir', array(
                                'controller' => 'trucks',
                                'action' => 'driver_reports',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'driver_reports' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per RIT', array(
                                'controller' => 'revenues',
                                'action' => 'achievement_rit_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'achievement_rit_report' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pencapaian Per Customer Per Hari', array(
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Biaya Uang Jalan', array(
                                'controller' => 'trucks',
                                'action' => 'ttuj_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ttuj_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan leadtime', array(
                                'controller' => 'trucks',
                                'action' => 'leadtime_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'leadtime_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Expense Revenue per Truk', array(
                                'controller' => 'revenues',
                                'action' => 'report_expense_per_truck',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_expense_per_truck' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Laba Rugi Truk', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'trucks',
                                'action' => 'profit_loss',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'profit_loss_truck' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'leasings' => array(
                            'index', 'leasing_report',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeTruck = false;
                        $truckMenu = array(
                            'view_leasing', 'leasing_report',
                        );

                        if( !empty($active_menu) && in_array($active_menu, $truckMenu) ) {
                            $activeTruck = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeTruck; ?>">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Leasing</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Leasing', array(
                                'controller' => 'leasings',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'view_leasing' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Leasing', array(
                                'controller' => 'leasings',
                                'action' => 'leasing_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'leasing_report' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'insurances' => array(
                            'index', 'report',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeTruck = false;
                        $listMenu = array(
                            'insurance', 'insurance_report',
                        );

                        if( !empty($active_menu) && in_array($active_menu, $listMenu) ) {
                            $activeTruck = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeTruck; ?>">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Asuransi</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Asuransi', array(
                                'controller' => 'insurances',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'insurance' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Asuransi', array(
                                'controller' => 'insurances',
                                'action' => 'report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'insurance_report' )?'active':'',
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
                            'balik', 'pool', 'surat_jalan',
                            'report_surat_jalan',
                        ),
                        'ttujs' => array(
                            'report_recap_sj',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeTtuj = false;
                        $ttujMenu = array(
                            'ttuj', 'truk_tiba', 'bongkaran',
                            'balik', 'pool', 'uang_jalan',
                            'uang_kuli_muat', 'uang_kuli_bongkar',
                            'surat_jalan', 'report_surat_jalan',
                            'report_recap_sj',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Surat Jalan', array(
                                'controller' => 'revenues',
                                'action' => 'surat_jalan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'surat_jalan' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Surat Jalan', array(
                                'controller' => 'revenues',
                                'action' => 'report_surat_jalan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_surat_jalan' )?'active':'',
                            ));

                            echo $this->Common->link(__('Laporan Rekap Penerimaan Surat Jalan') , array(
                                'controller' => 'ttujs',
                                'action' => 'report_recap_sj',
                                'admin' => false,
                            ), array(
                                'data-wrapper' => 'li',
                                'data-icon' => 'angle-double-right',
                                'data-slug' => 'report_recap_sj',
                                'data-active' => $active_menu,
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
                            'report_revenue_monthly', 'report_revenue_period',
                            'report_revenue',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'index', 'tarif_angkutan', 'invoices',
                            'invoice_reports', 'revenues', 'ar_period_reports',
                            'list_kwitansi', 'report_customers',
                            'report_revenue_customers', 'report_monitoring_sj_revenue',
                            'report_revenue_monthly', 'report_revenue_period',
                            'report_revenue',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Detail Revenue', array(
                                'controller' => 'revenues',
                                'action' => 'report_revenue_period',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_revenue_period' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Detail Revenue (Rinci)', array(
                                'controller' => 'revenues',
                                'action' => 'report_revenue',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_revenue' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'lkus' => array(
                            'index', 'reports',
                        ),
                        'ksus' => array(
                            'index', 'ksu_reports',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu  ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'lkus', 'ksus', 'lku_reports', 'ksu_reports',
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
                         
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan LKU', array(
                                'controller' => 'lkus',
                                'action' => 'reports',
                                'lku',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'lku_reports' )?'active':'',
                            ));
                         
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan KSU', array(
                                'controller' => 'lkus',
                                'action' => 'reports',
                                'ksu',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ksu_reports' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    }

                    $dataMenu = array(
                        'lakas' => array(
                            'index', 'reports'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeMenu = false;
                        $dataMenu = array(
                            'lakas', 'laka_reports'
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
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan LAKA', array(
                                'controller' => 'lakas',
                                'action' => 'reports',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'laka_reports' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    /*
                    $dataMenu = array(
                        'settings' => array(
                            'coas', 'banks',
                        ),
                        'cashbanks' => array(
                            'coa_setting', 'closing',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'coas', 'banks', 'coa_setting',
                            'closing'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-folder-open"></i>
                    <span>COA</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Bank', array(
                                'controller' => 'settings',
                                'action' => 'banks',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'banks' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> COA', array(
                                'controller' => 'settings',
                                'action' => 'coas',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'coas' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pengaturan COA', array(
                                'controller' => 'cashbanks',
                                'action' => 'coa_setting'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'coa_setting' )?'active':'',
                            ));
                            
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Closing', array(
                                'controller' => 'cashbanks',
                                'action' => 'closing'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'closing' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }
                    */
            ?>
            <?php
                    $activeSetting = false;
                    $settingMenu = array(
                        'general_ledgers', 'journal_report', 'profit_loss',
                        'balance_sheets', 'cash_flows', 'journal_rinci_report',
                        'coas', 'banks', 'coa_setting',
                        'closing'
                    );
                    $dataMenu = array(
                        'cashbanks' => array(
                            'general_ledgers', 'journal_report', 'profit_loss',
                            'balance_sheets', 'cash_flows', 'journal_rinci_report',
                            'coa_setting', 'closing',
                        ),
                        'settings' => array(
                            'coas', 'banks',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu  ) ) {
                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }

                        echo $this->Html->tag('li',
                            $this->Html->link(
                                $this->Common->icon('crosshairs').
                                $this->Html->tag('span', __('Accounting')).
                                $this->Common->icon('angle-left', false, 'i', 'pull-right'), 
                                '#', array(
                                'escape' => false,
                            )).
                            $this->Html->tag('ul',
                                $this->Html->tag('li', $this->Html->link(__('%s Bank', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'settings',
                                    'action' => 'banks',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( $active_menu == 'banks' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s COA', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'settings',
                                    'action' => 'coas',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( $active_menu == 'coas' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Pengaturan COA', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'coa_setting'
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( $active_menu == 'coa_setting' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Closing', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'closing'
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( $active_menu == 'closing' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Jurnal Umum', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'general_ledgers'
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( $active_menu == 'general_ledgers' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Laporan Jurnal', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'journal_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'journal_report' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Laporan Jurnal Rinci', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'journal_rinci_report',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'journal_rinci_report' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Laporan Laba Rugi', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'profit_loss',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'profit_loss' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Laporan Neraca', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'balance_sheets',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'balance_sheets' )?'active':'',
                                )).
                                $this->Html->tag('li', $this->Html->link(__('%s Laporan Cash Flow', $this->Common->icon('angle-double-right')), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'cash_flows'
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'cash_flows' )?'active':'',
                                )), array(
                                'class' => 'treeview-menu',
                            )), array(
                            'class' => __('treeview %s', $activeSetting),
                        ));
                    }
            ?>
            <?php
                    $activeSetting = false;
                    $settingMenu = array(
                        'cash_bank', 'invoice_payments', 
                        'lku_payments', 'ksu_payments',
                        'uang_jalan_commission_payments',
                        'biaya_ttuj_payments',
                        'prepayment_report', 'ledger_report',
                        'report_ttuj_payment', 'report_ttuj_outstanding',
                        'document_payments', 'laka_payments',
                        'asset_sells', 'leasing_payments',
                        'insurance_payments',
                    );
                    $dataMenu = array(
                        'cashbanks' => array(
                            'index', 'prepayment_report',
                            'ledger_report',
                        ),
                        'trucks' => array(
                            'kir_payments', 'stnk_payments', 'siup_payments',
                            'document_payments',
                        ),
                        'revenues' => array(
                            'invoice_payments', 'ttuj_payments',
                            'report_ttuj_payment', 'report_ttuj_outstanding'
                        ),
                        'lkus' => array(
                            'payments', 'ksu_payments',
                        ),
                        'lakas' => array(
                            'laka_payments',
                        ),
                        'assets' => array(
                            'sells',
                        ),
                        'insurances' => array(
                            'payments',
                        ),
                        'leasings' => array(
                            'payments',
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kas/Bank', array(
                                'controller' => 'cashbanks',
                                'action' => 'index'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cash_bank' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Penjualan Asset', array(
                                'controller' => 'assets',
                                'action' => 'sells'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'asset_sells' )?'active':'',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Leasing', array(
                                'controller' => 'leasings',
                                'action' => 'payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'leasing_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Asuransi', array(
                                'controller' => 'insurances',
                                'action' => 'payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'insurance_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran Surat-surat Truk', array(
                                'controller' => 'trucks',
                                'action' => 'document_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'document_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran LAKA', array(
                                'controller' => 'lakas',
                                'action' => 'payments'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'laka_payments' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Prepayment', array(
                                'controller' => 'cashbanks',
                                'action' => 'prepayment_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'prepayment_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ledger', array(
                                'controller' => 'cashbanks',
                                'action' => 'ledger_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ledger_report' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Pembayaran Biaya Uang Jalan', array(
                                'controller' => 'revenues',
                                'action' => 'report_ttuj_payment',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_ttuj_payment' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Saldo Biaya Uang Jalan', array(
                                'controller' => 'revenues',
                                'action' => 'report_ttuj_outstanding',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'report_ttuj_outstanding' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }
            ?>
            <?php

                    $activeSetting = false;
                    $settingMenu = array(
                        'asset_groups', 'assets',
                        'asset_reports'
                    );
                    $dataMenu = array(
                        'assets' => array(
                            'groups', 'index',
                            'reports',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu  ) ) {
                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <?php                            
                        $contentMenu = $this->Common->icon('files-o');
                        $contentMenu .= $this->Html->tag('span', __('Asset'));
                        $contentMenu .= $this->Common->icon('angle-left', false, 'i', 'pull-right');

                        echo $this->Html->link($contentMenu, '#', array(
                            'escape' => false,
                        ));
                ?>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Html->tag('li', $this->Html->link(sprintf(__('%s Group Asset'), $this->Common->icon('double-right')), array(
                                'controller' => 'assets',
                                'action' => 'groups'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'group_assets' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(sprintf(__('%s Asset'), $this->Common->icon('double-right')), array(
                                'controller' => 'assets',
                                'action' => 'index'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'assets' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(sprintf(__('%s Laporan Asset'), $this->Common->icon('double-right')), array(
                                'controller' => 'assets',
                                'action' => 'reports'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'asset_reports' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    $dataMenu = array(
                        'products' => array(
                            'categories', 'units',
                            'receipts', 'expenditures',
                            'current_stock_reports',
                            'stock_cards',
                            'expenditure_reports',
                            'receipt_reports',
                            'adjustment',
                            'min_stock_report',
                            'index', 'retur',
                            'category_report',
                            'target_categories',
                            'indicator_maintenance',
                        ),
                        'spk' => array(
                            'index', 'tire_reports', 'spk_reports',
                            'maintenance_cost_report', 'adjustment_report',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'spk', 'product_categories', 'products',
                            'receipts', 'product_units', 'expenditures',
                            'current_stock_reports', 'stock_cards',
                            'expenditure_reports',
                            'receipt_reports', 'tire_reports', 'spk_reports',
                            'maintenance_cost_report', 'adjustment',
                            'adjustment_report', 'min_stock_report',
                            'retur', 'spk_payment', 'category_report',
                            'target_categories', 'indicator_maintenance',
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-cubes"></i>
                    <span>Gudang</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Html->tag('li', $this->Html->link(__('%s Satuan', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'units'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'product_units' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Grup Barang', array(
                                'controller' => 'products',
                                'action' => 'categories'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'product_categories' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Target Grup Barang', array(
                                'controller' => 'products',
                                'action' => 'target_categories'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'target_categories' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Barang', array(
                                'controller' => 'products',
                                'action' => 'index'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'products' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> SPK', array(
                                'controller' => 'spk',
                                'action' => 'index'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'spk' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Pengeluaran', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'expenditures'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'expenditures' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Penerimaan', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'receipts'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'receipts' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Retur', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'retur'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'retur' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Penyesuaian Qty', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'adjustment'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'adjustment' )?'active':'',
                            ));
                            // echo $this->Html->tag('li', $this->Html->link(__('%s Pembayaran SPK Eksternal', $this->Common->icon('angle-double-right')), array(
                            //     'controller' => 'spk',
                            //     'action' => 'payments'
                            // ), array(
                            //     'escape' => false
                            // )), array(
                            //     'class' => ( !empty($active_menu) && $active_menu == 'spk_payment' )?'active':'',
                            // ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Kartu Stok', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'stock_cards'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'stock_cards' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Current Stok', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'current_stock_reports'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'current_stock_reports' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Pengeluaran', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'expenditure_reports'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'expenditure_reports' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Penerimaan', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'receipt_reports'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'receipt_reports' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan SPK', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'spk',
                                'action' => 'spk_reports'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'spk_reports' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Perbaikan', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'spk',
                                'action' => 'maintenance_cost_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'maintenance_cost_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Penyesuaian Qty', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'adjustment_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'adjustment_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Minimum Stok', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'min_stock_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'min_stock_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('%s Laporan Grup Barang', $this->Common->icon('angle-double-right')), array(
                                'controller' => 'products',
                                'action' => 'category_report'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'category_report' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Indikator Maintenance', array(
                                'controller' => 'products',
                                'action' => 'indicator_maintenance'
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'indicator_maintenance' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>

            <?php 
                    }

                    $dataMenu = array(
                        'purchases' => array(
                            'supplier_quotations', 'purchase_orders',
                            'payments', 'reports',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $lblQuotation = __('Penawaran Supplier');
                        $lblPO = __('Purchase Order');
                        $lblPOPayment = __('Pembayaran PO/SPK');
                        $lblPOReport = __('Laporan PO');
                        
                        $settingMenu = array(
                            $lblQuotation, $lblPO, $lblPOPayment,
                            $lblPOReport,
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <?php 
                        echo $this->Common->link(__('Purchase Order'), '#', array(
                            'data-icon' => 'tag',
                            'data-caret' => $this->Common->icon('angle-left', false, 'i', 'pull-right'),
                        ));
                ?>
                <ul class="treeview-menu">
                    <?php
                            echo $this->Common->link($lblQuotation , array(
                                'controller' => 'purchases',
                                'action' => 'supplier_quotations',
                                'admin' => false,
                            ), array(
                                'data-wrapper' => 'li',
                                'data-icon' => 'angle-double-right',
                                'data-active' => $active_menu,
                            ));
                            echo $this->Common->link($lblPO , array(
                                'controller' => 'purchases',
                                'action' => 'purchase_orders',
                                'admin' => false,
                            ), array(
                                'data-wrapper' => 'li',
                                'data-icon' => 'angle-double-right',
                                'data-active' => $active_menu,
                            ));
                            echo $this->Common->link($lblPOPayment , array(
                                'controller' => 'purchases',
                                'action' => 'payments',
                                'admin' => false,
                            ), array(
                                'data-wrapper' => 'li',
                                'data-icon' => 'angle-double-right',
                                'data-active' => $active_menu,
                            ));
                            echo $this->Common->link($lblPOReport , array(
                                'controller' => 'purchases',
                                'action' => 'reports',
                                'admin' => false,
                            ), array(
                                'data-wrapper' => 'li',
                                'data-icon' => 'angle-double-right',
                                'data-active' => $active_menu,
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'cogs', 'cogs_setting'
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'cogs', 'cogs_setting'
                        );

                        if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                            $activeSetting = 'active';
                        }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-wrench"></i>
                    <span>COGS</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Cost Center', array(
                                'controller' => 'settings',
                                'action' => 'cogs',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cogs' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pengaturan Cost Center', array(
                                'controller' => 'settings',
                                'action' => 'cogs_setting',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cogs_setting' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    }

                    $dataMenu = array(
                        'settings' => array(
                            'perlengkapan', 'banks',
                            'cities', 'companies', 'vendors',
                            'jenis_sim', 'classifications',
                            'calendar_colors', 'calendar_icons',
                            'parts_motor', 'index', 'approval_setting',
                            'branches',
                        ),
                    );

                    if( $this->Common->allowMenu( $dataMenu ) ) {
                        $activeSetting = false;
                        $settingMenu = array(
                            'cities', 'vendors', 'companies',
                            'perlengkapan', 'branches', 
                            'classifications', 'calendar_colors',
                            'calendar_icons', 'settings', 'jenis_sim',
                            'parts_motor', 'approval_setting',
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

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Supplier', array(
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pengaturan Approval', array(
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