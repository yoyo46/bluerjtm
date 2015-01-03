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
                        'customers', 'customer_groups'
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
                        'drivers', 'trucks', 'directions'
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
                    ?>
                </ul>
            </li>
            <?php
                    $activeMenu = false;
                    $dataMenu = array(
                        'kir', 'kir_payments'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                        $activeMenu = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span>KIR</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Perpanjang KIR', array(
                                'controller' => 'trucks',
                                'action' => 'kir',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran KIR', array(
                                'controller' => 'trucks',
                                'action' => 'kir_payments',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'kir_payments' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    $activeMenu = false;
                    $dataMenu = array(
                        'stnk', 'stnk_payments'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                        $activeMenu = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span>SNTK</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Perpanjang STNK', array(
                                'controller' => 'trucks',
                                'action' => 'stnk',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran STNK', array(
                                'controller' => 'trucks',
                                'action' => 'stnk_add',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php
                    $activeTtuj = false;
                    $ttujMenu = array(
                        'ttuj', 'truk_tiba', 'bongkaran',
                        'balik', 'pool'
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
                        'ritase_report'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $dataMenu) ) {
                        $activeMenu = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeMenu; ?>">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Laporan Truk</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Laporan Ritase', array(
                                'controller' => 'revenues',
                                'action' => 'ritase_report',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ritase_report' )?'active':'',
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
                    <span>Pembayaran</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori SIUP', array(
                                'controller' => 'trucks',
                                'action' => 'siup',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran SIUP', array(
                                'controller' => 'trucks',
                                'action' => 'siup_add',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    $activeSetting = false;
                    $settingMenu = array(
                        'cities', 'vendors', 'companies', 'uang_jalan',
                        'perlengkapan', 'coas', 'branches'
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Jalan', array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_jalan' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>